@php
    // Pages behind sign in send guests to it first, then straight on to the page they picked.
    $pages = [
        ['title' => 'Sign-in history', 'text' => 'Your own sign-ins and wrong passwords, in a sortable table.', 'icon' => 'shield-check', 'route' => 'dashboard', 'private' => true],
        ['title' => 'Settings', 'text' => 'Profile, password, other devices and deleting the account.', 'icon' => 'settings', 'route' => 'profile.edit', 'private' => true],
        ['title' => 'Sign in', 'text' => 'Rate limited, with remember me.', 'icon' => 'lock', 'route' => 'login', 'private' => false],
    ];

    if (config('security.registration')) {
        $pages[] = ['title' => 'Create account', 'text' => 'Live password rules, then email verification.', 'icon' => 'user', 'route' => 'register', 'private' => false];
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head />
</head>
<body class="bg-dots text-foreground min-h-screen font-sans antialiased">
    <header class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-4 gap-y-3 px-4 py-4 sm:px-6 sm:py-6">
        <span class="font-semibold">{{ config('app.name') }}</span>

        <nav aria-label="Account" class="flex items-center gap-2">
            <x-theme-toggle />
            @auth
                {{-- Just the icon on phones, so the header stays on one line; screen readers still hear "Dashboard". --}}
                <x-widget.button :href="route('dashboard')" size="sm" icon-start="inbox"><span class="sr-only sm:not-sr-only">Dashboard</span></x-widget.button>
                <x-account-menu />
            @else
                <x-widget.button :href="route('login')" variant="neutral" size="sm">Sign in</x-widget.button>
                @if (config('security.registration'))
                    <x-widget.button :href="route('register')" size="sm">Create account</x-widget.button>
                @endif
            @endauth
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
        <section class="mx-auto max-w-3xl py-12 text-center sm:py-20 lg:py-24">
            <h1 class="text-3xl font-semibold tracking-tight text-balance sm:text-5xl">Your Laravel app starts here</h1>
            <p class="text-muted mx-auto mt-4 max-w-xl text-base text-pretty sm:text-lg">
                Sign in, registration, password reset, email verification and settings, built from Bladewell widgets.
                Plain Blade and a little vanilla JS: no React, Vue or Alpine.
            </p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <x-widget.button :href="route('dashboard')" size="lg" icon-end="arrow-right">See the dashboard</x-widget.button>
                <x-widget.button href="https://www.bladewellui.com" variant="tertiary" size="lg" icon-end="external-link">Browse the widgets</x-widget.button>
            </div>
            @guest
                <p class="text-muted mt-3 text-sm">You'll be asked to sign in first.</p>
            @endguest
        </section>

        <section aria-labelledby="pages-heading">
            <h2 id="pages-heading" class="mb-4 text-lg font-semibold">What's inside</h2>

            <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($pages as $page)
                    <li>
                        <a href="{{ route($page['route']) }}" class="bg-surface border-line hover:border-line-strong focus-visible:outline-primary flex h-full flex-col rounded-2xl border p-5 focus-visible:outline-2 focus-visible:outline-offset-2">
                            <span class="bg-primary/10 text-primary grid size-10 place-items-center rounded-xl">
                                <x-widget.icon :name="$page['icon']" class="size-5" />
                            </span>
                            <span class="mt-4 font-semibold">{{ $page['title'] }}</span>
                            <span class="text-muted mt-1 text-sm">{{ $page['text'] }}</span>
                            @if ($page['private'] && auth()->guest())
                                <span class="text-muted mt-auto flex items-center gap-1.5 pt-4 text-xs">
                                    <x-widget.icon name="lock" class="size-3.5" /> Needs sign in
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    </main>
</body>
</html>
