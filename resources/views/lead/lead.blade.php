@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Lead') }} @if($lead->id) #{{ $lead->id }} @endif</h1>
    </header>
    <form method="POST" action="{{ url('/lead/save') }}" class="form">
        {{ csrf_field() }}
        <div class="card mt-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $lead->name) }}"
                               required="required" maxlength="80" class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="business_name">{{ __('Business name') }}</label>
                        <input type="text" name="business_name" id="business_name"
                               value="{{ old('business_name', $lead->business_name) }}" maxlength="80"
                               class="form-control form-control-lg">
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="external_id">{{ __('External ID') }}</label>
                        <input type="text" name="external_id" id="external_id" value="{{ old('external_id', $lead->external_id) }}"
                               maxlength="50" class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="source_id">{{ __('Source') }}</label>
                        <select name="source_id" id="source_id" class="form-select form-select-lg">
                            <option value=""></option>
                            @foreach ($sources as $source)
                            <option value="{{ $source->id }}" @if($source->id == $lead->source_id) selected="selected" @endif>
                                {{ __($source->name) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-10 col-md-4">
                        <label for="phone">{{ __('Phone') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-phone"></i></span>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $lead->phone) }}"
                                   maxlength="15" class="form-control form-control-lg">
                        </div>
                    </div>
                    <div class="col-2">
                        <label for="extension">{{ __('Extension') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="lab la-buromobelexperte"></i></span>
                            <input type="text" name="extension" id="extension" value="{{ old('extension', $lead->extension) }}"
                                   maxlength="6" class="form-control form-control-lg">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="phone2">{{ __('Phone') }} 2</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-phone"></i></span>
                            <input type="tel" name="phone2" id="phone2" value="{{ old('phone2', $lead->phone2) }}"
                                   maxlength="15" class="form-control form-control-lg">
                        </div>
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="email" class="">E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-envelope"></i></span>
                            <input type="email" name="email" id="email" value="{{ old('email', $lead->email) }}"
                                   maxlength="254" class="form-control form-control-lg">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="email2" class="">E-mail 2</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-envelope"></i></span>
                            <input type="email" name="email2" id="email2" value="{{ old('email2', $lead->email2) }}"
                                   maxlength="254" class="form-control form-control-lg">
                        </div>
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="mobile" class="">{{ __('Mobile') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="lab la-whatsapp"></i></span>
                            <input type="tel" name="mobile" id="mobile" value="{{ old('mobile', $lead->mobile) }}"
                                   maxlength="15" class="form-control form-control-lg">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="website" class="">{{ __('Website') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-globe"></i></span>
                            <input type="url" name="website" id="website" placeholder="https://www.website.com"
                                   value="{{ old('website', $lead->website) }}" maxlength="255"
                                   class="form-control form-control-lg">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="vat" class="">{{ __('Identity number') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-id-card"></i></span>
                            <input type="text" name="vat" id="vat" value="{{ old('vat', $lead->vat) }}"
                                   maxlength="20" class="form-control form-control-lg">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="dob">{{ __('Date of birth') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-calendar-day"></i></span>
                            <input type="date" name="dob" id="dob" value="{{ old('dob', $lead->dob) }}"
                                   class="form-control form-control-lg">
                        </div>
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col">
                        <label for="notes">{{ __('Notes') }}</label>
                        <textarea name="notes" id="notes" rows="8"
                                  class="form-control form-control-lg">{{ old('notes', $lead->notes) }}</textarea>
                    </div>
                </div><!--./row-->
            </div><!--./card-body-->
        </div><!--./card-->

        <div class="card mt-2">
            <div class="card-header">{{ __('Address') }}</div>
            <div class="card-body">
                <div class="row mb-1">
                    <div class="col">
                        <label for="search-address">{{ __('Search for an address') }}</label>
                        <div class="input-group">
                            <input type="text" id="search-address" placeholder="{{ __('Search for an address') }}" class="form-control form-control-lg">
                            <button type="button" onclick="getCoordinates()" class="btn btn-secondary"><i class="las la-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="country_id">{{ __('Country') }}</label>
                        <input name="country_id" id="country_id" list="countryList" value="{{ $lead->country_id }}" class="form-control form-control-lg">

                        <datalist id="countryList">
                            @foreach ($countries as $country)
                                <option value="{{ $country->code_2 }}" @if($lead->country_id == $country->code_2) selected="selected" @endif>
                                    {{ $country->name }} {{ $country->flag }}
                                </option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="province" class="">{{ __('Province') }}</label>
                        <input type="text" name="province" id="province"
                               value="{{ old('province', $lead->province) }}" class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="city" class="">{{ __('City') }}</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $lead->city) }}"
                               class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="locality" class="">{{ __('Locality') }}</label>
                        <input type="text" name="locality" id="locality"
                               value="{{ old('locality', $lead->locality) }}"
                               class="form-control form-control-lg">
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="street" class="">{{ __('Street') }}</label>
                        <input type="text" name="street" id="street" value="{{ old('street', $lead->street) }}"
                               class="form-control form-control-lg" maxlength="80">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="zipcode" class="">{{ __('Zipcode') }}</label>
                        <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $lead->zipcode) }}"
                               class="form-control form-control-lg" maxlength="10">
                    </div>
                </div><!--./row-->
                <div class="row">
                    <input type="hidden" name="latitude" id="latitude" value="{{ $lead->latitude }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ $lead->longitude }}">
                    <div class="col mt-3">
                        <x-geolocalization.map :latitude="$lead->latitude" :longitude="$lead->longitude" />
                    </div><!--./row-->
                </div>
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
                        <input type="url" name="facebook" id="facebook"
                               value="{{ old('facebook', $lead->facebook) }}"
                               placeholder="https://www.facebook.com/" maxlength="255"
                               class="form-control form-control-lg">
                    </div><!--./input-group-->
                </div><!--./col-->
                <div class="col-12 col-md-6">
                    <label for="instagram">Instagram</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-instagram"></i></span>
                        <input type="url" name="instagram" id="instagram"
                               value="{{ old('instagram', $lead->instagram) }}"
                               placeholder="https://www.instagram.com/"
                               maxlength="255" class="form-control form-control-lg">
                    </div><!--./input-group-->
                </div><!--./col-->
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <label for="linkedin">Linkedin</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-linkedin-in"></i></span>
                        <input type="url" name="linkedin" id="linkedin"
                               value="{{ old('linkedin', $lead->linkedin) }}"
                               placeholder="https://www.linkedin.com/" maxlength="255"
                               class="form-control form-control-lg">
                    </div><!--./input-group-->
                </div><!--./col-->
                <div class="col-12 col-md-6">
                    <label for="twitter">Twitter / X</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fa-brands fa-x-twitter"></i>
                        </span>
                        <input type="url" name="twitter" id="twitter" value="{{ old('twitter', $lead->twitter) }}"
                               placeholder="https://twitter.com/" maxlength="255" class="form-control form-control-lg">
                    </div>
                </div><!--./col-->
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <label for="youtube">YouTube</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-youtube"></i></span>
                        <input type="url" name="youtube" id="youtube" value="{{ old('youtube', $lead->youtube) }}"
                               placeholder="https://www.youtube.com/" maxlength="255"
                               class="form-control form-control-lg">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="tiktok">TikTok</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-brands fa-tiktok"></i></span>
                        <input type="url" name="tiktok" id="tiktok" value="{{ old('tiktok', $lead->tiktok) }}"
                               placeholder="https://www.tiktok.com/" maxlength="255"
                               class="form-control form-control-lg">
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
                        <select name="industry_id" id="industry_id" class="form-select form-select-lg">
                            <option value=""></option>
                            @foreach($industries as $industry)
                            <option value="{{ $industry->id }}" @if($lead->industry_id == $industry->id) selected="selected" @endif>
                                {{ __($industry->name) }}
                            </option>
                            @endforeach
                        </select>
                    </div><!--./col-->
                    <div class="col">
                        <label for="status">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-select form-select-lg">
                            <option value="">{{ __('Choose') }}</option>
                            @foreach(\App\Models\Lead::getStatus() as $key => $status)
                            <option value="{{ $key }}" @if($lead->status == $key) selected="selected" @endif>
                                {{ __($status) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="schedule_contact">{{ __('Remember contact') }}</label>
                        <input type="datetime-local" name="schedule_contact" id="schedule_contact"
                               value="{{ old('schedule_contact', $lead->schedule_contact) }}"
                               min="{{ date('Y-m-d H:i') }}" class="form-control form-control-lg">
                    </div>
                </div><!--./row-->
                <div class="row">
                    <div class="col mt-2">
                        <label for="tags"><i class="las la-hashtag"></i> {{ __('Tags') }}</label>
                        <textarea name="tags" id="tags" placeholder="keyword, special keyword, keyword2"
                                  class="form-control form-control-lg">
                            {{ (!empty($lead->tags)) ? implode(',', $lead->tags) : '' }}
                        </textarea>
                    </div>
                    <div class="col mt-2">
                        <label for="seller_id">{{ __('Seller') }} <span class="text-danger">*</span></label>
                        <select name="seller_id" id="seller_id" required="required" class="form-select form-select-lg">
                            <option value=""></option>
                            @foreach ($sellers as $seller)
                                <option value="{{ $seller->id }}"
                                        @if ($lead->seller_id == $seller->id) selected="selected" @endif>
                                    {{ $seller->first_name . ' ' . $seller->last_name }}</option>
                            @endforeach
                        </select>
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
        <div id="new-message" class="card mt-3 d-none">
            <div class="card">

                <div class="card-body">
                    <form method="post" action="{{ url("/lead/message/save") }}">
                        @csrf
                        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                        <div class="row">
                            <div class="col">
                                <label for="message">{{ __('Message') }}</label>
                                <textarea name="message" id="message" required class="form-control form-control-lg"></textarea>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col pt-2">
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('lead_customer.partials.messages', ['messages' => $lead->messages, 'url' => 'lead'])
    @endif

    @if($lead->id)
    <div class="card mt-2 mb-5">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    {{ __('Contacts') }}

                    <a class="btn btn-primary btn-sm" style="border-radius: 40px;" href="{{ url('/contact/create', ['lead', $lead->id]) }}">
                        <i class="las la-plus fw-bold"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body bg-white">
            <div class="mt-2 table-responsive">
                @include('contact.index', ['contacts' => $lead->contacts])
            </div>
        </div><!--./card-body-->
    </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <script>
        let input = document.getElementById('tags');
        const tagify = new Tagify(input, {
            dropdown: {
                maxItems :0,
                enabled: 0
            },
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
            //whitelist: ["a", "aa", "aaa", "b", "bb", "ccc"]
        });
    </script>
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
    </script>
    <script src="/asset/js/Contact.js"></script>
    <script src="/asset/js/Region.js"></script>
    @endpush
@endsection
