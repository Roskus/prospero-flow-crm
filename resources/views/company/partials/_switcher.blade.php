@inject('companyAccessService', 'App\Services\CompanyAccessService')
<li class="nav-item dropdown" id="company-switcher">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
       title="{{ __('Switch company') }}">
        <i class="las la-building"></i> {{ Auth::user()->company?->name }}
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><h6 class="dropdown-header">{{ __('Switch company') }}</h6></li>
        @foreach($companyAccessService->accessibleCompanies(Auth::user()) as $accessibleCompany)
            <li>
                <form method="POST" action="{{ route('company.switch', $accessibleCompany['id']) }}">
                    @csrf
                    <button type="submit" class="dropdown-item @if($accessibleCompany['id'] === (int) Auth::user()->company_id) active @endif"
                            @if($accessibleCompany['id'] === (int) Auth::user()->company_id) aria-current="true" @endif>
                        {{ $accessibleCompany['name'] }}
                        @if($accessibleCompany['id'] === Auth::user()->defaultCompanyId())
                            <small class="text-muted">({{ __('Own company') }})</small>
                        @endif
                    </button>
                </form>
            </li>
        @endforeach
    </ul>
</li>
