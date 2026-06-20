@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title"> Customer Lists</h3>
            </div>

            <div class="col-auto">
                {{-- Search handled by DataTables --}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Customer List</h4>
                    <div>
                        <a href="{{ route('admin.customer-create') }}" class="btn btn-info btn-sm">Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="customerTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Loan Collection Type</th>
                                    <th>Total Payable Amount</th>
                                    <th>Remaining Amount</th>
                                    <th>Branch</th>
                                    <th>Route</th>
                                    @canany(['customer-edit', 'customer-view'])
                                        <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>

                            <tbody id="customerTableBody">
                                @foreach ($data as $item)
                                    <tr class="customer-row">
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="customer-name">{{ $item->name }}</td>
                                        <td class="customer-phone">{{ $item->phone }}</td>
                                        <td>
                                            @if(($item->loanAssign->collection_type_id ?? '') == 1)
                                                Daily
                                            @elseif(($item->loanAssign->collection_type_id ?? '') == 2)
                                                Weekly
                                            @elseif(($item->loanAssign->collection_type_id ?? '') == 3)
                                                Monthly
                                            @else
                                                ---
                                            @endif
                                        </td>
                                        <td>{{ $item->loanAssign->total_payableamt ?? '---'}}</td>
                                        <td>
                                            {{ $item->loanAssign?->emiCollections?->last()?->latestDetail?->remaining_payable_amount ?? '---' }}
                                        </td>
                                        <td>{{ $item->branch->branch_name ?? '---' }}</td>
                                        <td>{{ $item->route->route_name ?? '---' }}</td>
                                        @canany(['customer-edit', 'customer-view'])
                                        <td>
                                            @can('customer-view')
                                            <a href="{{ route('admin.customer-view', $item->id) }}"
                                                class="btn btn-success btn-sm">View
                                            </a>
                                            @endcan
                                            @can('customer-edit')
                                            <a href="{{ route('admin.customer-edit', $item->id) }}"
                                                class="btn btn-warning btn-sm">Edit
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
            var table = $('#customerTable').DataTable({
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                buttons: [
                    { extend: 'copy', className: 'btn btn-default', title: 'Pannai - Customer List' },
                    { extend: 'csv', className: 'btn btn-default', title: 'Pannai - Customer List' },
                    { extend: 'excel', className: 'btn btn-default', title: 'Pannai - Customer List' },
                    { extend: 'pdf', className: 'btn btn-default', title: 'Pannai - Customer List' },
                    { extend: 'print', className: 'btn btn-default', title: 'Pannai - Customer List' }
                ]
            });

            table.buttons().container()
                .appendTo('#customerTable_wrapper .col-md-6:eq(0)');
        });
    </script>

@endsection




