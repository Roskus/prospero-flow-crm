<div class="card mt-3">
    <div class="card-header">
        {{ __('Messages') }}
        <a href="#" onclick="$('#new-message').removeClass('d-none');" class="btn btn-primary btn-sm">
            <i class="las la-plus fw-bold"></i> {{ __('New message') }}
        </a>
    </div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col">{{ __('Author') }}</th>
                <th scope="col">{{ __('Message') }}</th>
                <th scope="col">{{ __('Created at') }}</th>
                <th scope="col">{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($messages as $message)
                <tr>
                    <td>{{ $message->author->first_name }}</td>
                    <td>{{ $message->body }}</td>
                    <td>{{ $message->created_at->format('d/m/y h:m') }}</td>
                    <td>
                        @can('delete message')
                        <a href="/{{$url}}/message/delete/{{ $message->id }}" class="btn btn-primary btn-sm">
                            <i class="las la-trash-alt"></i>
                        </a>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
