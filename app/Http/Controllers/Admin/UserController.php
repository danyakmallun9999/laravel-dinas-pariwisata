<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * AUTH-02: Only super_admin can manage admin users.
     */
    private function authorizeSuperAdmin(): void
    {
        if (!auth('admin')->user()?->hasRole('super_admin')) {
            abort(403, 'Hanya super admin yang dapat mengelola pengguna.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        // Only show admins or specific roles?
        // Let's list all users who have admin access or are intended for admin
        // For now, list all users but maybe visually distinguish
        // Or filter by is_admin = true
        
        $users = Admin::query()
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
        $this->authorizeSuperAdmin();

        $roles = Role::where('guard_name', 'admin')
                     ->where('name', '!=', 'category_admin')
                     ->get();
        $places = Place::orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'places'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'place_id' => ['nullable', 'exists:places,id'],
        ]);

        // AUTH-02: Prevent role escalation — only super_admin can assign super_admin
        if ($request->role === 'super_admin' && !auth('admin')->user()->hasRole('super_admin')) {
            abort(403, 'Anda tidak dapat memberikan role super_admin.');
        }

        $user = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
        ]);

        $role = Role::findByName($request->role, 'admin');
        $user->assignRole($role);

        // AUTH-05: Log admin user creation
        Log::info('Admin user created', [
            'created_user_id' => $user->id,
            'created_email' => $user->email,
            'assigned_role' => $request->role,
            'created_by' => auth('admin')->id(),
        ]);

        if ($request->filled('place_id')) {
            $place = Place::find($request->place_id);
            if ($place) {
                $place->update(['created_by' => $user->id]);
            }
        }

        return redirect()->route('admin.users.index')->with('status', 'Admin berhasil ditambahkan.');
    }

    public function edit(Admin $user)
    {
        $this->authorizeSuperAdmin();

        $roles = Role::where('guard_name', 'admin')
                     ->where('name', '!=', 'category_admin')
                     ->get();
        $places = Place::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles', 'places'));
    }

    public function update(Request $request, Admin $user)
    {
        $this->authorizeSuperAdmin();

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

        // AUTH-02: Prevent role escalation
        if ($request->role === 'super_admin' && !auth('admin')->user()->hasRole('super_admin')) {
            abort(403, 'Anda tidak dapat memberikan role super_admin.');
        }

        // AUTH-05: Capture old roles for audit log
        $oldRoles = $user->getRoleNames()->toArray();

        $role = Role::findByName($request->role, 'admin');
        $user->syncRoles([$role]);

        $newRoles = $user->getRoleNames()->toArray();
        if ($oldRoles !== $newRoles) {
            Log::warning('Admin role changed', [
                'target_user_id' => $user->id,
                'target_email' => $user->email,
                'old_roles' => $oldRoles,
                'new_roles' => $newRoles,
                'changed_by' => auth('admin')->id(),
            ]);
        }

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

    public function destroy(Admin $user)
    {
        $this->authorizeSuperAdmin();

        if ($user->id === auth('admin')->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // AUTH-02: Prevent deleting super_admin by non-super_admin
        if ($user->hasRole('super_admin') && !auth('admin')->user()->hasRole('super_admin')) {
            abort(403, 'Anda tidak dapat menghapus super admin.');
        }

        // AUTH-05: Log admin deletion
        Log::warning('Admin user deleted', [
            'deleted_user_id' => $user->id,
            'deleted_email' => $user->email,
            'deleted_roles' => $user->getRoleNames()->toArray(),
            'deleted_by' => auth('admin')->id(),
        ]);

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Admin berhasil dihapus.');
    }
}
