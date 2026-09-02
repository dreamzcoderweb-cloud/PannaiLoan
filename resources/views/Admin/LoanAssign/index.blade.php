@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Loan Assign Lists</h3>
            </div>
            <div class="col-auto">
                <!-- Search will now be handled by DataTable -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Loan Assign List</h4>
                    <div>
                        <a href="{{ route('admin.loan-assign-create') }}" class="btn btn-info btn-sm">Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Branch Filter -->
                    <form method="GET" action="{{ route('admin.loan-assign-list') }}" class="mb-4" id="loanAssignFilterForm">
                        <div class="row align-items-end">
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group mb-0">
                                    <label for="branch_id" class="form-label fw-bold">Select Branch</label>
                                    <select name="branch_id" id="branch_id" class="form-select" onchange="document.getElementById('loanAssignFilterForm').submit();">
                                        <option value="">All Branches</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ (isset($branch_id) && $branch_id == $branch->id) ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="loanAssignTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Loan Name</th>
                                    <th>Loan Interest</th>
                                    <th>Loan Collection Type</th>
                                    <th>EMI Amount</th>
                                    <th>Total Payable Amount</th>
                                    <th>Remaining Amount</th>
                                    <th>Branch</th>
                                    <th>Routes</th>
                                    <th>Loan Amount</th>
                                   
                                    @canany(['loanassign-edit','loanassign-delete'])
                                    <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody id="loanAssignTableBody">
                                @foreach ($data as $item)
                                <tr class="loanassign-row">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="customer-name">{{ $item->client_name->name ?? '---' }}</td>
                                    <td class="customer-phone">{{ $item->phone }}</td>
                                    <td class="loan-type">{{ $item->loan->loan_name ?? '---' }}</td>
                                    <td>{{ $item->int->interest_id ?? '---' }}</td>
                                    <td>
                                        @if ($item->collection_type_id == 1)
                                            Daily
                                        @elseif ($item->collection_type_id == 2)
                                            Weekly
                                        @elseif ($item->collection_type_id == 3)
                                            Monthly
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->collection_type_id == 1)
                                            {{ $item->daily_emi }}
                                        @elseif($item->collection_type_id == 2)
                                            {{ $item->weekly_emi }}
                                        @elseif($item->collection_type_id == 3)
                                            {{ $item->monthly_emi }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->collection_type_id == 2)
                                        {{ $item->total_distribution }}
                                        @else 
                                         {{ $item->total_payableamt }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(optional($item->latestEmiCollection)->latestDetail)
                                            {{ $item->latestEmiCollection->latestDetail->remaining_payable_amount }}
                                        @else
                                           ----
                                        @endif
                                    </td>
                                    <td class="branch-name">{{ $item->branches->branch_name ?? '---' }}</td>
                                    <td class="route-name">{{ $item->routes->route_name ?? '---' }}</td>
                                    <td>{{ $item->loan_amount }}</td>
                                    
                                    @canany(['loanassign-edit','loanassign-delete'])
                                    <td>
                                         @if(optional($item->latestEmiCollection)->latestDetail)
                                            <a href="{{ route('admin.loan-assign-clientdetails',$item->id) }}" class="btn btn-info btn-sm">
                                                Foreclose
                                            </a>
                                        @endif
                                            
                                        @can('loanassign-edit')
                                        <a href="{{ route('admin.loan-assign-edit', $item->id) }}"
                                            class="btn btn-warning btn-sm">Edit
                                        </a>
                                        @endcan
                                        
                                       @can('loanassign-delete')
                                       <a href="{{ route('admin.loan-assign-delete', $item->id) }}"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this record?');">
                                        Delete
                                        </a>
                                       @endcan
                                    </td>
                                    @endcanany
                                </tr>
                                @endforeach
                                
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
      <script>
       $(document).ready(function() {
            var table = $('#loanAssignTable').DataTable({
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-default',
                        title: 'Pannai - Loan Assign List' 
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-default',
                        title: 'Pannai - Loan Assign List' 
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-default',
                        title: 'Pannai - Loan Assign List' 
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-default',
                        title: 'Pannai - Loan Assign List' 
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-default',
                        title: 'Pannai - Loan Assign List' 
                    }
                ]
            });

            // Append buttons to the container - usually the standard wrapper has length menu on left (col-sm-6 eq 0)
            // and search on right (col-sm-6 eq 1). 
            // We can place buttons after the length menu.
            table.buttons().container()
                .appendTo('#loanAssignTable_wrapper .col-md-6:eq(0)');
        });
    </script>

@endsection