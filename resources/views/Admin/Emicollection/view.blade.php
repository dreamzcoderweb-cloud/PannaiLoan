@extends('site.layouts.app')

@section('title', 'Admin Dashboard - EMI Collection Details')

@section('content')
    <style>
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .emi-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .summary-card {
            border-left: 4px solid #667eea;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .customer-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto;
        }

        .status-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .payment-indicator {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-weight: bold;
        }

        .installment-card {
            border-left: 4px solid;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .installment-card:hover {
            transform: translateX(5px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }


        .amount-highlight {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
        }

        .info-label {
            color: #718096;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .info-value {
            color: #2d3748;
            font-size: 1rem;
            font-weight: 500;
        }
    </style>

    <div class="row">
        <!-- Customer Profile Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header-custom p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Customer Profile</h5>
                            <p class="mb-0 opacity-75">Loan Account Details</p>
                        </div>
                        <div class="customer-avatar">
                            {{ substr($emicollection->clientname->name, 0, 1) }}
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <h6 class="info-label mb-1">Customer Name</h6>
                        <h5 class="info-value">{{ $emicollection->clientname->name }}</h5>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <h6 class="info-label mb-1">Customer Type</h6>
                            <span
                                class="emi-badge {{ $emicollection->loanassign->client_type == 1 ? 'bg-warning' : 'bg-info' }}">
                                {{ $emicollection->loanassign->client_type == 1 ? 'Old Customer' : 'New Customer' }}
                            </span>
                        </div>

                        <div class="col-6 mb-3">
                            <h6 class="info-label mb-1">Loan Status</h6>
                            <span class="emi-badge bg-success">
                                <span class="status-dot bg-success"></span>
                                {{ $emicollection->status }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="info-label mb-2">Contact Information</h6>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone-alt text-primary me-2"></i>
                            <span class="info-value">+91
                                XXXXXXX{{ substr($emicollection->clientname->phone ?? '0000000', -4) }}</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Loan Summary Card -->
        <div class="col-lg-8 col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Loan Summary</h5>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="summary-card p-4 bg-light">
                                <div class="d-flex align-items-center">

                                    <div class="ms-3">
                                        <h6 class="info-label mb-1">Total Payable Amount</h6>
                                        <h4 class="amount-highlight mb-0">
                                            ₹ {{ number_format($emicollection->total_payable_amount, 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="summary-card p-4 bg-light">
                                <div class="d-flex align-items-center">

                                    <div class="ms-3">
                                        <h6 class="info-label mb-1">Loan Name</h6>
                                        <h4 class="info-value mb-0">{{ $emicollection->loan->loan_name ?? '---' }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="summary-card p-4 bg-light">
                                <div class="d-flex align-items-center">

                                    <div class="ms-3">
                                        <h6 class="info-label mb-1">Payment Frequency</h6>
                                        <h4 class="info-value mb-0">

                                            @if($emicollection->collection_type_id == 3)
                                                Monthly
                                            @elseif($emicollection->collection_type_id == 2)
                                                Weekly
                                            @elseif($emicollection->collection_type_id == 1)
                                                Daily
                                            @else
                                                Custom
                                            @endif
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="summary-card p-4 bg-light">
                                <div class="d-flex align-items-center">

                                    <div class="ms-3">
                                        <h6 class="info-label mb-1">Total Installments</h6>
                                        <h4 class="info-value mb-0">{{ $emicollection->details->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Installments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Installment Schedule</h5>
                            <p class="text-muted mb-0">Track all payment installments</p>
                        </div>
                        <div class="d-flex">
                            <span class="badge bg-success me-2">
                                <span class="status-dot bg-success"></span>
                                Paid: {{ $emicollection->details->where('status', 'Paid')->count() }}
                            </span>
                            <span class="badge bg-warning">
                                <span class="status-dot bg-warning"></span>
                                Pending: {{ $emicollection->details->where('status', '!=', 'Paid')->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Due Date</th>
                                    <th class="text-end">EMI Amount</th>
                                    <th class="text-end">Remaining</th>
                                    <th class="text-center">Paid Date</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($emicollection->details as $detail)
                                    <tr class="{{ $detail->status == 'Paid' ? 'table-success' : 'table-light' }}">
                                        <td class="text-center">
                                            <div
                                                class="payment-indicator {{ $detail->status == 'Paid' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $loop->iteration }}
                                            </div>
                                        </td>
                                        @php
                                            $days = [
                                                1 => 'Monday',
                                                2 => 'Tuesday',
                                                3 => 'Wednesday',
                                                4 => 'Thursday',
                                                5 => 'Friday',
                                                6 => 'Saturday',
                                                7 => 'Sunday',
                                            ];
                                        @endphp
                                        <td>
                                            @if ($detail->due_date)
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-calendar text-primary me-2"></i>
                                                    <span>{{ \Carbon\Carbon::parse($detail->due_date)->format('d M, Y') }}</span>
                                                </div>
                                            @elseif ($detail->week_duedays_id)
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-calendar text-info me-2"></i>
                                                    <span>Week Day: {{ $days[$detail->week_duedays_id] ?? '-' }}</span>
                                                </div>

                                            @elseif ($detail->daily_duedays_id)
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-calendar text-warning me-2"></i>
                                                    <span>Day: {{ $days[$detail->daily_duedays_id] ?? '-' }}</span>
                                                </div>
                                            @endif
                                        </td>

                                        <td class="text-end">
                                            <span class="amount-highlight">
                                                ₹ {{ number_format($detail->emi_amount, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span
                                                class="{{ $detail->remaining_payable_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                ₹ {{ number_format($detail->remaining_payable_amount, 2) }}
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            @if($detail->paid_date)
                                                <span class="badge bg-light text-dark">
                                                    {{ \Carbon\Carbon::parse($detail->paid_date)->format('d-m-Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($detail->paid_amount)
                                                <span class="text-success">
                                                    ₹ {{ number_format($detail->paid_amount, 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge {{ $detail->status == 'Paid' ? 'bg-success' : 'bg-warning' }} px-3 py-2">
                                                <span
                                                    class="status-dot {{ $detail->status == 'Paid' ? 'bg-success' : 'bg-warning' }}"></span>
                                                {{ $detail->status }}
                                            </span>
                                        </td>
                                       <td class="text-center">
                                            @can('emicollection-delete')

                                            <form action="{{ route('admin.emicollection-delete', $detail->id) }}"
                                                method="POST"
                                                style="display:inline;">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this record?');">
                                                    Delete
                                                </button>

                                            </form>

                                            @endcan
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Footer -->
                    <div class="row mt-4 pt-3 border-top">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">Total Paid</h6>
                                    <h4 class="text-success mb-0">
                                        ₹ {{ number_format($emicollection->total_collected, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">

                                <div>
                                    <h6 class="mb-0">Total Pending</h6>
                                    <h4 class="text-warning mb-0">
                                        ₹ {{ number_format($emicollection->total_remaining, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-percentage fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Completion Rate</h6>
                                    <h4 class="text-primary mb-0">
                                        @php
                                        $total = $emicollection->details->count();
                                        $paid = $emicollection->details->where('status', 'Paid')->count();
                                        $percentage = $total > 0 ? round(($paid / $total) * 100) : 0;
                                        @endphp
                                        {{ $percentage }}%
                                    </h4>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>

                <div class="card-footer bg-white border-top-0 pt-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Last updated: {{ now()->format('d M, Y h:i A') }}
                            </small>
                        </div>
                        {{-- <div>
                            <a href="{{ route('admin.emicollection-list') }}" class="btn back-btn">
                                <i class="ti ti-back-left me-2"></i>Back to Collections
                            </a>
                            @canany(['emicollection-delete'])
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteEmiModal">
                                    <i class="ti ti-trash me-2"></i>Delete
                                </button>
                            @endcanany
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteEmiModal" tabindex="-1" role="dialog" aria-labelledby="deleteEmiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteEmiModalLabel">
                        <i class="ti ti-alert-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Are you sure you want to delete this EMI collection?</p>
                    <div class="alert alert-info" role="alert">
                        <strong>Customer:</strong> {{ $emicollection->clientname->name ?? '---' }}<br>
                        <strong>Total Amount:</strong> ₹{{ number_format($emicollection->total_payable_amount, 2) }}<br>
                        <strong>Collection Type:</strong>
                        @if($emicollection->collection_type_id == 3) Monthly
                        @elseif($emicollection->collection_type_id == 2) Weekly
                        @elseif($emicollection->collection_type_id == 1) Daily
                        @else Custom
                        @endif<br>
                        <strong>Total Installments:</strong> {{ $emicollection->details->count() }}
                    </div>
                    <p class="text-danger mb-0"><strong>⚠️ Warning:</strong> This action cannot be undone. All
                        {{ $emicollection->details->count() }} installment records will be permanently deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.emicollection-delete', $emicollection->id) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-trash me-2"></i>Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Add animation to table rows
        document.addEventListener('DOMContentLoaded', function () {
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
                row.classList.add('animate__animated', 'animate__fadeIn');
            });

            // Payment button click handler
            document.querySelectorAll('.btn-primary[data-bs-toggle="modal"]').forEach(button => {
                button.addEventListener('click', function () {
                    const installmentNo = this.closest('tr').querySelector('td:nth-child(2) strong').textContent;
                    console.log('Process payment for:', installmentNo);
                });
            });
        });
    </script>
@endpush
