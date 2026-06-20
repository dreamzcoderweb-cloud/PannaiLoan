<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function create()
    {
        return view('Admin.Branch.create');
    }

    public function store(Request $request)
    {
        $request->validate(
    [
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
    ],
    [
        'branch_name.regex' => 'The :attribute may only contain letters and spaces.',
        'city_name.regex'   => 'The :attribute may only contain letters and spaces.',
        'area_name.regex'   => 'The :attribute may only contain letters and spaces.',
    ],
    [
        'branch_name' => 'Branch Name',
        'city_name'   => 'City Name',
        'area_name'   => 'Area Name',
    ]
);

        Branch::create($request->all());
        return redirect()
            ->route('admin.branch-list')
            ->with('success', 'Branch added successfully');
    }

    public function index()
    {
        $data = Branch::latest()->get();
        return view('Admin.Branch.index', compact('data'));
    }

    public function edit($id)
    {
        $item = Branch::findOrFail($id);
        return view('Admin.Branch.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(
    [
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
    ],
    [
        'branch_name.regex' => 'The :attribute may only contain letters and spaces.',
        'city_name.regex'   => 'The :attribute may only contain letters and spaces.',
        'area_name.regex'   => 'The :attribute may only contain letters and spaces.',
    ],
    [
        'branch_name' => 'Branch Name',
        'city_name'   => 'City Name',
        'area_name'   => 'Area Name',
    ]
);

        $item = Branch::findOrFail($id);

        $item->update([
            'branch_name'        => $request->branch_name,
            'city_name'            => $request->city_name,
            'area_name'              => $request->area_name,
        ]);

        return redirect()
            ->route('admin.branch-list')
            ->with('success', 'Branch Updated successfully');
    }

     public function view($id)
    {
        $item = Branch::findOrFail($id);
        return view('Admin.Branch.view', compact('item'));
    }
}
