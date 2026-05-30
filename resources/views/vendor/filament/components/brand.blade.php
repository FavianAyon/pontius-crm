<div
    x-data="{ mode: 'light' }"
    x-on:dark-mode-toggled.window="mode = $event.detail"
>
    <span x-show="mode === 'light'">
        <img src="{{ asset('img/logo-blue.svg') }}" alt="Pontius Realty" class="h-6">
    </span>

    <span x-show="mode === 'dark'">
        <img src="{{ asset('img/logo-white.svg') }}" alt="Pontius Realty" class="h-6">
    </span>
</div>
