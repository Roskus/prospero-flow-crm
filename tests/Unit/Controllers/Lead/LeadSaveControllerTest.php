<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Lead;

use App\Http\Controllers\Lead\LeadSaveController;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class LeadSaveControllerTest extends TestCase
{
    /** @test */
    public function it_can_save_urls(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $urls = [
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

        foreach ($urls as $url) {
            $data = [
                'name' => 'Lead name',
                'website' => $url['wrong_url'],
                'linkedin' => $url['wrong_url'],
                'facebook' => $url['wrong_url'],
                'instagram' => $url['wrong_url'],
                'twitter' => $url['wrong_url'],
                'youtube' => $url['wrong_url'],
                'tiktok' => $url['wrong_url'],
            ];
            $request = Request::create('lead/save', 'POST', $data);

            $contoller = new LeadSaveController($request);
            $response = $contoller->save($request);

            $last_lead = Lead::all()->last();
            $this->assertEquals($last_lead->name, $data['name']);

            $this->assertEquals($last_lead->website, $url['correct_url']);

            $this->assertEquals($last_lead->linkedin, $url['correct_url']);
            $this->assertEquals($last_lead->facebook, $url['correct_url']);
            $this->assertEquals($last_lead->instagram, $url['correct_url']);
            $this->assertEquals($last_lead->twitter, $url['correct_url']);
            $this->assertEquals($last_lead->youtube, $url['correct_url']);
            $this->assertEquals($last_lead->tiktok, $url['correct_url']);

            $this->assertEquals($response->status(), 302);
        }
    }
}
