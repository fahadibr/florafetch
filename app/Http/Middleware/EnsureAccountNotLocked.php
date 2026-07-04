<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountNotLocked
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && $request->routeIs('login')) {
            $identifier = $request->input('email') ?? $request->input('phone');
            if ($identifier) {
                $user = \App\Models\User::where('email', $identifier)
                    ->orWhere('phone', $identifier)
                    ->first();

                if ($user && $user->isLocked()) {
                    $minutes = now()->diffInMinutes($user->locked_until, false);
                    return back()->withErrors([
                        'email' => "Account is locked. Try again in {$minutes} minute(s).",
                    ])->withInput($request->except('password'));
                }
            }
        }

        return $next($request);
    }
}
