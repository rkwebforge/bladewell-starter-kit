{{-- Light, dark or system theme. resources/js/theme.js does the switching. --}}
<x-widget.dropdown align="end" label="Theme" {{ $attributes }}>
    <x-slot:trigger class="hover:bg-field size-10 justify-center">
        <x-widget.icon name="sun" class="size-5 dark:hidden" />
        <x-widget.icon name="moon" class="hidden size-5 dark:block" />
    </x-slot:trigger>

    @foreach (['light' => ['Light', 'sun'], 'dark' => ['Dark', 'moon'], 'system' => ['System', 'settings']] as $choice => [$label, $icon])
        <x-widget.dropdown.item :icon="$icon" data-theme-choice="{{ $choice }}">
            <span class="flex items-center justify-between gap-6">
                {{ $label }}
                <x-widget.icon name="check" data-theme-tick class="invisible size-4" />
            </span>
        </x-widget.dropdown.item>
    @endforeach
</x-widget.dropdown>
