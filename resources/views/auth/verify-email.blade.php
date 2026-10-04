<x-layouts.guest title="Check your email" description="We sent a link to {{ auth()->user()->email }}. Open it to verify your address and carry on.">
    <x-widget.alert flash="status" tone="success" class="mb-6" />

    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-widget.button type="submit" variant="secondary" icon-start="mail">Send it again</x-widget.button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-widget.button type="submit" variant="link">Sign out</x-widget.button>
        </form>
    </div>
</x-layouts.guest>
