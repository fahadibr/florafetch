<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $addresses = $user->addresses;
        $plantHistory = $user->orders()
            ->where('status', 'delivered')
            ->with('items')
            ->latest()
            ->get();

        return view('profile.show', compact('user', 'addresses', 'plantHistory'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'  => 'required|string|min:1|max:100',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|regex:/^\+[1-9]\d{1,14}$/|unique:users,phone,' . $user->id,
        ]);

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
