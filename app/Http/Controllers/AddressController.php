<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->addresses()->count() >= config('florafetch.max_addresses')) {
            return back()->with('error', 'You can save a maximum of ' . config('florafetch.max_addresses') . ' addresses.');
        }

        $data = $request->validate([
            'label'       => 'required|string|min:1|max:50',
            'street'      => 'required|string',
            'city'        => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        $user->addresses()->create($data);

        return back()->with('success', 'Address saved.');
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'label'       => 'required|string|min:1|max:50',
            'street'      => 'required|string',
            'city'        => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        $address->update($data);

        return back()->with('success', 'Address updated.');
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Address has been deleted.');
    }
}
