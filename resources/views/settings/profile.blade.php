<x-layouts.app title="Settings" :breadcrumbs="[
    ['label' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'home', 'iconOnly' => true],
    'Settings',
]">
    <div class="max-w-2xl space-y-6">
        <section aria-labelledby="profile-heading" class="bg-surface border-line rounded-2xl border p-6">
            <h2 id="profile-heading" class="text-lg font-semibold">Profile</h2>
            <p class="text-muted mt-1 text-sm">Your name and the address you sign in with. Changing the address asks you to verify it again.</p>

            <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PATCH')

                <x-widget.text-input name="name" label="Name" :value="$user->name" autocomplete="name" required />
                <x-widget.text-input name="email" type="email" label="Email" :value="$user->email" autocomplete="username" required />

                <x-widget.button type="submit">Save</x-widget.button>
            </form>
        </section>

        <section aria-labelledby="password-heading" class="bg-surface border-line rounded-2xl border p-6">
            <h2 id="password-heading" class="text-lg font-semibold">Password</h2>
            <p class="text-muted mt-1 text-sm">Use a long password you don't use anywhere else.</p>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <x-widget.password name="current_password" label="Current password" bag="updatePassword" required />
                <x-widget.password name="password" label="New password" bag="updatePassword" new required />
                <x-widget.password name="password_confirmation" label="Confirm new password" bag="updatePassword" autocomplete="new-password" required />

                <x-widget.button type="submit">Change password</x-widget.button>
            </form>
        </section>

        <section aria-labelledby="delete-heading" class="bg-surface border-error/30 rounded-2xl border p-6">
            <h2 id="delete-heading" class="text-lg font-semibold">Delete account</h2>
            <p class="text-muted mt-1 text-sm">Removes your account and everything in it, for good.</p>

            <x-widget.button variant="danger" icon-start="trash" data-modal-open="delete-account" class="mt-6">Delete account</x-widget.button>
        </section>
    </div>

    {{-- Reopens after a wrong password, with the error under the field. --}}
    <x-widget.modal id="delete-account" title="Delete your account?" size="sm" :open="$errors->userDeletion->isNotEmpty()" reset-on-close>
        <form id="delete-account-form" method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('DELETE')

            <p class="text-foreground/75 text-sm">This can't be undone. Enter your password to confirm.</p>
            <x-widget.password name="password" id="delete-account-password" label="Password" bag="userDeletion" required />
        </form>

        <x-slot:footer>
            <x-widget.button variant="neutral" data-modal-close>Cancel</x-widget.button>
            <x-widget.button type="submit" form="delete-account-form" variant="danger">Delete account</x-widget.button>
        </x-slot:footer>
    </x-widget.modal>
</x-layouts.app>
