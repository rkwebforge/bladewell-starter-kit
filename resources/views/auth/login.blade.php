<x-layouts.guest title="Sign in" description="Welcome back. Enter your email and password.">
    <x-widget.alert flash="status" tone="success" class="mb-6" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-widget.text-input name="email" type="email" label="Email" autocomplete="username" required autofocus />
        <x-widget.password name="password" label="Password" required />

        <div class="flex items-center justify-between gap-4">
            <x-widget.checkbox name="remember" label="Remember me" />
            <a href="{{ route('password.request') }}" class="text-link hover:text-link-hover text-sm whitespace-nowrap underline-offset-4 hover:underline">Forgot password?</a>
        </div>

        <x-widget.button type="submit" class="w-full">Sign in</x-widget.button>
    </form>

    <x-slot:footer>
        No account yet? <a href="{{ route('register') }}" class="text-link hover:text-link-hover font-medium underline-offset-4 hover:underline">Create one</a>
    </x-slot:footer>
</x-layouts.guest>
