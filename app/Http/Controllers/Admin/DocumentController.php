<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function create()
    {
        return view('Admin.Document.create');
    }
    public function store(Request $request)
    {
        $request->validate(
            [
                'document_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^[A-Za-z\s]+$/'
                ],
            ],
            [
                'document_name.regex' => 'The :attribute may only contain letters and spaces.',
            ],
            [
                'document_name' => 'Document Name',
            ]
        );

        Document::create($request->all());

        return redirect()->route('admin.document-list')->with('success', 'Document Added successfully');
    }

    public function index()
    {
        $data = Document::latest()->get();
        return view('Admin.Document.index', compact('data'));
    }
    public function edit($id)
    {
        $data = Document::findOrFail($id);
        return view('Admin.Document.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       $request->validate(
            [
                'document_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^[A-Za-z\s]+$/'
                ],
            ],
            [
                'document_name.regex' => 'The :attribute may only contain letters and spaces.',
            ],
            [
                'document_name' => 'Document Name',
            ]
        );

        $item = Document::findOrFail($id);

        $item->update([
            'document_name'        => $request->document_name,
        ]);

        return redirect()->route('admin.document-list')->with('success', 'Document updated successfully');
    }
}
