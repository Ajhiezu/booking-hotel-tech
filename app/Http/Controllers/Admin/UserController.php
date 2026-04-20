<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->filled('role')) {
            $query->role($request->role);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        $user->load(['roles', 'bookings.hotel', 'hotels', 'reviews']);
        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot deactivate Super Admin account.');
        }
        $user->update(['is_active' => !$user->is_active]);
        AuditLog::log($user->is_active ? 'user_activated' : 'user_deactivated', $user);

        return back()->with('success', 'User status updated successfully.');
    }

    public function approveOwner(User $user)
    {
        $user->update(['verification_status' => 'approved']);
        AuditLog::log('owner_approved', $user);

        // Send notification email
        try {
            \Mail::to($user->email)->send(new \App\Mail\OwnerApproved($user));
        } catch (\Exception $e) {
            // Fail silently - mail not configured
        }

        return back()->with('success', 'Hotel owner approved successfully.');
    }

    public function rejectOwner(User $user, Request $request)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $user->update(['verification_status' => 'rejected']);
        AuditLog::log('owner_rejected', $user, [], ['reason' => $request->reason]);

        return back()->with('success', 'Hotel owner rejected.');
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot delete Super Admin.');
        }
        $user->delete();
        AuditLog::log('user_deleted', null, ['user_email' => $user->email]);

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
