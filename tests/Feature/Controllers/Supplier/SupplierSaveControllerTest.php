<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Supplier;

use App\Models\Supplier;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupplierSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_supplier(): void
    {
        $data = Supplier::factory()->create()->toArray();

        $response = $this->post('/supplier/save', $data);

        $response->assertRedirect('/supplier');
        $this->equalTo(Supplier::all()->last(), $data);
    }

    #[Test]
    public function it_can_update_supplier(): void
    {
        $data = Supplier::factory()->create()->toArray();
        unset($data['id']);

        $response = $this->post('/supplier/save', $data);

        $response->assertRedirect('/supplier');
        $this->equalTo(Supplier::all()->last(), $data);
    }
}
