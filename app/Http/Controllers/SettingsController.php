<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index() {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    public function update(Request $request) {
        $request->validate([
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        $user->No_Telp = $request->phone;

        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $user->password = bcrypt($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
