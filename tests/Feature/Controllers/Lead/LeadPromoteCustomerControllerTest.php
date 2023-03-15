<?php

namespace Tests\Feature\Controllers\Lead;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Lead;
use Tests\TestCase;

class LeadPromoteCustomerControllerTest extends TestCase
{
    /** @test */
    public function it_can_promote_from_lead_to_customer()
    {
        $lead = Lead::factory()->create(['status' => 'open']);

        $this->get('lead/promote/'.$lead->id)
            ->assertRedirect('/customer')
            ->assertSessionHas('status', 'success')
            ->assertSessionHas('message', 'Lead promoted to customer successfully');

        $customer = Customer::latest()->first();
        $lead = Lead::withTrashed()->find($lead->id);

        $this->assertTrue($lead->trashed());
        $this->assertEquals(array_except($lead->toArray(), 'id'), array_except($customer->toArray(), 'id'));
    }

    /** @test */
    public function it_can_promote_from_lead_to_customer_with_contacts()
    {
        $lead = Lead::factory()->has(Contact::factory()->count(2))->create(['status' => 'open']);

        $this->get('lead/promote/'.$lead->id);

        $customer = Customer::latest()->first();

        $this->assertEquals(array_except($lead->load('contacts')->toArray(), 'id'), array_except($customer->load('contacts')->toArray(), 'id'));
    }
}
