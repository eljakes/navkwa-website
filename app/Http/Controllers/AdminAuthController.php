<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_DECAY_SECONDS = 60;

    public function create(Request $request)
    {
        if ($request->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return $this->loginView();
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureLoginIsNotRateLimited($request);

        if ($request->user() && ! $request->user()->isAdminUser()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'account_status' => 'active',
            fn ($query) => $query->whereIn('role', User::ADMIN_ROLES),
        ], false)) {
            RateLimiter::hit($this->throttleKey($request), self::LOGIN_DECAY_SECONDS);

            throw ValidationException::withMessages([
                'email' => 'These admin credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));
        $request->session()->regenerate();
        $request->user()->forceFill(['last_login_at' => now()])->save();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'Logged in to admin portal',
            'module' => 'Security',
            'record_type' => get_class($request->user()),
            'record_id' => $request->user()->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request)
    {
        if ($request->user()) {
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Logged out of admin portal',
                'module' => 'Security',
                'record_type' => get_class($request->user()),
                'record_id' => $request->user()->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function loginView()
    {
        return response()
            ->view('admin.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function ensureLoginIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), self::MAX_LOGIN_ATTEMPTS)) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => 'Too many sign-in attempts. Please try again in '.RateLimiter::availableIn($this->throttleKey($request)).' seconds.',
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return 'admin-login|'.Str::lower((string) $request->input('email')).'|'.$request->ip();
    }
}
