<div class="card">
    <div class="card-body">
        <form method="post" action="{{ url('/contact/save') }}">
            @csrf
            <input type="hidden" name="id" value="{{ !empty($contact) ? $contact->id : '' }}">
            <input type="hidden" name="lead_id" value="{{ !empty($contact) ? $contact->lead_id : '' }}">
            <input type="hidden" name="customer_id" value="{{ !empty($contact) ? $contact->customer_id : '' }}">
            <div class="row">
                <div class="col">
                    <label for="contact_first_name">{{ __('First name') }}</label>
                    <input type="text" name="contact_first_name" id="contact_first_name" value="{{ !empty($contact) ? $contact->first_name : '' }}" required class="form-control form-control-lg">
                </div>
                <div class="col">
                    <label for="contact_last_name">{{ __('Last name') }}</label>
                    <input type="text" name="contact_last_name" id="contact_last_name" value="{{ !empty($contact) ? $contact->last_name : '' }}" class="form-control form-control-lg">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="contact_mobile">{{ __('Mobile') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-mobile"></i></span>
                        <input type="tel" name="contact_mobile" id="contact_mobile"
                               value="{{ !empty($contact) ? $contact->mobile : '' }}" maxlength="15"
                               class="form-control form-control-lg">
                    </div>
                </div>
                <div class="col-10 col-md-4">
                    <label for="contact_phone">{{ __('Phone') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-phone"></i></span>
                        <input type="tel" name="contact_phone" id="contact_phone" value="{{ !empty($contact) ? $contact->phone : '' }}" maxlength="15" class="form-control form-control-lg">
                    </div>
                </div>
                <div class="col-2">
                    <label for="contact_extension">{{ __('Extension') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-buromobelexperte"></i></span>
                        <input type="text" name="contact_extension" id="contact_extension"
                               value="{{ old('contact_extension', $contact->extension ?? '') }}"
                               maxlength="6" class="form-control form-control-lg">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="contact_email">E-mail</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-envelope"></i></span>
                        <input type="email" name="contact_email" id="contact_email"
                               value="{{ !empty($contact) ? $contact->email : '' }}" maxlength="254"
                               class="form-control form-control-lg">
                    </div>
                </div>
                <div class="col">
                    <label for="contact_job_title">{{ __('Job title') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-briefcase"></i></span>
                        <input type="text" name="contact_job_title" id="contact_job_title"
                               value="{{ !empty($contact) ? $contact->job_title : '' }}" maxlength="80"
                               class="form-control form-control-lg">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="contact_linkedin">Linkedin</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-linkedin"></i></span>
                        <input type="url" name="contact_linkedin" id="contact_linkedin"
                               placeholder="https://linkedin.com/in/"
                               value="{{ !empty($contact->linkedin) ? $contact->linkedin : '' }}"
                               class="form-control form-control-lg">
                    </div>
                </div>
                <div class="col">
                    <label for="contact_twitter">Twitter</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-twitter"></i></span>
                        <input type="url" name="contact_twitter" id="contact_twitter"
                               placeholder="https://twitter.com/"
                               value="{{ !empty($contact->twitter) ? $contact->twitter : '' }}"
                               class="form-control form-control-lg">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="contact_notes">{{ __('Notes') }}</label>
                    @php
                        $notes = !empty($contact) ? $contact->notes : '';
                    @endphp
                    <textarea name="contact_notes" id="contact_notes" class="form-control form-control-lg">{{ $notes }}</textarea>
                </div>
            </div>
            <div class="row">
                <div class="col mt-2">
                    <a href="{{ url('/') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function() {
    $('#contact_linkedin').on('paste', function(event) {
        setTimeout(() => {
            let url = $(this).val();
            let matches = url.match(/linkedin\.com\/in\/([^\/]+)\//);

            if (matches && matches[1]) {
                let fullName = matches[1].replace(/-/g, ' '); // Reemplazar guiones con espacios
                let nameParts = fullName.split(' ');
                let firstName = '';
                let lastName = '';

                nameParts.forEach(function(part, index) {
                    if (index === 0) {
                        firstName += part.charAt(0).toUpperCase() + part.slice(1).toLowerCase(); // Convertir primera letra en mayúscula
                    } else {
                        if (index === nameParts.length - 1) {
                            // Última parte, eliminar cualquier hash alfanumérico
                            lastName += part.replace(/[0-9a-fA-F]+$/, '').charAt(0).toUpperCase() + part.slice(1).toLowerCase();
                        } else {
                            lastName += part.charAt(0).toUpperCase() + part.slice(1).toLowerCase(); // Convertir primera letra en mayúscula
                        }
                        lastName += ' ';
                    }
                });

                // Eliminar espacio adicional al final del apellido
                lastName = lastName.trim();

                // Asignar valores a los campos de nombre y apellido
                $('#contact_first_name').val(firstName);
                $('#contact_last_name').val(lastName);
            }
        }, 0);
    });
});
</script>
@endpush
