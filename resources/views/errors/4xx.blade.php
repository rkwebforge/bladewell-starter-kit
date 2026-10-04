{{-- Every 4xx error without a page of its own. Says what happened in plain words, with a way back. --}}
@php
    [$title, $description] = match ($exception->getStatusCode()) {
        403 => ['You can\'t open this page', 'You don\'t have access, or the link has expired. Links in emails only work for a while: ask for a new one.'],
        404 => ['Page not found', 'The address may be mistyped, or the page has moved.'],
        419 => ['This page expired', 'It was open too long, so for your safety the form can\'t be sent. Go back, refresh the page and try again.'],
        429 => ['Too many attempts', 'Wait a minute, then try again.'],
        default => ['Something went wrong', 'The request couldn\'t be completed.'],
    };
@endphp

<x-layouts.guest :title="$title" :description="$description">
    <x-widget.button :href="route('home')" class="w-full">Go to the home page</x-widget.button>
</x-layouts.guest>
