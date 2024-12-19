<?php

namespace App\Containers\AppSection\ABTest\UI\API\Tests\Functional;

use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestPermissions;
use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestStatus;
use App\Containers\AppSection\ABTest\Models\ABTest;
use App\Containers\AppSection\ABTest\UI\API\Tests\ApiTestCase;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;


class UpdateABTestTest extends ApiTestCase
{
    protected string $endpoint = 'put@v1/ab-tests/{id}';

    protected array $access = [
        'permissions' => ABTestPermissions::UPDATE->value,
        'roles' => '',
    ];

    public function testUpdateExistingABTest(): void
    {
        $aBTest = ABTest::factory()->create([
            'name' => fake()->name,
        ]);
        $data = [
            'name' => 'test-name',
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

        $response = $this->injectId($aBTest->name)
            ->makeCall($data);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.id', $aBTest->id)
                ->where('data.name', $data['name'])
                ->etc()
        );
    }

    public function testUpdateNonExistingABTest(): void
    {
        $invalidId = 7777;

        $response = $this->injectId($invalidId)->makeCall([]);

        $response->assertStatus(422);
    }

    public function testUpdateExistingABTestWithEmptyValues(): void
    {
        $aBTest = ABTest::factory()->create();
        $data = [
        ];

        $response = $this->injectId($aBTest->id)->makeCall($data);

        $response->assertStatus(422);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('errors')
                ->where('errors.name.0', 'The name field is required.')
                ->etc()
        );
    }
}
