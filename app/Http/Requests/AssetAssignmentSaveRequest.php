<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\AssetAssignment;
use App\Models\User;
use App\Services\AssetService;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Corrects a recorded assignment: holder, dates and notes. An open assignment stays open;
 * it is closed by returning the asset.
 */
class AssetAssignmentSaveRequest extends FormRequest
{
    private ?AssetAssignment $assignment = null;

    public function authorize(): bool
    {
        return Auth::user()?->can('update asset') ?? false;
    }

    public function assignment(): AssetAssignment
    {
        return $this->assignment ??= AssetAssignment::query()
            ->whereHas('asset', fn (Builder $query) => $query->where('company_id', (int) Auth::user()->company_id))
            ->findOrFail($this->integer('id'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $assignment = $this->assignment();

        return [
            'id' => ['required', 'integer'],
            'user_id' => ['required', 'integer', $this->holderRule($assignment)],
            'start_date' => ['required', 'date', $this->noOverlapRule($assignment)],
            'end_date' => $assignment->isOpen()
                ? ['prohibited']
                : ['required', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * An active user of the company, or the recorded holder even if deactivated since.
     */
    private function holderRule(AssetAssignment $assignment): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($assignment): void {
            if ((int) $value === $assignment->user_id) {
                return;
            }

            $isActiveCompanyUser = User::query()
                ->whereKey((int) $value)
                ->where('company_id', (int) Auth::user()->company_id)
                ->where('active', true)
                ->exists();

            if (! $isActiveCompanyUser) {
                $fail(__('The selected user is not valid.'));
            }
        };
    }

    /**
     * The asset can only have one holder at a time. Handing it over on the same day is allowed:
     * one assignment may end the day the next one starts. Unparseable dates are left to their
     * own date rules; the save repeats this check under the asset lock.
     */
    private function noOverlapRule(AssetAssignment $assignment): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($assignment): void {
            $start = $this->parseDate($value);
            $end = $assignment->isOpen() ? null : $this->parseDate($this->input('end_date'));

            if ($start === null || (! $assignment->isOpen() && $end === null)) {
                return;
            }

            if (app(AssetService::class)->assignmentOverlaps($assignment->asset_id, $start, $end, $assignment->id)) {
                $fail(__('The dates overlap with another assignment of this asset.'));
            }
        };
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || strtotime($value) === false) {
            return null;
        }

        return Carbon::parse($value)->startOfDay();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => __('Select the user who receives the asset.'),
            'end_date.required' => __('The return date is required for a closed assignment.'),
            'end_date.after_or_equal' => __('The return date cannot be earlier than the assignment start date.'),
            'end_date.prohibited' => __('Return the asset to close the assignment.'),
        ];
    }
}
