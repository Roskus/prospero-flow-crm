<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Asset;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssetAssignmentControllerTest extends TestCase
{
    private function colleague(array $attributes = []): User
    {
        return User::factory()->create(['company_id' => $this->user->company_id, ...$attributes]);
    }

    #[Test]
    public function it_assigns_an_available_asset_to_a_user_of_the_company(): void
    {
        $ana = $this->colleague(['first_name' => 'Ana']);
        $notebook = Asset::factory()->forCompany($this->user->company_id)->device()->create();

        $this->post('/inventory/assign/'.$notebook->id, ['user_id' => $ana->id, 'notes' => 'Con cargador'])
            ->assertRedirect(route('asset.show', $notebook->id));

        $notebook->refresh();
        $this->assertSame(AssetStatus::Assigned, $notebook->status);
        $this->assertSame($ana->id, $notebook->currentAssignment->user_id);
        $this->assertTrue($notebook->currentAssignment->start_date->isToday());
        $this->assertSame($this->user->id, $notebook->currentAssignment->assigned_by);
        $this->get(route('asset.show', $notebook->id))->assertSeeText('Ana');
    }

    #[Test]
    public function the_user_must_be_an_active_user_of_the_company(): void
    {
        $notebook = Asset::factory()->forCompany($this->user->company_id)->device()->create();
        $foreign = User::factory()->create();
        $inactive = $this->colleague(['active' => false]);

        $this->post('/inventory/assign/'.$notebook->id, ['user_id' => $foreign->id])->assertSessionHasErrors('user_id');
        $this->post('/inventory/assign/'.$notebook->id, ['user_id' => $inactive->id])->assertSessionHasErrors('user_id');
        $this->post('/inventory/assign/'.$notebook->id, [])->assertSessionHasErrors('user_id');

        $this->assertSame(AssetStatus::Available, $notebook->fresh()->status);
    }

    #[Test]
    public function an_assigned_asset_must_be_returned_before_assigning_it_again(): void
    {
        $ana = $this->colleague();
        $luis = $this->colleague();
        $notebook = Asset::factory()->forCompany($this->user->company_id)->device()->assignedTo($ana)->create();

        $this->post('/inventory/assign/'.$notebook->id, ['user_id' => $luis->id])
            ->assertSessionHasErrors(['user_id' => __('The asset must be returned before assigning it again.')]);

        $this->assertSame(1, $notebook->assignments()->count());
        $this->assertSame($ana->id, $notebook->fresh()->currentAssignment->user_id);
    }

    #[Test]
    public function retired_or_in_maintenance_assets_cannot_be_assigned(): void
    {
        $ana = $this->colleague();
        $retired = Asset::factory()->forCompany($this->user->company_id)->device()->retired()->create();
        $inWorkshop = Asset::factory()->forCompany($this->user->company_id)->vehicle()->inMaintenance()->create();

        $this->post('/inventory/assign/'.$retired->id, ['user_id' => $ana->id])->assertSessionHasErrors('user_id');
        $this->post('/inventory/assign/'.$inWorkshop->id, ['user_id' => $ana->id])->assertSessionHasErrors('user_id');

        $this->assertDatabaseCount('asset_assignment', 0);
    }

    #[Test]
    public function it_returns_an_assigned_asset(): void
    {
        $ana = $this->colleague();
        $notebook = Asset::factory()->forCompany($this->user->company_id)->device()->assignedTo($ana)->create();

        $this->post('/inventory/return/'.$notebook->id, ['notes' => 'Pantalla rayada'])
            ->assertRedirect(route('asset.show', $notebook->id));

        $notebook->refresh();
        $assignment = $notebook->assignments()->first();
        $this->assertSame(AssetStatus::Available, $notebook->status);
        $this->assertTrue($assignment->end_date->isToday());
        $this->assertStringContainsString('Pantalla rayada', $assignment->notes);
    }

    #[Test]
    public function the_return_date_cannot_be_before_the_start_date(): void
    {
        $ana = $this->colleague();
        $notebook = Asset::factory()->forCompany($this->user->company_id)->device()->assignedTo($ana)->create();
        $start = $notebook->currentAssignment->start_date;

        $this->post('/inventory/return/'.$notebook->id, ['end_date' => $start->copy()->subDay()->toDateString()])
            ->assertSessionHasErrors('end_date');

        $this->assertSame(AssetStatus::Assigned, $notebook->fresh()->status);
    }

    #[Test]
    public function returning_an_asset_that_is_not_assigned_fails(): void
    {
        $notebook = Asset::factory()->forCompany($this->user->company_id)->device()->create();

        $this->post('/inventory/return/'.$notebook->id)->assertSessionHasErrors('end_date');
    }

    #[Test]
    public function the_history_lists_every_assignment_newest_first_even_for_deleted_users(): void
    {
        $ana = $this->colleague(['first_name' => 'Ana', 'last_name' => 'Antigua']);
        $luis = $this->colleague(['first_name' => 'Luis', 'last_name' => 'Actual']);
        $notebook = Asset::factory()->forCompany($this->user->company_id)->device()->assignedTo($luis)->create();
        AssetAssignment::factory()->create([
            'asset_id' => $notebook->id,
            'user_id' => $ana->id,
            'start_date' => today()->subYear(),
            'end_date' => today()->subMonths(2),
        ]);
        $ana->delete();

        $this->get(route('asset.show', $notebook->id))
            ->assertOk()
            ->assertSeeTextInOrder([__('Assignment history'), 'Luis Actual', 'Ana Antigua']);
    }

    #[Test]
    public function assignment_routes_do_not_reach_other_companies(): void
    {
        $other = Asset::factory()->device()->create();

        $this->post('/inventory/assign/'.$other->id, ['user_id' => $this->user->id])->assertNotFound();
        $this->post('/inventory/return/'.$other->id)->assertNotFound();
    }

    #[Test]
    public function an_open_assignment_can_be_corrected_but_stays_open(): void
    {
        $holder = $this->colleague();
        $rightHolder = $this->colleague();
        $asset = Asset::factory()->forCompany($this->user->company_id)->device()->assignedTo($holder)->create();
        $assignment = $asset->currentAssignment()->firstOrFail();

        $this->get(route('asset.show', $asset->id))->assertSee('/inventory/assignment/update/'.$assignment->id, false);
        $this->get('/inventory/assignment/update/'.$assignment->id)->assertOk()->assertSeeText(__('Still assigned. Return the asset to close it.'));

        $this->post('/inventory/assignment/save', ['id' => $assignment->id, 'user_id' => $rightHolder->id, 'start_date' => '2026-11-02', 'notes' => 'Fecha corregida'])
            ->assertRedirect(route('asset.show', $asset->id));

        $assignment->refresh();
        $this->assertSame($rightHolder->id, $assignment->user_id);
        $this->assertSame('2026-11-02', $assignment->start_date->toDateString());
        $this->assertNull($assignment->end_date);
        $this->assertSame('Fecha corregida', $assignment->notes);

        $this->post('/inventory/assignment/save', ['id' => $assignment->id, 'user_id' => $rightHolder->id, 'start_date' => '2026-11-02', 'end_date' => '2026-11-10'])
            ->assertSessionHasErrors('end_date');
    }

    #[Test]
    public function a_closed_assignment_needs_a_return_date_not_before_its_start(): void
    {
        $holder = $this->colleague();
        $assignment = AssetAssignment::factory()->create([
            'asset_id' => Asset::factory()->forCompany($this->user->company_id)->device()->create()->id,
            'user_id' => $holder->id,
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-20',
        ]);
        $data = ['id' => $assignment->id, 'user_id' => $holder->id, 'start_date' => '2026-10-05'];

        $this->post('/inventory/assignment/save', $data)->assertSessionHasErrors('end_date');
        $this->post('/inventory/assignment/save', [...$data, 'end_date' => '2026-10-04'])->assertSessionHasErrors('end_date');
        $this->post('/inventory/assignment/save', [...$data, 'end_date' => '2026-10-25'])->assertSessionHasNoErrors();

        $this->assertSame('2026-10-25', $assignment->fresh()->end_date->toDateString());
    }

    #[Test]
    public function corrected_dates_cannot_overlap_another_assignment_but_can_hand_over_the_same_day(): void
    {
        $first = $this->colleague();
        $second = $this->colleague();
        $asset = Asset::factory()->forCompany($this->user->company_id)->device()->create();
        $previous = AssetAssignment::factory()->create(['asset_id' => $asset->id, 'user_id' => $first->id, 'start_date' => '2026-09-01', 'end_date' => '2026-10-01']);
        $current = AssetAssignment::factory()->create(['asset_id' => $asset->id, 'user_id' => $second->id, 'start_date' => '2026-10-01', 'end_date' => null]);

        $this->post('/inventory/assignment/save', ['id' => $current->id, 'user_id' => $second->id, 'start_date' => '2026-09-15'])->assertSessionHasErrors('start_date');
        $this->post('/inventory/assignment/save', ['id' => $previous->id, 'user_id' => $first->id, 'start_date' => '2026-09-01', 'end_date' => '2026-10-05'])->assertSessionHasErrors('start_date');
        $this->post('/inventory/assignment/save', ['id' => $previous->id, 'user_id' => $first->id, 'start_date' => '2026-08-20', 'end_date' => '2026-10-01'])->assertSessionHasNoErrors();
    }

    #[Test]
    public function the_holder_must_be_an_active_user_of_the_company_unless_it_is_the_recorded_one(): void
    {
        $holder = $this->colleague();
        $asset = Asset::factory()->forCompany($this->user->company_id)->device()->assignedTo($holder)->create();
        $assignment = $asset->currentAssignment()->firstOrFail();
        $holder->forceFill(['active' => false])->save();
        $data = ['id' => $assignment->id, 'start_date' => $assignment->start_date->toDateString()];

        $this->post('/inventory/assignment/save', [...$data, 'user_id' => User::factory()->create()->id])->assertSessionHasErrors('user_id');
        $this->post('/inventory/assignment/save', [...$data, 'user_id' => $this->colleague(['active' => false])->id])->assertSessionHasErrors('user_id');
        $this->post('/inventory/assignment/save', [...$data, 'user_id' => $holder->id])->assertSessionHasNoErrors();
    }

    #[Test]
    public function assignments_of_another_company_cannot_be_edited(): void
    {
        $other = AssetAssignment::factory()->create();

        $this->get('/inventory/assignment/update/'.$other->id)->assertNotFound();
        $this->post('/inventory/assignment/save', ['id' => $other->id, 'user_id' => $this->user->id, 'start_date' => '2026-11-01'])->assertNotFound();
    }
}
