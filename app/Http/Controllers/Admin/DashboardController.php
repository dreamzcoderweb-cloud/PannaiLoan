<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\User;
use App\Models\Emicollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
  

    public function home()
    {
        
        $routeCustomerCounts = DB::table('customers')
    ->join('branches', 'customers.branch_id', '=', 'branches.id')
    ->join('routes', 'customers.route_id', '=', 'routes.id')
    ->select(
        'branches.branch_name',
        'routes.route_name',
        DB::raw('COUNT(customers.id) as customer_count')
    )
    ->groupBy('branches.branch_name', 'routes.route_name')
    ->get();
    
        $totalLoans = Loan::count();
        $totalCustomers = Customer::count();
        $totalBranches = Branch::count();
        $totalStaffs = User::count();
        $totalcollections = (int) Emicollection::whereDate('created_at', today())->sum('total_collected');
    
        // Branch-wise customer count
        $branchCustomerCounts = Branch::withCount('customers')->get();
    
       return view('Admin.dashboard', compact(
            'totalLoans',
            'totalCustomers',
            'totalBranches',
            'totalStaffs',
            'totalcollections',
            'branchCustomerCounts',
            'routeCustomerCounts'
        ));
    }
}
