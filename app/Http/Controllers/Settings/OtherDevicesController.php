<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class OtherDevicesController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('otherDevices', [
            'password' => ['required', 'current_password'],
        ]);

        // Re-hashes the password, and the auth.session middleware then ends every session that still holds the old
        // hash: all of them but this one.
        Auth::logoutOtherDevices($validated['password']);

        return back()->with('success', __('Signed out on your other devices.'));
    }
}
