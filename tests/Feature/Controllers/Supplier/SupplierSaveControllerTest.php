<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Supplier;

use App\Models\Supplier;
use App\Models\User;
use Tests\TestCase;

class SupplierSaveControllerTest extends TestCase
{
    /** @test */
    public function it_can_save_supplier(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = Supplier::factory()->create()->toArray();

        $response = $this->post('supplier/save', $data);

        $response->assertRedirect('/supplier');
        $this->equalTo(Supplier::all()->last(), $data);
    }

    /** @test */
    public function it_can_update_supplier(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = Supplier::factory()->create()->toArray();
        unset($data['id']);

        $response = $this->post('supplier/save', $data);

        $response->assertRedirect('/supplier');
        $this->equalTo(Supplier::all()->last(), $data);
    }
}
