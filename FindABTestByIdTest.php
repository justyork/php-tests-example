<?php

namespace App\Containers\AppSection\ABTest\UI\API\Tests\Functional;

use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestPermissions;
use App\Containers\AppSection\ABTest\Models\ABTest;
use App\Containers\AppSection\ABTest\UI\API\Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class FindABTestByIdTest extends ApiTestCase
{
    protected string $endpoint = 'get@v1/ab-tests/{id}';

    protected array $access = [
        'permissions' => ABTestPermissions::FIND->value,
        'roles' => '',
    ];

    public function testFindABTestById(): void
    {
        $aBTest = ABTest::factory()->create();

        $response = $this->injectId($aBTest->id)->makeCall();

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.id', $aBTest->id)
                ->where('data.name', $aBTest->name)
                ->where('data.status', $aBTest->status->value)
                ->etc()
        );
    }

    public function testFindABTestByName(): void
    {
        $aBTest = ABTest::factory()->create();

        $response = $this->injectId($aBTest->name)->makeCall();

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.id', $aBTest->id)
                ->where('data.name', $aBTest->name)
                ->where('data.status', $aBTest->status->value)
                ->etc()
        );
    }

    public function testFindNonExistingABTest(): void
    {
        $invalidId = 7777;

        $response = $this->injectId($invalidId)->makeCall([]);

        $response->assertStatus(404);
    }
}
