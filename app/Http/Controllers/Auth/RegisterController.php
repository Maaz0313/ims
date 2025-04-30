<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    // In Laravel 12, middleware is applied in routes rather than in controllers

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        // Check if user is authenticated and is admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Admin access required.');
        }

        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    // Public registration is completely disabled

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        // Check if user is authenticated and is admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Admin access required.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,id'],
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin'),
            'is_active' => true,
        ]);

        // Assign the selected role
        $role = Role::findOrFail($request->role);
        $user->roles()->attach($role);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }
}
