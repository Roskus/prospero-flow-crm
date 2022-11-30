<div id="sheduleEventModal" class="modal" tabindex="-1">
    <form action="{{ route('calendar.save') }}" method="POST">
        @csrf
        <input type="hidden" name="id" id="id">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add event') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <label>{{ __('Title') }}</label>
                            <input type="text" class="form-control" name="title" id="title" required placeholder="{{ __('Add title') }}">
                        </div>
                    </div>
                    <div>
                        <div class="row">
                            <div class="col">
                                <label for="date" class="form-label">{{ __('Date') }}</label>
                                <input type="date" name="date" id="date" required class="form-control mb-3">
                            </div>
                            <div class="col">
                                <label for="date" class="form-label">{{ __('Date') }}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control mb-3">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="start_time" class="form-label">{{ __('Start time') }}</label>
                                <input class="form-check-input" type="checkbox" value="1" id="is_all_day" name="is_all_day">
                                <label class="form-check-label" for="is_all_day">
                                    {{ __('Is all day?') }}
                                </label>
                                <input type="time" class="form-control" name="start_time" id="start_time">
                            </div>
                            <div class="col">
                                <label for="end_time" class="form-label">{{ __('End time') }}</label>
                                <input type="time" class="form-control" name="end_time" id="end_time">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <label for="description" class="form-label">{{ __('Description') }}</label>
                                <textarea class="form-control mb-3" name="description" id="description" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="guests" class="form-label">{{ __('Guests') }}</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="guests" id="guests">
                                    <a class="input-group-text btn btn-primary" id="basic-addon1">
                                        <i class="las la-plus-circle"></i>
                                    </a>
                                </div>
                                <select name="guest_list" readonly multiple class="form-control">

                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="meeting" class="form-label">{{ __('Meeting') }}</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="las la-video"></i>
                                    </span>
                                    <input type="text" class="form-control" name="meeting" id="meeting">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="address" class="form-label">{{ __('Address') }}</label>
                                <input type="text" class="form-control mb-3" name="address" id="address">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="latitude" id="latitude" value="">
                    <input type="hidden" name="longitude" id="longitude" value="">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </form>
</div>
