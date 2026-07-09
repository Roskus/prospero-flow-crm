@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Time Entries')])

<div class="row mb-2">
    <div class="col d-flex align-items-center gap-2">
        <a href="{{ url('/rrhh/time-entries?week='.($week_offset - 1)) }}" class="btn btn-outline-secondary btn-sm">&larr;</a>
        <strong>{{ $start_of_week->format('d/m/Y') }} — {{ $end_of_week->format('d/m/Y') }}</strong>
        <a href="{{ url('/rrhh/time-entries?week='.($week_offset + 1)) }}" class="btn btn-outline-secondary btn-sm">&rarr;</a>
        @if($week_offset != 0)
        <a href="{{ url('/rrhh/time-entries') }}" class="btn btn-outline-primary btn-sm">{{ __('Today') }}</a>
        @endif
    </div>
    <div class="col text-end">
        <strong>{{ __('Estimated') }}: {{ number_format($weekly_estimated, 1) }}h</strong> |
        <strong>{{ __('Actual') }}: {{ number_format($weekly_total, 1) }}h</strong>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body p-0">
        <table class="table table-bordered table-sm mb-0">
        <thead>
        <tr>
            <th style="width:120px">{{ __('Day') }}</th>
            <th>{{ __('Entries') }}</th>
            <th style="width:80px">{{ __('Scheduled') }}</th>
            <th style="width:80px">{{ __('Actual') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($days as $day)
        <tr class="{{ $day['date']->isToday() ? 'table-active' : '' }}">
            <td class="text-nowrap">
                <strong>{{ __($day['date']->format('l')) }}</strong><br>
                <small class="text-muted">{{ $day['date']->format('d/m/Y') }}</small>
                @if($day['is_holiday'])
                <br><span class="badge bg-danger mt-1">{{ $day['holiday_name'] }}</span>
                @endif
            </td>
            <td>
                @forelse($day['entries'] as $entry)
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-{{ $entry->type == 'break' ? 'warning' : 'primary' }}">
                            {{ $entry->start_time->format('H:i') }} — {{ $entry->end_time?->format('H:i') ?? '...' }}
                        </span>
                        <small class="text-muted">{{ $entry->end_time ? $entry->start_time->diffInHours($entry->end_time).'h' : __('In progress') }}</small>
                        @if($entry->notes)
                        <small class="text-muted"><i class="las la-comment"></i> {{ Str::limit($entry->notes, 30) }}</small>
                        @endif
                        @if($entry->is_manual)
                        <a href="#" onclick="editEntry({{ $entry->id }}, '{{ $entry->start_time->format('Y-m-d') }}', '{{ $entry->start_time->format('H:i') }}', '{{ $entry->end_time?->format('H:i') }}', '{{ addslashes($entry->notes ?? '') }}'); return false;" class="text-muted"><i class="las la-pen"></i></a>
                        @endif
                    </div>
                @empty
                    <span class="text-muted small">—</span>
                @endforelse
                @if($day['date']->isToday())
                    @if(!$open_entry)
                    <form method="POST" action="{{ url('/rrhh/clock/in') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-success mt-1"><i class="las la-play"></i> {{ __('Clock in') }}</button>
                    </form>
                    @else
                    <form method="POST" action="{{ url('/rrhh/clock/out') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-danger mt-1"><i class="las la-stop"></i> {{ __('Clock out') }}</button>
                    </form>
                    @endif
                @endif
                @if($day['total'] < $day['gross_total'])
                    <div class="small text-muted mt-1"><i class="las la-utensils"></i> -1h {{ __('break deducted') }}</div>
                @endif
            </td>
            <td class="text-center">{{ $day['estimated'] > 0 ? number_format($day['estimated'], 1).'h' : '—' }}</td>
            <td class="text-center">{{ $day['total'] > 0 ? number_format($day['total'], 1).'h' : '—' }}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="fw-bold">
            <td>{{ __('Weekly total') }}</td>
            <td></td>
            <td class="text-center">{{ number_format($weekly_estimated, 1) }}h</td>
            <td class="text-center">{{ number_format($weekly_total, 1) }}h</td>
        </tr>
        </tfoot>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <button class="btn btn-sm btn-primary" onclick="resetEntryForm(); $('#manualEntryForm').toggle()">{{ __('Add manual entry') }}</button>
    </div>
    <div class="card-body" id="manualEntryForm" style="display:none">
        <form method="POST" action="{{ url('/rrhh/time-entries/save') }}" id="entryForm" class="form">
            @csrf
            <input type="hidden" name="user_id" value="{{ $employee_id }}">
            <input type="hidden" name="entry_id" id="entry_id" value="">
            <div class="row">
                <div class="col-md-3">
                    <label for="entry_date" class="form-label">{{ __('Date') }}</label>
                    <input type="date" name="entry_date" id="entry_date" value="{{ date('Y-m-d') }}" required class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="start_time" class="form-label">{{ __('Start') }}</label>
                    <input type="time" name="start_time" id="start_time" required class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="end_time" class="form-label">{{ __('End') }}</label>
                    <input type="time" name="end_time" id="end_time" required class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="notes" class="form-label">{{ __('Notes') }}</label>
                    <input type="text" name="notes" id="notes" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" id="entrySaveBtn" class="btn btn-success w-100">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editEntry(id, date, start, end, notes) {
    document.getElementById('entry_id').value = id;
    document.getElementById('entry_date').value = date;
    document.getElementById('start_time').value = start;
    document.getElementById('end_time').value = end;
    document.getElementById('notes').value = notes;
    document.getElementById('entryForm').action = '{{ url("/rrhh/time-entries/update") }}/' + id;
    document.getElementById('entrySaveBtn').textContent = '{{ __("Update") }}';
    document.getElementById('manualEntryForm').style.display = '';
}

function resetEntryForm() {
    document.getElementById('entry_id').value = '';
    document.getElementById('entry_date').value = '{{ date('Y-m-d') }}';
    document.getElementById('start_time').value = '';
    document.getElementById('end_time').value = '';
    document.getElementById('notes').value = '';
    document.getElementById('entryForm').action = '{{ url("/rrhh/time-entries/save") }}';
    document.getElementById('entrySaveBtn').textContent = '{{ __("Save") }}';
}
</script>
@endpush
