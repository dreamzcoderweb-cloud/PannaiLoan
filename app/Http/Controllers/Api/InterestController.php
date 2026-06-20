<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class InterestController extends Controller
{
    public function index(Request $request)
    {
        $query = Interest::latest();
        
        if ($request->has('id')) {
            $data = $query->find($request->id);
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Interest not found'
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
            'interest_id' => [
                'required',
                'regex:/^\d+(\.\d+)?%?$/',
                'unique:interests,interest_id',
            ],
            'collection_type' => 'required|in:1,2,3',
        ], [
            'interest_id.regex' => 'The :attribute must contain only numbers, decimal point, and percentage symbol.',
            'interest_id.unique' => 'This :attribute already exists.',
            'collection_type.required' => 'Please select a collection type.',
        ], [
            'interest_id' => 'Interest',
            'collection_type' => 'Collection Type',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //  Extract numeric value (remove %)
        $interestValue = (float) str_replace('%', '', $request->interest_id);

        //  Manual min & max check
        if ($interestValue < 0 || $interestValue > 100) {
            return response()->json([
                'status' => false,
                'message' => 'Interest must be between 0 and 100.',
                'errors' => ['interest_id' => ['Interest must be between 0 and 100.']]
            ], 422);
        }

        $interest = Interest::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Interest Added successfully',
            'data' => $interest
        ], 201);
    }

    public function show($id)
    {
        $interest = Interest::find($id);

        if (!$interest) {
            return response()->json([
                'status' => false,
                'message' => 'Interest not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $interest
        ]);
    }

    public function update(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

        $interest = Interest::find($id);

        if (!$interest) {
            return response()->json([
                'status' => false,
                'message' => 'Interest not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'interest_id' => [
                'required',
                'regex:/^\d+(\.\d+)?%?$/',
                Rule::unique('interests', 'interest_id')->ignore($id),
            ],
            'collection_type' => 'required|in:1,2,3',
        ], [
            'interest_id.regex' => 'The :attribute must contain only numbers, decimal point, and percentage symbol.',
            'interest_id.unique' => 'This :attribute already exists.',
            'collection_type.required' => 'Please select a collection type.',
        ], [
            'interest_id' => 'Interest',
            'collection_type' => 'Collection Type',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $value = (float) str_replace('%', '', $request->interest_id);

        if ($value < 0 || $value > 100) {
            return response()->json([
                'status' => false,
                'message' => 'Interest must be between 0 and 100.',
                'errors' => ['interest_id' => ['Interest must be between 0 and 100.']]
            ], 422);
        }

        $interest->update([
            'interest_id'        => $request->interest_id,
            'collection_type'    => $request->collection_type,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Interest updated successfully',
            'data' => $interest
        ]);
    }
}
