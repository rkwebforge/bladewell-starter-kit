<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head />
</head>
<body class="bg-field text-foreground min-h-screen font-sans antialiased">
    <header class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-6 sm:px-6">
        <span class="font-semibold">{{ config('app.name') }}</span>

        <nav aria-label="Account" class="flex items-center gap-2">
            @auth
                <x-widget.button :href="route('dashboard')">Dashboard</x-widget.button>
            @else
                <x-widget.button :href="route('login')" variant="neutral">Sign in</x-widget.button>
                <x-widget.button :href="route('register')">Create account</x-widget.button>
            @endauth
        </nav>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-24 text-center sm:px-6">
        <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">Your Laravel app starts here</h1>
        <p class="text-muted mx-auto mt-4 max-w-xl text-lg">
            Sign in, registration, password reset, email verification and settings, built from LarawellUI widgets.
            Plain Blade and a little vanilla JS: no React, Vue or Alpine.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <x-widget.button :href="route('register')" size="lg">Get started</x-widget.button>
            <x-widget.button href="https://larawellui.wasmer.app" variant="tertiary" size="lg" icon-end="external-link">Browse the widgets</x-widget.button>
        </div>
    </main>
</body>
</html>
