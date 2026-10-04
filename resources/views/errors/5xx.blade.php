{{-- Every server error, while APP_DEBUG is off. Never shows details: they're in storage/logs. --}}
<x-layouts.guest title="Something went wrong on our side" description="It's not something you did. Try again in a minute.">
    <x-widget.button :href="route('home')" class="w-full">Go to the home page</x-widget.button>
</x-layouts.guest>
