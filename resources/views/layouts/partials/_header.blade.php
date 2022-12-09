<header class="mb-3 mt-2 rounded bg-light text-dark">
    <div class="m-0 py-1 d-flex">
        <h1 class="px-2 m-0 display-5">{{ Str::upper($title) }}</h1>
        @isset($count)
            <h2 class="my-2"><span class="badge rounded-pill text-bg-success">{{ $count }}</span></h2>
        @endisset        
    </div>
</header>