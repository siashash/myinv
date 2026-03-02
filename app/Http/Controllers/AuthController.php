<?php

namespace App\Http\Controllers;

use App\Models\ManagedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        if (session()->has('managed_user_id')) {
            redirect()->route('dashboard')->send();
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        if ($request->session()->has('managed_user_id')) {
            return redirect()->route('dashboard');
        }

        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = ManagedUser::with('role')
            ->where('name', $credentials['name'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['name' => 'Invalid username or password.'])
                ->onlyInput('name');
        }

        $request->session()->regenerate();
        $request->session()->put('managed_user_id', $user->id);
        $request->session()->put('managed_user_name', $user->name);
        $request->session()->put('role_id', $user->role_id);
        $request->session()->put('managed_user_role_name', $user->role?->role_name);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
