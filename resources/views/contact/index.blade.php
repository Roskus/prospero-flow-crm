<table class="table table-bordered table-hover table-striped table-sm">
<thead>
<tr>
    <th>{{ __('First name') }}</th>
    <th>{{ __('Last name') }}</th>
    <th>{{ __('Job title') }}</th>
    <th>{{ __('Phone') }}</th>
    <th>{{ __('Mobile') }}</th>
    <th>E-mail</th>
    <th>Social</th>
    <th>{{ __('Created at') }}</th>
    <th>{{ __('Updated at') }}</th>
    <th>{{ __('Actions') }}</th>
</tr>
</thead>
<tbody>
@if($contacts)
    @foreach($contacts as $contact)
    <form method="post" id="form_contact_{{ $contact->id }}" action="{{ url('/contact/save') }}">
    <tr>
        <td>
            <input type="hidden" name="contact_id" value="{{ $contact->id }}">
            <input type="text" name="contact_first_name" value="{{ $contact->first_name }}" disabled class="form-control">
        </td>
        <td>
            <input type="text" name="contact_last_name" value="{{ $contact->last_name }}" disabled class="form-control">
        </td>
        <td>
            <div class="">{{ $contact->job_title }}</div>
            <input type="text" name="contact_job_title" value="{{ $contact->job_title }}" disabled maxlength="80" class="form-control d-none">
        </td>
        <td>
            @if($contact->phone)
                <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
            @endif
            <div class="d-none">
            <input type="tel" name="contact_phone" value="{{ $contact->phone }}" maxlength="15" class="form-control">
            </div>
        </td>
        <td>
            @if($contact->mobile)
                <a href="https://api.whatsapp.com/send/?phone={{ $contact->mobile }}&text={{ __('Hello') }}">
                    {{ $contact->mobile }}
                </a>
            @endif
        </td>
        <td>
            @if($contact->email)
                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
            @endif
            <div class="d-none">
            <input type="email" name="contact_email" value="{{ $contact->email }}" maxlength="254" class="form-control">
            </div>
        </td>
        <td>
            @if($contact->linkedin)
                <a href="{{ $contact->linkedin }}"><i class="lab la-linkedin fs-3"></i></a>
            @endif
        </td>
        <td class="">{{ ($contact->created_at) ? $contact->created_at->format('d/m/Y H:i') : '' }}</td>
        <td>{{ ($contact->updated_at) ? $contact->updated_at->format('d/m/Y H:i') : '' }}</td>
        <td class="no-wrap">
            <a href="#" onclick="Contact.update({{ $contact->id }})" class="btn btn-xs btn-warning text-white">
                <i class="las la-pen"></i>
            </a>
            <a href="{{ url('/contact/export-vcard/'.$contact->id) }}" target="_blank" data-bs-toggle="tooltip"
               data-bs-placement="top" data-bs-original-title="{{ __('Download vCard') }}"
               class="btn btn-xs btn-primary text-white">
                <i class="las la-address-card"></i>
            </a>
            <a href="#"
               onclick="Contact.delete({{ $contact->id }}, '{{ __('Do you want to delete the contact: :name', ['name' => $contact->first_name]) }}')"
               class="btn btn-xs btn-danger text-white">
                <i class="las la-trash"></i>
            </a>
        </td>
    </tr>
    </form>
    @endforeach
@else
    <tr>
        <td colspan="8">{{ __('No contacts') }}</td>
    </tr>
@endif
</tbody>
</table>
