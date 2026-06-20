<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\LoanAssign;
use App\Models\Branch;
use App\Models\Emicollection;
use App\Models\Route;
use DB;

use App\Models\Group;
use App\Models\GroupLoan;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::withCount('loanAssigns')->get();
        return view('Admin.Group.index', compact('groups'));
    }

    public function create()
    {
        return view('Admin.Group.create');
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'group_name' => 'required|min:3|max:100',
            'customer_id' => 'required|array|min:1',
            'total_loan_amount' => 'required|numeric|min:0',
            'total_remaining_amount' => 'required|numeric|min:0',
        ], [
            'group_name.required' => 'Group Name is required',
            'customer_id.required' => 'Please select at least one customer',
        ]);

        DB::beginTransaction();
        try{
            $group = Group::create([
            'group_name' => $request->group_name,
            'total_loan_amount' => $request->total_loan_amount,
            'total_remaining_amount' => $request->total_remaining_amount,
           ]);

            if ($request->has('loanassign_id')) {
                foreach ($request->loanassign_id as $index => $loanassignId) {
                $loanAmount = $request->input('customer_amount_' . $loanassignId, 0);
                $remainingAmount = $request->input('customer_remaining_' . $loanassignId, 0);
                $customerId = $request->customer_id[$index] ?? null;
            
                    GroupLoan::create([
                        'group_id' => $group->id,
                        'loanassign_id' => $loanassignId,
                        'client_id' => $customerId,
                        'loan_amount' => $loanAmount,
                        'remaining_amount' => $remainingAmount,
                    ]);
                }
            }
            DB::commit();
           return redirect()->route('admin.group-list')->with('success', 'Group created successfully.');
        }catch  (\Exception $e){
             DB::rollBack();
             return back()->with('error', 'Error creating group: ' . $e->getMessage())->withInput();
        }
        
    }

    public function show($id)
    {
        $group = Group::with(['loanAssigns.loan', 'loanAssigns.branches', 'loanAssigns.routes', 'loanAssigns.client_name', 'loanAssigns.emiCollections'])->findOrFail($id);
        return view('Admin.Group.view', compact('group'));
    }

    public function edit($id)
    {
        $group = Group::with(['loanAssigns.loan', 'loanAssigns.branches', 'loanAssigns.routes', 'loanAssigns.client_name', 'loanAssigns.emiCollections'])->findOrFail($id);
      
        return view('Admin.Group.edit', compact('group'));
    }

    
    public function update(Request $request, $id)
    {
        $request->validate([
            'group_name' => 'required|min:3|max:100',
            'loanassign_id' => 'required|array|min:1', 
            'total_loan_amount' => 'required|numeric|min:0',
            'total_remaining_amount' => 'required|numeric|min:0',
        ], [
            'group_name.required' => 'Group Name is required',
            'loanassign_id.required' => 'Please select at least one customer', 
        ]);
        
        DB::beginTransaction();
        
        try {
            $group = Group::findOrFail($id);
            
            // Update group details including financial fields
            $group->update([
                'group_name' => $request->group_name,
                'total_loan_amount' => $request->total_loan_amount,
                'total_remaining_amount' => $request->total_remaining_amount,
            ]);

            // Delete existing group loans
            GroupLoan::where('group_id', $id)->delete();
            
            // Loop through all loanassign_ids
            if ($request->has('loanassign_id')) {
                foreach ($request->loanassign_id as $index => $loanassignId) {
                    $loanAmount = $request->input('customer_amount_' . $loanassignId, 0);
                    $remainingAmount = $request->input('customer_remaining_' . $loanassignId, 0);
                    
                    // You'll need to get the actual customer_id from the loanassign record
                    $loanAssign = \App\Models\LoanAssign::find($loanassignId);
                    $customerId = $loanAssign ? $loanAssign->client_id : null;
                    
                    GroupLoan::create([
                        'group_id' => $group->id,
                        'loanassign_id' => $loanassignId,
                        'client_id' => $customerId,
                        'loan_amount' => $loanAmount,
                        'remaining_amount' => $remainingAmount,
                    ]);
                }
            }
            
            DB::commit();
            return redirect()->route('admin.group-list')->with('success', 'Group updated successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating group: ' . $e->getMessage())->withInput();
        }
    }
    public function delete($id)
    {
        $group = Group::findOrFail($id);
        $group->delete(); // This will cascade delete group_loans if foreign keys are set correctly
        return redirect()->route('admin.group-list')->with('success', 'Group deleted successfully.');
    }

    public function searchCustomers(Request $request)
    {
        
        $search = $request->get('search');
        
        $customers = Customer::where('name', 'LIKE', "%$search%")
            ->orWhere('phone', 'LIKE', "%$search%")
            ->get();

        $results = [];

        foreach ($customers as $customer) {
            $loans = LoanAssign::with(['loan', 'branches', 'routes', 'emiCollections'])
                ->where('client_id', $customer->id)
                ->get();

            foreach ($loans as $loan) {
                // Get remaining amount from emicollections
                $emi = $loan->emiCollections->first();
                $remaining = $emi ? $emi->total_remaining : $loan->total_payableamt;
                
                // Get collection type description
                $collection_type = '';
                if ($loan->collection_type_id == 1) $collection_type = 'Daily';
                elseif ($loan->collection_type_id == 2) $collection_type = 'Weekly';
                elseif ($loan->collection_type_id == 3) $collection_type = 'Monthly';

                $results[] = [
                    'id' => $loan->id,
                    'customer_id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'branch' => $loan->branches->branch_name ?? '---',
                    'route' => $loan->routes->route_name ?? '---',
                    'loan_name' => $loan->loan->loan_name ?? '---',
                    'collection_type' => $collection_type,
                    'loan_amount' => $loan->loan_amount,
                    'remaining_amount' => $remaining ?? '---',
                ];
            }
        }

        return response()->json($results);
    }
    public function emidetails($id)
    {
        $loanAssign = LoanAssign::findOrFail($id);

        $emicollection = Emicollection::with(
            'details',
            'clientname',
            'loanassign',
            'loan'
        )
        ->where('loan_assign_id', $loanAssign->id)
        ->first(); 

        if (!$emicollection) {
            return redirect()->back()
                ->with('error', 'EMI details not found for this customer');
        }

        return view('Admin.Emicollection.view', compact('emicollection'));
   }

}
