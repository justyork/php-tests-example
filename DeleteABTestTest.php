<?php

namespace App\Containers\AppSection\ABTest\UI\API\Tests\Functional;

use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestPermissions;
use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestStatus;
use App\Containers\AppSection\ABTest\Models\ABTest;
use App\Containers\AppSection\ABTest\UI\API\Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class DeleteABTestTest extends ApiTestCase
{
    protected string $endpoint = 'delete@v1/ab-tests/{id}';

    protected array $access = [
        'permissions' => ABTestPermissions::DELETE->value,
        'roles' => '',
    ];

    public function testDeleteExistingABTest(): void
    {
        $aBTest = ABTest::factory()->create();

        $response = $this->injectId($aBTest->id)->makeCall();

        $response->assertStatus(204);
    }

    public function testDeleteExistingNonDraftABTest(): void
    {
        $aBTest = ABTest::factory()->create([
            'status' => ABTestStatus::Active,
        ]);

        $response = $this->injectId($aBTest->id)->makeCall();

        $response->assertStatus(417);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('message')
                ->where('message', 'Not allowed to delete non draft test')
                ->etc()
        );
    }

    public function testDeleteNonExistingABTest(): void
    {
        $invalidId = 7777;

        $response = $this->injectId($invalidId)->makeCall([]);

        $response->assertStatus(404);
    }
}
