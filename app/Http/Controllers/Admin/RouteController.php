<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Route;
use Illuminate\Validation\Rule;
class RouteController extends Controller
{
    public function create()
    {
        $branches = Branch::all();
        return view('Admin.Route.create', compact('branches'));
    }
    public function store(Request $request)
    {
        $request->validate(
            [
                'route_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^[A-Za-z\s]+$/',
                   Rule::unique('routes')
                        ->where(fn ($query) => 
                            $query->where('branch_id', $request->branch_id)
                        ),

                ],
                'branch_id' => 'required',
            ],
            [
                'route_name.regex' => 'The :attribute may only contain letters and spaces.',
                'route_name.unique' => 'The :attribute already exists.',
            ],
            [
                'route_name' => 'Route Name',
                'branch_id'  => 'Branch',
            ]
        );

        Route::create($request->all());

        return redirect()
            ->route('admin.route-list')
            ->with('success', 'Route Added successfully');
    }

    public function index()
    {
        $data = Route::with('branch')->latest()->get();
        return view('Admin.Route.index', compact('data'));
    }
    public function edit($id)
    {
        $branches = Branch::all();
        $original = Route::findOrFail($id);
        return view('Admin.Route.edit', compact('original', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'route_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^[A-Za-z\s]+$/',
                   Rule::unique('routes')
                       ->where(fn ($query) => 
                     $query->where('branch_id', $request->branch_id))
    ->ignore($id),
                ],
                'branch_id' => 'required',
            ],
            [
                'route_name.regex' => 'The :attribute may only contain letters and spaces.',
                'route_name.unique' => 'The :attribute already exists.',
            ],
            [
                'route_name' => 'Route Name',
                'branch_id'  => 'Branch',
            ]
        );
        $original = Route::findOrFail($id);
        $original->update($request->all());

        return redirect()
            ->route('admin.route-list')
            ->with('success', 'Route Updated successfully');
    }
}
