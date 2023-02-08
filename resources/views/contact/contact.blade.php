<form method="post" action="{{ url('/contact/save') }}">
    @csrf
    <input type="hidden" name="lead_id" value="{{ $id }}">
    <div class="row">
        <div class="col">
            <label for="contact_first_name">{{ __('First name') }}</label>
            <input type="text" name="contact_first_name" id="contact_first_name" required class="form-control">
        </div>
        <div class="col">
            <label for="contact_last_name">{{ __('Last name') }}</label>
            <input type="text" name="contact_last_name" id="contact_last_name" class="form-control">
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label for="contact_phone">{{ __('Phone') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="las la-phone"></i></span>
                <input type="tel" name="contact_phone" id="contact_phone" maxlength="15" class="form-control">
            </div>
        </div>
        <div class="col">
            <label for="contact_mobile">{{ __('Mobile') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="las la-mobile"></i></span>
                <input type="tel" name="contact_mobile" id="contact_mobile" maxlength="15" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label for="contact_email">E-mail</label>
            <div class="input-group">
                <span class="input-group-text"><i class="las la-envelope"></i></span>
                <input type="email" name="contact_email" id="contact_email" maxlength="254" class="form-control">
            </div>
        </div>
        <div class="col">
            <label for="contact_linkedin">Linkedin</label>
            <div class="input-group">
                <span class="input-group-text"><i class="lab la-linkedin-in"></i></span>
                <input type="url" name="contact_linkedin" id="contact_linkedin" placeholder="https://linkedin.com/in/" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label for="contact_job_title">{{ __('Job title') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="las la-briefcase"></i></span>
                <input type="text" name="contact_job_title" id="contact_job_title" maxlength="80" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label for="contact_notes">{{ __('Notes') }}</label>
            <textarea name="contact_notes" id="contact_notes" class="form-control"></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col mt-2">
            <button type="submit" class="btn btn-secondary">{{ __('Save') }}</button>
        </div>
    </div>
</form>
