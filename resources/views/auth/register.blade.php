<x-layouts.guest title="Create an account" description="It takes a minute.">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-widget.text-input name="name" label="Name" autocomplete="name" required autofocus />
        <x-widget.text-input name="email" type="email" label="Email" autocomplete="username" required />
        <x-new-password required />
        <x-widget.password name="password_confirmation" label="Confirm password" autocomplete="new-password" required />

        <x-widget.button type="submit" class="w-full">Create account</x-widget.button>
    </form>

    <x-slot:footer>
        Already have one? <a href="{{ route('login') }}" class="text-link hover:text-link-hover font-medium underline-offset-4 hover:underline">Sign in</a>
    </x-slot:footer>
</x-layouts.guest>
