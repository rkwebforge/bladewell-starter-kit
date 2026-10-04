@props([
    // The page's name: the heading and the tab title.
    'title',
    // A line under the heading.
    'description' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head :title="$title" />
</head>
<body class="bg-dots text-foreground min-h-screen font-sans antialiased">
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <a href="{{ route('home') }}" class="mb-8 text-lg font-semibold">{{ config('app.name') }}</a>

        <div class="bg-surface border-line w-full max-w-md rounded-2xl border p-6 shadow-sm sm:p-8">
            <h1 class="text-xl font-semibold">{{ $title }}</h1>
            @if ($description)
                <p class="text-muted mt-1 text-sm">{{ $description }}</p>
            @endif

            <div class="mt-6">
                {{ $slot }}
            </div>
        </div>

        @isset($footer)
            <p class="text-muted mt-6 text-sm">{{ $footer }}</p>
        @endisset
    </main>
</body>
</html>
