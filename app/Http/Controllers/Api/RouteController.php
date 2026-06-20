<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Route;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    public function index(Request $request)
    {

         $query = Route::with('branch')
                ->byEmployeeRoute() //
                ->latest();

        if ($request->has('id')) {
            $data = $query->find($request->id);
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Route not found'
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
            'route_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/',
                'unique:routes,route_name'
            ],
            'branch_id' => 'required',
        ], [
            'route_name.regex'  => 'The :attribute may only contain letters and spaces.',
            'route_name.unique' => 'The :attribute already exists.',
        ], [
            'route_name' => 'Route Name',
            'branch_id'  => 'Branch',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $route = Route::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Route Added successfully',
            'data' => $route
        ], 201);
    }

    public function show($id)
    {
        $route = Route::with('branch')->find($id);

        if (!$route) {
            return response()->json([
                'status' => false,
                'message' => 'Route not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $route
        ]);
    }

    public function update(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

        $route = Route::find($id);

        if (!$route) {
            return response()->json([
                'status' => false,
                'message' => 'Route not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
        'route_name' => [
            'required',
            'string',
            'min:3',
            'max:100',
            'regex:/^[A-Za-z\s]+$/',
            'unique:routes,route_name,' . $id
        ],
        'branch_id' => 'required',
        ], [
            'route_name.regex'  => 'The :attribute may only contain letters and spaces.',
            'route_name.unique' => 'The :attribute already exists.',
        ], [
            'route_name' => 'Route Name',
            'branch_id'  => 'Branch',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $route->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Route Updated successfully',
            'data' => $route
        ]);
    }

    public function destroy(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

        $route = Route::find($id);

        if (!$route) {
            return response()->json([
                'status' => false,
                'message' => 'Route not found'
            ], 404);
        }

        $route->delete();

        return response()->json([
            'status' => true,
            'message' => 'Route Deleted successfully'
        ]);
    }

}
