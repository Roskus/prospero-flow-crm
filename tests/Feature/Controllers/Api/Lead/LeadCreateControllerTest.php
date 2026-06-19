<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Lead;

use App\Models\Lead;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadCreateControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_lead(): void
    {
        $this->actingAs($this->user, 'api');

        $data = array_except(Lead::factory()->create(['seller_id' => auth()->id()])->toArray(), 'id');
        $data['tags'] = implode(',', $data['tags']);

        $response = $this->post('/api/lead', $data);

        $response->assertStatus(201);
        $response->assertJsonPath('id', Lead::all()->last()->id);

        $this->equalTo(Lead::all()->last(), $data);
    }

    #[Test]
    public function it_denies_user_without_permission(): void
    {
        $userWithoutPermission = User::factory()->create();

        $this->actingAs($userWithoutPermission, 'api');

        $data = array_except(Lead::factory()->create(['seller_id' => $userWithoutPermission->id])->toArray(), 'id');
        $data['tags'] = implode(',', $data['tags']);

        $response = $this->post('/api/lead', $data);

        $response->assertStatus(403);
    }

    #[Test]
    public function it_denies_seller_from_different_company(): void
    {
        $userFromDifferentCompany = User::factory()->create();

        $this->actingAs($this->user, 'api');

        $data = array_except(Lead::factory()->create(['seller_id' => $userFromDifferentCompany->id])->toArray(), 'id');
        $data['tags'] = implode(',', $data['tags']);

        $response = $this->postJson('/api/lead', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('seller_id');
    }
}
