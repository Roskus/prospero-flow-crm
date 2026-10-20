<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Repositories\CompanyRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Only place that writes user_company and user.default_company_id, so both stay in sync.
 */
class CompanyAccessService
{
    public function __construct(
        private SecurityLogger $securityLogger,
        private CompanyRepository $companyRepository,
    ) {}

    /**
     * Create a client company of the user's default company and grant the user access to it. The parent company
     * row is locked so concurrent creations cannot exceed its quota.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws AuthorizationException
     */
    public function createClientCompany(User $user, array $data): Company
    {
        return DB::transaction(function () use ($user, $data): Company {
            $parentCompany = Company::query()->lockForUpdate()->findOrFail($user->defaultCompanyId());

            if ($parentCompany->availableClientQuota() === 0) {
                throw new AuthorizationException(__('The companies quota has been reached.'));
            }

            $clientCompany = $this->companyRepository->save([...$data, 'parent_company_id' => $parentCompany->id]);
            $this->grant($user, $clientCompany, $user);

            return $clientCompany;
        });
    }

    /**
     * The first extra company stores the user's current company as their default company. A company without
     * parent becomes a client company of the user's default company, so it counts against its quota.
     */
    public function grant(User $user, Company $company, ?User $grantedBy = null): void
    {
        if ((int) $company->id === $user->defaultCompanyId()) {
            return;
        }

        DB::transaction(function () use ($user, $company, $grantedBy): void {
            if (! $user->hasMultiCompanyAccess()) {
                $user->default_company_id = $user->company_id;
                $user->save();
            }

            $user->extraCompanies()->syncWithoutDetaching([
                $company->id => ['granted_by' => $grantedBy?->id],
            ]);

            $this->assignParentCompany($company, $user->defaultCompanyId());
        });

        $this->forgetCompanies($user);
        $this->securityLogger->log('company_access_granted', [
            'target_user_id' => $user->id,
            'company_id' => $company->id,
            'parent_company_id' => $company->parent_company_id,
        ]);
    }

    /**
     * Only one level of client companies: the company must have no parent nor client companies, and the
     * parent must not be a client company itself.
     */
    private function assignParentCompany(Company $company, int $parentCompanyId): void
    {
        if ($company->parent_company_id !== null || $company->clientCompanies()->withTrashed()->exists()) {
            return;
        }

        if (Company::query()->whereKey($parentCompanyId)->whereNotNull('parent_company_id')->exists()) {
            return;
        }

        $company->parent_company_id = $parentCompanyId;
        $company->save();
    }

    /**
     * Revoking the active company moves the user back to their default company; revoking the last extra company clears the default.
     */
    public function revoke(User $user, Company $company): void
    {
        if ((int) $company->id === $user->defaultCompanyId()) {
            throw new InvalidArgumentException('The default company of a user cannot be revoked.');
        }

        DB::transaction(function () use ($user, $company): void {
            $user->extraCompanies()->detach($company->id);

            if ((int) $user->company_id === (int) $company->id) {
                $user->company_id = $user->defaultCompanyId();
            }

            if (! $user->extraCompanies()->exists()) {
                $user->company_id = $user->defaultCompanyId();
                $user->default_company_id = null;
            }

            $user->save();
        });

        $this->forgetCompanies($user);
        $this->securityLogger->log('company_access_revoked', [
            'target_user_id' => $user->id,
            'company_id' => $company->id,
        ]);
    }

    /**
     * Grant and revoke so the user's extra companies match the given ids (the default company is ignored).
     *
     * @param  array<int, int|string|null>  $companyIds
     */
    public function syncExtraCompanies(User $user, array $companyIds, ?User $grantedBy = null): void
    {
        $wantedIds = collect($companyIds)
            ->filter()
            ->map(fn ($companyId): int => (int) $companyId)
            ->reject(fn (int $companyId): bool => $companyId === $user->defaultCompanyId())
            ->unique();
        $user->extraCompanies()->detach($user->defaultCompanyId());
        $currentIds = $user->extraCompanies()->pluck('company.id')->map(fn ($companyId): int => (int) $companyId);

        Company::query()->whereIn('id', $wantedIds->diff($currentIds))->get()
            ->each(fn (Company $company) => $this->grant($user->refresh(), $company, $grantedBy));

        Company::withTrashed()->whereIn('id', $currentIds->diff($wantedIds))->get()
            ->each(fn (Company $company) => $this->revoke($user->refresh(), $company));
    }

    /**
     * Default and extra companies that are not deleted, cached for the navbar switcher: the default company
     * first and the rest by name.
     *
     * @return Collection<int, array{id: int, name: string}>
     */
    public function accessibleCompanies(User $user): Collection
    {
        if (! $user->hasMultiCompanyAccess()) {
            return collect();
        }

        return Cache::rememberForever(self::cacheKey($user->id), function () use ($user): Collection {
            $defaultCompanyId = $user->defaultCompanyId();
            $companyIds = $user->extraCompanies()->pluck('company.id')->push($defaultCompanyId);

            return Company::query()
                ->whereIn('id', $companyIds)
                ->orderByRaw('id = ? DESC', [$defaultCompanyId])
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (Company $company): array => ['id' => (int) $company->id, 'name' => $company->name]);
        });
    }

    public function forgetCompanies(User $user): void
    {
        Cache::forget(self::cacheKey($user->id));
    }

    /**
     * Forget the switcher of every user who can access the company, e.g. after it is deleted or restored.
     */
    public function forgetCompanyUsers(Company $company): void
    {
        User::query()
            ->multiCompany()
            ->where(fn ($query) => $query->where('default_company_id', $company->id)
                ->orWhereHas('extraCompanies', fn ($query) => $query->whereKey($company->id)))
            ->pluck('id')
            ->each(fn (int $userId) => Cache::forget(self::cacheKey($userId)));
    }

    public static function cacheKey(int $userId): string
    {
        return "user.{$userId}.companies";
    }
}
