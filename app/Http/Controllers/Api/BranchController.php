<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::latest();

        if ($request->has('id')) {
            $data = $query->find($request->id);
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Branch not found'
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
            'branch_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'city_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'area_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/'
            ],
        ], [
            'branch_name.regex' => 'The :attribute may only contain letters and spaces.',
            'city_name.regex'   => 'The :attribute may only contain letters and spaces.',
            'area_name.regex'   => 'The :attribute may only contain letters and spaces.',
        ], [
            'branch_name' => 'Branch Name',
            'city_name'   => 'City Name',
            'area_name'   => 'Area Name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $branch = Branch::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Branch added successfully',
            'data' => $branch
        ], 201);
    }

    public function show($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $branch
        ]);
    }

    public function update(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

         $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'branch_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'city_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'area_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/'
            ],
        ], [
            'branch_name.regex' => 'The :attribute may only contain letters and spaces.',
            'city_name.regex'   => 'The :attribute may only contain letters and spaces.',
            'area_name.regex'   => 'The :attribute may only contain letters and spaces.',
        ], [
            'branch_name' => 'Branch Name',
            'city_name'   => 'City Name',
            'area_name'   => 'Area Name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $branch->update([
            'branch_name' => $request->branch_name,
            'city_name'   => $request->city_name,
            'area_name'   => $request->area_name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Branch Updated successfully',
            'data' => $branch
        ]);
    }

    public function destroy(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found'
            ], 404);
        }

        $branch->delete();

        return response()->json([
            'status' => true,
            'message' => 'Branch deleted successfully'
        ]);
    }
}
