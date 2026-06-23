<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function create()
    {
        $branches = Branch::all();
        $routes = Route::all();
        return view('Admin.Customer.create', compact('branches', 'routes'));
    }
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => [
                    'required',
                    'min:3',
                    'max:50',
                    'regex:/^[A-Za-z\s]+$/'
                ],
                'phone' => [
                    'required',
                    'unique:customers,phone',
                    'regex:/^[0-9]{10}$/'
                ],
                'password'  => 'required|min:6|max:20',
                'branch_id' => 'required',
                'route_id'  => 'required',
            ],
            [
                'name.regex'  => 'The :attribute may only contain letters and spaces.',
                'phone.regex' => 'The :attribute must be a 10-digit number.',
            ],
            [
                'name'      => 'Name',
                'phone'     => 'Phone',
                'password'  => 'Password',
                'branch_id' => 'Branch',
                'route_id'  => 'Route',
            ]
        );

        // Store logic here
        // Hash password before saving
        $data = $request->all();
        $data['name'] = ucwords(strtolower($request->name));
        $data['password'] = Hash::make($request->password);

        Customer::create($data);

        return redirect()
            ->route('admin.customer-list')
            ->with('success', 'Customer added successfully');
    }

    // In your controller (CustomerController.php)

    public function index(Request $request)
    {
        $data = Customer::with(['branch', 'route', 'loanAssign.emiCollections.latestDetail'])->get();
        return view('Admin.Customer.index', compact('data'));
    }
    public function edit($id)
    {
        $original = Customer::findOrFail($id);
        $branches = Branch::all();
        $routes = Route::all();
        return view('Admin.Customer.edit', compact('original', 'branches', 'routes'));
    }

    public function update(Request $request, $id)
    {
        $original = Customer::findOrFail($id);

        $request->validate(
            [
                'name' => [
                    'required',
                    'min:3',
                    'max:20',
                    'regex:/^[A-Za-z\s]+$/'
                ],
                'phone' => [
                    'required',
                    'regex:/^[0-9]{10}$/',
                    'unique:customers,phone,' . $id
                ],
                'password' => [
                    'nullable',
                    'min:6',
                    'max:20',
                ],
                'branch_id' => 'required',
                'route_id'  => 'required',
            ]
        );



        $data = $request->except('password');

        // If user enters a new password, hash it
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $original->update($data);

        return redirect()
            ->route('admin.customer-list')
            ->with('success', 'Customer updated successfully');
    }
    public function view($id)
    {
        $data = Customer::with('branch', 'route')->findOrFail($id);
        return view('Admin.Customer.view', compact('data'));
    }
}
