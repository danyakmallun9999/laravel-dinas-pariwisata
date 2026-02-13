<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Only show admins or specific roles?
        // Let's list all users who have admin access or are intended for admin
        // For now, list all users but maybe visually distinguish
        // Or filter by is_admin = true
        
        $users = User::where('is_admin', true)
            ->with(['roles', 'ownedPlaces'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'admin')
                     ->where('name', '!=', 'category_admin')
                     ->get();
        $places = Place::orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'places'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'place_id' => ['nullable', 'exists:places,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
        ]);

        $role = Role::findByName($request->role, 'admin');
        $user->assignRole($role);

        if ($request->filled('place_id')) {
            $place = Place::find($request->place_id);
            if ($place) {
                $place->update(['created_by' => $user->id]);
            }
        }

        return redirect()->route('admin.users.index')->with('status', 'Admin berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::where('guard_name', 'admin')
                     ->where('name', '!=', 'category_admin')
                     ->get();
        $places = Place::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles', 'places'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'exists:roles,name'],
            'place_id' => ['nullable', 'exists:places,id'],
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        
        if ($request->filled('password')) {
             $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $role = Role::findByName($request->role, 'admin');
        $user->syncRoles([$role]);

        if ($request->filled('place_id')) {
            $place = Place::find($request->place_id);
            if ($place) {
                // Should we unassign the previous owner? 
                // Usually yes, if we are "assigning" this place to this user.
                // But let's just update owner.
                $place->update(['created_by' => $user->id]);
            }
        }

        return redirect()->route('admin.users.index')->with('status', 'Admin berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Admin berhasil dihapus.');
    }
}
