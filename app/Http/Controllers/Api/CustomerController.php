<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
   public function index(Request $request)
    {
        $query = Customer::with(['branch', 'route', 'loanAssign.emiCollections.latestDetail'])
                         ->byEmployeeRoute();

        if ($request->has('id')) {
            $data = $query->find($request->id);
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $page = max(1, (int) $request->input('page', 1));
        $limit = min(100, max(1, (int) $request->input('limit', 10)));
        $skip = ($page - 1) * $limit;

        $total = (clone $query)->count();

        $data = $query
            ->skip($skip)
            ->limit($limit)
            ->get();

        $lastPage = $total > 0 ? (int) ceil($total / $limit) : 0;

        return response()->json([
            'status' => true,
            'count' => $data->count(),
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $total === 0 ? 0 : $skip + 1,
                'to' => min($skip + $limit, $total),
                'has_more' => $page < $lastPage,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'min:3',
                'max:20',
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
        ], [
            'name.regex'  => 'The :attribute may only contain letters and spaces.',
            'phone.regex' => 'The :attribute must be a 10-digit number.',
        ], [
            'name'      => 'Name',
            'phone'     => 'Phone',
            'password'  => 'Password',
            'branch_id' => 'Branch',
            'route_id'  => 'Route',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['name'] = ucwords(strtolower($request->name));
        $data['password'] = Hash::make($request->password);

        $customer = Customer::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Customer added successfully',
            'data' => $customer
        ], 201);
    }

    public function show($id)
    {
        $customer = Customer::with('branch', 'route')
                           ->byEmployeeRoute()
                           ->find($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $customer
        ]);
    }

    public function update(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

        $customer = Customer::byEmployeeRoute()->find($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
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
        ], [
            'name.regex'  => 'The :attribute may only contain letters and spaces.',
            'phone.regex' => 'The :attribute must be a 10-digit number.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('password');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $customer->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Customer updated successfully',
            'data' => $customer
        ]);
    }

    public function getMasters()
    {
        $branches = Branch::all();
        $routes = Route::all();

        return response()->json([
            'status' => true,
            'data' => [
                'branches' => $branches,
                'routes' => $routes
            ]
        ]);
    }

    public function destroy(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

        $customer = Customer::byEmployeeRoute()->find($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        // Check if customer has any loan assigned
        $hasLoan = \App\Models\LoanAssign::where('client_id', $id)->exists();
        
        // Check if customer has any EMI collection marked as 'Foreclosed'
        $hasForeclosed = \App\Models\Emicollection::where('client_id', $id)
                            ->where('status', 'Foreclosed')
                            ->exists();

        if ($hasLoan || $hasForeclosed) {
            return response()->json([
                'status' => false,
                'message' => 'Customer cannot be deleted because a loan is assigned.'
            ], 422); // 422 Unprocessable Entity
        }

        $customer->delete();

        return response()->json([
            'status' => true,
            'message' => 'Customer deleted successfully'
        ]);
    }
}
