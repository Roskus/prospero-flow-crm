<div class="modal" tabindex="-1" id="duplicate-email-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('email.duplicate') }}" method="POST">
                @csrf
                <input type="hidden" name="email_id" value="{{ $email->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Duplicate') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <label for="email_to" class="form-label">
                        {{ __('Email') }} <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email_to" id="email_to" required class="form-control">
                    <div class="form-text">
                        {{ __('Please enter the recipient\'s email.') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Close') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('Duplicate') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
