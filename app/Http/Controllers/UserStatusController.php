<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserStatusController extends Controller
{
    /**
     * Toggle the user's active status.
     */
    public function toggleStatus(User $user)
    {
        // Check if user is authenticated and is admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Admin access required.');
        }

        // Prevent toggling your own status
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot change your own account status.');
        }

        // Toggle the status
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $statusText = $user->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('users.index')
            ->with('success', "User {$user->name} has been {$statusText}.");
    }
}
