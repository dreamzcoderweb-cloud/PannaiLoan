<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use COM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class StaffController extends Controller
{
    public function create()
    {
        $role = Role::all();
        $permissions = Permission::all();
        $userPermissions = [];
        return view('Admin.Staff.create', compact('role', 'permissions', 'userPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
    'name' => [
        'required',
        'min:3',
        'max:20',
        'regex:/^[A-Za-z\s]+$/'
    ],
    'email' => 'required|email|unique:users,email',
    'password' => 'required|min:6|max:20',   // set max password length
    'role_name' => 'required|exists:roles,name',
    'permissions' => 'nullable|array',
], [
    'name.regex' => 'The name may only contain letters and spaces.',
]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // ✅ Assign role
        $user->assignRole($request->role_name);

        // ✅ Assign permissions
        $user->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('admin.staff-list')
            ->with('success', 'Staff created successfully.');
    }



    public function index()
    {
        $data = Role::all();
        return view('Admin.Staff.index', compact('data'));
    }
    public function edit($id)
    {
        $original = User::findOrFail($id);
        $roles = Role::all();
        $permissions = Permission::all();

        // current role name
        $roleName = $original->roles->first()?->name;

        // if admin → all permissions
        if ($roleName === 'admin') {
            $userPermissions = $permissions->pluck('name')->toArray();
        } else {
            $userPermissions = $original->getPermissionNames()->toArray();
        }

        return view(
            'Admin.Staff.edit',
            compact('original', 'roles', 'permissions', 'userPermissions', 'roleName')
        );
    }



    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
    'name' => [
        'required',
        'min:3',
        'max:20',
        'regex:/^[A-Za-z\s]+$/'
    ],
    'email' => [
        'nullable',
        'email',
        'unique:users,email,' . $id
    ],
    'password' => [
        'nullable',
        'min:6',
        'max:20'
    ],
    'role_name' => 'required|exists:roles,name',
    'permissions' => 'nullable|array',
], [
    'name.regex' => 'The name may only contain letters and spaces.',
]);


        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // ✅ Update role
        $user->syncRoles([$request->role_name]);

        // ✅ Update permissions
        $user->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('admin.staff-list')
            ->with('success', 'Staff updated successfully');
    }
}
