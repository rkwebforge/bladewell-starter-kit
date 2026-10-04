<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    // Only these reach orderBy(): the table sends whatever is in the query string.
    private const array SORTABLE = ['name', 'email', 'created_at'];

    public function __invoke(Request $request): View
    {
        $sort = in_array($request->query('sort'), self::SORTABLE, true) ? $request->query('sort') : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        return view('dashboard', [
            'users' => User::query()->orderBy($sort, $direction)->paginate(10)->withQueryString(),
        ]);
    }
}
