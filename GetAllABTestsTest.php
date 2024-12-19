<?php

namespace App\Containers\AppSection\ABTest\UI\API\Tests\Functional;

use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestPermissions;
use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestStatus;
use App\Containers\AppSection\ABTest\Models\ABTest;
use App\Containers\AppSection\ABTest\UI\API\Tests\ApiTestCase;
use App\Containers\AppSection\User\Models\User;
use App\Containers\ProjectSection\Project\Models\Project;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;

class GetAllABTestsTest extends ApiTestCase
{
    protected string $endpoint = 'get@v1/ab-tests';

    protected array $access = [
        'permissions' => ABTestPermissions::All->value,
        'roles' => '',
    ];

    public function testGetAllABTestsByAdmin(): void
    {
        $project = Project::factory()->create();
        $this->getTestingUserWithoutAccess(createUserAsAdmin: true);
        ABTest::factory()->count(2)->create([
            'project_id' => $project->id,
        ]);

        $response = $this->makeCall(headers: [
            config('projectSection-project.header') => $project->key,
        ]);

        $response->assertStatus(200);
        $responseContent = $this->getResponseContentObject();

        $this->assertCount(2, $responseContent->data);
    }

    /** @dataProvider filterDataProvider */
    public function testSearchABTestsByFields(string $key, mixed $value): void
    {
        $project = Project::factory()->create();
        $author = User::factory()->create();
        $publisher = User::factory()->create();
        ABTest::factory()->count(3)->create([
            'project_id' => $project->id,
        ]);
        // create a model with specific field values
        $aBTest = ABTest::factory()->create([
            'project_id' => $project->id,
            'name' => fake()->name,
            'status' => ABTestStatus::Active,
            'first_published_at' => Carbon::now()->subDays(5),
            'published_at' => Carbon::now()->subDays(2),
            'created_at' => Carbon::now()->subDays(10),
            'created_by' => $author->id,
            'published_by' => $publisher->id,
            'first_published_by' => $publisher->id,
        ]);

        $search = [];
        if (is_array($value)) {
            if ($value[array_key_first($value)] instanceof Carbon) {
                foreach ($value as $k => $data) {
                    $search[$key."[$k]"] = $data->format('Y-m-d H:i:s');
                }
            } else {
                $search[$key] = $value;
            }

        } elseif ($value === null) {
            $search[$key] = $aBTest->$key;
        } else {
            $search[$key] = $value;
        }

        // search by the above values
        $response = $this->makeCall(data: $search, headers: [
            config('projectSection-project.header') => $project->key,
        ]);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.0.name', $aBTest->name)
                ->where('data.0.is_published', (bool) $aBTest->published_at)
                ->where('data.0.author.id', $author->id)
                ->where('data.0.publisher.id', $publisher->id)
                ->where('data.0.first_publisher.id', $publisher->id)
                ->etc()
        );
    }

    public function filterDataProvider(): array
    {
        return [
            'name' => ['key' => 'name', 'value' => null],
            'status' => ['key' => 'status', 'value' => [ABTestStatus::Active->value, ABTestStatus::Paused->value]],
            'is_published' => ['key' => 'is_published', 'value' => true],
            'created_at' => ['key' => 'created_at', 'value' => [
                0 => Carbon::now()->subDays(11),
                1 => Carbon::now()->subDays(9),
            ]],
            'published_at' => ['key' => 'published_at', 'value' => [
                0 => Carbon::now()->subDays(3),
                1 => Carbon::now()->subDays(),
            ]],
            'first_published_between' => ['key' => 'first_published_at', 'value' => [
                0 => Carbon::now()->subDays(7),
                1 => Carbon::now()->subDays(3),
            ]],
            'first_published_greater' => ['key' => 'first_published_at', 'value' => [
                0 => Carbon::now()->subDays(7),
            ]],
            'first_published_less' => ['key' => 'first_published_at', 'value' => [
                1 => Carbon::now()->subDays(3),
            ]],
            'created_by' => ['key' => 'created_by', 'value' => null],
            'published_by' => ['key' => 'published_by', 'value' => null],
            'first_published_by' => ['key' => 'first_published_by', 'value' => null],
        ];
    }
}
