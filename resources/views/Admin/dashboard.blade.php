@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="welcome-banner">
            <div class="row align-items-center">
                <div class="col-md-9">
                    @auth
                    <h2>Welcome back, {{ strtoupper(auth()->user()->name) }}! 👋</h2>
                    @endauth

                    <p>Here's what's happening with Pannai Loan today. Check your latest statistics and collection reports.</p>
                </div>
                <div class="col-md-3 text-end d-none d-md-block">
                    <img src="{{ asset('assets/img/logo-small.svg') }}" class="banner-img" alt="Pannai">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Widget Info -->
    <div class="col-12">
        <div class="row">
            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card flex-fill stat-card border-start border-primary border-4">
                    <div class="card-body">
                        <span class="avatar bg-primary-transparent text-primary mb-3">
                            <i class="ti ti-currency-rupee fs-24"></i>
                        </span>
                        <h6 class="mb-1">Today Collection</h6>
                        <h3 class="mb-3 text-dark fw-bold">{{ isset($totalcollections) ? moneyFormatIndia($totalcollections) : '---' }}</h3>
                        <a href="{{ route('admin.emicollection-list') }}" class="link-default">View Report</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card flex-fill stat-card border-start border-secondary border-4">
                    <div class="card-body">
                        <span class="avatar bg-secondary-transparent text-secondary mb-3">
                            <i class="ti ti-receipt-2 fs-24"></i>
                        </span>
                        <h6 class="mb-1">Active Loans</h6>
                        <h3 class="mb-3 text-dark fw-bold">{{ isset($totalLoans) ? moneyFormatIndia($totalLoans) : '---' }}</h3>
                        <a href="{{ route('admin.loan-list') }}" class="link-default">Manage Loans</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card flex-fill stat-card border-start border-info border-4">
                    <div class="card-body">
                        <span class="avatar bg-info-transparent text-info mb-3">
                            <i class="ti ti-users-group fs-24"></i>
                        </span>
                        <h6 class="mb-1">Total Customers</h6>
                        <h3 class="mb-3 text-dark fw-bold">{{ isset($totalCustomers) ? moneyFormatIndia($totalCustomers) : '---' }}</h3>
                        <a href="{{ route('admin.customer-list') }}" class="link-default">Client List</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card flex-fill stat-card border-start border-danger border-4">
                    <div class="card-body">
                        <span class="avatar bg-danger-transparent text-danger mb-3">
                            <i class="ti ti-building-bank fs-24"></i>
                        </span>
                        <h6 class="mb-1">Total Branches</h6>
                        <h3 class="mb-3 text-dark fw-bold">{{ isset($totalBranches) ? moneyFormatIndia($totalBranches) : '---' }}</h3>
                        <a href="{{ route('admin.branch-list') }}" class="link-default">Branch Info</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card flex-fill stat-card border-start border-success border-4">
                    <div class="card-body">
                        <span class="avatar bg-success-transparent text-success mb-3">
                            <i class="ti ti-user-shield fs-24"></i>
                        </span>
                        <h6 class="mb-1">Staff Members</h6>
                        <h3 class="mb-3 text-dark fw-bold">{{ isset($totalStaffs) ? moneyFormatIndia($totalStaffs) : '---' }}</h3>
                        <a href="{{ route('admin.staff-list') }}" class="link-default">Manage Staff</a>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                  <div class="col-12">
                     <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Branch & Route Wise Customers</h5>
                            </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                        <tr>
                                <table class="table table-bordered" id="branchroute_customer_cnt">
                                    <thead>
                                            <th>Branch</th>
                                            <th>Route</th>
                                            <th>Customer Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($routeCustomerCounts as $data)
                                            <tr>
                                                <td>{{ $data->branch_name }}</td>
                                                <td>{{ $data->route_name }}</td>
                                                <td>{{ $data->customer_count }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No Data Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
function moneyFormatIndia($num) {
    $num = (int)$num;
    $explrestunits = "";
    if(strlen($num) > 3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3);
        $restunits = (strlen($restunits)%2 == 1) ? "0".$restunits : $restunits;
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; 
            } else {
                $explrestunits .= $expunit[$i].","; 
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash;
}
@endphp


<script>
        $(document).ready(function () {
            var table = $('#branchroute_customer_cnt').DataTable({
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                buttons: [
                    { extend: 'copy', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'csv', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'excel', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'pdf', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'print', className: 'btn btn-default', title: 'Pannai - EMI Collection List' }
                ]
            });

            table.buttons().container()
                .appendTo('#emiCollectionTable_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection