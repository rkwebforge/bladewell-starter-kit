@props([
    // The page's name: the heading and the tab title.
    'title',
    // The trail above the heading, as breadcrumbs takes it. Leave out for top-level pages.
    'breadcrumbs' => null,
])

@php
    $links = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'inbox'],
        ['label' => 'Settings', 'route' => 'profile.edit', 'icon' => 'settings'],
    ];
    // The Settings page's sections, by their heading ids. Full URLs, so they open Settings from anywhere; on Settings
    // itself the accordion's script treats them as #section links and moves the highlight as they're clicked.
    $settings = [
        ['label' => 'Profile', 'section' => 'profile-heading'],
        ['label' => 'Password', 'section' => 'password-heading'],
        ['label' => 'Other devices', 'section' => 'devices-heading'],
        ['label' => 'Delete account', 'section' => 'delete-heading'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head :title="$title" />
</head>
<body class="bg-dots text-foreground min-h-screen font-sans antialiased">
    <a href="#main" class="bg-surface sr-only rounded-lg px-4 py-2 focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50">Skip to content</a>

    {{-- Large screens: a sidebar that stays put while the page scrolls; its menu scrolls on its own if it gets long. --}}
    <aside class="bg-surface border-line fixed inset-y-0 start-0 z-20 hidden w-64 flex-col border-e lg:flex">
        <div class="border-line flex h-16 shrink-0 items-center border-b px-6">
            <a href="{{ route('home') }}" class="font-semibold">{{ config('app.name') }}</a>
        </div>
        <x-widget.accordion.menu label="Main" class="min-h-0 flex-1 px-3 py-4">
            <x-widget.accordion.menu-item :href="route('dashboard')" :current="request()->routeIs('dashboard')">Dashboard</x-widget.accordion.menu-item>
            <x-widget.accordion variant="menu" title="Settings">
                @foreach ($settings as $index => $item)
                    {{-- Profile is current on arrival; the script moves it with the URL's #hash. --}}
                    <x-widget.accordion.menu-item :href="route('profile.edit').'#'.$item['section']" :current="$index === 0 && request()->routeIs('profile.edit')">{{ $item['label'] }}</x-widget.accordion.menu-item>
                @endforeach
            </x-widget.accordion>
        </x-widget.accordion.menu>
    </aside>

    <div class="lg:ps-64">
        <header class="bg-surface border-line border-b">
            {{-- The same centred width and padding as <main>, so the header's and the page's edges line up. --}}
            <div class="mx-auto flex h-16 max-w-(--breakpoint-2xl) items-center gap-3 px-4 sm:gap-6 sm:px-6">
                {{-- The sidebar carries these on large screens. --}}
                <a href="{{ route('home') }}" class="font-semibold lg:hidden">{{ config('app.name') }}</a>

                <nav aria-label="Main" class="flex items-center gap-1 lg:hidden">
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

        <main id="main" class="mx-auto max-w-(--breakpoint-2xl) px-4 py-8 sm:px-6">
            @if ($breadcrumbs)
                <x-widget.breadcrumbs :items="$breadcrumbs" class="mb-3" />
            @endif
            <h1 class="mb-6 text-2xl font-semibold">{{ $title }}</h1>

            {{ $slot }}
        </main>
    </div>

    {{-- Shows what a controller flashes under success, error, warning or info. --}}
    <x-widget.toast />
</body>
</html>
