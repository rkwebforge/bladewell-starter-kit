{{-- The signed-in person's menu: who they are, their profile, and signing out. --}}
@php($user = auth()->user())

<x-widget.dropdown align="end" label="Account: {{ $user->name }}, {{ $user->email }}">
    <x-slot:trigger class="hover:bg-field py-1.5 ps-1.5 pe-3">
        <span aria-hidden="true" class="bg-primary/10 text-primary grid size-8 place-items-center rounded-full text-xs font-semibold">{{ $user->initials() }}</span>
        <span class="hidden text-sm font-medium sm:inline">{{ $user->name }}</span>
        <x-widget.icon name="chevron-down" class="size-4 opacity-60" />
    </x-slot:trigger>

    <div aria-hidden="true" class="px-3 pt-1.5 pb-2">
        <p class="text-foreground/60 text-xs">Signed in as</p>
        <p class="truncate font-medium">{{ $user->email }}</p>
    </div>
    <x-widget.dropdown.divider />
    <x-widget.dropdown.item :href="route('profile.edit')" icon="user">Profile</x-widget.dropdown.item>
    <x-widget.dropdown.item :action="route('logout')" icon="log-out">Sign out</x-widget.dropdown.item>
</x-widget.dropdown>
