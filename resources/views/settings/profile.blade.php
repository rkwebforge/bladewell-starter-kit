<x-layouts.app title="Settings" :breadcrumbs="[
    ['label' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'inbox', 'iconOnly' => true],
    'Settings',
]">
    <div class="max-w-2xl space-y-6">
        <section aria-labelledby="profile-heading" class="bg-surface border-line rounded-2xl border p-6">
            <h2 id="profile-heading" class="scroll-mt-12 text-lg font-semibold">Profile</h2>
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
            <h2 id="password-heading" class="scroll-mt-12 text-lg font-semibold">Password</h2>
            <p class="text-muted mt-1 text-sm">Use a long password you don't use anywhere else.</p>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <x-widget.password name="current_password" label="Current password" bag="updatePassword" required />
                <x-new-password label="New password" bag="updatePassword" required />
                <x-widget.password name="password_confirmation" label="Confirm new password" bag="updatePassword" autocomplete="new-password" required />

                <x-widget.button type="submit">Change password</x-widget.button>
            </form>
        </section>

        <section aria-labelledby="devices-heading" class="bg-surface border-line rounded-2xl border p-6">
            <h2 id="devices-heading" class="scroll-mt-12 text-lg font-semibold">Other devices</h2>
            <p class="text-muted mt-1 text-sm">Signed in somewhere you no longer use, or see a sign-in on your dashboard that wasn't you? Sign out everywhere except here.</p>

            <x-widget.button variant="secondary" icon-start="log-out" data-modal-open="other-devices" class="mt-6">Sign out other devices</x-widget.button>
        </section>

        <section aria-labelledby="delete-heading" class="bg-surface border-error/30 rounded-2xl border p-6">
            <h2 id="delete-heading" class="scroll-mt-12 text-lg font-semibold">Delete account</h2>
            <p class="text-muted mt-1 text-sm">Removes your account and everything in it, for good.</p>

            <x-widget.button variant="danger" icon-start="trash" data-modal-open="delete-account" class="mt-6">Delete account</x-widget.button>
        </section>
    </div>

    <x-widget.modal id="other-devices" title="Sign out other devices?" size="sm" :open="$errors->otherDevices->isNotEmpty()" reset-on-close>
        <form id="other-devices-form" method="POST" action="{{ route('other-devices.destroy') }}" class="space-y-4">
            @csrf
            @method('DELETE')

            <p class="text-foreground/75 text-sm">Every other browser and device signed in to your account is signed out. Enter your password to confirm.</p>
            <x-widget.password name="password" id="other-devices-password" label="Password" bag="otherDevices" required />
        </form>

        <x-slot:footer>
            <x-widget.button variant="neutral" data-modal-close>Cancel</x-widget.button>
            <x-widget.button type="submit" form="other-devices-form">Sign out other devices</x-widget.button>
        </x-slot:footer>
    </x-widget.modal>

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
