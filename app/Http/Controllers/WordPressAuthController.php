<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\WordPressUser;
use App\Services\EnhancedWordPressHasher;

class WordPressAuthController extends Controller
{
    protected $hasher;

    public function __construct()
    {
        $this->hasher = app('wordpress.hash');
    }

    public function showLoginForm()
    {
        // If already logged in, redirect
        if (Auth::guard('wordpress')->check()) {
            return redirect()->intended('/home');
        }

        return view('auth.wp-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user_login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find WordPress user
        $user = WordPressUser::where('user_login', $credentials['user_login'])
                            ->orWhere('user_email', $credentials['user_login'])
                            ->first();

        if (!$user) {
            return back()->withErrors([
                'user_login' => 'User not found.',
            ])->withInput($request->except('password'));
        }

        // Check password using enhanced hasher
        if ($this->hasher->check_password($credentials['password'], $user->user_pass)) {

            // Method 1: Try loginUsingId first
            try {
                Auth::guard('wordpress')->loginUsingId($user->ID, $request->boolean('remember'));
            } catch (\Exception $e) {
                // Fallback: Use regular login
                Auth::guard('wordpress')->login($user, $request->boolean('remember'));
            }

            // Verify the login worked
            if (!Auth::guard('wordpress')->check()) {
                \Log::error('WordPress auth failed after login attempt', [
                    'user_id' => $user->ID,
                    'user_login' => $user->user_login,
                    'session_data' => session()->all()
                ]);

                return back()->withErrors([
                    'user_login' => 'Authentication failed. Please try again.',
                ]);
            }

            // Regenerate session for security
            $request->session()->regenerate();

            // Debug info (remove in production)
            \Log::info('WordPress login successful', [
                'user_id' => Auth::guard('wordpress')->id(),
                'user_login' => Auth::guard('wordpress')->user()->user_login,
                'session_id' => session()->getId(),
            ]);

            return redirect()->intended('/home')
                           ->with('success', 'Welcome back, ' . $user->display_name . '!');
        }

        return back()->withErrors([
            'user_login' => 'Invalid credentials.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::guard('wordpress')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/wp-login')->with('success', 'You have been logged out.');
    }
}
