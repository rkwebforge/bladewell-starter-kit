<x-layouts.guest title="Forgot your password?" description="Enter your email and we'll send you a link to choose a new one.">
    <x-widget.alert flash="status" tone="success" class="mb-6" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <x-widget.text-input name="email" type="email" label="Email" autocomplete="username" required autofocus />

        <x-widget.button type="submit" class="w-full">Email me a reset link</x-widget.button>
    </form>

    <x-slot:footer>
        Remembered it? <a href="{{ route('login') }}" class="text-link hover:text-link-hover font-medium underline-offset-4 hover:underline">Sign in</a>
    </x-slot:footer>
</x-layouts.guest>
