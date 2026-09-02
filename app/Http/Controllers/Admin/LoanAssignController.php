<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\LoanAssign;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Route;
use App\Exports\LoanAssignExport;
use App\Models\Emicollection;
use App\Models\EmicollectionDetail;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
class LoanAssignController extends Controller
{
    public function create()
    {
        $interests = Interest::all();
        $loan = Loan::all();
        $branches = Branch::all();
        $routes = Route::all();
        $customers = Customer::all();
        $documents = Document::all();
        return view('Admin.LoanAssign.create', compact('interests', 'loan', 'branches', 'routes', 'customers', 'documents'));
    }

    public function loanSearchCustomers(Request $request)
    {
        $search = $request->search;
    
    // Try case-insensitive search
    $customers = Customer::where(function($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('phone', 'LIKE', '%' . $search . '%');
        })
        ->orWhere(function($query) use ($search) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(phone) LIKE ?', ['%' . strtolower($search) . '%']);
        })
        ->limit(10)
        ->get(['id', 'name', 'phone']);
    
    return response()->json([
        'customers' => $customers,
        'search_term' => $search,
        'count' => $customers->count()
    ]);
    }
    // Store data
    public function store(Request $request)
    {
        // First, validate common fields
        $request->validate(
            [
                'client_id' => 'required',
                'client_type_id' => 'required',
                'address' => 'required',
                'phone' => 'required|digits:10',
                'city' => 'required',
                'pincode' => 'required|digits:6',
                'loan_type_id' => 'required',
                'document_type_id'   => 'required|array',
                'document_type_id.*' => 'exists:documents,id',
                'interest_id' => 'required',
                'collection_type_id' => 'required',
                'loanassign_date' => 'required|date',
                'branch_id' => 'required',
                'route_id' => 'required',
                'loan_amount' => 'required|numeric',
                'loan_tenure' => 'required|numeric',
            ],
            [],
            [
                'client_type_id' => 'Client Type',
                'loan_type_id' => 'Loan Type',
                'document_type_id' => 'Document Type',
                'interest_id' => 'Interest',
                'collection_type_id' => 'Loan Collection Type',
                'branch_id' => 'Branch',
                'route_id' => 'Routes',
                'loan_tenure' => 'Loan Tenure',
            ]
        );

        // Collection type specific validations
        $collectionSpecificRules = [];
        $collectionSpecificAttributes = [];

        if ($request->collection_type_id == 1) {
            // Daily validation
            $collectionSpecificRules = [
                'daily_duedays_id' => 'required',
                'daily_emi' => 'required|numeric',
                'total_interest' => 'required|numeric',
                'total_payableamt' => 'required|numeric',
            ];
            $collectionSpecificAttributes = [
                'daily_duedays_id' => 'Daily Due Days',
                'daily_emi' => 'Daily EMI',
            ];
        } elseif ($request->collection_type_id == 2) {
            // Weekly validation
            $collectionSpecificRules = [
                'week_duedays_id' => 'required',
                'weekly_emi' => 'required|numeric',
                'total_distribution' => 'required|numeric',
            ];
            $collectionSpecificAttributes = [
                'week_duedays_id' => 'Weekly Due Days',
                'weekly_emi' => 'Weekly EMI',
                'total_distribution' => 'Total Distribution',
            ];
        } elseif ($request->collection_type_id == 3) {
            // Monthly validation
            $collectionSpecificRules = [
                'monthlydue_date' => 'required|date',
                'monthly_emi' => 'required|numeric',
                'total_interest' => 'required|numeric',
                'total_payableamt' => 'required|numeric',
            ];
            $collectionSpecificAttributes = [
                'monthlydue_date' => 'Monthly Due Date',
                'monthly_emi' => 'Monthly EMI',
            ];
        }

        // Merge all validations
        $request->validate($collectionSpecificRules, [], $collectionSpecificAttributes);

        try {
            // Create the loan assignment
            LoanAssign::create([
                'loanAssign_id' => $request->loan_assign_id,
                'client_id' => $request->client_id,
                'client_type' => $request->client_type_id,
                'address' => $request->address,
                'phone' => $request->phone,
                'city' => $request->city,
                'pincode' => $request->pincode,
                'loan_type_id' => $request->loan_type_id,
                'document_type_id' => $request->document_type_id, // This will be automatically cast to JSON
                'interest_id' => $request->interest_id,
                'collection_type_id' => $request->collection_type_id,
                'loanassign_date' => $request->loanassign_date,
                'branch_id' => $request->branch_id,
                'route_id' => $request->route_id,
                'loan_amount' => $request->loan_amount,
                'loan_tenure' => $request->loan_tenure,
                'daily_duedays_id' => $request->daily_duedays_id,
                'week_duedays_id' => $request->week_duedays_id,
                'weeklyemi_date' =>$request->weeklyemi_date,
                'monthlydue_date' => $request->monthlydue_date,
                'daily_emi' => $request->daily_emi,
                'weekly_emi' => $request->weekly_emi,
                'monthly_emi' => $request->monthly_emi,
                'total_distribution' => $request->total_distribution,
                'total_interest' => $request->total_interest,
                'total_payableamt' => $request->total_payableamt,
            ]);

            return redirect()
                ->route('admin.loan-assign-list')
                ->with('success', 'Loan assigned successfully');

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Loan Assignment Error: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Failed to assign loan. Please try again.');
        }
    }

    public function index(Request $request)
    {
        $branches = Branch::all();
        $branch_id = $request->branch_id;

        $query = LoanAssign::with('int', 'loan', 'branches', 'routes', 'client_name', 'latestEmiCollection.latestDetail', 'emiCollections');

        if ($branch_id) {
            $query->where('branch_id', $branch_id);
        }

        $data = $query->get();

        return view('Admin.LoanAssign.index', compact('data', 'branches', 'branch_id'));
    }

    // Edit Form
    public function edit($id)
    {
        $original  = LoanAssign::findOrFail($id);
        $interests = Interest::all();
        $loan = Loan::all();
        $branches = Branch::all();
        $routes = Route::all();
        $customers = Customer::all();
        $documents = Document::orderBy('document_name')->get();
        $originalDocumentIds = [];
        $customersname = Customer::where('id', $original->client_id)->value('name');
        if (!empty($original->document_type_id)) {
            // If cast is NOT used
            if (is_string($original->document_type_id)) {
                $originalDocumentIds = json_decode($original->document_type_id, true) ?? [];
            }
            // If cast IS used
            elseif (is_array($original->document_type_id)) {
                $originalDocumentIds = $original->document_type_id;
            }
        }

        return view('Admin.LoanAssign.edit', compact('original', 'interests', 'loan', 'branches', 'routes', 'customers', 'documents', 'originalDocumentIds', 'customersname'));
    }

    public function exportPdf(Request $request)
    {
        $query = LoanAssign::with('int', 'loan', 'branches', 'routes', 'client_name', 'latestEmiCollection.latestDetail');

        // Apply search filter if exists
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('phone', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('client_name', function ($q2) use ($searchTerm) {
                        $q2->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('loan', function ($q3) use ($searchTerm) {
                        $q3->where('loan_name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('branches', function ($q4) use ($searchTerm) {
                        $q4->where('branch_name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('routes', function ($q5) use ($searchTerm) {
                        $q5->where('route_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        $data = $query->get();

        // Generate PDF
        $pdf = Pdf::loadView('Admin.LoanAssign.pdf', compact('data'));

        // Download PDF with filename
        return $pdf->download('loan-assign-list-' . date('Y-m-d') . '.pdf');
    }
    
   public function update(Request $request, $id)
   {
    $request->validate([
        'address'    => 'required',
        'phone'      => 'required',
        'city'       => 'required',
        'pincode'    => 'required',
        'branch_id'  => 'required',
        'route_id'   => 'required'
    ], [
        'address.required' => 'Address is required',
        'phone.required'   => 'Phone Number is required',
        'city.required'    => 'City is required',
        'pincode.required' => 'Pincode is required',
        'branch_id.required'=> 'Branch is required',
        'route_id.required' => 'Route is required'
    ]);

    $item = LoanAssign::findOrFail($id);

    $item->update([
        'client_name' => $request->client_name,
        'address'     => $request->address,
        'phone'       => $request->phone,
        'city'        => $request->city,
        'pincode'     => $request->pincode,
        'branch_id'   => $request->branch_id,
        'route_id'    => $request->route_id, // fixed
    ]);

    return redirect()->route('admin.loan-assign-list')
                     ->with('success', 'Loan Assign updated successfully');
}
    public function exportExcel(Request $request)
    {
        $searchTerm = $request->search;

        return Excel::download(new LoanAssignExport($searchTerm), 'loan-assignments.xlsx');
    }
    // Delete
    public function delete($id)
    {
        // Get all statuses of related emi collections
        $statuses = Emicollection::where('loan_assign_id', $id)->pluck('status');

        if ($statuses->isEmpty()) {
            // No EMI records, safe to delete
            LoanAssign::findOrFail($id)->delete();
            return redirect()->route('admin.loan-assign-list')->with('success', 'Loan Assign deleted successfully');
        }

        // Check if ALL statuses are 'Foreclosed' (case-sensitive check)
        $allForeclosed = $statuses->every(fn($status) => strtolower($status) === 'Foreclosed');

        if ($allForeclosed) {
            LoanAssign::findOrFail($id)->delete();
            return redirect()->route('admin.loan-assign-list')->with('success', 'Loan Assign deleted successfully');
        }

        // Otherwise, do not delete
        return redirect()->route('admin.loan-assign-list')->with('error', 'Cannot delete loan assign with active collections. Please foreclose the loan first.');
    }

    public function clientdetails($id)
    {
       $getpaidemicollection = Emicollection::with('details','loanassign')->where('loan_assign_id', $id) ->get();
       
       return view('Admin.Emicollection.client', compact('getpaidemicollection'));
    }




    public function foreclose(Request $request)
    {
        $request->validate([
            'emicollection_id' => 'required|exists:emicollections,id',
            'emiamount'        => 'required|numeric|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $collection = Emicollection::with(['loanassign', 'details'])
                    ->lockForUpdate()
                    ->findOrFail($request->emicollection_id);

               
                if ($collection->status === 'Foreclosed') {
                    throw new \Exception('This loan is already foreclosed.');
                }

                $remainingBalance = (float) $collection->total_remaining;
                $paidAmount       = (float) $request->emiamount;

                if ($remainingBalance <= 0) {
                    throw new \Exception('No remaining balance to foreclose.');
                }

                if ($paidAmount > $remainingBalance) {
                    throw new \Exception('Paid amount cannot exceed remaining balance.');
                }

                //  Discount calculation
                $discountAmount = $remainingBalance - $paidAmount;

                //  Get loan_type_id safely
                $lastDetail = EmicollectionDetail::where('emi_collection_id', $collection->id)
                    ->whereNotNull('loan_type_id')
                    ->orderBy('installment_no', 'desc')
                    ->first();

                $loanTypeId = $lastDetail->loan_type_id 
                    ?? $collection->loanassign->loan_type_id 
                    ?? null;

                $lastInstallmentNo = EmicollectionDetail::where('emi_collection_id', $collection->id)
                    ->max('installment_no') ?? 0;

                $today = now()->format('Y-m-d');
                $now   = now();

                //  SINGLE foreclosure entry
                EmicollectionDetail::create([
                    'emi_collection_id'        => $collection->id,
                    'loan_type_id'             => $loanTypeId,
                    'daily_duedays_id'         => $lastDetail->daily_duedays_id ?? null,
                    'week_duedays_id'          => $lastDetail->week_duedays_id ?? null,
                    'installment_no'           => $lastInstallmentNo + 1,
                    'emi_amount'               => $remainingBalance,
                    'paid_amount'              => $paidAmount,
                    'discount'                 => $discountAmount,
                    'remaining_payable_amount' => 0,
                    'due_date'                 => $today,
                    'paid_date'                => $today,
                    'status'                   => 'Paid',
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ]);

                //  Update master table
                $collection->update([
                    'total_collected' => $collection->total_collected + $paidAmount,
                    'total_remaining' => 0,
                    'discount'        => $discountAmount,
                    'status'          => 'Foreclosed',
                    'foreclosed_at'   => $now,
                ]);

                // OPTIONAL – loan assign status
                // if ($collection->loanassign) {
                //     $collection->loanassign()->update([
                //         'status' => 'Completed',
                //     ]);
                // }
            });

            return redirect()->route('admin.loan-assign-list')->with('success', 'Loan foreclosed successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Foreclosure failed: ' . $e->getMessage());
        }
    }

}
