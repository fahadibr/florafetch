<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|min:1|max:100',
            'email'    => 'nullable|email|unique:users,email|required_without:phone',
            'phone'    => 'nullable|string|regex:/^\+[1-9]\d{1,14}$/|unique:users,phone|required_without:email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = $this->authService->register($data);

        auth()->login($user);

        return redirect()->route('catalog.index')
            ->with('success', 'Welcome to FloraFetch! Your account has been created.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'nullable|required_without:phone',
            'phone'    => 'nullable|required_without:email',
            'password' => 'required',
        ]);

        $success = $this->authService->attemptLogin($request->only('email', 'phone', 'password', 'remember'));

        if (!$success) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput($request->except('password'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('catalog.index'));
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        return redirect()->route('catalog.index');
    }
}
