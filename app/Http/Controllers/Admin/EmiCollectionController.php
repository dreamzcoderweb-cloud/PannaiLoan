<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\LoanAssign;
use App\Models\Emicollection;
use App\Models\EmicollectionDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmiCollectionController extends Controller
{
    public function create()
    {
        $customers = Customer::all();
        return view('Admin.Emicollection.create', compact('customers'));
    }

    public function getLoansByClient($clientId)
    {
        $loans = LoanAssign::with('loan','int')
            ->where('client_id', $clientId)
            ->get();

        if ($loans->isEmpty()) {
            return response()->json([
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
                'id'        => $loan->id,
                'loan_type'=> $loanType,
                'amount'   => $amount,
                'interest_rate' => $loan->int->interest_id,
                'date'     => $loan->loanassign_date,
                'status'   => 'Active'
            ];
        });

        return response()->json([
            'loans' => $loanData
        ]);
    }


    public function getLoanEmiDetails($loanId)
    {
        // 1. Get specific Loan Assign
        $loanAssign = LoanAssign::find($loanId);

        if (!$loanAssign) {
            return response()->json([
                'customer_type' => 'none',
                'emi' => []
            ]);
        }

        $customerType = ($loanAssign->client_type == 1) ? 'old' : 'new';
        $collectionType = $loanAssign->collection_type_id;

        $emiamount = '';
        $duedate   = '';
        $dueDay    = null;

        // Get appropriate EMI amount and due information
        if ($collectionType == 1) {
            // DAILY
            $emiamount = $loanAssign->daily_emi;
            $dueDay    = $loanAssign->daily_duedays_id; // 1–7
            $totalPayable = $loanAssign->total_payableamt;
        } elseif ($collectionType == 2) {
            // WEEKLY
            $emiamount = $loanAssign->weekly_emi;
            $dueDay    = $loanAssign->week_duedays_id; // 1–7
            $duedate   = $loanAssign->weeklyemi_date;
            $totalPayable = $loanAssign->loan_amount;
        } elseif ($collectionType == 3) {
            // MONTHLY
            $emiamount = $loanAssign->monthly_emi;
            $duedate   = $loanAssign->monthlydue_date;
            $totalPayable = $loanAssign->total_payableamt;
        }

        // 2. Existing EMI collections for THIS loan
        try {
            // Note: We need to filter by loan_assign_id now, not just client_id
            $emiCollections = Emicollection::with('details')
                ->where('loan_assign_id', $loanId)
                ->get();
        } catch (\Exception $e) {
            $emiCollections = collect(); // Empty collection on error
        }

        // If no records found, return first EMI
        if ($emiCollections->isEmpty()) {
            return response()->json([
                'customer_type' => $customerType,
                'emi' => [[
                    'due_date'           => $duedate,
                    'emi_amount'         => $emiamount,
                    'payable_amount'     => $totalPayable,
                    'remaining'          => $totalPayable,
                    'loan_type_id'       => $loanAssign->loan_type_id,
                    'daily_duedays_id'   => ($collectionType == 1) ? $dueDay : null,
                    'week_duedays_id'    => ($collectionType == 2) ? $dueDay : null,
                    'status'             => 'Pending',
                    'flag'               => 2,
                    'collection_type_id' => $loanAssign->collection_type_id,
                    'due_day'            => $dueDay,
                ]]
            ]);
        }

        // If records found, get details
        $emiData = [];
        $paidAmount = 0;

        foreach ($emiCollections as $collection) {
            $details = DB::table('emicollection_details')
                ->where('emi_collection_id', $collection->id)
                ->get();

            foreach ($details as $detail) {
                $paidAmount += $detail->paid_amount;
                // Calculate remaining for display context if needed, though usually sequential
                // logic here repeats per row which might be existing behavior
                $remaining = max($totalPayable - $paidAmount, 0);

                $emiData[] = [
                    'due_date'           => $detail->due_date,
                    'emi_amount'         => $detail->emi_amount,
                    'payable_amount'     => $totalPayable,
                    'remaining'          => $remaining,
                    'loan_type_id'       => $detail->loan_type_id,
                    'daily_duedays_id'   => $detail->daily_duedays_id,
                    'week_duedays_id'    => $detail->week_duedays_id,
                    'status'             => $detail->status,
                    'flag'               => 1,
                    'collection_type_id' => $loanAssign->collection_type_id,
                    'due_day'            => $dueDay,
                ];
            }
        }

        return response()->json([
            'customer_type' => $customerType,
            'emi' => $emiData
        ]);
    }


    public function store(Request $request)
    {
        $request->validate(
            [
                'client_id' => 'required|exists:customers,id',
                'loan_assign_id' => 'nullable|exists:loan_assigns,id',
                'loan_type_id.*' => 'required|exists:loans,id',

                'emi.due_date.*' => 'nullable|date',
                'emi.due_day.*'  => 'nullable|in:1,2,3,4,5,6,7',

                'emi.emi_amount.*' => 'required|numeric|min:1',
                'emi.remaining_amount.*' => 'required|numeric|min:0',
                'emi.status.*' => 'required|in:Pending,Paid',
            ],
            [],
            [
                'client_id' => 'Customer',
                'loan_assign_id' => 'Loan Assignment',
                'loan_type_id.*' => 'Loan Type',
                'emi.due_date.*' => 'Due Date',
                'emi.due_day.*'  => 'Due Day',
                'emi.emi_amount.*' => 'EMI Amount',
                'emi.remaining_amount.*' => 'Remaining Payable Amount',
                'emi.status.*' => 'Status',
            ]
        );

        DB::beginTransaction();

        try {
            // Prefer explicit loan assignment from UI; fallback for cases where it was not posted.
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
                    ->filter(fn ($value) => !empty($value))
                    ->isNotEmpty();
                $hasDueDay = collect($request->input('emi.due_day', []))
                    ->filter(fn ($value) => !empty($value))
                    ->isNotEmpty();

                $collectionTypeId = null;
                if ($hasDueDate && $hasDueDay) {
                    $collectionTypeId = 2; // Weekly
                } elseif ($hasDueDate) {
                    $collectionTypeId = 3; // Monthly
                } elseif ($hasDueDay) {
                    $collectionTypeId = 1; // Daily
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
                return back()->with('error', 'Loan assign not found');
            }

            if($loanAssign->collection_type_id == 2){
                $amount = $loanAssign->loan_amount;
            }else{
                $amount = $loanAssign->total_payableamt;
            }
            // Create master row only once; never reset totals on subsequent collections.
            $existingCollection = DB::table('emicollections')
                ->where('loan_assign_id', $loanAssign->id)
                ->where('client_id', $request->client_id)
                ->first();

            if (!$existingCollection) {
                $emiCollectionId = DB::table('emicollections')->insertGetId([
                    'loan_assign_id'        => $loanAssign->id,
                    'client_id'             => $request->client_id,
                    'collection_type_id'    => $loanAssign->collection_type_id,
                    'total_payable_amount'  => $amount,
                    'total_collected'       => 0,
                    'total_remaining'       => $amount,
                    'status'                => 'Pending',
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);

                $existingCollection = DB::table('emicollections')->find($emiCollectionId);
            } else {
                $emiCollectionId = $existingCollection->id;
            }

            $totalCollected = (float) ($existingCollection->total_collected ?? 0);
            $totalRemaining = (float) ($existingCollection->total_remaining ?? $amount);

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
                } elseif ($loanAssign->collection_type_id == 1) { // Daily - ADDED THIS!
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

                  //  THIS is the correct remaining for details table
                $remainingAmount = $totalRemaining;
                } else {
                   $paidAmount = 0;
                  $remainingAmount = $totalRemaining;
                }


                // Create EMI Collection detail
                DB::table('emicollection_details')->insert([
                    'emi_collection_id'        => $emiCollectionId,
                    'loan_type_id'             => $loanTypeId,
                    'installment_no'           => $installmentNo,
                    'due_date'                 => $dueDate,
                    'week_duedays_id'          => $weeklyDueDayId,
                    'daily_duedays_id'         => $dailyDueDayId,
                    'emi_amount'               => $emiAmount,
                    'remaining_payable_amount' => $remainingAmount,
                    'status'                   => $status,
                    'paid_amount'              => $paidAmount,
                    'paid_date'                => $status === 'Paid' ? now() : null,
                    'created_at'               => now(),
                    'updated_at'               => now(),
                ]);
            }

            // Calculate the new status
            $newStatus = 'Pending';

            // Get the updated total paid amount from all EMI details
            $totalPaidAmount = DB::table('emicollection_details')
                ->where('emi_collection_id', $emiCollectionId)
                ->sum('paid_amount');

            $totalPayableAmount = (float) ($existingCollection->total_payable_amount ?? $amount);


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
                    'status'          => $newStatus,
                    'updated_at'      => now(),
                ]);

            DB::commit();

            return redirect()
                ->route('admin.emicollection-list')
                ->with('success', 'EMI Collection saved successfully');
        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function emisearchCustomers(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');
        })
            ->select('id', 'name', 'phone')
            ->limit(10)
            ->get();

        return response()->json([
            'customers' => $customers
        ]);
    }

    public function index(Request $request)
    {
        $data = Emicollection::with('clientname')->get();
        return view('Admin.Emicollection.index', compact('data'));
    }

    public function view($id)
    {

        $emicollection = Emicollection::with('details', 'clientname', 'loanassign', 'loan')->findOrFail($id);

        return view('Admin.Emicollection.view', compact('emicollection'));
    }

    public function destroy($id)
    {
        try {
            $detail = EmicollectionDetail::findOrFail($id);
            $emiCollectionId = $detail->emi_collection_id;

            $detail->delete();

            // Recalculate totals for parent EMI collection
            $masterCollection = Emicollection::find($emiCollectionId);
            if ($masterCollection) {
                $totalPaidAmount = EmicollectionDetail::where('emi_collection_id', $emiCollectionId)
                    ->where('status', 'Paid')
                    ->sum('paid_amount');

                $totalPayableAmount = (float) $masterCollection->total_payable_amount;
                $totalRemaining = max(0, $totalPayableAmount - $totalPaidAmount);

                if ($totalPaidAmount >= $totalPayableAmount) {
                    $newStatus = 'Completed';
                } else if ($totalPaidAmount > 0) {
                    $newStatus = 'Partially Paid';
                } else {
                    $newStatus = 'Pending';
                }

                $masterCollection->update([
                    'total_collected' => $totalPaidAmount,
                    'total_remaining' => $totalRemaining,
                    'status'          => $newStatus,
                    'updated_at'      => now(),
                ]);
            }

            return redirect()
                ->route('admin.emicollection-view', $emiCollectionId)
                ->with('success', 'Record deleted successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete record: ' . $e->getMessage());
        }
    }
}
