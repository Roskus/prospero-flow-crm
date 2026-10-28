<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Dashboard;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeRedirectTest extends TestCase
{
    #[Test]
    public function home_redirects_to_the_dashboard(): void
    {
        $response = $this->get('/home');

        $response->assertRedirect('/');
    }
}
