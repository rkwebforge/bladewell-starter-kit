@php
    // Prompts to give an AI agent. The app has no database yet, so each one sets it up if it's the first to need it.
    $setup = 'This app has no database yet: if there is no users table, set up SQLite with Laravel\'s users migration and the User model first.';

    $prompts = [
        'login' => ['Login page', 'Add sign-in to this app with Bladewell components (https://www.bladewellui.com/llms.txt). '.$setup.' Build a login page at /login: email and password fields, a "Remember me" checkbox and a "Log in" button. Check the email and password in a Form Request, lock an email and IP address out for a minute after 5 wrong tries, regenerate the session and redirect home. When the email or password is wrong, show an error alert above the form and keep the email filled in. Add a "Log out" button to the home page header for people who are signed in. Install only the components you use with php artisan bladewell:add, and add feature tests.'],
        'sign-up' => ['Sign-up page', 'Add sign-up to this app with Bladewell components (https://www.bladewellui.com/llms.txt). '.$setup.' Build a sign-up page at /register: name, email, password and password confirmation, and a "Create account" button. The password field shows a live checklist of the rules (at least 12 characters, upper and lower case, a number), and the Form Request enforces the same ones with Password::min(12)->mixedCase()->numbers(); the email must not be taken. Hash the password, sign the new person in and redirect home, and allow 10 sign-ups an hour per IP address. Show each field\'s error under it, install only the components you use with php artisan bladewell:add, and add feature tests.'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head />
</head>
<body class="bg-dots text-foreground min-h-screen font-sans antialiased">
    <header class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6 sm:py-6">
        <span class="font-semibold">{{ config('app.name') }}</span>
        <x-theme-toggle />
    </header>

    <main class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
        <section class="mx-auto max-w-3xl py-12 text-center sm:py-20 lg:py-24">
            <h1 class="text-3xl font-semibold tracking-tight text-balance sm:text-5xl">Your Laravel app starts here</h1>
            <p class="text-muted mx-auto mt-4 max-w-xl text-base text-pretty sm:text-lg">
                Blade, Tailwind CSS and Bladewell widgets, with no React, Vue or Alpine.
                Add the widgets you need, or ask your AI agent to build the pages for you.
            </p>
            <div class="mt-8 flex justify-center">
                <x-widget.button href="https://www.bladewellui.com/components" target="_blank" rel="noopener" size="lg" icon-end="external-link">Browse the widgets<span class="sr-only"> (opens in a new tab)</span></x-widget.button>
            </div>
        </section>

        <section aria-labelledby="prompts-heading">
            <h2 id="prompts-heading" class="text-lg font-semibold">Ask your agent</h2>
            <p class="text-muted mt-1 text-sm">Copy a prompt into Claude Code, Cursor, Codex or another agent. Change the routes, fields and wording to fit your app.</p>

            <ul class="mt-4 grid gap-4 lg:grid-cols-2">
                @foreach ($prompts as $key => [$title, $prompt])
                    <li class="bg-surface border-line flex flex-col rounded-2xl border p-5">
                        <div class="flex items-center justify-between gap-4">
                            <h3 class="font-semibold">{{ $title }}</h3>
                            <x-widget.button variant="neutral" size="sm" icon-start="copy" data-copy="prompt-{{ $key }}"><span data-copy-label>Copy</span><span class="sr-only"> the {{ strtolower($title) }} prompt</span></x-widget.button>
                        </div>
                        <p id="prompt-{{ $key }}" class="text-muted mt-3 text-sm">{{ $prompt }}</p>
                    </li>
                @endforeach
            </ul>
            {{-- resources/js/copy.js says "Copied" here, so screen readers hear it too. --}}
            <p role="status" class="sr-only" data-copy-status></p>
        </section>
    </main>
</body>
</html>
