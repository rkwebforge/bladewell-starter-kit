<x-layouts.guest title="Choose a new password">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-widget.text-input name="email" type="email" label="Email" :value="$request->query('email')" autocomplete="username" required />
        <x-new-password label="New password" required autofocus />
        <x-widget.password name="password_confirmation" label="Confirm new password" autocomplete="new-password" required />

        <x-widget.button type="submit" class="w-full">Save new password</x-widget.button>
    </form>
</x-layouts.guest>
