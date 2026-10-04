<x-layouts.app title="Dashboard">
    <p class="text-muted -mt-4 mb-8">Welcome back, {{ auth()->user()->name }}. Replace this page with your app.</p>

    @if ($recentFailures > 0)
        <x-widget.alert tone="warning" title="{{ trans_choice(':count wrong password in the last 30 days|:count wrong passwords in the last 30 days', $recentFailures) }}" class="mb-6">
            If that wasn't you, someone may be guessing. <a href="{{ route('profile.edit') }}" class="font-medium underline underline-offset-4">Change your password</a> and sign out your other devices.
        </x-widget.alert>
    @endif

    <section aria-labelledby="sign-ins-heading">
        <h2 id="sign-ins-heading" class="text-lg font-semibold">Sign-in history</h2>
        <p class="text-muted mt-1 mb-3 text-sm">Every sign-in to your account, and every wrong password, for the last {{ config('security.sign_in_history_days') }} days.</p>

        {{-- Sorting is checked against a fixed list in DashboardController before it reaches the query. --}}
        <x-widget.table caption="Sign-in history" :rows="$signIns" pagination="numbers" stack empty="No sign-ins yet" :columns="[
            ['label' => 'When', 'key' => 'created_at', 'sortable' => true],
            'Result',
            'Device',
            ['label' => 'IP address', 'key' => 'ip_address', 'sortable' => true, 'align' => 'end'],
        ]">
            @foreach ($signIns as $signIn)
                <x-widget.table.row>
                    <td><time datetime="{{ $signIn->created_at->toIso8601String() }}">{{ $signIn->created_at->toDayDateTimeString() }}</time></td>
                    <td>
                        @if ($signIn->succeeded)
                            <x-widget.table.badge tone="success">Signed in</x-widget.table.badge>
                        @else
                            <x-widget.table.badge tone="error">Wrong password</x-widget.table.badge>
                        @endif
                    </td>
                    <td>{{ $signIn->device() }}</td>
                    <td class="tabular-nums">{{ $signIn->ip_address }}</td>
                </x-widget.table.row>
            @endforeach
        </x-widget.table>
    </section>
</x-layouts.app>
