@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Lead') }}
    </header>
    <form method="POST" action="{{ url('/lead/save') }}" class="form">
        {{ csrf_field() }}
        <div class="card mt-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ $lead->name }}" required="required" maxlength="80" class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="business_name">{{ __('Business name') }}</label>
                        <input type="text" name="business_name" id="business_name" value="{{ $lead->business_name }}" maxlength="80" class="form-control form-control-lg">
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="phone">{{ __('Phone') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-phone"></i></span>
                            <input type="tel" name="phone" id="phone" value="{{ $lead->phone }}" maxlength="15" class="form-control form-control-lg">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="phone2">{{ __('Phone') }} 2</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-phone"></i></span>
                            <input type="tel" name="phone2" id="phone2" value="{{ $lead->phone2 }}" maxlength="15" class="form-control form-control-lg">
                        </div>
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="email" class="">E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-envelope"></i></span>
                            <input type="email" name="email" id="email" value="{{ $lead->email }}" maxlength="254" class="form-control form-control-lg">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="email2" class="">E-mail 2</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-envelope"></i></span>
                            <input type="email" name="email2" id="email2" value="{{ $lead->email2 }}" maxlength="254" class="form-control form-control-lg">
                        </div>
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="mobile" class="">{{ __('Mobile') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="lab la-whatsapp"></i></span>
                            <input type="tel" name="mobile" id="mobile" value="{{ $lead->mobile }}" maxlength="15" class="form-control form-control-lg">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="website" class="">Website</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-globe"></i></span>
                            <input type="url" name="website" id="website" placeholder="https://www.website.com" value="{{ $lead->website }}" maxlength="255" class="form-control form-control-lg">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="vat" class="">{{ __('Identity number') }}</label>
                        <input type="text" name="vat" id="vat" value="{{ $lead->vat }}" maxlength="20" class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="dob">{{ __('Date of birth') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-calendar-day"></i></span>
                            <input type="date" name="dob" id="dob" value="{{ $lead->dob }}" class="form-control form-control-lg">
                        </div>
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col">
                        <label for="notes">{{ __('Notes') }}</label>
                        <textarea name="notes" id="notes" rows="8" class="form-control form-control-lg">{{ $lead->notes }}</textarea>
                    </div>
                </div><!--./row-->
            </div><!--./card-body-->
        </div><!--./card-->

        <div class="card mt-2">
            <div class="card-header">{{ __('Address') }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="country_id">{{ __('Country') }} <span class="text-danger">*</span></label>
                        <select name="country_id" id="country_id" required class="form-select form-control-lg">
                            <option value=""></option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->code_2 }}" @if($lead->country_id == $country->code_2) selected="selected" @endif>{{ $country->name }} {{ $country->flag }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="province" class="">{{ __('Province') }}</label>
                        <input type="text" name="province" id="province" value="{{ $lead->province }}" class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="city" class="">{{ __('City') }}</label>
                        <input type="text" name="city" id="city" value="{{ $lead->city }}" class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="locality" class="">{{ __('Locality') }}</label>
                        <input type="text" name="locality" id="locality" value="{{ $lead->locality }}" class="form-control form-control-lg">
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="street" class="">{{ __('Street') }}</label>
                        <input type="text" name="street" id="street" value="{{ $lead->street }}" class="form-control form-control-lg" maxlength="80">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="zipcode" class="">{{ __('Zipcode') }}</label>
                        <input type="text" name="zipcode" id="zipcode" value="{{ $lead->zipcode }}" class="form-control form-control-lg" maxlength="10">
                    </div>
                </div><!--./row-->
            </div><!--./card-body-->
        </div><!--./card-->

        <div class="card mt-2">
            <div class="card-header">{{ __('Social networks') }}</div>
            <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <label for="facebook">Facebook</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-facebook-f"></i></span>
                        <input type="url" name="facebook" id="facebook" value="{{ $lead->facebook }}" placeholder="https://www.facebook.com/" maxlength="255" class="form-control form-control-lg">
                    </div><!--./input-group-->
                </div><!--./col-->
                <div class="col-12 col-md-6">
                    <label for="instagram">Instagram</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-instagram"></i></span>
                        <input type="url" name="instagram" id="instagram" value="{{ $lead->instagram }}" placeholder="https://www.instagram.com/" maxlength="255" class="form-control form-control-lg">
                    </div><!--./input-group-->
                </div><!--./col-->
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <label for="linkedin">Linkedin</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-linkedin-in"></i></span>
                        <input type="url" name="linkedin" id="linkedin" value="{{ $lead->linkedin }}" placeholder="https://www.linkedin.com/" maxlength="255" class="form-control form-control-lg">
                    </div><!--./input-group-->
                </div><!--./col-->
                <div class="col-12 col-md-6">
                    <label for="twitter">Twitter</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-twitter"></i></span>
                        <input type="url" name="twitter" id="twitter" value="{{ $lead->twitter }}" placeholder="https://twitter.com/" maxlength="255" class="form-control form-control-lg">
                    </div>
                </div><!--./col-->
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <label for="youtube">YouTube</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-youtube"></i></span>
                        <input type="url" name="youtube" id="youtube" value="{{ $lead->youtube }}" placeholder="https://www.youtube.com/" maxlength="255" class="form-control form-control-lg">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="tiktok">TikTok</label>
                    <div class="input-group">
                        <span class="input-group-text"></span>
                        <input type="url" name="tiktok" id="tiktok" value="{{ $lead->tiktok }}" placeholder="https://www.tiktok.com/" maxlength="255" class="form-control form-control-lg">
                    </div>
                </div>
            </div>
            </div><!--./card-body-->
        </div><!--./card-->

        <div class="card mt-2">
            <div class="card-header">{{ __('Details') }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <label for="industry_id">{{ __('Industry') }}</label>
                        <select name="industry_id" id="industry_id" class="form-select form-control-lg">
                            <option value=""></option>
                            @foreach($industries as $industry)
                            <option value="{{ $industry->id }}" @if($lead->industry_id == $industry->id) selected="selected" @endif>{{ __($industry->name) }}</option>
                            @endforeach
                        </select>
                    </div><!--./col-->
                    <div class="col">
                        <label for="status">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-select form-control-lg">
                            <option value="">{{ __('Choose') }}</option>
                            @foreach(\App\Models\Lead::getStatus() as $key => $status)
                            <option value="{{ $key }}" @if($lead->status == $key) selected="selected" @endif>{{ __($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="schedule_contact">{{ __('Remember contact') }}</label>
                        <input type="datetime-local" name="schedule_contact" id="schedule_contact" value="{{ $lead->schedule_contact }}" min="{{ date('Y-m-d H:i') }}" class="form-control form-control-lg">
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col mt-2">
                        <label for="tags"><i class="las la-hashtag"></i> {{ __('Tags') }}</label>
                        <textarea name="tags" id="tags" placeholder="keyword, special keyword, keyword2" class="form-control form-control-lg">{{ (!empty($lead->tags)) ? implode(',', $lead->tags) : '' }}</textarea>
                    </div>
                </div>
            </div><!--./card-body-->
        </div><!--./card-->

        <div class="row">
            <div class="col mt-2">
                <a href="{{ url('/lead') }}" class="btn btn-lg btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-lg btn-primary">{{ __('Save') }}</button>
            </div>
        </div>
        <input type="hidden" name="id" value="{{ (!empty($lead)) ? $lead->id : '' }}">
    </form>

    @if($lead->id)
    <div class="card accordion mt-2 mb-5">
        <div class="accordion-header" id="headingContact">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContact" aria-expanded="true" aria-controls="collapseContact">
            {{ __('Contacts') }}
            </button>
        </div>

            <div class="accordion-body bg-white">
                <div id="collapseContact" class="accordion-collapse collapse hide" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <form method="post" action="{{ url('/contact/save') }}">
                    @csrf
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
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
                </div><!--./collapse-->

                <div class="mt-2 table-responsive">
                    <table class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                    <tr>
                        <th>{{ __('First name') }}</th>
                        <th>{{ __('Last name') }}</th>
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
                    @if($lead->contacts)
                        @foreach($lead->contacts as $contact)
                        <form method="post" id="form_contact_{{ $contact->id }}" action="{{ url('/contact/save') }}">
                        <tr>
                            <td>
                                <input type="hidden" name="contact_id" value="{{ $contact->id }}">
                                <input type="text" name="contact_first_name" value="{{ $contact->first_name }}" disabled class="form-control">
                            </td>
                            <td><input type="text" name="contact_first_name" value="{{ $contact->last_name }}" disabled class="form-control"></td>
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
                                    <a href="https://api.whatsapp.com/send/?phone={{ $contact->mobile }}&text={{ __('Hello') }}">{{ $contact->mobile }}</a>
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
                                <a href="#" onclick="Contact.delete({{ $contact->id }})" class="btn btn-xs btn-danger text-white">
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
                </div>
            </div><!--./card-body-->

    </div>
    @endif

    @push('scripts')
    <script>
        $('#phone, #phone2').on('keyup paste', function() {
            let $el = $(this);
            setTimeout(function() {
                $el.val(function(i, val) {
                    return val.replace(/[ ()-.]/g, '')
                })
            })
        });

        $('#mobile').on('keyup paste', function() {
            let $el = $(this);
            setTimeout(function() {
                $el.val(function(i, val) {
                    return val.replace(/[ ()-.]/g, '')
                })
            })
        });

        $('#email, #email2').on('keyup paste', function() {
            let $el = $(this);
            setTimeout(function() {
                $el.val(function(i, val) {
                    return val.replace('mailto:', '').trim()
                })
            })
        });

        $('#website').on('keyup paste', function() {
            let $el = $(this);
            setTimeout(function() {
                $el.val(function(i, val) {
                    if(val.length == 0) return;
                    let protocol = 'http://';
                    let protocolSecure = 'https://';
                    if (val.substr(0, protocol.length) !== protocol && val.substr(0, protocolSecure.length) !== protocolSecure)
                    {
                        val = protocolSecure + val.trim();
                    }
                    return val;
                })
            })
        });

        const Contact = {
            update: function (id) {
                let form = $('#form_contact_'+id);
                let inputs = form.filter(':input');
                inputs.each(function() {
                    this.removeAttr('disabled');
                });
            }
        }
    </script>
    @endpush
@endsection
