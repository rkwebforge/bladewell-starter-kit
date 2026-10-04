<x-layouts.app title="Dashboard">
    <p class="text-muted -mt-4 mb-8">Welcome back, {{ auth()->user()->name }}. Replace this page with your app.</p>

    <section aria-labelledby="users-heading">
        <h2 id="users-heading" class="mb-3 text-lg font-semibold">Users</h2>

        {{-- Sorting is checked against a fixed list in DashboardController before it reaches the query. --}}
        <x-widget.table caption="Users" :rows="$users" pagination="numbers" stack :columns="[
            ['label' => 'Name', 'key' => 'name', 'sortable' => true],
            ['label' => 'Email', 'key' => 'email', 'sortable' => true],
            'Status',
            ['label' => 'Joined', 'key' => 'created_at', 'sortable' => true, 'align' => 'end'],
        ]">
            @foreach ($users as $user)
                <x-widget.table.row>
                    <td class="font-medium">{{ $user->name }}</td>
                    {{-- Long addresses break before the @ on narrow cards, and anywhere only if a half still won't fit. --}}
                    <td><span class="min-w-0 wrap-anywhere">{{ Str::before($user->email, '@') }}<wbr>{{ '@'.Str::after($user->email, '@') }}</span></td>
                    <td>
                        @if ($user->hasVerifiedEmail())
                            <x-widget.table.badge tone="success">Verified</x-widget.table.badge>
                        @else
                            <x-widget.table.badge tone="warning">Unverified</x-widget.table.badge>
                        @endif
                    </td>
                    <td><time datetime="{{ $user->created_at?->toIso8601String() }}">{{ $user->created_at?->toFormattedDateString() }}</time></td>
                </x-widget.table.row>
            @endforeach
        </x-widget.table>
    </section>
</x-layouts.app>
