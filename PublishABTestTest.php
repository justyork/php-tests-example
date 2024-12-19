<?php

namespace App\Containers\AppSection\ABTest\UI\API\Tests\Functional;

use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestPermissions;
use App\Containers\AppSection\ABTest\Data\Dictionaries\ABTestStatus;
use App\Containers\AppSection\ABTest\Models\ABTest;
use App\Containers\AppSection\ABTest\UI\API\Tests\ApiTestCase;
use App\Containers\AppSection\Logging\Data\Enums\LogAction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

class PublishABTestTest extends ApiTestCase
{
    protected string $endpoint = 'post@v1/ab-tests/{id}/publish';

    protected array $access = [
        'permissions' => ABTestPermissions::PUBLISH->value,
        'roles' => '',
    ];

    public function testPublishABTest(): void
    {
        Queue::fake();
        $aBTest = ABTest::factory()->create([
            'status' => ABTestStatus::Active->value,
        ]);

        Http::fake([
            '*' => Http::response(['field' => 'bar']),
        ]);

        $response = $this->injectId($aBTest->id)->makeCall();
        $response->assertStatus(200);

        $this->assertDatabaseHas('logs', [
            'model_id' => $aBTest->id,
            'action' => LogAction::Publish->value,
        ]);
    }

    public function testPublishDraftABTest(): void
    {
        Queue::fake();
        $aBTest = ABTest::factory()->create([
            'status' => ABTestStatus::Draft->value,
        ]);

        Http::fake([
            '*' => Http::response(['field' => 'bar']),
        ]);

        $response = $this->injectId($aBTest->id)->makeCall();
        $response->assertStatus(422);

    }

    public function testUpdateNonExistingABTest(): void
    {
        $invalidId = 7777;

        $response = $this->injectId($invalidId)->makeCall([]);

        $response->assertStatus(422);
    }
}
