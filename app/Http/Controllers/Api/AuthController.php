<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;


class AuthController extends Controller
{
    public function login(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = MobileEmployee::with('employeeIDproof_file')->where('phone', $request->phone)->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $employee->createToken('mobile_employee_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'employee' => $employee,
                'token' => $token
            ]
        ], 200);
    }

    public function profile(Request $request)
    {
        $customerCount = Customer::byEmployeeRoute()->count();
        return response()->json([
            'status' => true,
            'message' => 'Profile retrieved successfully',
            'data' => $request->user()->load('employeeIDproof_file','branch','route'),
            'customer_count' => $customerCount
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful',
        ]);
    }
}
