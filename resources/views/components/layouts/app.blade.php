@props([
    // The page's name: the heading and the tab title.
    'title',
    // The trail above the heading, as breadcrumbs takes it. Leave out for top-level pages.
    'breadcrumbs' => null,
])

@php
    $links = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
        ['label' => 'Settings', 'route' => 'profile.edit', 'icon' => 'settings'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head :title="$title" />
</head>
<body class="bg-dots text-foreground min-h-screen font-sans antialiased">
    <a href="#main" class="bg-surface sr-only rounded-lg px-4 py-2 focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50">Skip to content</a>

    <header class="bg-surface border-line border-b">
        <div class="mx-auto flex h-16 max-w-6xl items-center gap-3 px-4 sm:gap-6 sm:px-6">
            <a href="{{ route('home') }}" class="font-semibold">{{ config('app.name') }}</a>

            <nav aria-label="Main" class="flex items-center gap-1">
                @foreach ($links as $link)
                    @php($current = request()->routeIs($link['route']))
                    <a
                        href="{{ route($link['route']) }}"
                        @if ($current) aria-current="page" @endif
                        @class([
                            'flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium',
                            'bg-primary/10 text-primary' => $current,
                            'text-muted hover:bg-field hover:text-foreground' => ! $current,
                        ])
                    >
                        <x-widget.icon :name="$link['icon']" class="size-4" />
                        {{-- sr-only, not hidden, so the icon-only links on phones keep their names. --}}
                        <span class="sr-only sm:not-sr-only">{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="ms-auto flex items-center gap-1">
                <x-theme-toggle />
                <x-account-menu />
            </div>
        </div>
    </header>

    <main id="main" class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        @if ($breadcrumbs)
            <x-widget.breadcrumbs :items="$breadcrumbs" class="mb-3" />
        @endif
        <h1 class="mb-6 text-2xl font-semibold">{{ $title }}</h1>

        {{ $slot }}
    </main>

    {{-- Shows what a controller flashes under success, error, warning or info. --}}
    <x-widget.toast />
</body>
</html>
