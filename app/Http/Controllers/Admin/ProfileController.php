<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Check if user is Super Admin / Admin
     */
    private function isAdmin($user): bool
    {
        if (!$user) {
            return false;
        }
        $roleName = strtolower($user->roles->first()?->name ?? '');
        return $roleName === 'admin' || $user->hasRole(['ADMIN', 'admin']);
    }

    /**
     * Show the profile edit form (Super Admin only).
     */
    public function edit()
    {
        $user = auth()->user();

        if (!$this->isAdmin($user)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'You do not have permission to access My Profile.');
        }

        return view('Admin.Profile.index', compact('user'));
    }

    /**
     * Update the authenticated user's profile (Super Admin only).
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$this->isAdmin($user)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'You do not have permission to update profile.');
        }

        $request->validate([
            'name' => [
                'required',
                'min:3',
                'max:50',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'email' => [
                'required',
                'email',
                'unique:users,email,' . $user->id
            ],
            'current_password' => [
                'nullable',
                'required_with:password'
            ],
            'password' => [
                'nullable',
                'min:6',
                'max:20',
                'confirmed'
            ],
        ], [
            'name.regex' => 'The name may only contain letters and spaces.',
            'current_password.required_with' => 'Current password is required to set a new password.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'The current password provided is incorrect.'])
                    ->withInput();
            }
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()
            ->route('admin.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
