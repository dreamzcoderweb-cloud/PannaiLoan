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
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }
        .dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

#customer_results {
    position: absolute;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: -1px;
}
    </style>
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Loan Assign Create Form</h3>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Center Form -->
    <div class="row center-page">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <form action="{{ route('admin.loan-assign-store') }}" method='POST' id="loanAssignForm">
                        @csrf
                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label"> Name of Customer <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text"
                                       name="customer_search"
                                       id="customer_search"
                                       class="form-control"
                                       placeholder="Type customer name or phone..."
                                       value="{{ old('customer_search') }}"
                                       autocomplete="off">

                                <!-- Hidden field to store the selected customer ID -->
                                <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id') }}">



                                <!-- Search results dropdown -->
                                <div id="customer_results" class="dropdown-menu" style="display: none; max-height: 200px; overflow-y: auto; width: 100%;">
                                    <!-- Search results will appear here -->
                                </div>

                                <div class="error-container" id="client_id_error"></div>
                                @error('client_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Loan Assign ID <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                        <input type="number" name="loan_assign_id"  id="loan_assign_id" class="form-control" value="{{old('loan_assign_id')}}">
                                @error('loan_assign_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Customer Type <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="client_type_id" id="client_type_id">
                                    <option value="">Select</option>
                                    <option value="1" {{old('client_type_id') == 1 ? 'selected' : ''}}>Old</option>
                                    <option value="2" {{old('client_type_id') == 2 ? 'selected' : ''}}>New</option>
                                </select>
                                <div class="error-container" id="client_type_id_error"></div>
                                @error('client_type_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Address <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <textarea name="address" class="form-control" rows="2">{{old('address')}}</textarea>
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Phone <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="phone" class="form-control" value="{{old('phone')}}">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">City <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="city" value="{{old('city')}}" class="form-control">
                                @error('city')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Pincode <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="pincode" value="{{old('pincode')}}" class="form-control">
                                @error('pincode')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Loan Type <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="loan_type_id">
                                    <option value="">Select</option>
                                    @foreach ($loan as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('loan_type_id', $original->id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->loan_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error-container" id="loan_type_id_error"></div>
                                @error('loan_type_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Document Type <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="document_type_id[]" id="document_type_id" multiple>
                                    <option ></option>
                                    @foreach ($documents as $item)
                                        <option value="{{ $item->id }}"
                                            {{ in_array($item->id, old('document_type_id', $originalDocumentIds ?? [])) ? 'selected' : '' }}>
                                            {{ $item->document_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error-container" id="document_type_id_error"></div>
                                @error('document_type_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Loan Collection Type <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="collection_type_id" id="collection_type_id">
                                    <option value="">Select</option>
                                    <!--<option value="1" {{old('collection_type_id') == 1 ? 'selected' : ''}}>Daily</option>-->
                                    <option value="2" {{old('collection_type_id') == 2 ? 'selected' : ''}}>Weekly</option>
                                    <option value="3" {{old('collection_type_id') == 3 ? 'selected' : ''}}>Monthly</option>
                                </select>
                                <div class="error-container" id="collection_type_id_error"></div>
                                @error('collection_type_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group" id="daily_div">
                            <label class="col-lg-3 form-label">Daily Due Days<span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="daily_duedays_id" id="daily_duedays_id">
                                    <option value="">Select</option>
                                    <option value="1" {{old('daily_duedays_id') == 1 ? 'selected' : ''}}>Monday</option>
                                    <option value="2" {{old('daily_duedays_id') == 2 ? 'selected' : ''}}>Tuesday</option>
                                    <option value="3" {{old('daily_duedays_id') == 3 ? 'selected' : ''}}>Wednesday</option>
                                    <option value="4" {{old('daily_duedays_id') == 4 ? 'selected' : ''}}>Thursday</option>
                                    <option value="5" {{old('daily_duedays_id') == 5 ? 'selected' : ''}}>Friday</option>
                                    <option value="6" {{old('daily_duedays_id') == 6 ? 'selected' : ''}}>Saturday</option>
                                    <option value="7" {{old('daily_duedays_id') == 7 ? 'selected' : ''}}>Sunday</option>
                                </select>
                                <div class="error-container" id="daily_duedays_id_error"></div>
                                @error('daily_duedays_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group" id="weekly_div">
                            <label class="col-lg-3 form-label">Weekly Due Days<span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="week_duedays_id" id="week_duedays_id">
                                    <option value="">Select</option>
                                    <option value="1" {{old('week_duedays_id') == 1 ? 'selected' : ''}}>Monday</option>
                                    <option value="2" {{old('week_duedays_id') == 2 ? 'selected' : ''}}>Tuesday</option>
                                    <option value="3" {{old('week_duedays_id') == 3 ? 'selected' : ''}}>Wednesday</option>
                                    <option value="4" {{old('week_duedays_id') == 4 ? 'selected' : ''}}>Thursday</option>
                                    <option value="5" {{old('week_duedays_id') == 5 ? 'selected' : ''}}>Friday</option>
                                    <option value="6" {{old('week_duedays_id') == 6 ? 'selected' : ''}}>Saturday</option>
                                    <option value="7" {{old('week_duedays_id') == 7 ? 'selected' : ''}}>Sunday</option>
                                </select>
                                <input type="hidden" id="weeklyemi_date" name="weeklyemi_date">
                                <div class="error-container" id="week_duedays_id_error"></div>
                                @error('week_duedays_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Loan Assign Date<span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="date" name="loanassign_date" class="form-control">
                                <div class="error-container" id="loanassign_date_error"></div>
                                @error('loanassign_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group" id="monthly_div">
                            <label class="col-lg-3 form-label">Monthly Due Date<span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="date" name="monthlydue_date" class="form-control">
                                <div class="error-container" id="monthlydue_date_error"></div>
                                @error('monthlydue_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group" id="loan_tenure_div">
                            <label class="col-lg-3 form-label">Loan tenure (in months)<span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="loan_tenure" id="loan_tenure" class="form-control">
                                @error('loan_tenure')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Select Branch <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select name="branch_id" class="form-control select">
                                    <option value="">Select</option>
                                    @foreach ($branches as $item)
                                        <option value="{{ $item->id }}" data-rate="{{ $item->branch_name }}"
                                            {{ old('branch_id', $original->id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->branch_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error-container" id="branch_id_error"></div>
                                @error('branch_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Select Routes <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select name="route_id" class="form-control select">
                                    <option value="">Select</option>
                                    @foreach ($routes as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('route_id', $original->id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->route_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error-container" id="route_id_error"></div>
                                @error('route_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Loan Amount <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="number" name="loan_amount" id="loan_amount" value="{{old('loan_amount')}}" class="form-control">
                                @error('loan_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Interest <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="interest_id" id="interest_id">
                                    <option value="">Select</option>
                                    @foreach ($interests as $item)
                                        <option value="{{ $item->id }}" data-rate="{{ $item->interest_id }}" data-collection-type="{{ $item->collection_type }}"
                                            {{ old('interest_id', $original->id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->interest_id }} ({{ $item->collection_type == 1 ? 'Daily' : ($item->collection_type == 2 ? 'Weekly' : 'Monthly') }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error-container" id="interest_id_error"></div>
                                @error('interest_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div id="dailyemi_div" class="row mb-3 form-group">
                            <label  class="col-lg-3 form-label">Daily EMI <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" id="daily_emi" class="form-control" name="daily_emi" readonly>
                                @error('daily_emi')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            </div>

                        </div>

                        <div id="weeklyemi_div" class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Weekly Collection <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" id="weekly_emi"  class="form-control" name="weekly_emi" readonly>
                                @error('weekly_emi')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            </div>

                        </div>



                        <div id="monthlyemi_div" class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Monthly EMI <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="number" name="monthly_emi" id="monthly_emi" class="form-control" readonly>
                                @error('monthly_emi')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        
                        <div id="totaldistub_div" class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Total Distribution  <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="number" name="total_distribution" id="total_distribution" class="form-control" readonly>
                                @error('total_distribution')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div id="totalinterest_div" class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Total Interest <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="number" name="total_interest" id="total_interest" class="form-control" readonly>
                                @error('total_interest')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div id="totalpayable_div" class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Total Payable Amount <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="number" name="total_payableamt" id="total_payableamt" class="form-control" readonly>
                                @error('total_payableamt')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- In your create.blade.php, replace the entire script section with: --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="{{ asset('assets/js/loan-assign-validation.js') }}"></script>
<script>

$(document).ready(function () {

    //week_duedays_id on change event
    $('#week_duedays_id').on('change', function () {

    var dayId = parseInt($(this).val()); // 1 = Monday ... 7 = Sunday
    if (!dayId) return;

    // JS getDay(): Sunday = 0, Monday = 1, ...
    let today = new Date();
    let todayDay = today.getDay();

    // Convert Sunday(0) to 7
    todayDay = (todayDay === 0) ? 7 : todayDay;

    // Difference between selected day and today
    let diff = dayId - todayDay;

    // Get selected weekday date
    let selectedDate = new Date(today);
    selectedDate.setDate(today.getDate() + diff);

    // Format YYYY-MM-DD
    let yyyy = selectedDate.getFullYear();
    let mm = String(selectedDate.getMonth() + 1).padStart(2, '0');
    let dd = String(selectedDate.getDate()).padStart(2, '0');

    let finalDate = `${yyyy}-${mm}-${dd}`;
    $("#weeklyemi_date").val(finalDate);
   

});
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Customer search functionality
    let searchTimeout;
    $('#customer_search').on('keyup', function() {
        const searchTerm = $(this).val().trim();
        console.log('Search term:', searchTerm);

        // Clear previous timeout
        clearTimeout(searchTimeout);

        // Show/hide results based on input
        if (searchTerm.length < 2) {
            $('#customer_results').hide().empty();
            return;
        }

        // Set timeout to avoid too many requests
        searchTimeout = setTimeout(() => {
            searchCustomers(searchTerm);
        }, 300);
    });

    // Function to search customers
    function searchCustomers(searchTerm) {
        console.log('Making AJAX request for:', searchTerm);
        
        $.ajax({
            url: "{{ route('admin.loansearch-customers') }}",
            type: 'GET',
            data: {
                search: searchTerm
            },
            success: function (response) {
               // console.log('Search response:', response);
                
                const results = $('#customer_results');
                results.empty();

                if (response.customers && response.customers.length > 0) {
                    $.each(response.customers, function (index, customer) {
                        const item = $('<a>')
                            .addClass('dropdown-item')
                            .attr('href', '#')
                            .data('customer-id', customer.id)
                            .data('customer-name', customer.name)
                            .data('customer-phone', customer.phone)
                            .html(`${customer.name} - ${customer.phone}`);

                        item.on('click', function (e) {
                            e.preventDefault();
                            selectCustomer(
                                $(this).data('customer-id'),
                                $(this).data('customer-name'),
                                $(this).data('customer-phone')
                            );
                        });

                        results.append(item);
                    });

                    results.show();
                } else {
                    results.html('<div class="dropdown-item text-muted">No customers found</div>').show();
                }
            },
            error: function(xhr, status, error) {
              // console.error('Search error:', error);
                $('#customer_results').html('<div class="dropdown-item text-danger">Error searching. Please try again.</div>').show();
            }
        });
    }

    // Function to handle customer selection
    function selectCustomer(id, name, phone) {
        console.log('Customer selected:', {id, name, phone});
        
        // Set hidden field value
        $('#client_id').val(id);

        // Update phone field if it's empty
        if (!$('input[name="phone"]').val()) {
            $('input[name="phone"]').val(phone);
        }

        // Hide search box and results
        $('#customer_search').val(name);
        $('#customer_results').hide().empty();
    }

    // Hide results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#customer_search, #customer_results').length) {
            $('#customer_results').hide();
        }
    });

    // Rest of your existing code...
    $('#document_type_id').select2({
        placeholder: "Select Document Type",
        allowClear: true,
        width: '100%'
    });

    initializeLoanAssignValidation();
    
    $('#daily_div').hide();
    $('#weekly_div').hide();
    $('#monthly_div').hide();
    $('#loan_tenure_div').show();
    $('#monthlyemi_div').hide();
    $('#dailyemi_div').hide();
    $('#weeklyemi_div').hide();
    $('#totaldistub_div').hide();
    $('#totalinterest_div').hide();
    $('#totalpayable_div').hide();
});
</script>
@endsection
