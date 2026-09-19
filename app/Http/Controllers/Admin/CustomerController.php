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
        $branches = Branch::all();
        $branch_id = $request->branch_id;

        if ($request->ajax()) {
            $query = Customer::with(['branch', 'route', 'loanAssign.latestEmiCollection.latestDetail']);

            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('collection_type', function ($row) {
                    $type = $row->loanAssign->collection_type_id ?? null;
                    return match ($type) {
                        1 => 'Daily',
                        2 => 'Weekly',
                        3 => 'Monthly',
                        default => '---',
                    };
                })
                ->addColumn('total_payable', function ($row) {
                    return $row->loanAssign->total_payableamt ?? '---';
                })
                ->addColumn('remaining_amount', function ($row) {
                    return $row->loanAssign?->latestEmiCollection?->latestDetail?->remaining_payable_amount ?? '---';
                })
                ->addColumn('branch_name', function ($row) {
                    return $row->branch->branch_name ?? '---';
                })
                ->addColumn('route_name', function ($row) {
                    return $row->route->route_name ?? '---';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user() && auth()->user()->can('customer-view')) {
                        $actions .= '<a href="' . route('admin.customer-view', $row->id) . '" class="btn btn-success btn-sm me-1">View</a>';
                    }
                    if (auth()->user() && auth()->user()->can('customer-edit')) {
                        $actions .= '<a href="' . route('admin.customer-edit', $row->id) . '" class="btn btn-warning btn-sm">Edit</a>';
                    }
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Admin.Customer.index', compact('branches', 'branch_id'));
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
