<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadCreateControllerTest extends TestCase
{
    #[Test]
    public function it_can_create_lead()
    {
        $response = $this->get('/lead/create');

        $response->assertOk();
        $response->assertViewIs('lead.lead');
        $response->assertSee(__('Name'));
        $response->assertSee(__('Phone'));
        $response->assertSee(__('Country'));
        $response->assertSee(__('Street'));
        $response->assertSee(__('Facebook'));
        $response->assertSee(__('Status'));
        $response->assertSee(__('Seller'));
    }

    #[Test]
    public function it_keeps_tags_from_old_input_after_validation_error(): void
    {
        $response = $this->withSession(['_old_input' => ['tags' => 'vip,panama']])
            ->get('/lead/create');

        $response->assertOk();
        $response->assertSee('vip,panama');
    }
}
