@extends('site.layouts.app')

@section('title', 'Group Details')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Group Details: {{ $group->group_name }}</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.group-list') }}" class="btn btn-secondary btn-sm">Back to List</a>
                <a href="{{ route('admin.group-edit', $group->id) }}" class="btn btn-primary btn-sm">Edit Group</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Group Members Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="groupDetailsTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Branch</th>
                                    <th>Route</th>
                                    <th>Loan</th>
                                    <th>Collection Type</th>
                                    <th>Loan Amount</th>
                                    <th>Remaining Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group->loanAssigns as $loan)
                                    @php
                                        $emi = $loan->emiCollections->first();
                                        $remaining = $emi ? $emi->total_remaining : $loan->total_payableamt;
                                        
                                        $collection_type = '';
                                        if ($loan->collection_type_id == 1) $collection_type = 'Daily';
                                        elseif ($loan->collection_type_id == 2) $collection_type = 'Weekly';
                                        elseif ($loan->collection_type_id == 3) $collection_type = 'Monthly';
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($loan->emiCollections->count() > 0)
                                                <a href="{{ route('admin.getclient-emidetails', $loan->id) }}"
                                                class="text-primary fw-bold">
                                                    {{ $loan->client_name->name ?? '---' }}
                                                </a>
                                            @else
                                                <span class="text-muted" title="No EMI details">
                                                    {{ $loan->client_name->name ?? '---' }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>{{ $loan->phone ?? '---' }}</td>
                                        <td>{{ $loan->branches->branch_name ?? '---' }}</td>
                                        <td>{{ $loan->routes->route_name ?? '---' }}</td>
                                        <td>{{ $loan->loan->loan_name ?? '---' }}</td>
                                        <td>{{ $collection_type }}</td>
                                        <td>{{ number_format($loan->loan_amount, 2) }}</td>
                                        <td><span class="text-danger font-weight-bold">{{ number_format($remaining, 2) }}</span></td>
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
            var table = $('#groupDetailsTable').DataTable({
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                dom: '<"row align-items-center"<"col-sm-12 col-md-6"lB><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row align-items-center"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [
                    { extend: 'copy', className: 'btn btn-default', title: 'Pannai - Group List' },
                    { extend: 'csv', className: 'btn btn-default', title: 'Pannai - Group List' },
                    { extend: 'excel', className: 'btn btn-default', title: 'Pannai - Group List' },
                    { extend: 'pdf', className: 'btn btn-default', title: 'Pannai - Group List' },
                    { extend: 'print', className: 'btn btn-default', title: 'Pannai - Group List' }
                ]
            });
        });
    </script>
@endsection
