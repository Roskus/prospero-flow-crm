@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Work Schedule')])

<div class="row mb-2">
    <div class="col">
        <form method="get" action="{{ url('/rrhh/schedule') }}" class="form-inline">
            <div class="input-group">
                <select name="user_id" class="form-select" onchange="this.form.submit()">
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $employee_id == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card mb-2">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ __('Weekly Schedule') }}</span>
        <button class="btn btn-sm btn-primary" onclick="resetScheduleForm(); $('#addBlockForm').toggle()">{{ __('Add block') }}</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
            <thead>
            <tr>
                <th>{{ __('Day') }}</th>
                <th>{{ __('Start') }}</th>
                <th>{{ __('End') }}</th>
                <th>{{ __('Hours') }}</th>
                <th>{{ __('Type') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @php $days = [1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; @endphp
            @foreach($days as $num => $day)
                @php $blocks = $schedule->get($num, collect()); @endphp
                @if($blocks->isEmpty())
                <tr>
                    <td>{{ __($day) }}</td>
                    <td colspan="5" class="text-muted">{{ __('Not set') }}</td>
                </tr>
                @else
                    @foreach($blocks as $block)
                    <tr id="schedule-{{ $block->id }}">
                        <td>{{ __($day) }}</td>
                        <td><span class="start-display">{{ substr($block->start_time, 0, 5) }}</span></td>
                        <td><span class="end-display">{{ substr($block->end_time, 0, 5) }}</span></td>
                        @php
                            $s = \Carbon\Carbon::parse($block->start_time);
                            $e = \Carbon\Carbon::parse($block->end_time);
                            $hours = $s->diffInMinutes($e) / 60;
                        @endphp
                        <td>{{ number_format($hours, 1) }}h</td>
                        <td><span class="type-display">{{ $block->type == 'break' ? __('Break') : __('Work') }}</span></td>
                        <td class="text-nowrap">
                            <a href="#" class="btn btn-xs btn-warning text-white" onclick="editSchedule({{ $block->id }}, {{ $block->day_of_week }}, '{{ substr($block->start_time, 0, 5) }}', '{{ substr($block->end_time, 0, 5) }}', '{{ $block->type }}'); return false;"><i class="las la-pen"></i></a>
                            <a href="{{ url("/rrhh/schedule/delete/{$block->id}?user_id={$employee_id}") }}" class="btn btn-xs btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')"><i class="las la-trash-alt"></i></a>
                        </td>
                    </tr>
                    @endforeach
                @endif
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card" id="addBlockForm" style="display:none">
    <div class="card-header">{{ __('Time Block') }}</div>
    <div class="card-body">
        <form method="POST" action="{{ url('/rrhh/schedule/save') }}" id="scheduleForm" class="form">
            @csrf
            <input type="hidden" name="user_id" value="{{ $employee_id }}">
            <input type="hidden" name="schedule_id" id="schedule_id" value="">
            <div class="row">
                <div class="col-md-3">
                    <label for="edit_day_of_week" class="form-label">{{ __('Day') }}</label>
                    <select name="day_of_week" id="edit_day_of_week" required class="form-select">
                        <option value="1">{{ __('Monday') }}</option>
                        <option value="2">{{ __('Tuesday') }}</option>
                        <option value="3">{{ __('Wednesday') }}</option>
                        <option value="4">{{ __('Thursday') }}</option>
                        <option value="5">{{ __('Friday') }}</option>
                        <option value="6">{{ __('Saturday') }}</option>
                        <option value="7">{{ __('Sunday') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="edit_start_time" class="form-label">{{ __('Start') }}</label>
                    <input type="time" name="start_time" id="edit_start_time" required class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="edit_end_time" class="form-label">{{ __('End') }}</label>
                    <input type="time" name="end_time" id="edit_end_time" required class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="edit_type" class="form-label">{{ __('Type') }}</label>
                    <select name="type" id="edit_type" required class="form-select">
                        <option value="work">{{ __('Work') }}</option>
                        <option value="break">{{ __('Break') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" id="scheduleSaveBtn" class="btn btn-success w-100">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editSchedule(id, day, start, end, type) {
    document.getElementById('schedule_id').value = id;
    document.getElementById('edit_day_of_week').value = day;
    document.getElementById('edit_start_time').value = start;
    document.getElementById('edit_end_time').value = end;
    document.getElementById('edit_type').value = type;
    document.getElementById('scheduleForm').action = '{{ url("/rrhh/schedule/update") }}/' + id;
    document.getElementById('scheduleSaveBtn').textContent = '{{ __("Update") }}';
    document.getElementById('addBlockForm').style.display = '';
}

function resetScheduleForm() {
    document.getElementById('schedule_id').value = '';
    document.getElementById('edit_start_time').value = '';
    document.getElementById('edit_end_time').value = '';
    document.getElementById('edit_type').value = 'work';
    document.getElementById('scheduleForm').action = '{{ url("/rrhh/schedule/save") }}';
    document.getElementById('scheduleSaveBtn').textContent = '{{ __("Save") }}';
}
</script>
@endpush
