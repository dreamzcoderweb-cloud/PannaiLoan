<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LoansExport;
use Maatwebsite\Excel\Facades\Excel;

class LoanController extends Controller
{
    public function create()
    {
        return view('Admin.Loan.create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'loan_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^[A-Za-z\s]+$/'
                ],
            ],
            [
                'loan_name.regex' => 'The :attribute may only contain letters and spaces.',
            ],
            [
                'loan_name' => 'Name of Loan',
            ]
        );
        Loan::create($request->all());

        return redirect()
            ->route('admin.loan-list')
            ->with('success', 'Loan added successfully');
    }

    public function index()
    {
        $data = Loan::latest()->get();
        return view('Admin.Loan.index', compact('data'));
    }

    public function edit($id)
    {
        $item = Loan::findOrFail($id);
        return view('Admin.Loan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'loan_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^[A-Za-z\s]+$/'
                ],
            ],
            [
                'loan_name.regex' => 'The :attribute may only contain letters and spaces.',
            ],
            [
                'loan_name' => 'Name of Loan',
            ]
        );

        $item = Loan::findOrFail($id);

        $item->update([
            'loan_name'        => $request->loan_name,

        ]);

        return redirect()
            ->route('admin.loan-list')
            ->with('success', 'Loan updated successfully');
    }

    public function view($id)
    {
        $item = Loan::findOrFail($id);
        return view('Admin.Loan.view', compact('item'));
    }
    public function exportPdf()
    {
        $data = Loan::latest()->get();
        $pdf = Pdf::loadView('Admin.Loan.pdf', compact('data'));
        // Download PDF with filename
        return $pdf->download('loan-list-' . date('Y-m-d') . '.pdf');
    }
    public function exportExcel(Request $request) // Add Request parameter
    {
        $searchTerm = $request->search ?? ''; // Use null coalescing operator

        return Excel::download(new LoansExport($searchTerm), 'loan-' . date('Y-m-d-H-i-s') . '.xlsx');
    }
}
