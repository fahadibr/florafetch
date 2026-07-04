<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'] ?? null,
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'customer',
        ]);
    }

    public function attemptLogin(array $credentials): bool
    {
        $identifier = $credentials['email'] ?? $credentials['phone'] ?? null;
        $password   = $credentials['password'];

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            if ($user) {
                $this->incrementFailedAttempts($user);
            }
            return false;
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'This account has been deactivated.',
            ]);
        }

        // Reset failed attempts on success
        $user->update(['failed_login_attempts' => 0, 'locked_until' => null]);
        Auth::login($user, $credentials['remember'] ?? false);

        return true;
    }

    public function incrementFailedAttempts(User $user): void
    {
        $attempts = $user->failed_login_attempts + 1;
        $lockedUntil = null;

        if ($attempts >= 5) {
            $lockedUntil = now()->addMinutes(15);
            $attempts = 0;
        }

        $user->update([
            'failed_login_attempts' => $attempts,
            'locked_until'          => $lockedUntil,
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
