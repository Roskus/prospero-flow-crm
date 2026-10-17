<div id="quickEventModal" class="modal fade" tabindex="-1" aria-labelledby="quickEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="quick_event_form" action="{{ route('calendar.save') }}" method="POST">
                @csrf
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="quickEventModalLabel">{{ __('Add event') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control form-control-lg mb-2" name="title" id="quick_event_title" required maxlength="255" placeholder="{{ __('Add title') }}">
                    <div class="row g-2 mb-2">
                        <div class="col-12 col-sm-5">
                            <label for="quick_event_date" class="form-label small mb-0">{{ __('Date') }}</label>
                            <input type="date" class="form-control" name="date" id="quick_event_date" required>
                        </div>
                        <div class="col-6 col-sm-3">
                            <label for="quick_event_start_time" class="form-label small mb-0">{{ __('Start time') }}</label>
                            <input type="time" class="form-control" name="start_time" id="quick_event_start_time" required>
                        </div>
                        <div class="col-6 col-sm-4">
                            <label for="quick_event_end_time" class="form-label small mb-0">{{ __('End time') }}</label>
                            <input type="time" class="form-control" name="end_time" id="quick_event_end_time" required data-invalid-message="{{ __('End time must be after start time') }}">
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="1" id="quick_event_is_all_day" name="is_all_day">
                        <label class="form-check-label" for="quick_event_is_all_day">{{ __('Is all day?') }}</label>
                    </div>
                    <textarea class="form-control" name="description" id="quick_event_description" rows="2" placeholder="{{ __('Description') }}"></textarea>
                </div>
                <div class="modal-footer py-2 justify-content-between">
                    <a href="{{ url('/calendar') }}" id="quick_event_more_options" class="btn btn-link px-0">{{ __('More options') }}</a>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
