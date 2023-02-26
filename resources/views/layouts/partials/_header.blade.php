<header class="mb-3 mt-2 rounded bg-light text-dark">
    <div class="m-0 py-1 d-flex">
        <div class="row">
            <div class="col">
                <h1 class="px-2 m-0 display-5">{{ Str::upper($title) }}</h1>
            </div>
            @isset($print)
            <div class="col">
            <a href="#" onclick="window.print()" class="btn btn-outline-secondary d-print-none">
                <i class="las la-print"></i> {{ __('Print') }}
            </a>
            </div>
            @endisset
        </div>
        @isset($count)
            <h2 class="my-2"><span class="badge rounded-pill text-bg-success">{{ $count }}</span></h2>
        @endisset

    </div>
</header>
