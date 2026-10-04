<x-layouts.guest title="Confirm your password" :back="route('dashboard')" back-label="Dashboard" description="This part of your account is protected. Enter your password to carry on; we won't ask again for a while.">
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <x-widget.password name="password" label="Password" required autofocus />

        <x-widget.button type="submit" class="w-full">Confirm</x-widget.button>
    </form>
</x-layouts.guest>
