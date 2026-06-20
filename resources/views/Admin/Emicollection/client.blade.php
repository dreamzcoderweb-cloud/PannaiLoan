@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<style>
        .center-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
    </style>

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Loan Details View</h3>
            </div>
        </div>
    </div>

    <div class="row center-page">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">

                <div class="card-body">

                    <h4 class="mb-4">Client Details</h4>

                    @foreach ($getpaidemicollection as $item)

                        <table class="table table-bordered mb-4">
                            <tbody>

                                <tr>
                                    <th>Collection Type</th>
                                    <td>
                                        {{ $item->collection_type_id == 1 ? 'Daily' : ($item->collection_type_id == 2 ? 'Weekly' : 'Monthly') }}
                                    </td>
                                </tr>
                                
                                 <tr>
                                    <th>Total Payable Amount</th>
                                    <td>{{ number_format($item->loanassign->total_payableamt, 2) }}</td>
                                </tr>
                                
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-info">{{ $item->status }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $item->created_at->format('d M Y') }}</td>
                                </tr>

                            </tbody>
                        </table>

                        {{-- EMI DETAILS --}}
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Installment No</th>
                                    <th>Due Date</th>
                                    <th>EMI Amount</th>
                                    <th>Paid Amount</th>
                                    
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                @foreach ($item->details as $detail)
                                
                               
                                    <tr>
                                        <td>{{ $detail->installment_no }}</td>
                                       <td>{{ \Carbon\Carbon::parse($detail->due_date)->format('d-m-Y') }}</td>
                                        <td>{{ $detail->emi_amount }}</td>
                                        <td>{{ $detail->paid_amount }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $detail->status }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                             
                            <tfoot>
                                <tr class="fw-bold bg-light">
                                    <td colspan="3" class="text-end">Total Discount</td>
                                    <td>{{ number_format($item->discount, 2) }}</td>
                                    <td></td>
                                </tr>
                                <tr class="fw-bold bg-light">
                                    <td colspan="3" class="text-end">Total Paid Amount</td>
                                    <td>{{ number_format($item->total_collected, 2) }}</td>
                                    <td></td>
                                </tr>
                                
                            </tfoot>

                        </table>
                        @if ($item->total_remaining > 0 && $item->status !== 'Foreclosed')
                        <div class="card mt-3">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Foreclose Options</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <h6>Foreclose Details:</h6>
                                            <ul class="mb-0">
                                                <li>Remaining Balance: <strong>₹{{ number_format($item->total_remaining, 2) }}</strong></li>
                                                <li>Regular EMI: <strong>₹{{ number_format($item->loanassign->monthly_emi ?? $item->loanassign->weekly_emi ?? $item->loanassign->daily_emi, 2) }}</strong></li>
                                                <li>Suggested Foreclose Amount: <strong>₹{{ number_format($item->total_remaining, 2) }}</strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.loan.foreclose') }}" method="POST" id="forecloseForm">
                                            @csrf
                                            @method('POST')
                                            
                                            <input type="hidden" name="emicollection_id" value="{{ $item->id }}">
                                            <input type="hidden" name="loan_assign_id" value="{{ $item->loan_assign_id }}">
                                            
                                            <div class="mb-3">
                                                <label for="foreclose_amount" class="form-label">Foreclose Amount</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number"  class="form-control"  id="foreclose_amount" name="emiamount" value="{{ $item->total_remaining }}" min="1" max="{{ $item->total_remaining }}"step="0.01">
                                                </div>
                                                <div class="form-text">
                                                    Enter the amount to foreclose (max: ₹{{ number_format($item->total_remaining, 2) }})
                                                </div>
                                            </div>
                                            
                                            {{-- <div class="mb-3">
                                                <label class="form-label">Payment Method</label>
                                                <select class="form-select" name="payment_method">
                                                    <option value="cash">Cash</option>
                                                    <option value="bank_transfer">Bank Transfer</option>
                                                    <option value="cheque">Cheque</option>
                                                    <option value="online">Online Payment</option>
                                                </select>
                                            </div> --}}
                                            
                                            {{-- <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="confirm_foreclose" required>
                                                <label class="form-check-label" for="confirm_foreclose">
                                                    I confirm that I want to foreclose this loan and understand this action cannot be undone.
                                                </label>
                                            </div> --}}
                                            
                                            <button type="submit" class="btn btn-danger w-100" id="forecloseBtn">
                                                <i class="ti ti-file me-2"></i>Foreclose Loan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    @endif

                    @endforeach


                    <a href="{{ route('admin.loan-assign-list') }}" class="btn btn-primary mt-3">Back</a>

                </div>

            </div>
        </div>
    </div>
<script>
document.getElementById('forecloseForm').addEventListener('submit', function(e) {
    const amount = parseFloat(document.getElementById('foreclose_amount').value);
    const remaining = parseFloat({{ $item->total_remaining }});
    const confirmCheck = document.getElementById('confirm_foreclose');
    
    if (!confirmCheck.checked) {
        e.preventDefault();
        alert('Please confirm foreclosure by checking the checkbox.');
        return false;
    }
    
    if (amount > remaining) {
        e.preventDefault();
        alert('Foreclosure amount cannot exceed remaining balance.');
        return false;
    }
    
    if (amount <= 0) {
        e.preventDefault();
        alert('Please enter a valid foreclosure amount.');
        return false;
    }
    
    return confirm(`Are you sure you want to foreclose this loan?\n\nAmount: ₹${amount.toFixed(2)}\n\nThis action cannot be undone.`);
});
</script>
@endsection