<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::latest();

        if ($request->has('id')) {
            $data = $query->find($request->id);
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Document not found'
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
            'document_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/'
            ],
        ], [
            'document_name.regex' => 'The :attribute may only contain letters and spaces.',
        ], [
            'document_name' => 'Document Name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $document = Document::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Document Added successfully',
            'data' => $document
        ], 201);
    }

    public function show($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json([
                'status' => false,
                'message' => 'Document not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $document
        ]);
    }

    public function update(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

        $document = Document::find($id);

        if (!$document) {
            return response()->json([
                'status' => false,
                'message' => 'Document not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'document_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z\s]+$/'
            ],
        ], [
            'document_name.regex' => 'The :attribute may only contain letters and spaces.',
        ], [
            'document_name' => 'Document Name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $document->update([
            'document_name' => $request->document_name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Document updated successfully',
            'data' => $document
        ]);
    }
}
