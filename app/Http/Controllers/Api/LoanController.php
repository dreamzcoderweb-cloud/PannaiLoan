<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::latest();

        if ($request->has('id')) {
            $data = $query->find($request->id);
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Loan not found'
                ], 404);
            }
        } else {
             $data = $query->get();
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/'
            ],
        ], [
            'loan_name.regex' => 'The :attribute may only contain letters and spaces.',
        ], [
            'loan_name' => 'Name of Loan',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $loan = Loan::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Loan added successfully',
            'data' => $loan
        ], 201);
    }

    public function show($id)
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json([
                'status' => false,
                'message' => 'Loan not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $loan
        ]);
    }

    public function update(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json([
                'status' => false,
                'message' => 'Loan not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'loan_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/'
            ],
        ], [
            'loan_name.regex' => 'The :attribute may only contain letters and spaces.',
        ], [
            'loan_name' => 'Name of Loan',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $loan->update([
            'loan_name' => $request->loan_name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Loan updated successfully',
            'data' => $loan
        ]);
    }
}
