<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);

        $users = User::with('organizationalUnit', 'roles')->latest()->get();
        $units = OrganizationalUnit::where('is_active', true)->get();
        $roles = Role::orderBy('name')->get();

        $stats = [
            'total'        => $users->count(),
            'active'       => $users->where('is_active', true)->count(),
            'super_admin'  => $users->filter(fn($u) => $u->hasRole('super_admin'))->count(),
            'unit_manager' => $users->filter(fn($u) => $u->hasRole('unit_manager'))->count(),
            'admin'        => $users->filter(fn($u) => $u->hasRole('admin'))->count(),
        ];

        return view('admin.users.index', compact('users', 'units', 'roles', 'stats'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'email', 'unique:users'],
            'password'               => ['required', Password::min(8)],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
            'is_active'              => ['boolean'],
            'role'                   => ['nullable', 'exists:roles,name'],
        ]);

        $role = $data['role'] ?? 'admin';
        unset($data['role']);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);
        $user->assignRole($role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'email', Rule::unique('users')->ignore($user)],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
            'is_active'              => ['boolean'],
            'role'                   => ['nullable', 'exists:roles,name'],
        ]);

        $role = $data['role'] ?? null;
        unset($data['role']);

        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->filled('password')) {
            $request->validate(['password' => [Password::min(8)]]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($role) {
            $user->syncRoles([$role]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus akun sendiri.']);
        }
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
