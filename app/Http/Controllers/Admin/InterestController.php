<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class InterestController extends Controller
{
    public function create()
    {
        return view('Admin.interest.create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'interest_id' => [
                    'required',
                    'regex:/^\d+(\.\d+)?%?$/',
                    'unique:interests,interest_id',
                ],
                'collection_type' => 'required|in:1,2,3',
            ],
            [
                'interest_id.regex' => 'The :attribute must contain only numbers, decimal point, and percentage symbol.',
                'interest_id.unique' => 'This :attribute already exists.',
                'collection_type.required' => 'Please select a collection type.',
            ],
            [
                'interest_id' => 'Interest',
                'collection_type' => 'Collection Type',
            ]
        );

        //  Extract numeric value (remove %)
        $interestValue = (float) str_replace('%', '', $request->interest_id);

        //  Manual min & max check
        if ($interestValue < 0 || $interestValue > 100) {
            return back()
                ->withErrors(['interest_id' => 'Interest must be between 0 and 100.'])
                ->withInput();
        }
        Interest::create($request->all());

        return redirect()
            ->route('admin.interest-list')
            ->with('success', 'Interest Added successfully');
    }
    public function index()
    {
        $data = Interest::latest()->get();
        return view('Admin.interest.index', compact('data'));
    }

    public function edit($id)
    {
        $item = Interest::findOrFail($id);
        return view('Admin.interest.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(
    [
        'interest_id' => [
            'required',
            'regex:/^\d+(\.\d+)?%?$/',
            Rule::unique('interests', 'interest_id')->ignore($id),
        ],
        'collection_type' => 'required|in:1,2,3',
    ],
    [
        'interest_id.regex' => 'The :attribute must contain only numbers, decimal point, and percentage symbol.',
        'interest_id.unique' => 'This :attribute already exists.',
        'collection_type.required' => 'Please select a collection type.',
    ],
    [
        'interest_id' => 'Interest',
        'collection_type' => 'Collection Type',
    ]
);
       

        $value = (float) str_replace('%', '', $request->interest_id);

        if ($value < 0 || $value > 100) {
            return back()
                ->withErrors(['interest_id' => 'Interest must be between 0 and 100.'])
                ->withInput();
        }

        $item = Interest::findOrFail($id);

        $item->update([
            'interest_id'        => $request->interest_id,
            'collection_type'    => $request->collection_type,
        ]);

        return redirect()
            ->route('admin.interest-list')
            ->with('success', 'Interest updated successfully');
    }

    public function view($id)
    {
        $item = Interest::findOrFail($id);
        return view('Admin.interest.view', compact('item'));
    }
}

