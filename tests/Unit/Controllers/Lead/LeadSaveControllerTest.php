<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Lead;

use App\Http\Controllers\Lead\LeadSaveController;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class LeadSaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider urlProvider
     */
    public function it_can_save_urls($wrong_url, $correct_url): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'name' => 'Lead name',
            'website' => $wrong_url,
            'linkedin' => $wrong_url,
            'facebook' => $wrong_url,
            'instagram' => $wrong_url,
            'twitter' => $wrong_url,
            'youtube' => $wrong_url,
            'tiktok' => $wrong_url,
        ];
        $request = Request::create('lead/save', 'POST', $data);

        $contoller = new LeadSaveController($request);
        $response = $contoller->save($request);

        $last_lead = Lead::all()->last();
        $this->assertEquals($last_lead->name, $data['name']);

        $this->assertEquals($last_lead->website, $correct_url);

        $this->assertEquals($last_lead->linkedin, $correct_url);
        $this->assertEquals($last_lead->facebook, $correct_url);
        $this->assertEquals($last_lead->instagram, $correct_url);
        $this->assertEquals($last_lead->twitter, $correct_url);
        $this->assertEquals($last_lead->youtube, $correct_url);
        $this->assertEquals($last_lead->tiktok, $correct_url);

        $this->assertEquals($response->status(), 302);
    }

    public static function urlProvider(): array
    {
        return [
            [
                'wrong_url' => null,
                'correct_url' => null,
            ],
            [
                'wrong_url' => '     https://roskus.com/es      ',
                'correct_url' => 'https://roskus.com/es',
            ],
            [
                'wrong_url' => 'https://www.linkedin.com/company/roskus/?lipi=urn%3Ali%3Apage%3Acompanies_company_about_index%3B249fb58b-77f0-412a-9cc3-27ffd637c002',
                'correct_url' => 'https://www.linkedin.com/company/roskus/',
            ],
        ];
    }
}
