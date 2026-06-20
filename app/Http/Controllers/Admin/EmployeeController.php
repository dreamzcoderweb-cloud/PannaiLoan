<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Route;
use App\Models\EmployeeIdProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $data = Employee::with(['branch', 'route', 'idProofs'])->get();
        return view('Admin.Employee.index', compact('data'));
    }

    public function create()
    {
        $branches = Branch::all();
        $routes = Route::all();
        return view('Admin.Employee.create', compact('branches', 'routes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:50',
            'phone' => 'required|unique:employees,phone|regex:/^[0-9]{10}$/',
            'password' => 'required|min:6|max:12',
            'branch_id' => 'required',
            'route_id' => 'required',
            'id_proof.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],
        [
            'name.min' => 'Name must be at least 3 characters long',
            'name.max' => 'Name cannot be more than 50 characters long',
            'phone.unique' => 'Phone already exists',
            'phone.regex' => 'Phone must be 10 digits long',
            'password.min' => 'Password must be at least 6 characters long',
            'password.max' => 'Password cannot be more than 12 characters long',
            'branch_id.required' => 'Branch is required',
            'route_id.required' => 'Route is required',
            'id_proof.*.image' => 'Each Id proof must be an image',
            'id_proof.*.mimes' => 'Each Id proof must be a jpeg, png, jpg, or gif',
            'id_proof.*.max' => 'Each Id proof must not be more than 2MB',
        ]);

        $data = $request->except('id_proof');
        $data['password'] = Hash::make($request->password);

        $employee = Employee::create($data);

        if ($request->hasFile('id_proof')) {
            foreach ($request->file('id_proof') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move(public_path('uploads/employees'), $imageName);
                
                EmployeeIdProof::create([
                    'employee_id' => $employee->id,
                    'image' => $imageName
                ]);
            }
        }

        return redirect()->route('admin.employee-list')->with('success', 'Employee created successfully.');
    }

    public function edit($id)
    {
        $original = Employee::with('idProofs')->findOrFail($id);
        $branches = Branch::all();
        $routes = Route::all();
        return view('Admin.Employee.edit', compact('original', 'branches', 'routes'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'name' => 'required|min:3|max:50',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:employees,phone,' . $id,
            'branch_id' => 'required',
            'route_id' => 'required',
            'id_proof.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'id_proof']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        if ($request->hasFile('id_proof')) {
            foreach ($request->file('id_proof') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move(public_path('uploads/employees'), $imageName);
                
                EmployeeIdProof::create([
                    'employee_id' => $employee->id,
                    'image' => $imageName
                ]);
            }
        }

        return redirect()->route('admin.employee-list')->with('success', 'Employee updated successfully.');
    }

    public function delete($id)
    {
        $employee = Employee::with('idProofs')->findOrFail($id);
        
        foreach ($employee->idProofs as $proof) {
            if (File::exists(public_path('uploads/employees/' . $proof->image))) {
                File::delete(public_path('uploads/employees/' . $proof->image));
            }
            $proof->delete();
        }
        
        $employee->delete();

        return redirect()->route('admin.employee-list')->with('success', 'Employee deleted successfully.');
    }
}

