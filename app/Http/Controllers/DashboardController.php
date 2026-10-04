<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    // Only these reach orderBy(): the table sends whatever is in the query string.
    private const array SORTABLE = ['created_at', 'ip_address'];

    public function __invoke(Request $request): View
    {
        $sort = in_array($request->query('sort'), self::SORTABLE, true) ? $request->query('sort') : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        /** @var User $user */
        $user = $request->user();

        return view('dashboard', [
            // Through the relation, so the query can only ever return this person's own rows.
            'signIns' => $user->signIns()->orderBy($sort, $direction)->orderByDesc('id')->paginate(10)->withQueryString(),
            'recentFailures' => $user->signIns()->where('succeeded', false)->where('created_at', '>=', now()->subDays(30))->count(),
        ]);
    }
}
