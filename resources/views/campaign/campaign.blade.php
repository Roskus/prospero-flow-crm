@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Campaign') }}</h1>
    </header>

    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ url('campaign/save') }}">
            @csrf
            <input type="hidden" name="id" value="{{ (!empty($campaign->id)) ? $campaign->id : '' }}">
            <div class="row">
                <div class="col">
                    <label for="name">
                        {{ __('Name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $campaign->name) }}" required maxlength="255" class="form-control form-control-lg @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div><!--./row-->
            <div class="row">
                <div class="col">
                    <label for="subject">
                        {{ __('Subject') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject', $campaign->subject) }}" required maxlength="255" class="form-control form-control-lg @error('subject') is-invalid @enderror">
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div><!--./row-->
            <div class="row">
                <div class="col">
                    <label for="from" class="">
                        {{ __('From') }} <span class="text-danger">*</span>
                    </label>
                    <select name="from" id="from" required class="form-select @error('from') is-invalid @enderror">
                        <option value=""></option>
                        @foreach ($froms as $from)
                            <option value="{{ $from['email'] }}"
                                    @selected(old('from', $campaign->from) === $from['email'])>
                                {{ $from['email'] }}
                                &lt;{{ $from['name'] }}&gt;
                            </option>
                        @endforeach
                    </select>
                    @error('from') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="body" class="">
                        {{ __('Message') }} <span class="text-danger">*</span>
                    </label>
                    <div class="mb-1">
                        <a onclick="addVariable2Editor('body', ' name ')" title="{{ __('Name') }}">
                            <span class="btn btn-outline-secondary">&#123;&#123; name &#125;&#125;</span>
                        </a>
                        <a onclick="addVariable2Editor('body', ' business_name ')" title="{{ __('Business name') }}">
                            <span class="btn btn-outline-secondary">&#123;&#123; business_name &#125;&#125;</span>
                        </a>
                    </div>
                    <textarea name="body" id="body">{{ old('body', $campaign->body) }}</textarea>
                    @error('body') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col mt-2">
                    <span class="form-label d-block">{{ __('Recipients') }} <span class="text-danger">*</span></span>
                    @php($recipientType = old('recipient_type', $campaign->recipient_type ?? \App\Models\Campaign::RECIPIENTS_LEADS))
                    @foreach ([\App\Models\Campaign::RECIPIENTS_LEADS => __('Leads'), \App\Models\Campaign::RECIPIENTS_CUSTOMERS => __('Customers'), \App\Models\Campaign::RECIPIENTS_BOTH => __('Leads and customers')] as $value => $label)
                        <div class="form-check form-check-inline">
                            <input type="radio" name="recipient_type" id="recipient_type_{{ $value }}" value="{{ $value }}" class="form-check-input" @checked($recipientType === $value)>
                            <label for="recipient_type_{{ $value }}" class="form-check-label">{{ $label }}</label>
                        </div>
                    @endforeach
                    @error('recipient_type') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col mt-2">
                    <span class="form-label d-block">{{ __('Send to') }} <span class="text-danger">*</span></span>
                    @php($audience = old('audience', $campaign->audience ?? \App\Models\Campaign::AUDIENCE_ALL))
                    <div class="form-check form-check-inline">
                        <input type="radio" name="audience" id="audience_all" value="{{ \App\Models\Campaign::AUDIENCE_ALL }}" class="form-check-input" @checked($audience === \App\Models\Campaign::AUDIENCE_ALL)>
                        <label for="audience_all" class="form-check-label">{{ __('Everyone with opt in') }}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="audience" id="audience_tags" value="{{ \App\Models\Campaign::AUDIENCE_TAGS }}" class="form-check-input" @checked($audience === \App\Models\Campaign::AUDIENCE_TAGS)>
                        <label for="audience_tags" class="form-check-label">{{ __('Only those with any of these tags') }}</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mt-2">
                    <label for="tags"><i class="las la-hashtag"></i> {{ __('Tags') }}</label>
                    <textarea name="tags" id="tags" placeholder="keyword, special keyword, keyword2"
                              class="form-control form-control-lg">
                        {{ (!empty($campaign->tags)) ? implode(',', $campaign->tags) : '' }}
                    </textarea>
                    @error('tags') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col mt-2">
                    <label for="schedule_send_date"><i class="las la-calendar"></i> {{ __('Schedule date') }}</label>
                    <input type="date" name="schedule_send_date" id="schedule_send_date" value="{{ $campaign->schedule_send_date }}" min="{{ date('Y-m-d') }}" class="form-control form-control-lg">
                </div>
                <div class="col mt-2">
                    <label for="schedule_send_time"><i class="las la-clock"></i> {{ __('Schedule time') }}</label>
                    <input type="time" name="schedule_send_time" id="schedule_send_time" value="{{ $campaign->schedule_send_time }}" class="form-control form-control-lg">
                </div>
            </div>
            <div class="row">
                <div class="col mt-2">
                    <a href="{{ url('campaign') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div><!--./row-->
            </form>
        </div><!--card-body-->
    </div><!--card-->
    @include('html_editor', ['id' => 'body', 'placeholder' => 'Email body message'])
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
@endsection
