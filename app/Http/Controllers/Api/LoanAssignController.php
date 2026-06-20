<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\LoanAssign;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Route;
use App\Models\Emicollection;
use App\Models\EmicollectionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoanAssignController extends Controller
{
    public function index(Request $request)

    {
        $query = LoanAssign::with(
            'int',
            'loan',
            'branches',
            'routes',
            'client_name',
            'latestEmiCollection.latestDetail',
            'emiCollections'
        )->whereHas('client_name', function ($q) {
            $q->byEmployeeRoute();
        });

        if ($request->has('id')) {
            $data = $query->find($request->id);
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Loan Assign not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('loanAssign_id', 'like', "%{$search}%")
                  ->orWhereHas('client_name', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $page = max(1, (int) $request->input('page', 1));
        $limit = min(100, max(1, (int) $request->input('limit', 10)));
        $skip = ($page - 1) * $limit;

        $total = (clone $query)->count();

        $data = $query
            ->skip($skip)
            ->limit($limit)
            ->get();

        $lastPage = $total > 0 ? (int) ceil($total / $limit) : 0;

        return response()->json([
            'status' => true,
            'count' => $data->count(),
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $total === 0 ? 0 : $skip + 1,
                'to' => min($skip + $limit, $total),
                'has_more' => $page < $lastPage,
            ],
        ]);
    }

    public function store(Request $request)
    {
        // First, validate common fields
        $validator = Validator::make($request->all(), [
                'loan_assign_id' => 'required',
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
            ], [], [
                'client_type_id' => 'Client Type',
                'loan_type_id' => 'Loan Type',
                'document_type_id' => 'Document Type',
                'interest_id' => 'Interest',
                'collection_type_id' => 'Loan Collection Type',
                'branch_id' => 'Branch',
                'route_id' => 'Routes',
                'loan_tenure' => 'Loan Tenure',
            ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

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
        $validator2 = Validator::make($request->all(), $collectionSpecificRules, [], $collectionSpecificAttributes);

        if ($validator2->fails()) {
            return response()->json(['errors' => $validator2->errors()], 422);
        }

        try {
            // Create the loan assignment
            $loanAssign = LoanAssign::create([
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

            return response()->json([
                'status' => true,
                'message' => 'Loan assigned successfully',
                'data' => $loanAssign
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Loan Assignment Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to assign loan. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $loanAssign = LoanAssign::with('int', 'loan', 'branches', 'routes', 'client_name', 'latestEmiCollection.latestDetail')->find($id);

        if (!$loanAssign) {
            return response()->json([
                'status' => false,
                'message' => 'Loan Assign not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $loanAssign
        ]);
    }

    public function update(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            
        ], [
            'client_name.required' => 'Client Name is required',
            'address.required' => 'Address is required',
            'phone.required' => 'Phone Number is required',
            'city.required' => 'City is required',
            'pincode.required' => 'Pincode is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item = LoanAssign::find($id);

        if (!$item) {
             return response()->json([
                'status' => false,
                'message' => 'Loan Assign not found'
            ], 404);
        }

        $item->update([
            'client_name'        => $request->client_name,
            'address'            => $request->address,
            'phone'              => $request->phone,
            'city'               => $request->city,
            'pincode'            => $request->pincode,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Loan Assign updated successfully',
            'data' => $item
        ]);
    }

    public function destroy(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $request->id;
        }

        $item = LoanAssign::find($id);
        if (!$item) {
             return response()->json([
                'status' => false,
                'message' => 'Loan Assign not found'
            ], 404);
        }
       
        // Get all statuses of related emi collections
        $statuses = Emicollection::where('loan_assign_id', $item->id)->pluck('status');
        if ($statuses->isEmpty()) {
            // No EMI records, safe to delete
            LoanAssign::findOrFail($id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Loan Assign deleted successfully'
            ]);
        }else{
            // Check if any status is not 'Foreclosed'
            $hasNonForeclosed = $statuses->contains(fn($status) => strtolower($status) !== 'foreclosed');

            if ($hasNonForeclosed) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete Loan Assign. There are associated EMI collections that are not foreclosed.'
                ], 400);
            }
        }
    
    }

    public function clientdetails(Request $request, $id = null)
    {
       $loanAssignId = $id ?? $request->input('id');

       if (empty($loanAssignId)) {
           return response()->json([
               'status' => false,
               'message' => 'Loan Assign ID is required'
           ], 400);
       }

       $getpaidemicollection = Emicollection::with('details', 'loanassign')
           ->where('loan_assign_id', $loanAssignId)
           ->get();

        if ($getpaidemicollection->isEmpty()) {
                return response()->json([
                'status' => false,
                'message' => 'No EMI collections found for the given Loan Assign ID'
            ], 404);
        }else{
                return response()->json([
                    'status' => true,
                    'data' => $getpaidemicollection
            ]);
        }
    }

    public function foreclose(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emicollection_id' => 'required|exists:emicollections,id',
            'emiamount'        => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

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
            });

            return response()->json([
                'status' => true,
                'message' => 'Loan foreclosed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Foreclosure failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMasters()
    {
        $interests = Interest::all();
        $loans = Loan::all();
        $branches = Branch::all();
        $routes = Route::all();
        $customers = Customer::all();
        $documents = Document::orderBy('document_name')->get();

        return response()->json([
            'status' => true,
            'data' => [
                'interests' => $interests,
                'loans' => $loans,
                'branches' => $branches,
                'routes' => $routes,
                'customers' => $customers,
                'documents' => $documents
            ]
        ]);
    }
}
