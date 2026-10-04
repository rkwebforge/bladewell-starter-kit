@props([
    // The page's name: the heading and the tab title.
    'title',
    // The trail above the heading, as breadcrumbs takes it. Leave out for top-level pages.
    'breadcrumbs' => null,
])

@php
    $user = auth()->user();
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
<body class="bg-field text-foreground min-h-screen font-sans antialiased">
    <a href="#main" class="bg-surface sr-only rounded-lg px-4 py-2 focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50">Skip to content</a>

    <header class="bg-surface border-line border-b">
        <div class="mx-auto flex h-16 max-w-6xl items-center gap-6 px-4 sm:px-6">
            <a href="{{ route('dashboard') }}" class="font-semibold">{{ config('app.name') }}</a>

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

            <div class="ms-auto">
                <x-widget.dropdown align="end" label="Account: {{ $user->name }}, {{ $user->email }}">
                    <x-slot:trigger class="hover:bg-field py-1.5 ps-1.5 pe-3">
                        <span aria-hidden="true" class="bg-primary/10 text-primary grid size-8 place-items-center rounded-full text-xs font-semibold">{{ $user->initials() }}</span>
                        <span class="hidden text-sm font-medium sm:inline">{{ $user->name }}</span>
                        <x-widget.icon name="chevron-down" class="size-4 opacity-60" />
                    </x-slot:trigger>

                    <div aria-hidden="true" class="px-3 pt-1.5 pb-2">
                        <p class="text-foreground/60 text-xs">Signed in as</p>
                        <p class="truncate font-medium">{{ $user->email }}</p>
                    </div>
                    <x-widget.dropdown.divider />
                    <x-widget.dropdown.item :href="route('profile.edit')" icon="user">Profile</x-widget.dropdown.item>
                    <x-widget.dropdown.item :action="route('logout')" icon="log-out">Sign out</x-widget.dropdown.item>
                </x-widget.dropdown>
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
