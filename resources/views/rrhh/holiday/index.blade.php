@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Company Holidays')])

<div class="row mb-2">
    <div class="col">
        <button class="btn btn-primary" onclick="resetHolidayForm(); $('#holidayForm').toggle()">{{ __('Add Holiday') }}</button>
    </div>
    <div class="col">
        <form method="get" action="{{ url('/rrhh/holidays') }}" class="form-inline">
            <div class="input-group">
                <select name="year" class="form-select">
                    @for($y = date('Y') - 1; $y <= date('Y') + 3; $y++)
                    <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button class="btn btn-outline-primary" type="submit"><i class="las la-filter"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-2" id="holidayForm" style="display:none">
    <div class="card-body">
        <form method="POST" action="{{ url('/rrhh/holidays/save') }}" id="holidayFormEl" class="form form-inline">
            @csrf
            <input type="hidden" name="holiday_id" id="holiday_id" value="">
            <div class="row">
                <div class="col">
                    <input type="date" name="date" id="edit_holiday_date" required class="form-control">
                </div>
                <div class="col">
                    <input type="text" name="name" id="edit_holiday_name" placeholder="{{ __('Holiday name') }}" required class="form-control">
                </div>
                <div class="col">
                    <button type="submit" id="holidaySaveBtn" class="btn btn-success">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover table-sm">
            <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($holidays as $holiday)
            <tr>
                <td>{{ $holiday->date->format('Y-m-d') }}</td>
                <td>{{ $holiday->name }}</td>
                <td class="text-nowrap">
                    <a href="#" class="btn btn-xs btn-warning text-white" onclick="editHoliday({{ $holiday->id }}, '{{ $holiday->date->format('Y-m-d') }}', '{{ addslashes($holiday->name) }}'); return false;"><i class="las la-pen"></i></a>
                    <a onclick="if(!confirm('{{ __('Are you sure?') }}')) return false; window.location='{{ url("/rrhh/holidays/delete/{$holiday->id}") }}'" class="btn btn-xs btn-danger"><i class="las la-trash-alt"></i></a>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editHoliday(id, date, name) {
    document.getElementById('holiday_id').value = id;
    document.getElementById('edit_holiday_date').value = date;
    document.getElementById('edit_holiday_name').value = name;
    document.getElementById('holidayFormEl').action = '{{ url("/rrhh/holidays/update") }}/' + id;
    document.getElementById('holidaySaveBtn').textContent = '{{ __("Update") }}';
    document.getElementById('holidayForm').style.display = '';
}

function resetHolidayForm() {
    document.getElementById('holiday_id').value = '';
    document.getElementById('edit_holiday_date').value = '';
    document.getElementById('edit_holiday_name').value = '';
    document.getElementById('holidayFormEl').action = '{{ url("/rrhh/holidays/save") }}';
    document.getElementById('holidaySaveBtn').textContent = '{{ __("Save") }}';
}
</script>
@endpush
