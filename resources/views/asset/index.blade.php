@extends('layouts.app')

@section('content')
    @php($isVehicle = $category === \App\Enums\AssetCategory::Vehicle)
    @include('layouts.partials._header', ['title' => $category->label(), 'count' => $assets->total()])

    <div class="row mb-2">
        <div class="col-auto">
            @can('create asset')
                <a href="{{ url('/inventory/'.$category->value.'/create') }}" class="btn btn-primary">
                    <i class="las la-plus"></i> {{ __('New') }}
                </a>
            @endcan
        </div>
        <div class="col">
            <form method="get" action="{{ route('asset.index', $category->value) }}" class="row g-2 justify-content-end">
                <div class="col-md-3">
                    <label for="search" class="visually-hidden">{{ __('Search') }}</label>
                    <input type="search" name="search" id="search" value="{{ request('search') }}" placeholder="{{ $isVehicle ? __('Code, name, plate...') : __('Code, name, hostname, IMEI, number...') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select" aria-label="{{ __('Type') }}">
                        <option value="">{{ __('All types') }}</option>
                        @foreach(\App\Enums\AssetType::forCategory($category) as $type)
                            <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select" aria-label="{{ __('Status') }}">
                        <option value="">{{ __('Active') }}</option>
                        @foreach(\App\Enums\AssetStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="branch_id" class="form-select" aria-label="{{ __('Branch') }}">
                        <option value="">{{ __('All branches') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((int) request('branch_id') === $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="user_id" class="form-select" aria-label="{{ __('Assigned to') }}">
                        <option value="">{{ __('Any holder') }}</option>
                        @foreach($users as $holder)
                            <option value="{{ $holder->id }}" @selected((int) request('user_id') === $holder->id)>{{ $holder->first_name }} {{ $holder->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="expiring" id="expiring" value="1" class="form-check-input" @checked(request()->boolean('expiring'))>
                        <label for="expiring" class="form-check-label text-nowrap">{{ __('Expiring soon') }}</label>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary" title="{{ __('Search') }}"><i class="las la-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Brand') }} / {{ __('Model') }}</th>
                            @if($isVehicle)
                                <th>{{ __('License plate') }}</th>
                            @else
                                <th>{{ __('Line') }}</th>
                            @endif
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Branch') }}</th>
                            <th>{{ __('Assigned to') }}</th>
                            <th>{{ __('Due dates') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                            <tr>
                                <td style="width: 56px;">@include('asset._thumbnail', ['asset' => $asset, 'size' => 40])</td>
                                <td class="font-monospace text-nowrap"><a href="{{ route('asset.show', $asset->id) }}">{{ $asset->code }}</a></td>
                                <td>{{ $asset->name }}</td>
                                <td class="text-nowrap"><i class="{{ $asset->type->icon() }}"></i> {{ $asset->type->label() }}</td>
                                <td>{{ trim($asset->brand.' '.$asset->model) }}</td>
                                @if($isVehicle)
                                    <td class="font-monospace text-nowrap">{{ $asset->vehicle?->license_plate }}</td>
                                @else
                                    <td class="small">
                                        @if($asset->device?->imei)
                                            <div class="font-monospace">{{ $asset->device->imei }}</div>
                                        @endif
                                        @if($asset->device?->phone_number || $asset->device?->carrier)
                                            <div>{{ $asset->device->phone_number }} @if($asset->device->carrier)<span class="text-muted">· {{ $asset->device->carrier->name }}</span>@endif</div>
                                        @endif
                                    </td>
                                @endif
                                <td><span class="badge {{ $asset->status->badgeClass() }}">{{ $asset->status->label() }}</span></td>
                                <td>{{ $asset->branch?->name }}</td>
                                <td>{{ $asset->currentAssignment?->user?->getFullName() }}</td>
                                <td>@include('asset._expiry_badge', ['state' => $asset->expiryState()])</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('asset.show', $asset->id) }}" class="btn btn-xs btn-info text-white" title="{{ __('View') }}"><i class="las la-eye"></i></a>
                                    @can('update asset')
                                        <a href="{{ url('/inventory/update/'.$asset->id) }}" class="btn btn-xs btn-warning" title="{{ __('Edit') }}"><i class="las la-pen"></i></a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">{{ __('No assets found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-2">{{ $assets->links() }}</div>
@endsection
