@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Lead') }}
    </header>
    <form method="POST" action="{{ url('/lead/save') }}" class="form">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-12 col-md-6">
                <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ $lead->name }}" required="required" maxlength="80" class="form-control form-control-lg">
            </div>
            <div class="col-12 col-md-6">
                <label for="business_name">{{ __('Business name') }}</label>
                <input type="text" name="business_name" value="{{ $lead->business_name }}" maxlength="80" class="form-control form-control-lg">
            </div>
        </div><!--./row-->
        <div class="row">
            <div class="col-12 col-md-6">
                <label for="phone">{{ __('Phone') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ $lead->phone }}" maxlength="15" class="form-control form-control-lg">
            </div>
            <div class="col-12 col-md-6">
                <label for="mobile" class="">{{ __('Mobile') }}</label>
                <input type="tel" name="mobile" id="mobile" value="{{ $lead->mobile }}" maxlength="15" class="form-control form-control-lg">
            </div>
        </div><!--./row-->
        <div class="row">
            <div class="col-12 col-md-6">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" value="{{ $lead->email }}" maxlength="254" class="form-control form-control-lg">
            </div>
            <div class="col-12 col-md-6">
                <label for="website" class="">Website</label>
                <input type="url" name="website" id="website" value="{{ $lead->website }}" placeholder="https://www.website.com" class="form-control form-control-lg">
            </div>
        </div><!--./row-->
        <div class="row">
            <div class="col-12 col-md-6">
                <label for="vat" class="">{{ __('Identity number') }}</label>
                <input type="text" name="vat" value="{{ $lead->vat }}" maxlength="20" class="form-control form-control-lg">
            </div>
            <div class="col-12 col-md-6">
                <label for="dob">{{ __('Date of birth') }}</label>
                <input type="date" name="dob" id="dob" value="{{ $lead->dob }}" class="form-control form-control-lg">
            </div>
        </div><!--./row-->
        <div class="row">
            <div class="col">
                <label>{{ __('Notes') }}</label>
                <textarea name="notes" id="notes" rows="8" class="form-control form-control-lg">{{ $lead->notes }}</textarea>
            </div>
        </div><!--./row-->

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
                    <div class="col-12 col-md-4">
                        <label for="province" class="">{{ __('Province') }}</label>
                        <input type="text" name="province" id="province" value="{{ $lead->province }}" class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="" for="city">{{ __('City') }}</label>
                        <input type="text" name="city" id="city" value="{{ $lead->city }}" class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="" for="locality">{{ __('Locality') }}</label>
                        <input type="text" name="locality" id="locality" value="{{ $lead->locality }}" class="form-control form-control-lg">
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label class="">{{ __('Street') }}</label>
                        <input type="text" name="street" id="street" value="{{ $lead->street }}" class="form-control form-control-lg" maxlength="80">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="">{{ __('Zipcode') }}</label>
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
                    <label for="linkedin">Linkedin</label>
                    <input type="url" name="linkedin" value="{{ $lead->linkedin }}" placeholder="https://www.linkedin.com/" class="form-control form-control-lg">
                </div>
                <div class="col-12 col-md-6">
                    <label for="facebook">Facebook</label>
                    <input type="url" name="facebook" value="{{ $lead->facebook }}" placeholder="https://www.facebook.com/" class="form-control form-control-lg">
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <label for="instagram">Instagram</label>
                    <input type="url" name="instagram" value="{{ $lead->instagram }}" placeholder="https://www.instagram.com/" class="form-control form-control-lg">
                </div>
                <div class="col-12 col-md-6">
                    <label for="twitter">Twitter</label>
                    <input type="url" name="twitter" value="{{ $lead->twitter }}" placeholder="https://twitter.com/" class="form-control form-control-lg">
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <label for="youtube">YouTube</label>
                    <input type="url" name="youtube" value="{{ $lead->youtube }}" placeholder="https://www.youtube.com/" class="form-control form-control-lg">
                </div>
                <div class="col-12 col-md-6">
                    <label>TikTok</label>
                    <input type="url" name="tiktok" value="{{ $lead->tiktok }}" placeholder="https://www.tiktok.com/" class="form-control form-control-lg">
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
                        <label for="tags">{{ __('Tags') }}</label>
                        <textarea name="tags" id="tags" class="form-control form-control-lg">{{ (!empty($lead->tags)) ? implode(',', $lead->tags) : '' }}</textarea>
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
    <div class="card accordion mt-2">
        <div class="accordion-header" id="headingContact">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContact" aria-expanded="true" aria-controls="collapseContact">
            {{ __('Contacts') }}
            </button>
        </div>
        <div id="collapseContact" class="accordion-collapse collapse hide" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
            <div class="accordion-body">
                <form method="post" action="{{ url('/contact/save') }}">
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    <div class="row">
                        <div class="col">
                            <label>{{ __('First name') }}</label>
                            <input type="text" name="contact_first_name" class="form-control">
                        </div>
                        <div class="col">
                            <label>{{ __('Last name') }}</label>
                            <input type="text" name="contact_last_name" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>{{ __('Phone') }}</label>
                            <input type="tel" name="contact_phone" maxlength="15" class="form-control">
                        </div>
                        <div class="col">
                            <label>{{ __('Mobile') }}</label>
                            <input type="tel" name="contact_mobile" maxlength="15" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>E-mail</label>
                            <input type="email" name="contact_email" class="form-control">
                        </div>
                        <div class="col">
                            <label>Linkedin</label>
                            <input type="url" name="contact_linkedin" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mt-1">
                            <button type="submit" class="btn btn-secondary">{{ __('Save') }}</button>
                        </div>
                    </div>
                </form>


                <div class="mt-2 table-responsive">
                    <table class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                    <tr>
                        <th>{{ __('First name') }}</th>
                        <th>{{ __('Last name') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Mobile') }}</th>
                        <th>E-mail</th>
                        <th>Linkedin</th>
                        <th>{{ __('Created at') }}</th>
                        <th>{{ __('Updated at') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($lead->contacts)
                        @foreach($lead->contacts as $contact)
                        <tr>
                            <td>{{ $contact->first_name }}</td>
                            <td>{{ $contact->last_name }}</td>
                            <td>
                                @if($contact->phone)
                                <a href="https://api.whatsapp.com/send/?phone={{ $contact->phone }}&text={{ __('Hello') }}">{{ $contact->phone }}</a>
                                @endif
                            </td>
                            <td>
                                @if($contact->email)
                                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                @endif
                            </td>
                            <td>
                                @if($contact->linkedin)
                                <a href="{{ $contact->linkedin }}">{{ $contact->linkedin }}</a>
                                @endif
                            </td>
                            <td>{{ ($contact->created_at) ? $contact->created_at->format('d/m/Y H:i') : '' }}</td>
                            <td>{{ ($contact->updated_at) ? $contact->updated_at->format('d/m/Y H:i') : '' }}</td>
                            <td>
                                <a href="/contact/update/{{ $contact->id }}" class="btn btn-xs btn-warning text-white">
                                    <i class="las la-pen"></i>
                                </a>
                            </td>
                        </tr>
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
        </div><!--./collapse-->
    </div>
    @endif

    @push('scripts')
        <script>
            $('#phone').on('keyup paste', function() {
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

            $('#email').on('paste', function() {
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
                        let prefix = 'https://';
                        if (val.substr(0, prefix.length) !== prefix)
                        {
                            val = prefix + val.trim();
                        }
                        return val;
                    })
                })
            });
        </script>
    @endpush
@endsection
