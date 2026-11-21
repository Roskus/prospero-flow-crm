@extends('layouts.app')

@section('content')
    @php($isVehicle = $asset->isVehicle())
    @include('layouts.partials._header', ['title' => $asset->code.' · '.$asset->name])

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('asset.index', $asset->category->value) }}" class="btn btn-outline-secondary"><i class="las la-arrow-left"></i> {{ $asset->category->label() }}</a>
        @can('update asset')
            <a href="{{ url('/inventory/update/'.$asset->id) }}" class="btn btn-warning"><i class="las la-pen"></i> {{ __('Edit') }}</a>
        @endcan
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="las la-print"></i> {{ __('Print label') }}
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('asset.label', [$asset->id, 'format' => 'barcode']) }}" target="_blank"><i class="las la-barcode"></i> {{ __('Barcode') }}</a></li>
                <li><a class="dropdown-item" href="{{ route('asset.label', [$asset->id, 'format' => 'qr']) }}" target="_blank"><i class="las la-qrcode"></i> {{ __('QR code') }}</a></li>
            </ul>
        </div>
        @can('delete asset')
            <form method="POST" action="{{ url('/inventory/delete/'.$asset->id) }}" class="d-inline"
                  onsubmit="return confirm('{{ __('Are you sure you want to delete this asset?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="las la-trash"></i> {{ __('Delete') }}</button>
            </form>
        @endcan
    </div>

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex gap-3">
                        @if($asset->hasPhoto())
                            <a href="{{ route('asset.photo', $asset->id) }}" target="_blank">@include('asset._thumbnail', ['asset' => $asset, 'size' => 220])</a>
                        @else
                            @include('asset._thumbnail', ['asset' => $asset, 'size' => 220])
                        @endif
                        <dl class="row mb-0 flex-grow-1">
                            <dt class="col-sm-4">{{ __('Type') }}</dt>
                            <dd class="col-sm-8"><i class="{{ $asset->type->icon() }}"></i> {{ $asset->type->label() }}</dd>
                            <dt class="col-sm-4">{{ __('Status') }}</dt>
                            <dd class="col-sm-8"><span class="badge {{ $asset->status->badgeClass() }}">{{ $asset->status->label() }}</span></dd>
                            <dt class="col-sm-4">{{ __('Brand') }} / {{ __('Model') }}</dt>
                            <dd class="col-sm-8">{{ trim($asset->brand.' '.$asset->model) ?: '—' }}</dd>
                            <dt class="col-sm-4">{{ __('Serial number') }}</dt>
                            <dd class="col-sm-8 font-monospace">{{ $asset->serial_number ?: '—' }}</dd>
                            <dt class="col-sm-4">{{ __('Branch') }}</dt>
                            <dd class="col-sm-8">{{ $asset->branch?->name ?? '—' }}</dd>
                            <dt class="col-sm-4">{{ __('Age') }}</dt>
                            <dd class="col-sm-8">
                                @if($asset->age())
                                    {{ $asset->age() }}
                                    <span class="text-muted small">
                                        @if($asset->agesFromManufacturingYear())
                                            ({{ __('manufactured in') }} {{ $asset->vehicle->manufacturing_year }})
                                        @else
                                            ({{ __('since') }} {{ $asset->ageStartDate()->format('d/m/Y') }})
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">{{ $isVehicle ? __('Unknown, add the manufacturing year') : __('Unknown, add the purchase date') }}</span>
                                @endif
                            </dd>
                            <dt class="col-sm-4">{{ __('Acquisition') }}</dt>
                            <dd class="col-sm-8">{{ $asset->acquisition_type->label() }}</dd>
                            <dt class="col-sm-4">{{ $asset->isRenting() ? __('Renting company') : __('Supplier') }}</dt>
                            <dd class="col-sm-8">{{ $asset->supplier?->name ?? '—' }}</dd>
                            @if($asset->isRenting())
                                <dt class="col-sm-4">{{ __('Renting contract') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $asset->rental_starts_at?->format('d/m/Y') ?? '—' }} → {{ $asset->rental_ends_at?->format('d/m/Y') ?? '—' }}
                                    @if($asset->rental_monthly_fee !== null) · {{ number_format((float) $asset->rental_monthly_fee, 2, ',', '.') }} {{ $asset->currency }} {{ __('per month') }} @endif
                                </dd>
                            @else
                                <dt class="col-sm-4">{{ __('Purchase') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $asset->purchase_date?->format('d/m/Y') ?? '—' }}
                                    @if($asset->purchase_price !== null) · {{ number_format((float) $asset->purchase_price, 2, ',', '.') }} {{ $asset->currency }} @endif
                                </dd>
                            @endif
                        </dl>
                    </div>
                    @if($asset->notes)
                        <p class="mt-3 mb-0" style="white-space: pre-line;">{{ $asset->notes }}</p>
                    @endif
                </div>
            </div>

            @if($isVehicle && $asset->vehicle)
                @php($vehicle = $asset->vehicle)
                <div class="card mb-2">
                    <div class="card-header"><span class="card-title">{{ __('Vehicle data') }}</span></div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">{{ __('License plate') }}</dt><dd class="col-sm-3 font-monospace">{{ $vehicle->license_plate ?: '—' }}</dd>
                            <dt class="col-sm-3">{{ __('VIN / chassis number') }}</dt><dd class="col-sm-3 font-monospace">{{ $vehicle->vin ?: '—' }}</dd>
                            <dt class="col-sm-3">{{ __('Year') }}</dt><dd class="col-sm-3">{{ $vehicle->manufacturing_year ?: '—' }}</dd>
                            <dt class="col-sm-3">{{ __('Fuel') }}</dt><dd class="col-sm-3">{{ $vehicle->fuel_type?->label() ?? '—' }}</dd>
                            <dt class="col-sm-3">{{ __('Odometer (km)') }}</dt><dd class="col-sm-3">{{ $vehicle->odometer_km !== null ? number_format($vehicle->odometer_km, 0, ',', '.') : '—' }}</dd>
                            <dt class="col-sm-3">{{ __('Usage hours') }}</dt><dd class="col-sm-3">{{ $vehicle->usage_hours !== null ? number_format($vehicle->usage_hours, 0, ',', '.') : '—' }}</dd>
                            <dt class="col-sm-3">{{ __('Insurance') }}</dt><dd class="col-sm-9">{{ trim($vehicle->insurance_company.' '.$vehicle->insurance_policy) ?: '—' }}</dd>
                        </dl>
                    </div>
                </div>
            @elseif($asset->device)
                @php($device = $asset->device)
                @if($asset->type->hasPhoneLine())
                    <div class="card mb-2">
                        <div class="card-header"><span class="card-title">{{ __('Phone line') }}</span></div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-3">{{ __('IMEI') }}</dt><dd class="col-sm-9 font-monospace">{{ $device->imei ?: '—' }}</dd>
                                <dt class="col-sm-3">{{ __('Carrier') }}</dt>
                                <dd class="col-sm-9">{{ $device->carrier?->name ?? '—' }} @if($device->carrier?->trashed())<span class="text-muted small">({{ __('deleted') }})</span>@endif</dd>
                                <dt class="col-sm-3">{{ __('Mobile number') }}</dt><dd class="col-sm-9">{{ $device->phone_number ?: '—' }}</dd>
                                @can('update asset')
                                    @foreach(['sim_pin' => __('SIM PIN'), 'sim_puk' => __('SIM PUK')] as $simField => $simLabel)
                                        <dt class="col-sm-3">{{ $simLabel }}</dt>
                                        <dd class="col-sm-9">
                                            @if($device->{$simField})
                                                <span class="font-monospace sim-code" data-code="{{ $device->{$simField} }}">••••••</span>
                                                <button type="button" class="btn btn-link btn-sm p-0 ms-2 sim-reveal">{{ __('Show') }}</button>
                                            @else
                                                —
                                            @endif
                                        </dd>
                                    @endforeach
                                @endcan
                            </dl>
                        </div>
                    </div>
                @endif
                <div class="card mb-2">
                    <div class="card-header"><span class="card-title">{{ __('Device data') }}</span></div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">{{ __('Hostname') }}</dt><dd class="col-sm-3 font-monospace">{{ $device->hostname ?: '—' }}</dd>
                            <dt class="col-sm-3">{{ __('Operating system') }}</dt><dd class="col-sm-3">{{ $device->operating_system ?: '—' }}</dd>
                            <dt class="col-sm-3">{{ __('IP address') }}</dt><dd class="col-sm-3 font-monospace">{{ $device->ip_address ?: '—' }}</dd>
                            <dt class="col-sm-3">{{ __('MAC address') }}</dt><dd class="col-sm-3 font-monospace">{{ $device->mac_address ?: '—' }}</dd>
                            <dt class="col-sm-3">{{ __('CPU') }}</dt><dd class="col-sm-3">{{ $device->cpu ?: '—' }}</dd>
                            <dt class="col-sm-3">{{ __('RAM') }}</dt><dd class="col-sm-3">{{ $device->ram ?: '—' }}</dd>
                            <dt class="col-sm-3">{{ __('Storage') }}</dt><dd class="col-sm-9">{{ $device->storage ?: '—' }}</dd>
                        </dl>
                        @if($device->specs)
                            <p class="mt-2 mb-0" style="white-space: pre-line;">{{ $device->specs }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="card-title">{{ __('Maintenance') }}</span>
                    @can('update asset')
                        <a href="{{ url('/inventory/maintenance/create/'.$asset->id) }}" class="btn btn-sm btn-primary"><i class="las la-plus"></i> {{ __('Log maintenance') }}</a>
                    @endcan
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Supplier') }}</th>
                                    <th class="text-end">{{ __('Cost') }}</th>
                                    <th>{{ __('Next due') }}</th>
                                    @can('update asset')<th></th>@endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asset->maintenances as $maintenance)
                                    <tr>
                                        <td class="text-nowrap">{{ $maintenance->date->format('d/m/Y') }}</td>
                                        <td>{{ $maintenance->type->label() }}</td>
                                        <td>{{ $maintenance->description }}</td>
                                        <td>{{ $maintenance->supplier?->name }}</td>
                                        <td class="text-end text-nowrap">@if($maintenance->cost !== null){{ number_format((float) $maintenance->cost, 2, ',', '.') }} {{ $maintenance->currency }}@endif</td>
                                        <td class="text-nowrap">{{ $maintenance->next_due_at?->format('d/m/Y') }}</td>
                                        @can('update asset')
                                            <td class="text-nowrap">
                                                <a href="{{ url('/inventory/maintenance/update/'.$maintenance->id) }}" class="btn btn-xs btn-warning" title="{{ __('Edit') }}"><i class="las la-pen"></i></a>
                                                <form method="POST" action="{{ url('/inventory/maintenance/delete/'.$maintenance->id) }}" class="d-inline"
                                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this maintenance record?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger" title="{{ __('Delete') }}"><i class="las la-trash"></i></button>
                                                </form>
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-3">{{ __('No maintenance records') }}</td></tr>
                                @endforelse
                            </tbody>
                            @if($maintenanceTotals->isNotEmpty())
                                <tfoot>
                                    @foreach($maintenanceTotals as $currency => $total)
                                        <tr class="fw-bold">
                                            <td colspan="4" class="text-end">{{ __('Total') }}</td>
                                            <td class="text-end text-nowrap maintenance-total">{{ number_format((float) $total, 2, ',', '.') }} {{ $currency }}</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    @endforeach
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-2">
                <div class="card-header"><span class="card-title">{{ __('Due dates') }}</span></div>
                <ul class="list-group list-group-flush">
                    @forelse($asset->dueDates() as $label => $date)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $label }}</span>
                            <span>{{ $date->format('d/m/Y') }} @include('asset._expiry_badge', ['state' => \App\Models\Asset::dateState($date)])</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">{{ __('No due dates') }}</li>
                    @endforelse
                </ul>
            </div>

            <div class="card mb-2">
                <div class="card-header"><span class="card-title">{{ __('Assigned to') }}</span></div>
                <div class="card-body">
                    @if($asset->currentAssignment)
                        <p class="mb-2">
                            <strong>{{ $asset->currentAssignment->user?->getFullName() }}</strong><br>
                            <small class="text-muted">{{ __('Since') }} {{ $asset->currentAssignment->start_date->format('d/m/Y') }}</small>
                        </p>
                        @can('update asset')
                            <form method="POST" action="{{ url('/inventory/return/'.$asset->id) }}">
                                @csrf
                                <div class="mb-2">
                                    <label for="end_date" class="form-label small">{{ __('Return date') }}</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', today()->toDateString()) }}" class="form-control form-control-sm @error('end_date') is-invalid @enderror">
                                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-2">
                                    <textarea name="notes" rows="2" maxlength="2000" placeholder="{{ __('Notes') }}" class="form-control form-control-sm"></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="las la-undo"></i> {{ __('Return') }}</button>
                            </form>
                        @endcan
                    @else
                        <p class="text-muted mb-2">{{ __('Not assigned') }}</p>
                        @can('update asset')
                            @if($asset->status === \App\Enums\AssetStatus::Available)
                                <form method="POST" action="{{ url('/inventory/assign/'.$asset->id) }}">
                                    @csrf
                                    <div class="mb-2">
                                        <label for="user_id" class="form-label small">{{ __('User') }}</label>
                                        <select name="user_id" id="user_id" required class="form-select form-select-sm @error('user_id') is-invalid @enderror">
                                            <option value=""></option>
                                            @foreach($users as $holder)
                                                <option value="{{ $holder->id }}" @selected((int) old('user_id') === $holder->id)>{{ $holder->getFullName() }}</option>
                                            @endforeach
                                        </select>
                                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-2">
                                        <label for="start_date" class="form-label small">{{ __('Start date') }}</label>
                                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', today()->toDateString()) }}" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="notes" rows="2" maxlength="2000" placeholder="{{ __('Notes') }}" class="form-control form-control-sm"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="las la-user-check"></i> {{ __('Assign') }}</button>
                                </form>
                            @else
                                <small class="text-muted">{{ __('Only available assets can be assigned.') }}</small>
                            @endif
                        @endcan
                    @endif
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-header"><span class="card-title">{{ __('Assignment history') }}</span></div>
                <ul class="list-group list-group-flush assignment-history">
                    @forelse($asset->assignments as $assignment)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">{{ $assignment->user?->getFullName() }}</span>
                                @can('update asset')
                                    <a href="{{ url('/inventory/assignment/update/'.$assignment->id) }}" class="btn btn-xs btn-warning" title="{{ __('Edit') }}"><i class="las la-pen"></i></a>
                                @endcan
                            </div>
                            <small class="text-muted">
                                {{ $assignment->start_date->format('d/m/Y') }} → {{ $assignment->end_date?->format('d/m/Y') ?? __('now') }}
                                @if($assignment->assignedBy) · {{ __('by') }} {{ $assignment->assignedBy->getFullName() }} @endif
                            </small>
                            @if($assignment->notes)
                                <div class="small" style="white-space: pre-line;">{{ $assignment->notes }}</div>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-muted">{{ __('Never assigned') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.sim-reveal').forEach((button) => button.addEventListener('click', () => {
        const code = button.previousElementSibling;
        const hidden = code.textContent !== code.dataset.code;
        code.textContent = hidden ? code.dataset.code : '••••••';
        button.textContent = hidden ? @json(__('Hide')) : @json(__('Show'));
    }));
</script>
@endpush
