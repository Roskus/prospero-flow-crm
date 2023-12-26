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
                    <label for="subject">{{ __('Subject') }}</label>
                    <input type="text" name="subject" id="subject" value="{{ $campaign->subject }}" class="form-control form-control-lg">
                </div>
            </div><!--./row-->
            <div class="row">
                <div class="col">
                    <label for="from" class="">{{ __('From') }}</label>
                    <select name="from" id="from" required class="form-select">
                        <option value=""></option>
                        @foreach ($froms as $from)
                            <option value="{{ $from['email'] }}"
                                    @if ($campaign->from == $from['email'] ) selected="selected" @endif>
                                {{ $from['email'] }}
                                &lt;{{ $from['name'] }}&gt;
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="body" class="">{{ __('Message') }}</label>
                    <div class="mb-1">
                        <a onclick="addVariable2Editor('body', '$prospect->first_name')">
                            <span class="btn btn-outline-secondary">&#123;&#123; $prospect->first_name &#125;&#125;</span>
                        </a>
                    </div>
                    <textarea name="body" id="body">{{ old('body', $campaign->body) }}</textarea>
                </div>
            </div>
            <div class="row">
                <div class="col mt-2">
                    <label for="tags"><i class="las la-hashtag"></i> {{ __('Tags') }}</label>
                    <textarea name="tags" id="tags" placeholder="keyword, special keyword, keyword2"
                              class="form-control form-control-lg">
                        {{ (!empty($campaign->tags)) ? implode(',', $campaign->tags) : '' }}
                    </textarea>
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
