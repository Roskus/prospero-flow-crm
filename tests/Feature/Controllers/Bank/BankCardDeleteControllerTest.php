<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BankCardDeleteControllerTest extends TestCase
{
    #[Test]
    public function user_with_only_read_permission_cannot_delete_a_bank_card(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('read accounting', 'web'));

        $response = $this->actingAs($user)->delete('/bank-card/delete/1');

        $response->assertForbidden();
    }

    #[Test]
    public function user_with_delete_permission_passes_the_permission_gate(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('delete accounting', 'web'));

        $response = $this->actingAs($user)->delete('/bank-card/delete/999999');

        $response->assertNotFound();
    }
}
