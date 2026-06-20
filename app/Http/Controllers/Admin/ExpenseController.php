<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::orderBy('expense_date', 'desc')->get();
        return view('Admin.Expense.index', compact('expenses'));
    }

    public function create()
    {
        return redirect()->route('admin.expense-list');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'date' => 'required|date',
                'amount' => 'required|numeric',
            ],
            [
                'date.required' => 'Date is required',
                'amount.required' => 'Amount is required',
            ]
        );

        $data = [
            'expense_date' => $request->date,
            'expense_amount' => $request->amount,
        ];

        $expense = Expense::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense created successfully.',
                'data' => $expense
            ]);
        }

        return redirect()
            ->route('admin.expense-list')
            ->with('success', 'Expense created successfully.');
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate(
            [
                'date' => 'required|date',
                'amount' => 'required|numeric',
            ],
            [
                'date.required' => 'Date is required',
                'amount.required' => 'Amount is required',
            ]
        );

        $expense->update([
            'expense_date' => $request->date,
            'expense_amount' => $request->amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully.',
            'data' => $expense
        ]);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully.'
        ]);
    }
}
