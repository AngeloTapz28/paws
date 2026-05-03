<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
            )
            ->when($request->role, fn($q) =>
                $q->whereHas('roles', fn($r) => $r->where('name', $request->role))
            )
            ->latest()
            ->paginate(15);

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function show(User $user)
    {
        $user->load(['roles', 'adoptionApplications.pet', 'pets']);
        return view('admin.users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'nullable|string|max:20',
            'password'      => 'required|min:8',
            'role_id'       => 'required|exists:roles,id',
            'status'        => 'required|in:active,inactive,suspended',
            'gender'        => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'          => $data['name'],
                'email'         => $data['email'],
                'phone'         => $data['phone'] ?? null,
                'password'      => Hash::make($data['password']),
                'status'        => $data['status'],
                'gender'        => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
            ]);

            $user->roles()->attach($data['role_id'], [
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function toggleStatus(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->update([
                'status' => $user->status === 'active' ? 'suspended' : 'active',
            ]);
        });

        return back()->with('success', 'User status updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->adoptionApplications()->exists()) {
            return back()->with('error', 'Cannot delete this user because they have existing adoption applications. Suspend the account instead.');
        }

        DB::transaction(function () use ($user) {
            $user->roles()->detach();
            $user->delete();
        });

        return back()->with('success', 'User deleted successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'phone'         => 'nullable|string|max:20',
            'status'        => 'required|in:active,inactive,suspended',
            'gender'        => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
        ]);

        DB::transaction(function () use ($data, $user) {
            $user->update($data);
        });

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated.');
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->roles()->syncWithoutDetaching([$request->role_id => [
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
        ]]);

        return back()->with('success', 'Role assigned successfully.');
    }
}