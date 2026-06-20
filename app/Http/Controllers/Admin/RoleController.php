<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function create()
    {
        return view('Admin.Role.create');
    }
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate(
            [
                'role_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^[A-Za-z\s]+$/',
                    'unique:roles,name',
                ],
            ],
            [
                'role_name.regex' => 'The :attribute may only contain letters and spaces.',
            ],
            [
                'role_name' => 'Role',
            ]
        );
        // Create a new role
        $role = new Role();
        $role->name = $request->input('role_name');
        $role->save();

        return redirect()
            ->route('admin.role-list')
            ->with('success', 'Role Added successfully');
    }
    public function index()
    {
        $data = Role::where('name', '!=', 'admin')->get();
        return view('Admin.Role.index', compact('data'));
    }

    public function edit($id)
    {
        $data = Role::findOrFail($id);
        return view('Admin.Role.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
         $request->validate(
            [
                'role_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^[A-Za-z\s]+$/',
                    'unique:roles,name,' . $id,
                ],
            ],
            [
                'role_name.regex' => 'The :attribute may only contain letters and spaces.',
            ],
            [
                [
                    'role_name' => 'Role',
                ]
            ]
        );

        // Find the role by ID and update its name
        $role = Role::findOrFail($id);
        $role->name = $request->input('role_name');
        $role->save();

        return redirect()
            ->route('admin.role-list')
            ->with('success', 'Role Updated successfully');
    }
}
