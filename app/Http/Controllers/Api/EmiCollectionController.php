<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\LoanAssign;
use App\Models\Emicollection;
use App\Models\EmicollectionDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\MobileEmployee;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class EmiCollectionController extends Controller
{
      public function loanAssignList(Request $request)
    {
        $data = LoanAssign::with('int', 'loan', 'branches', 'routes', 'client_name', 'latestEmiCollection.latestDetail', 'emiCollections')
                          ->whereHas('client_name', function ($query) {
                              $query->byEmployeeRoute();
                          })
                          ->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function emiSearchCustomers(Request $request)
    {
        try {
            // Validate request parameters
            $request->validate([
                'search'    => 'nullable|string|max:100',
                'branch_id' => 'nullable|integer|exists:branches,id',
                'route_id'  => 'nullable|integer|exists:routes,id',
                'page'      => 'nullable|integer|min:1',
                'limit'     => 'nullable|integer|min:1|max:100',
            ], [
                'branch_id.exists' => 'The selected branch does not exist.',
                'route_id.exists'  => 'The selected route does not exist.',
            ]);

            $search = $request->input('search');
            $branchId = $request->input('branch_id');
            $routeId = $request->input('route_id');

            // Get pagination parameters with defaults
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Validate pagination parameters
            $page = max(1, intval($page));
            $limit = min(100, max(1, intval($limit)));

            // Calculate skip value
            $skip = ($page - 1) * $limit;

            $query = Customer::byEmployeeRoute();

            // Apply search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Apply branch filter (additional validation)
            if (!empty($branchId)) {
                $query->where('branch_id', $branchId);
            }

            // Apply route filter (additional validation)
            if (!empty($routeId)) {
                $query->where('route_id', $routeId);
            }

            // Get total count before pagination
            $total = $query->count();

            // Apply pagination
            $customers = $query
                ->select('id', 'name', 'phone', 'branch_id', 'route_id')
                ->skip($skip)
                ->limit($limit)
                ->get();

            // Calculate pagination metadata
            $lastPage = $total > 0 ? ceil($total / $limit) : 0;

            // Return response with data count
            return response()->json([
                'success'   => true,
                'count'     => $customers->count(),
                'customers' => $customers,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'last_page' => $lastPage,
                    'from' => $total == 0 ? 0 : $skip + 1,
                    'to' => min($skip + $limit, $total),
                    'has_more' => $page < $lastPage
                ],
                'message'   => $customers->count() > 0
                    ? 'Customers found successfully.'
                    : 'No customers found matching your criteria.'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching customers.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function getLoansByClient(Request $request, $clientId = null)
    {
        // Support both path param and query param
        $id = $clientId ?? $request->input('clientId');

        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Client ID is required'
            ], 400);
        }

        $loans = LoanAssign::with('loan', 'int')
            ->where('client_id', $id)
            ->get();

        if ($loans->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No loans found for the specified client.',
                'loans' => []
            ]);
        }

        $loanData = $loans->map(function ($loan) {
            if ($loan->collection_type_id == 1) {
                $loanType = 'Daily';
                $amount = $loan->total_payableamt;
            } elseif ($loan->collection_type_id == 2) {
                $loanType = 'Weekly';
                $amount = $loan->loan_amount;
            } else {
                $loanType = 'Monthly';
                $amount = $loan->total_payableamt;
            }

            return [
                'loanAssign_id'        => $loan->loanAssign_id,
                'loan_id' => $loan->id,
                'loan_type' => $loanType,
                'amount' => $amount,
                'interest_rate' => $loan->int->interest_id,
                'date' => $loan->loanassign_date,
                'status' => 'Active',
                'collection_type_id' => $loan->collection_type_id // Added for mobile clarity
            ];
        });

        return response()->json([
            'success' => true,
            'loans' => $loanData
        ]);
    }

    public function getLoanEmiDetails(Request $request, $loanassignId = null)
    {
        // Support both path param and query param
        $id = $loanassignId ?? $request->input('loanassignId');

        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Loan Assign ID is required'
            ], 400);
        }

        $loanAssign = LoanAssign::find($id);

        if (!$loanAssign) {
            return response()->json([
                'success' => false,
                'message' => 'Loan not found',
                'customer_type' => 'none',
                'emi' => []
            ]);
        }

        $customerType = ($loanAssign->client_type == 1) ? 'old' : 'new';
        $collectionType = $loanAssign->collection_type_id;

        $emiamount = '';
        $duedate = '';
        $dueDay = null;

        if ($collectionType == 1) {
            $emiamount = $loanAssign->daily_emi;
            $dueDay = $loanAssign->daily_duedays_id;
            $totalPayable = $loanAssign->total_payableamt;
        } elseif ($collectionType == 2) {
            $emiamount = $loanAssign->weekly_emi;
            $dueDay = $loanAssign->week_duedays_id;
            $duedate = $loanAssign->weeklyemi_date;
            $totalPayable = $loanAssign->loan_amount;
        } elseif ($collectionType == 3) {
            $emiamount = $loanAssign->monthly_emi;
            $duedate = $loanAssign->monthlydue_date;
            $totalPayable = $loanAssign->total_payableamt;
        }

        try {
            $emiCollections = Emicollection::with('details')
                ->where('loan_assign_id', $id)
                ->get();
        } catch (\Exception $e) {
            $emiCollections = collect();
        }

        if ($emiCollections->isEmpty()) {
            return response()->json([
                'success' => true,
                'customer_type' => $customerType,
                'emi' => [[
                    'due_date' => $duedate,
                    'client_id' => $loanAssign->client_id,
                    'emi_amount' => $emiamount,
                    'payable_amount' => $totalPayable,
                    'remaining' => $totalPayable,
                    'loan_type_id' => $loanAssign->loan_type_id,
                    'daily_duedays_id' => ($collectionType == 1) ? $dueDay : null,
                    'week_duedays_id' => ($collectionType == 2) ? $dueDay : null,
                    'status' => 'Pending',
                    'flag' => 2,
                    'collection_type_id' => $loanAssign->collection_type_id,
                    'due_day' => $dueDay,
                ]]
            ]);
        }

        $emiData = [];
        $paidAmount = 0;

        foreach ($emiCollections as $collection) {
            $details = DB::table('emicollection_details')
                ->where('emi_collection_id', $collection->id)
                ->get();

            foreach ($details as $detail) {
                $paidAmount += $detail->emi_amount;
                $remaining = max($totalPayable - $paidAmount, 0);

                $emiData[] = [
                    'due_date' => $detail->due_date,
                    'client_id' => $collection->client_id,
                    'emi_amount' => $detail->emi_amount,
                    'payable_amount' => $totalPayable,
                    'remaining' => $remaining,
                    'loan_type_id' => $detail->loan_type_id,
                    'daily_duedays_id' => $detail->daily_duedays_id,
                    'week_duedays_id' => $detail->week_duedays_id,
                    'status' => $detail->status,
                    'flag' => 1,
                    'collection_type_id' => $loanAssign->collection_type_id,
                    'due_day' => $dueDay,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'customer_type' => $customerType,
            'emi' => $emiData
        ]);
    }



    public function collectEmi(Request $request)
    {
        // Validation (with proper array notation)
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:customers,id',
            'loan_assign_id' => 'required|exists:loan_assigns,id',
            'loan_type_id' => 'required|array',
            'loan_type_id.*' => 'required|exists:loans,id',

            'emi' => 'required|array',

            'emi.due_date' => 'required|array',
            'emi.due_date.*' => 'nullable|date',

            // 'emi.due_day' => 'required|array',
            // 'emi.due_day.*' => 'nullable|in:1,2,3,4,5,6,7',

            'emi.emi_amount' => 'required|array',
            'emi.emi_amount.*' => 'required|numeric|min:1',

            'emi.remaining_amount' => 'required|array',
            'emi.remaining_amount.*' => 'required|numeric|min:0',

            'emi.status' => 'required|array',
            'emi.status.*' => 'required|in:Pending,Paid',
        ], [], [
            'client_id' => 'Customer',
            'loan_assign_id' => 'Loan Assignment',
            'loan_type_id.*' => 'Loan Type',
            'emi.due_date.*' => 'Due Date',
            'emi.due_day.*' => 'Due Day',
            'emi.emi_amount.*' => 'EMI Amount',
            'emi.remaining_amount.*' => 'Remaining Payable Amount',
            'emi.status.*' => 'Status',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Prefer explicit loan assignment from request; fallback for cases where it was not posted.
            if (!empty($request->loan_assign_id)) {
                $loanAssign = LoanAssign::where('id', $request->loan_assign_id)
                    ->where('client_id', $request->client_id)
                    ->first();
            } else {
                $loanTypeIds = collect($request->loan_type_id ?? [])
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $hasDueDate = collect($request->input('emi.due_date', []))
                    ->filter(fn($value) => !empty($value))
                    ->isNotEmpty();
                // $hasDueDay = collect($request->input('emi.due_day', []))
                //     ->filter(fn($value) => !empty($value))
                //     ->isNotEmpty();

                $collectionTypeId = null;
                if ($hasDueDate) {
                    $collectionTypeId = 2; // Weekly
                } elseif ($hasDueDate) {
                    $collectionTypeId = 3; // Monthly
                }

                $loanAssignQuery = LoanAssign::where('client_id', $request->client_id);

                if (!empty($loanTypeIds)) {
                    $loanAssignQuery->whereIn('loan_type_id', $loanTypeIds);

                }

                if (!empty($collectionTypeId)) {
                    $loanAssignQuery->where('collection_type_id', $collectionTypeId);

                }

                $loanAssign = $loanAssignQuery->latest('id')->first();

            }

            if (!$loanAssign) {
                return response()->json([
                    'status' => false,
                    'message' => 'Loan assign not found',
                ], 404);
            }

            if ($loanAssign->collection_type_id == 2) {
                $amount = $loanAssign->loan_amount;
            } else {
                $amount = $loanAssign->total_payableamt;
            }
            $user = request()->user();
            // Create master row only once; never reset totals on subsequent collections.
            $existingCollection = DB::table('emicollections')
                ->where('loan_assign_id', $loanAssign->id)
                ->where('client_id', $request->client_id)
                ->first();

            if (!$existingCollection) {
                $emiCollectionId = DB::table('emicollections')->insertGetId([
                    'loan_assign_id' => $loanAssign->id,
                    'client_id' => $request->client_id,
                    'collection_type_id' => $loanAssign->collection_type_id,
                    'total_payable_amount' => $amount,
                    'total_collected' => 0,
                    'total_remaining' => $amount,
                    'status' => 'Pending',
                    'Collect_by' => 'Employee', // Assuming collection is done by employee; adjust as needed
                    'emp_id' => $user->id, // Assuming authenticated user is the collector; adjust as needed
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Validate that main table insertion was successful
                if (!$emiCollectionId) {
                    throw new \Exception('Failed to create EMI collection record. Required fields may be missing or database error occurred.');
                }

                $existingCollection = DB::table('emicollections')->find($emiCollectionId);
            } else {
                $emiCollectionId = $existingCollection->id;
                // If old record created by Admin, update collector to Employee
                if ($existingCollection->Collect_by === 'Admin' && $existingCollection->emp_id == 0) {
                    DB::table('emicollections')
                        ->where('id', $emiCollectionId)
                        ->update([
                            'Collect_by' => 'Employee',
                            'emp_id' => $user->id,
                            'updated_at' => now(),
                        ]);

                    // Refresh the collection data
                    $existingCollection = DB::table('emicollections')->find($emiCollectionId);
                }
            }

            $totalCollected = (float)($existingCollection->total_collected ?? 0);
            $totalRemaining = (float)($existingCollection->total_remaining ?? $amount);

            foreach ($request->emi['emi_amount'] as $key => $emiAmount) {
                $installmentNo = $key + 1;
                $dueDate = null;
                $weeklyDueDayId = null;
                $dailyDueDayId = null;
                $loanTypeId = $request->loan_type_id[$key];

                // Handle different collection types
                if ($loanAssign->collection_type_id == 3) { // Monthly
                    $dueDate = $request->emi['due_date'][$key] ?? null;
                } elseif ($loanAssign->collection_type_id == 2) { // Weekly
                    $weeklyDueDayId = $request->emi['due_day'][$key] ?? $loanAssign->week_duedays_id;
                    $dueDate = $request->emi['due_date'][$key] ?? null;
                } elseif ($loanAssign->collection_type_id == 1) { // Daily
                    $dailyDueDayId = $request->emi['due_day'][$key] ?? null;
                }

                // Check if this installment already exists
                $existsQuery = DB::table('emicollection_details')
                    ->where('emi_collection_id', $emiCollectionId)
                    ->where('loan_type_id', $loanTypeId)
                    ->where('installment_no', $installmentNo);

                if (($loanAssign->collection_type_id == 3 || $loanAssign->collection_type_id == 2) && $dueDate) {
                    $existsQuery->where('due_date', $dueDate);
                }

                if ($existsQuery->exists()) {
                    continue; // Skip if already exists
                }

                // Calculate remaining amount properly
                $remainingAmount = $request->emi['remaining_amount'][$key];
                $emiAmount = $request->emi['emi_amount'][$key];
                $status = $request->emi['status'][$key];

                $paidAmount = 0;

                // If status is Paid, update totals
                if ($status === 'Paid') {
                    $paidAmount = $emiAmount;

                    $totalCollected += $emiAmount;
                    $totalRemaining = max(0, $totalRemaining - $emiAmount);

                    // This is the correct remaining for details table
                    $remainingAmount = $totalRemaining;
                } else {
                    $paidAmount = 0;
                    $remainingAmount = $totalRemaining;
                }

                // Create EMI Collection detail
                DB::table('emicollection_details')->insert([
                    'emi_collection_id' => $emiCollectionId,
                    'loan_type_id' => $loanTypeId,
                    'installment_no' => $installmentNo,
                    'due_date' => $dueDate,
                    'week_duedays_id' => $weeklyDueDayId,
                    'daily_duedays_id' => $dailyDueDayId,
                    'emi_amount' => $emiAmount,
                    'remaining_payable_amount' => $remainingAmount,
                    'status' => $status,
                    'paid_amount' => $paidAmount,
                    'paid_date' => $status === 'Paid' ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Calculate the new status
            $newStatus = 'Pending';

            // Get the updated total paid amount from all EMI details
            $totalPaidAmount = DB::table('emicollection_details')
                ->where('emi_collection_id', $emiCollectionId)
                ->sum('paid_amount');

            $totalPayableAmount = (float)($existingCollection->total_payable_amount ?? $amount);

            // Check if loan is completed (all amounts paid)
            if ($totalPaidAmount >= $totalPayableAmount) {
                $newStatus = 'Completed';
            } else if ($totalPaidAmount > 0) {
                $newStatus = 'Partially Paid';
            }

            // Update master record with new totals and status
            DB::table('emicollections')
                ->where('id', $emiCollectionId)
                ->update([
                    'total_collected' => $totalCollected,
                    'total_remaining' => $totalRemaining,
                    'status' => $newStatus,
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'EMI Collection saved successfully',
                'data' => [
                    'emi_collection_id' => $emiCollectionId,
                    'total_payable_amount' => $totalPayableAmount,
                    'total_collected' => $totalCollected,
                    'total_remaining' => $totalRemaining,
                    'status' => $newStatus,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function emiCollectionHistory(Request $request, $loanassignId = null)
    {
        $id = $loanassignId ?? $request->input('loanassignId');

        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Loan Assign ID is required'
            ], 400);
        }

        $historyData = $this->getEmiCollectionHistoryData($id);

        if (!$historyData['success']) {
            return response()->json([
                'success' => false,
                'message' => $historyData['message']
            ], $historyData['status_code']);
        }

        if ($request->boolean('pdf') || $request->input('format') === 'pdf') {
            // Determine displayed value based on EMI collection status
            $displayedValue = $this->getDisplayedValueForPdf($historyData['emi_collections']);

            $pdfData = [
                'generated_at' => now()->format('d-M-Y H:i:s'),
                'customer' => $historyData['customer'],
                'collection_type' => $historyData['collection_type'],
                'collection_type_label' => $this->getCollectionTypeLabel($historyData['collection_type']),
                'history' => $historyData['history'],
                'total_records' => $historyData['total_records'],
                'total_installments' => $historyData['total_installments'],
                'total_amount' => $historyData['total_amount'],
                'total_paid_amount' => $historyData['total_paid_amount'],
                'total_remaining_amount' => $historyData['total_remaining_amount'],
                'displayed_value' => $displayedValue['value'],
                'display_label' => $displayedValue['label'],
                'emi_collections' => $historyData['emi_collections'],
            ];

            $pdf = Pdf::loadView('Api.emi-collection-history-pdf', $pdfData);
            $fileName = 'emi_paid_history_' . $id . '_' . now()->format('Ymd_His') . '.pdf';

            if ($request->boolean('download', true)) {
                return $pdf->download($fileName);
            }

            return $pdf->stream($fileName);
        }

        return response()->json([
            'success' => true,
            'customer' => $historyData['customer'],
            'collection_type' => $historyData['collection_type'],
            'history' => $historyData['history'],
            'total_records' => $historyData['total_records'],
            'total_installments' => $historyData['total_installments'],
            'total_amount' => $historyData['total_amount'],
            'total_paid_amount' => $historyData['total_paid_amount'],
            'total_remaining_amount' => $historyData['total_remaining_amount'],
            'message' => $historyData['history']->isEmpty()
                ? 'Customer details retrieved. No EMI payments yet.'
                : 'Customer EMI collection history retrieved successfully.'
        ]);
    }

    public function emiCollectionHistoryPdf(Request $request, $emiColId = null)
    {
        $request->merge(['pdf' => true, 'download' => $request->input('download', true)]);
        return $this->emiCollectionHistory($request, $emiColId);
    }

    private function getEmiCollectionHistoryData($loanAssignId): array
    {
        $user = request()->user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Unauthenticated employee.',
                'status_code' => 401,
            ];
        }

        $getcustomer = LoanAssign::with('client_name','loan')->find($loanAssignId);

        if (!$getcustomer) {
            return [
                'success' => false,
                'message' => 'Customer not found',
                'status_code' => 404,
            ];
        }

        // $getemicollection = Emicollection::where('loan_assign_id', $getcustomer->id)
        // ->where('emp_id', $user->id)
        // ->where('Collect_by', 'Employee')
        // ->get();
        $getemicollection = Emicollection::where('loan_assign_id', $getcustomer->id)
           ->get();

        $collectionIds = $getemicollection->pluck('id');

        $getemihistory = EmiCollectionDetail::whereIn('emi_collection_id', $collectionIds)
            ->orderBy('id', 'desc')
            ->get();

        $totalPaidAmount = (float) $getemihistory->sum('paid_amount');
        $totalAmount = $this->calculateTotalAmount($getcustomer);
        $totalRemainingAmount = max($totalAmount - $totalPaidAmount, 0);

        return [
            'success' => true,
            'status_code' => 200,
            'customer' => $getcustomer,
            'collection_type' => $getcustomer->collection_type_id,
            'history' => $getemihistory,
            'total_records' => $getemihistory->count(),
            'total_installments' => $getcustomer->loan_tenure,
            'total_amount' => $totalAmount,
            'total_paid_amount' => $totalPaidAmount,
            'total_remaining_amount' => $totalRemainingAmount,
            'emi_collections' => $getemicollection,
        ];
    }

    private function calculateTotalAmount(LoanAssign $loanAssign): float
    {
        switch ($loanAssign->collection_type_id) {
            case 1:
            case 2:
                return (float) $loanAssign->loan_amount;
            case 3:
                return (float) ($loanAssign->total_payableamt ?? ($loanAssign->loan_amount + $loanAssign->total_interest));
            default:
                return (float) $loanAssign->loan_amount;
        }
    }

    private function getCollectionTypeLabel($collectionTypeId): string
    {
        switch ((int) $collectionTypeId) {
            case 1:
                return 'Daily';
            case 2:
                return 'Weekly';
            case 3:
                return 'Monthly';
            default:
                return 'N/A';
        }
    }

    /**
     * Determine the displayed value based on EMI collection status
     * If status is "Foreclose", display discount value
     * If status is "Partially Paid", display total_remaining value
     * Otherwise, display total_remaining value by default
     */
    private function getDisplayedValueForPdf($emiCollections): array
    {
        if ($emiCollections->isEmpty()) {
            return [
                'value' => 0,
                'label' => 'Remaining'
            ];
        }

        // Get the most recent EMI collection record
        $latestCollection = $emiCollections->sortByDesc('id')->first();

        if (!$latestCollection) {
            return [
                'value' => 0,
                'label' => 'Remaining'
            ];
        }

        $status = strtolower(trim($latestCollection->status));

        if (strpos($status, 'foreclose') !== false) {
            return [
                'value' => (float) ($latestCollection->discount ?? 0),
                'label' => 'Discount'
            ];
        } elseif (strpos($status, 'partially paid') !== false || strpos($status, 'partially') !== false) {
            return [
                'value' => (float) ($latestCollection->total_remaining ?? 0),
                'label' => 'Remaining'
            ];
        } else {
            // Default to total_remaining for other statuses
            return [
                'value' => (float) ($latestCollection->total_remaining ?? 0),
                'label' => 'Remaining'
            ];
        }
    }
}
