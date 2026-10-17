<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\User;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserDeleteControllerTest extends TestCase
{
    #[Test]
    public function super_admin_can_delete_a_user_of_another_company(): void
    {
        $otherCompanyUser = User::factory()->create();

        $response = $this->delete('/user/delete/'.$otherCompanyUser->id);

        $response->assertRedirect('/user');
        $this->assertSoftDeleted('user', ['id' => $otherCompanyUser->id]);
    }

    #[Test]
    public function it_returns_not_found_for_a_missing_user(): void
    {
        $response = $this->delete('/user/delete/999999');

        $response->assertNotFound();
    }
}
