@php
    // Prompts to give an AI agent. The app has no database yet, so each one sets it up if it's the first to need it.
    $setup = 'This app has no database yet: if there is no users table, set up SQLite with Laravel\'s users migration and the User model first.';

    $prompts = [
        'login' => ['Login page', 'lock', 'Builds /login with "Remember me", a lockout after 5 wrong tries, and tests.', 'Add sign-in to this app with Bladewell components (https://www.bladewellui.com/llms.txt). '.$setup.' Build a login page at /login: email and password fields, a "Remember me" checkbox and a "Log in" button. Check the email and password in a Form Request, lock an email and IP address out for a minute after 5 wrong tries, regenerate the session and redirect home. When the email or password is wrong, show an error alert above the form and keep the email filled in. Add a "Log out" button to the home page header for people who are signed in. Install only the components you use with php artisan bladewell:add, and add feature tests.'],
        'sign-up' => ['Sign-up page', 'user', 'Builds /register with a live password checklist, a limit per IP, and tests.', 'Add sign-up to this app with Bladewell components (https://www.bladewellui.com/llms.txt). '.$setup.' Build a sign-up page at /register: name, email, password and password confirmation, and a "Create account" button. The password field shows a live checklist of the rules (at least 12 characters, upper and lower case, a number), and the Form Request enforces the same ones with Password::min(12)->mixedCase()->numbers(); the email must not be taken. Hash the password, sign the new person in and redirect home, and allow 10 sign-ups an hour per IP address. Show each field\'s error under it, install only the components you use with php artisan bladewell:add, and add feature tests.'],
    ];

    // The parts of a prompt an agent acts on literally (the docs URL, routes, the command, code) are shown as code.
    // Each part is escaped; the tags add no characters, so Copy still copies the prompt word for word.
    $marked = fn (string $prompt): string => collect(preg_split('#(https://\S+?(?=\))|php artisan bladewell:add|Password::\S+?(?=;)|(?<=\s)/[a-z-]+)#', $prompt, -1, PREG_SPLIT_DELIM_CAPTURE))
        ->map(fn (string $part, int $i): string => $i % 2 ? '<code class="bg-primary/10 text-primary rounded px-1 py-0.5 font-mono text-[0.8125rem] wrap-break-word box-decoration-clone">'.e($part).'</code>' : e($part))
        ->implode('');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head />
</head>
<body class="bg-dots text-foreground min-h-screen font-sans antialiased">
    <header class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6 sm:py-6">
        <span class="font-semibold">{{ config('app.name') }}</span>
        <div class="flex items-center gap-2">
            <x-widget.button href="https://www.bladewellui.com/components" target="_blank" rel="noopener" size="sm" icon-end="external-link">Browse the widgets<span class="sr-only"> (opens in a new tab)</span></x-widget.button>
            <x-theme-toggle />
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 pb-12 sm:px-6">
        <section class="mx-auto max-w-3xl pt-6 pb-10 text-center sm:pt-10 sm:pb-12">
            <h1 class="text-3xl font-semibold tracking-tight text-balance sm:text-5xl">Your Laravel app starts here</h1>
            <p class="text-muted mx-auto mt-4 max-w-xl text-base text-pretty sm:text-lg">
                Blade, Tailwind CSS and Bladewell widgets, with no React, Vue or Alpine.
                Add the widgets you need, or ask your AI agent to build the pages for you.
            </p>
        </section>

        <section aria-labelledby="prompts-heading">
            <h2 id="prompts-heading" class="text-lg font-semibold">Ask your agent to build a page</h2>
            <p class="text-muted mt-1 text-sm">Change the routes, fields and wording to fit your app before you send it.</p>

            <ul class="mt-5 grid gap-6 lg:grid-cols-2">
                @foreach ($prompts as $key => [$title, $icon, $summary, $prompt])
                    {{-- One card drawn like an agent's input window: a title bar, then the prompt typed in after a caret. --}}
                    <li class="bg-surface border-foreground/25 flex flex-col overflow-hidden rounded-2xl border">
                        <div class="bg-field border-foreground/15 flex items-center gap-3 border-b px-4 py-3">
                            <span class="bg-primary/10 text-primary grid size-9 shrink-0 place-items-center rounded-lg">
                                <x-widget.icon :name="$icon" class="size-4.5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold">{{ $title }}</h3>
                                <p class="text-muted text-xs">{{ $summary }}</p>
                            </div>
                            <x-widget.button size="sm" icon-start="copy" data-copy="prompt-{{ $key }}" class="shrink-0"><span data-copy-label>Copy</span><span class="sr-only"> the {{ strtolower($title) }} prompt</span></x-widget.button>
                        </div>

                        <div class="flex flex-1 gap-3 p-4">
                            <span class="text-primary font-mono text-sm leading-relaxed font-semibold select-none" aria-hidden="true">&#x276F;</span>
                            {{-- Raw: $marked escaped every part of the prompt and added only its own <code> tags. --}}
                            <p id="prompt-{{ $key }}" class="min-w-0 text-sm leading-relaxed text-pretty">{!! $marked($prompt) !!}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
            {{-- resources/js/copy.js says "Copied" here, so screen readers hear it too. --}}
            <p role="status" class="sr-only" data-copy-status></p>
        </section>
    </main>
</body>
</html>
