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
    </style>
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Edit Loan Assign</h3>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Center Form -->
    <div class="row center-page">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <form action="{{ route('admin.loan-assign-update', $original->id) }}" method='POST' id="loanAssignForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label"> Name of Customer <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $customersname) }}" readonly>
                                @error('client_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Loan Assign ID <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                        <input type="number" name="loan_assign_id" class="form-control" value="{{old('loan_assign_id', $original->loanAssign_id) }}" readonly>
                                @error('loan_assign_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Customer Type <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="client_type_id" id="client_type_id" disabled>
                                    <option value="">Select</option>
                                    <option value="1" {{ old('client_type_id', $original->client_type) == 1 ? 'selected' : '' }}>Old</option>
                                    <option value="2" {{ old('client_type_id', $original->client_type) == 2 ? 'selected' : '' }}>New</option>

                                </select>
                                <div class="error-container" id="client_type_id_error"></div>
                                @error('collection_type_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Address <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $original->address) }}</textarea>
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Phone <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $original->phone) }}">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">City <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="city" value="{{ old('city', $original->city) }}" class="form-control">
                                @error('city')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Pincode <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="pincode" value="{{ old('pincode', $original->pincode) }}" class="form-control">
                                @error('pincode')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Loan Type <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="loan_type_id" disabled>
                                    <option value="">Select</option>
                                    @foreach ($loan as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('loan_type_id', $original->loan_type_id) == $item->id ? 'selected' : '' }}>
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
                                <select class="form-control select" name="document_type_id[]" id="document_type_id" multiple disabled>
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
                                <select class="form-control select" name="collection_type_id" id="collection_type_id" disabled>
                                    <option value="">Select</option>
                                    <option value="1" {{ old('collection_type_id', $original->collection_type_id) == 1 ? 'selected' : '' }}>Daily</option>
                                    <option value="2" {{ old('collection_type_id', $original->collection_type_id) == 2 ? 'selected' : '' }}>Weekly</option>
                                    <option value="3" {{ old('collection_type_id', $original->collection_type_id) == 3 ? 'selected' : '' }}>Monthly</option>
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
                                <select class="form-control select" name="daily_duedays_id" id="daily_duedays_id" disabled>
                                    <option value="">Select</option>
                                    <option value="1" {{ old('daily_duedays_id', $original->daily_duedays_id) == 1 ? 'selected' : '' }}>Monday</option>
                                    <option value="2" {{ old('daily_duedays_id', $original->daily_duedays_id) == 2 ? 'selected' : '' }}>Tuesday</option>
                                    <option value="3" {{ old('daily_duedays_id', $original->daily_duedays_id) == 3 ? 'selected' : '' }}>Wednesday</option>
                                    <option value="4" {{ old('daily_duedays_id', $original->daily_duedays_id) == 4 ? 'selected' : '' }}>Thursday</option>
                                    <option value="5" {{ old('daily_duedays_id', $original->daily_duedays_id) == 5 ? 'selected' : '' }}>Friday</option>
                                    <option value="6" {{ old('daily_duedays_id', $original->daily_duedays_id) == 6 ? 'selected' : '' }}>Saturday</option>
                                    <option value="7" {{ old('daily_duedays_id', $original->daily_duedays_id) == 7 ? 'selected' : '' }}>Sunday</option>
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
                                <select class="form-control select" name="week_duedays_id" id="week_duedays_id" disabled>
                                    <option value="">Select</option>
                                    <option value="1" {{ old('week_duedays_id', $original->week_duedays_id) == 1 ? 'selected' : '' }}>Monday</option>
                                    <option value="2" {{ old('week_duedays_id', $original->week_duedays_id) == 2 ? 'selected' : '' }}>Tuesday</option>
                                    <option value="3" {{ old('week_duedays_id', $original->week_duedays_id) == 3 ? 'selected' : '' }}>Wednesday</option>
                                    <option value="4" {{ old('week_duedays_id', $original->week_duedays_id) == 4 ? 'selected' : '' }}>Thursday</option>
                                    <option value="5" {{ old('week_duedays_id', $original->week_duedays_id) == 5 ? 'selected' : '' }}>Friday</option>
                                    <option value="6" {{ old('week_duedays_id', $original->week_duedays_id) == 6 ? 'selected' : '' }}>Saturday</option>
                                    <option value="7" {{ old('week_duedays_id', $original->week_duedays_id) == 7 ? 'selected' : '' }}>Sunday</option>
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
                                <input type="date" name="loanassign_date"  value="{{ old('loanassign_date', $original->loanassign_date ? \Carbon\Carbon::parse($original->loanassign_date)->format('Y-m-d') : '') }}" readonly class="form-control">
                                <div class="error-container" id="loanassign_date_error"></div>
                                @error('loanassign_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group" id="monthly_div">
                            <label class="col-lg-3 form-label">Monthly Due Date<span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="date" name="monthlydue_date" class="form-control" value="{{ old('monthlydue_date', $original->monthlydue_date ? \Carbon\Carbon::parse($original->monthlydue_date)->format('Y-m-d') : '') }}" readonly>
                                <div class="error-container" id="monthlydue_date_error"></div>
                                @error('monthlydue_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        

                        <div class="row mb-3 form-group" id="loan_tenure_div">
                            <label class="col-lg-3 form-label">Loan tenure (in months)<span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="loan_tenure" id="loan_tenure" class="form-control" value="{{ old('loan_tenure', $original->loan_tenure) }}" readonly>
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
                                        <option value="{{ $item->id }}"
                                            {{ old('branch_id', $original->branch_id ?? '') == $item->id ? 'selected' : '' }}>
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
                                            {{ old('route_id', $original->route_id ?? '') == $item->id ? 'selected' : '' }}>
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
                                <input type="number" name="loan_amount" id="loan_amount" value="{{ old('loan_amount', $original->loan_amount) }}" class="form-control" readonly>
                                @error('loan_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Interest <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="interest_id" id="interest_id" disabled>
                                    <option value="">Select</option>
                                    @foreach ($interests as $item)
                                        <option value="{{ $item->id }}"
                                            data-rate="{{ $item->interest_id }}"
                                            data-collection-type="{{ $item->collection_type }}"
                                            {{ old('interest_id', $original->interest_id) == $item->id ? 'selected' : '' }}>
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
                                <input type="text" id="daily_emi" class="form-control" name="daily_emi" value="{{ old('daily_emi',$original->daily_emi) }}" readonly>
                                @error('daily_emi')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            </div>

                        </div>

                        <div id="weeklyemi_div" class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Weekly Collection <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" id="weekly_emi"  class="form-control" name="weekly_emi" value="{{ old('weekly_emi',$original->weekly_emi) }}"  readonly>
                                @error('weekly_emi')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            </div>

                        </div>

                        <div id="monthlyemi_div" class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Monthly EMI <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="number" name="monthly_emi" id="monthly_emi" class="form-control" readonly value="{{ old('monthly_emi', $original->monthly_emi) }}">
                                @error('monthly_emi')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div id="totaldistub_div" class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Total Distribution  <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="number" name="total_distribution" id="total_distribution" class="form-control" value="{{ old('total_distribution', $original->total_distribution) }}"readonly>
                                @error('total_distribution')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div id="totalinterest_div" class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Total Interest <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="number" name="total_interest" id="total_interest" class="form-control" readonly value="{{ old('total_interest', $original->total_interest) }}">
                                @error('total_interest')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div id="totalpayable_div" class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Total Payable Amount <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="number" name="total_payableamt" id="total_payableamt" class="form-control" readonly value="{{ old('total_payableamt', $original->total_payableamt) }}">
                                @error('total_payableamt')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('admin.loan-assign-list') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="{{ asset(path: 'assets/js/loan-assign-validation.js') }}"></script>
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
    alert(finalDate);
    $("#weeklyemi_date").val(finalDate);
   

});
        // Initialize the validation
        initializeLoanAssignValidation();

        // Initialize UI based on collection type
        initializeEditForm();
    });

   function initializeEditForm() {
        // Get the collection type from the selected option or fallback
        var collectionTypeId = $("#collection_type_id").val();
        if (!collectionTypeId) {
            collectionTypeId = "{{ old('collection_type_id', $original->collection_type_id) }}";
        }
        
        console.log("Collection Type ID:", collectionTypeId); // For debugging
        
        // Hide all sections first
        $('#daily_div').hide();
        $('#weekly_div').hide();
        $('#monthly_div').hide();
        $('#dailyemi_div').hide();
        $('#weeklyemi_div').hide();
        $('#monthlyemi_div').hide();
        $('#totaldistub_div').hide();
        $('#totalinterest_div').hide();
        $('#totalpayable_div').hide();
        
        // Show relevant sections based on collection type
        if(collectionTypeId == 1) {
            // Daily
            $('#daily_div').show();
            $('#dailyemi_div').show();
            $('#totalinterest_div').show();
            $('#totalpayable_div').show();
        }
        else if(collectionTypeId == 2) {
            // Weekly - Hide total interest and total payable, show total distribution
            $('#weekly_div').show();
            $('#weeklyemi_div').show();
            $('#totaldistub_div').show();
            $('#totalinterest_div').show();
            $('#totalpayable_div').hide();
        }
        else if(collectionTypeId == 3) {
            // Monthly
            $('#monthly_div').show();
            $('#monthlyemi_div').show();
            $('#totalinterest_div').show();
            $('#totalpayable_div').show();
        }
        
        // Always show these sections
        $('#loan_tenure_div').show();
        
        // Trigger validation update if validator exists
        if ($("#loanAssignForm").data('validator')) {
            var validator = $("#loanAssignForm").validate();
            validator.element("#collection_type_id");
        }
    }

    </script>
@endsection
