<header class="mb-3 mt-2 rounded bg-light text-dark d-print-none">
    <div class="m-0 py-1">
        <div class="row">
            <div class="col-9">
                <h1 class="px-2 m-0">
                    <span class="display-5 text-uppercase">{{ $title }}</span>
                    @isset($count)
                        <span class="badge rounded-pill text-bg-success">{{ $count }}</span>
                    @endisset
                </h1>
            </div>

            @isset($print)
            <div class="col">
            <a href="#" onclick="window.print()" class="btn btn-lg btn-outline-secondary">
                <i class="las la-print"></i> {{ __('Print') }}
            </a>
            </div>
            @endisset
        </div>

    </div>
</header>
