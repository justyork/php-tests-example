<?php
declare(strict_types=1);

namespace App\Containers\AppSection\ABTest\UI\API\Tests\Functional;

use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestPermissions;
use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestStatus;
use App\Containers\AppSection\ABTest\UI\API\Tests\ApiTestCase;
use App\Containers\ProjectSection\Project\Models\Project;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;

class CreateABTestTest extends ApiTestCase
{
    protected string $endpoint = 'post@v1/ab-tests';

    protected array $access = [
        'permissions' => ABTestPermissions::CREATE->value,
        'roles' => '',
    ];

    public function test_create_ab_test(): void
    {
        $defaultProject = Project::factory()->create();

        $data = [
            'name' => str(fake()->word)->snake()->toString(),
            'description' => fake()->text,
            'groups' => [
                [
                    'name' => fake()->name,
                    'percent' => 30,
                ],
            ],
            'started_at' => Carbon::now(),
            'finished_at' => Carbon::now()->addWeek(),
            'affiliation' => null,
            'link' => fake()->url,
            'status' => ABTestStatus::Draft->value,
            'priority' => 40,
            'percent' => 50,
            'conditions' => [],
        ];

        $projectKey = config('projectSection-project.header');

        // send the HTTP request
        $response = $this->makeCall($data, headers: [
            $projectKey => $defaultProject->key,
        ]);

        // assert the response status
        $response->assertStatus(201);

        // make other asserts here
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.name', $data['name'])
                ->where('data.project.id', $defaultProject->id)
                ->etc()
        );
    }
}
