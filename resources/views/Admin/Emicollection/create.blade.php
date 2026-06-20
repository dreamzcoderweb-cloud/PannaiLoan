@extends('site.layouts.app')

@section('title', 'EMI Collection')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col">
            <h3 class="page-title">EMI Collection Create</h3>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.emicollection-store') }}" id="emiForm" method="POST">
                    @csrf
                    <input type="hidden" name="loan_assign_id" id="loan_assign_id">

                    {{-- SEARCH CUSTOMER --}}
                    <div class="row mb-3">
                        <label class="col-lg-3 form-label">
                            Search Customer <span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-9 position-relative">
                            <input type="text"
                                   id="customerSearch"
                                   class="form-control"
                                   placeholder="Type customer name or phone number"
                                   autocomplete="off">
                            <input type="hidden" name="client_id" id="client_id">

                            {{-- Search Results Container --}}
                            <div id="searchResults" class="search-results-container">
                                <!-- Results will be populated here -->
                            </div>

                            {{-- Selected Customer Display --}}
                            <div id="selectedCustomer" class="mt-2 selected-customer-card">
                                <!-- Selected customer will be shown here -->
                            </div>
                        </div>
                    </div>

                    {{-- LOAN SELECTION TABLE (Hidden by default) --}}
                    <div class="row mb-3" id="loanSelectionSection" style="display: none;">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white py-2">
                                    <h6 class="mb-0"><i class="ti ti-list me-1"></i> Customer Loan Details</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="loanListTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Loan Assign ID</th>
                                                    <th>Loan Type</th>
                                                    <th>Interest Rate</th>
                                                    <th>Total Amount</th>
                                                    <th>Assigned Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="loanListBody">
                                                <!-- Loans will be populated here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- EMI TABLE --}}
                    <div id="emiSection" style="display:none;">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered" id="emiTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="20%" class="text-center" id="dueDateHeader">
                                            <i class="ti ti-calendar me-1"></i>
                                            <span id="dueDateText">Monthly Due Date</span>
                                        </th>
                                        <th width="20%" class="text-center" id="EmiHeader">
                                            <span id="emiText">Monthly EMI</span>
                                        </th>
                                        <th width="20%" class="text-center">
                                            Total Payable Amount
                                        </th>
                                        <th width="20%" class="text-center">
                                            Remaining Amount
                                        </th>
                                        <th width="15%" class="text-center">
                                            Status
                                        </th>
                                        <th width="5%" class="text-center">
                                            <button type="button" id="addEmiRow" class="btn btn-success btn-sm">
                                                <i class="ti ti-plus"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="emiTbody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            Save EMI Collection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    let searchTimeout;
    let collectionType = null;
    let dueDay = null;
    let rowCounter = 0;

    // Initialize - hide all sections
    $('#loanSelectionSection').hide();
    $('#emiSection').hide();
    $('#selectedCustomer').hide();

    // Hide all sections function
    function hideAllSections() {
        $('#loanSelectionSection').hide();
        $('#emiSection').hide();
        $('#selectedCustomer').hide();
        $('#submitBtn').prop('disabled', true);
        $('#loan_assign_id').val('');
    }

    // Search customers on keyup
    $('#customerSearch').on('keyup', function() {
        clearTimeout(searchTimeout);

        const searchTerm = $(this).val().trim();

        // Hide search results
        $('#searchResults').hide();

        if (searchTerm.length < 2) {
            // Clear selection if search term is empty
            if (searchTerm.length === 0) {
                hideAllSections();
                $('#loanSelectionSection').hide();
                $('#client_id').val('');
            }
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route("admin.search-customers") }}',
                type: 'GET',
                data: {
                    search: searchTerm
                },
                beforeSend: function() {
                    // Show loading in search results
                    $('#searchResults').html(`
                        <div class="list-group">
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    <small class="text-muted">Searching...</small>
                                </div>
                            </div>
                        </div>
                    `).show();
                },
                success: function(response) {
                    const resultsContainer = $('#searchResults');
                    resultsContainer.empty();

                    if (response.customers.length > 0) {
                        const resultsList = $('<div class="list-group search-results-list"></div>');

                        response.customers.forEach(function(customer) {
                            const item = `
                                <a href="#" class="list-group-item list-group-item-action customer-item"
                                   data-id="${customer.id}"
                                   data-name="${customer.name}"
                                   data-phone="${customer.phone}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 customer-name">${customer.name}</h6>
                                        <small class="text-muted customer-id">ID: ${customer.id}</small>
                                    </div>
                                    <p class="mb-1 text-muted customer-phone">
                                        <i class="ti ti-phone"></i> ${customer.phone}
                                    </p>
                                </a>
                            `;
                            resultsList.append(item);
                        });

                        resultsContainer.append(resultsList);
                        resultsContainer.show();

                        // Hide loan select section when search results are shown
                        $('#loanSelectionSection').hide();
                    } else {
                        // No customers found - hide both sections
                        hideAllSections();

                        resultsContainer.html(`
                            <div class="list-group">
                                <div class="list-group-item list-group-item-warning">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-alert-circle me-2 text-warning"></i>
                                        <div>
                                            <p class="mb-0 fw-semibold">No customers found</p>
                                            <small class="text-muted">Try a different name or phone number</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                        resultsContainer.show();
                    }
                },
                error: function(xhr) {
                    // On error - hide both sections
                    hideAllSections();

                    $('#searchResults').html(`
                        <div class="list-group">
                            <div class="list-group-item list-group-item-danger">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-x-circle me-2 text-danger"></i>
                                    <div>
                                        <p class="mb-0 fw-semibold">Search Error</p>
                                        <small class="text-muted">Unable to search customers. Please try again.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).show();
                }
            });
        }, 300);
    });

    // Handle customer selection from search results
    $(document).on('click', '.customer-item', function(e) {
        e.preventDefault();

        const customerId = $(this).data('id');
        const customerName = $(this).data('name');
        const customerPhone = $(this).data('phone');

        // Set hidden input
        $('#client_id').val(customerId);

        // Display selected customer
        const selectedCard = `
            <div class="card border-success">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-success">
                                <i class="ti ti-user-check me-1"></i>
                                ${customerName}
                            </h6>
                            <small class="text-muted">
                                <i class="ti ti-phone me-1"></i>${customerPhone}
                            </small>
                        </div>
                        <div>
                            <small class="text-muted me-2">ID: ${customerId}</small>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clearSelection">
                                <i class="ti ti-x"></i> Change
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#selectedCustomer').html(selectedCard).show();

        // Clear search input and hide results
        $('#customerSearch').val('');
        $('#searchResults').hide();

        // Hide customer select section
        $('#loanSelectionSection').hide();

        // Load Client Loans
        fetchClientLoans(customerId);
    });

    // Clear selection (using event delegation for dynamic button)
    $(document).on('click', '#clearSelection', function() {
        // Reset everything
        $('#client_id').val('');
        $('#loan_assign_id').val('');
        $('#selectedCustomer').empty().hide();
        $('#customerSearch').val('').focus();
        $('#emiSection').hide();
        $('#submitBtn').prop('disabled', true);
        clearAllValidationErrors();

        // Hide loan section
        $('#loanSelectionSection').hide();
    });



    // Click outside to close search results
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#customerSearch, #searchResults').length) {
            $('#searchResults').hide();
        }
    });

    // Function to load Client Loans
    function fetchClientLoans(clientId) {
        $.ajax({
            url: '{{ route("admin.get-client-loans", "") }}/' + clientId,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                // Show loading indicator
                $('#selectedCustomer .card-body').append(`
                    <div class="mt-2 loading-indicator">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <small class="text-muted ms-2">Fetching loans...</small>
                    </div>
                `);
            },
            success: function(response) {
                // Remove loading indicator
                $('#selectedCustomer .loading-indicator').remove();

                if (response.loans && response.loans.length > 0) {
                    const tbody = $('#loanListBody');
                    tbody.empty();

                    response.loans.forEach(function(loan) {
                        const tr = `
                            <tr>
                                <td>${loan.loanAssign_id}</td>
                                <td><span class="badge bg-primary">${loan.loan_type}</span></td>
                                <td>${loan.interest_rate}</td>
                                <td>₹${parseFloat(loan.amount).toFixed(2)}</td>
                                <td>${loan.date ? formatDate(loan.date) : 'N/A'}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info text-white select-loan-btn" 
                                            data-id="${loan.id}">
                                        Select
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.append(tr);
                    });

                    $('#loanSelectionSection').show();
                    // Clean up EMI section if it was open
                    $('#emiSection').hide();
                    $('#submitBtn').prop('disabled', true);
                    
                } else {
                    // No loans found
                    $('#highlight-error').remove();
                    $('#selectedCustomer .card-body').append(`
                        <div id="highlight-error" class="alert alert-warning mt-2 mb-0 py-1">
                            <small><i class="ti ti-alert-triangle me-1"></i> No active loans found for this customer.</small>
                        </div>
                    `);
                    $('#loanSelectionSection').hide();
                    $('#emiSection').hide();
                }
            },
            error: function(xhr) {
                console.error(xhr);
                $('#selectedCustomer .loading-indicator').remove();
                $('#loanSelectionSection').hide();
                
                 $('#highlight-error').remove();
                 $('#selectedCustomer .card-body').append(`
                    <div id="highlight-error" class="alert alert-danger mt-2 mb-0 py-1">
                        <small><i class="ti ti-x-circle me-1"></i> Error loading loans.</small>
                    </div>
                `);
            }
        });
    }

    // Handle Loan Selection
    $(document).on('click', '.select-loan-btn', function() {
        const loanId = $(this).data('id');
        $('#loan_assign_id').val(loanId);
        
        // Highlight selected row
        $('#loanListBody tr').removeClass('table-active');
        $(this).closest('tr').addClass('table-active');

        fetchLoanEmiDetails(loanId);
    });

    function formatDate(date) {
     const d = new Date(date);
     const day = String(d.getDate()).padStart(2, '0');
     const month = String(d.getMonth() + 1).padStart(2, '0');
     const year = d.getFullYear();
     return `${day}-${month}-${year}`;
    }


    // Function to load EMI details for a specific loan
    function fetchLoanEmiDetails(loanId) {
        $.ajax({
            url: '{{ route("admin.get-loan-emi-details", "") }}/' + loanId,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                // Show loading overlay on EMI section if visible, or just wait
                 // We can show a small loader in the button invoked if we want, but simple is ok
                 $('#emiSection').css('opacity', '0.5');
            },
            success: function(response) {
                 $('#emiSection').css('opacity', '1');

                if (response.emi && response.emi.length > 0) {
                    collectionType = response.emi[0].collection_type_id;
                    dueDay = response.emi[0].due_day;

                    // Update table headers
                    updateTableHeaders(collectionType);

                    // Clear existing rows
                    $('#emiTbody').empty();
                    rowCounter = 0;

                    // Add existing EMI rows
                    response.emi.forEach(function(emi) {
                        addEmiRow(emi, false);
                    });

                    $('#emiSection').show();
                    $('#submitBtn').prop('disabled', false);
                    clearAllValidationErrors();
                    
                    // Scroll to EMI section
                    $('html, body').animate({
                        scrollTop: $("#emiSection").offset().top - 100
                    }, 500);

                } else {
                    // This shouldn't happen often if loan exists, but just in case
                     alert('No EMI schedule found for this loan.');
                     $('#emiSection').hide();
                }
            },
            error: function(xhr) {
                console.error(xhr);
                 $('#emiSection').css('opacity', '1');
                 alert('Error fetching EMI details.');
            }
        });
    }

    // Add new EMI row
    $('#addEmiRow').click(function() {
        if (!collectionType) {
            showValidationError('Please select a customer first', 'customerSearch');
            return;
        }

        const lastRow = $('#emiTbody tr:last');
        let lastRemaining = 0;
        let totalPayable = 0;
        let emiAmount = 0;

        if (lastRow.length > 0) {
            lastRemaining = parseFloat(lastRow.find('input[name^="emi[remaining_amount]"]').val()) || 0;
            totalPayable = parseFloat(lastRow.find('input[name^="emi[payable_amount]"]').val()) || 0;
            emiAmount = parseFloat(lastRow.find('input[name^="emi[emi_amount]"]').val()) || 0;
        }

        const newEmi = {
            due_date: (collectionType == 3 || collectionType == 2) ? '' : null,
            emi_amount: emiAmount,
            payable_amount: totalPayable,
            remaining: lastRemaining > 0 ? lastRemaining : totalPayable,
            loan_type_id: getLoanTypeId(),
            status: 'Pending',
            flag: 2,
            collection_type_id: collectionType,
            due_day: dueDay
        };

        addEmiRow(newEmi, true);
    });

    function addEmiRow(emiData, isNew = false) {
        rowCounter++;
        const rowId = 'emi-row-' + rowCounter;

        let dueDateInput = '';
        let weeklyDueDayHidden = '';

        if (collectionType == 3 || collectionType == 2) {
            dueDateInput = `<input type="date" class="form-control due-date"
                                  name="emi[due_date][]"
                                  value="${emiData.due_date || ''}"
                                  data-validation="required|date"
                                  data-validation-error-msg="Due date is required and must be a valid date">`;
            if (collectionType == 2) {
                weeklyDueDayHidden = `<input type="hidden" name="emi[due_day][]" value="${emiData.due_day || ''}">`;
            }
        } else if (collectionType == 1) {
            dueDateInput = `<select class="form-control due-day" name="emi[due_day][]"
                              data-validation="required|in:1,2,3,4,5,6,7"
                              data-validation-error-msg="Please select a valid due day">
                <option value="">Select Day</option>
                <option value="1" ${emiData.due_day == 1 ? 'selected' : ''}>Monday</option>
                <option value="2" ${emiData.due_day == 2 ? 'selected' : ''}>Tuesday</option>
                <option value="3" ${emiData.due_day == 3 ? 'selected' : ''}>Wednesday</option>
                <option value="4" ${emiData.due_day == 4 ? 'selected' : ''}>Thursday</option>
                <option value="5" ${emiData.due_day == 5 ? 'selected' : ''}>Friday</option>
                <option value="6" ${emiData.due_day == 6 ? 'selected' : ''}>Saturday</option>
                <option value="7" ${emiData.due_day == 7 ? 'selected' : ''}>Sunday</option>
            </select>`;
        }

        const remainingAmount = emiData.remaining || emiData.payable_amount;

        const row = `
        <tr id="${rowId}">
            <td>
                <input type="hidden" name="loan_type_id[]" value="${emiData.loan_type_id || ''}"
                       data-validation="required|numeric"
                       data-validation-error-msg="Loan type is required">
                ${weeklyDueDayHidden}
                ${dueDateInput}
            </td>
            <td>
                <input type="number" class="form-control emi-amount"
                       name="emi[emi_amount][]"
                       value="${emiData.emi_amount || ''}"
                       step="0.01"
                       min="1"
                       ${isNew ? '' : ''}
                       data-validation="required|number|min:1"
                       data-validation-error-msg="EMI amount must be at least 1">
            </td>
            <td>
                <input type="number" class="form-control payable-amount"
                       name="emi[payable_amount][]"
                       value="${emiData.payable_amount || ''}"
                       readonly
                       data-validation="required|number|min:0"
                       data-validation-error-msg="Payable amount is required">
            </td>
            <td>
                <input type="number" class="form-control remaining-amount"
                       name="emi[remaining_amount][]"
                       value="${remainingAmount}"
                       readonly
                       data-validation="required|number|min:0"
                       data-validation-error-msg="Remaining amount is required">
            </td>
            <td>
                <select class="form-control status-select" name="emi[status][]"
                        data-validation="required|in:Pending,Paid"
                        data-validation-error-msg="Please select a valid status">
                    <option value="">Select Status</option>
                    <option value="Pending" ${emiData.status == 'Pending' ? 'selected' : ''}>Pending</option>
                    <option value="Paid" ${emiData.status == 'Paid' ? 'selected' : ''}>Paid</option>
                </select>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeRow('${rowId}')">
                    <i class="ti ti-trash"></i>
                </button>
            </td>
        </tr>`;

        $('#emiTbody').append(row);

        if (isNew && $('#emiTbody tr').length > 1) {
            updateRemainingAmounts();
        }

        // Clear validation for this row
        clearRowValidation(rowId);
    }

    function updateRemainingAmounts() {
        let runningRemaining = null;

        $('#emiTbody tr').each(function() {
            const payable = parseFloat($(this).find('.payable-amount').val()) || 0;
            const emi = parseFloat($(this).find('.emi-amount').val()) || 0;
            const status = $(this).find('.status-select').val();

            if (runningRemaining === null) {
                runningRemaining = payable;
            }

            const currentOutstanding = runningRemaining;

            if (status === 'Paid') {
                runningRemaining = Math.max(runningRemaining - emi, 0);
            }

            $(this).find('.remaining-amount').val(runningRemaining.toFixed(2));

            // Validate EMI doesn't exceed remaining
            const rowId = $(this).attr('id');
            validateEmiAmount(rowId, emi, currentOutstanding);
        });
    }

    function updateTableHeaders(collectionTypeId) {
        const dueDateHeader = $('#dueDateText');
        const emiHeader = $('#emiText');

        switch(parseInt(collectionTypeId)) {
            case 1:
                dueDateHeader.text('Daily Due Day');
                emiHeader.text('Daily EMI');
                break;
            case 2:
                dueDateHeader.text('Weekly Due Date');
                emiHeader.text('Weekly EMI');
                break;
            case 3:
                dueDateHeader.text('Monthly Due Date');
                emiHeader.text('Monthly EMI');
                break;
        }
    }

    function getLoanTypeId() {
        const firstRow = $('#emiTbody tr:first');
        if (firstRow.length > 0) {
            return firstRow.find('input[name^="loan_type_id"]').val();
        }
        return '';
    }

    // Remove row function
    window.removeRow = function(rowId) {
        $('#' + rowId).remove();
        updateRemainingAmounts();
        validateForm();
    };

    // Live calculation when EMI amount changes
    $(document).on('input', '.emi-amount', function() {
        const rowId = $(this).closest('tr').attr('id');
        const emiValue = parseFloat($(this).val()) || 0;
        const payableElement = $(this).closest('tr').find('.payable-amount');
        const payableValue = parseFloat(payableElement.val()) || 0;

        // Validate EMI amount
        validateEmiAmount(rowId, emiValue, payableValue);

        updateRemainingAmounts();
        validateForm();
    });

    // Validate individual fields on blur
    $(document).on('blur', '.due-date, .due-day, .emi-amount, .status-select', function() {
        const rowId = $(this).closest('tr').attr('id');
        validateRow(rowId);
        validateForm();
    });

    $(document).on('change', '.status-select', function() {
        updateRemainingAmounts();
        validateForm();
    });

    // Form submission validation
    $('#emiForm').on('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            // Scroll to first error
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
            return false;
        }

        // If validation passes, submit the form
        this.submit();
    });

    // ==================== VALIDATION FUNCTIONS ====================

    function validateForm() {
        let isValid = true;

        // Validate customer selection
        if (!$('#client_id').val()) {
            showValidationError('Please select a customer', 'customerSearch');
            isValid = false;
        } else {
            clearValidationError('customerSearch');
        }

        // Validate at least one EMI row exists
        if ($('#emiTbody tr').length === 0) {
            showValidationError('At least one EMI entry is required', 'emiTable');
            isValid = false;
        } else {
            clearValidationError('emiTable');
        }

        // Validate selected loan assignment
        if (!$('#loan_assign_id').val()) {
            showValidationError('Please select a loan', 'loanListTable');
            isValid = false;
        } else {
            clearValidationError('loanListTable');
        }

        // Validate each EMI row
        $('#emiTbody tr').each(function() {
            if (!validateRow($(this).attr('id'))) {
                isValid = false;
            }
        });

        // Update submit button state
        $('#submitBtn').prop('disabled', !isValid);

        return isValid;
    }

    function validateRow(rowId) {
        const row = $('#' + rowId);
        let rowValid = true;

        // Validate due date/day based on collection type
        if (collectionType == 3 || collectionType == 2) {
            const dueDate = row.find('.due-date').val();
            if (!dueDate) {
                showRowError(rowId, 'due-date', 'Due date is required');
                rowValid = false;
            } else {
                clearRowError(rowId, 'due-date');
            }
        } else if (collectionType == 1) {
            const dueDay = row.find('.due-day').val();
            if (!dueDay) {
                showRowError(rowId, 'due-day', 'Please select a due day');
                rowValid = false;
            } else {
                clearRowError(rowId, 'due-day');
            }
        }

        // Validate EMI amount
        const emiAmount = parseFloat(row.find('.emi-amount').val()) || 0;
        const payableAmount = parseFloat(row.find('.payable-amount').val()) || 0;

        if (!validateEmiAmount(rowId, emiAmount, payableAmount)) {
            rowValid = false;
        }

        // Validate payable amount
        if (!payableAmount || payableAmount <= 0) {
            showRowError(rowId, 'payable-amount', 'Payable amount must be greater than 0');
            rowValid = false;
        } else {
            clearRowError(rowId, 'payable-amount');
        }

        // Validate remaining amount
        const remainingAmount = parseFloat(row.find('.remaining-amount').val()) || 0;
        if (remainingAmount < 0) {
            showRowError(rowId, 'remaining-amount', 'Remaining amount cannot be negative');
            rowValid = false;
        } else {
            clearRowError(rowId, 'remaining-amount');
        }

        // Validate status
        const status = row.find('.status-select').val();
        if (!status) {
            showRowError(rowId, 'status', 'Please select a status');
            rowValid = false;
        } else {
            clearRowError(rowId, 'status');
        }

        // Validate loan type
        const loanType = row.find('input[name^="loan_type_id"]').val();
        if (!loanType) {
            showRowError(rowId, 'loan-type', 'Loan type is required');
            rowValid = false;
        } else {
            clearRowError(rowId, 'loan-type');
        }

        return rowValid;
    }

    function validateEmiAmount(rowId, emiAmount, payableAmount) {
        const row = $('#' + rowId);

        if (!emiAmount || emiAmount <= 0) {
            showRowError(rowId, 'emi-amount', 'EMI amount must be greater than 0');
            return false;
        }

        if (emiAmount > payableAmount) {
            showRowError(rowId, 'emi-amount', 'EMI amount cannot exceed payable amount');
            return false;
        }

        clearRowError(rowId, 'emi-amount');
        return true;
    }

    function showRowError(rowId, fieldClass, message) {
        const row = $('#' + rowId);
        const field = row.find('.' + fieldClass);
        const errorId = `${rowId}-${fieldClass}-error`;

        // Remove existing error for this field
        row.find(`#${errorId}`).remove();

        // Add error class to field
        field.addClass('is-invalid');

        // Add error message
        const errorDiv = `
            <div id="${errorId}" class="invalid-feedback d-block">
                <small><i class="ti ti-alert-circle me-1"></i> ${message}</small>
            </div>
        `;

        field.after(errorDiv);
    }

    function clearRowError(rowId, fieldClass) {
        const row = $('#' + rowId);
        const field = row.find('.' + fieldClass);
        const errorId = `${rowId}-${fieldClass}-error`;

        field.removeClass('is-invalid');
        row.find(`#${errorId}`).remove();
    }

    function clearRowValidation(rowId) {
        const row = $('#' + rowId);
        row.find('.is-invalid').removeClass('is-invalid');
        row.find('.invalid-feedback').remove();
    }

    function showValidationError(message, elementId) {
        const element = $('#' + elementId);
        const errorId = `${elementId}-error`;

        // Remove existing error
        $(`#${errorId}`).remove();

        // Add error styling
        element.addClass('is-invalid');

        // Add error message
        const errorDiv = `
            <div id="${errorId}" class="invalid-feedback d-block">
                <small><i class="ti ti-alert-circle me-1"></i> ${message}</small>
            </div>
        `;

        element.after(errorDiv);
    }

    function clearValidationError(elementId) {
        const element = $('#' + elementId);
        element.removeClass('is-invalid');
        $(`#${elementId}-error`).remove();
    }

    function clearAllValidationErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }
});
</script>

<style>
/* Add this to your existing CSS */
.invalid-feedback {
    display: block !important;
    margin-top: 0.25rem;
    font-size: 0.875em;
}

.is-invalid {
    border-color: #dc3545 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.table .is-invalid {
    background-position: right calc(0.375em + 0.1875rem) center;
}

.table td {
    position: relative;
}
.search-results-container {
    position: absolute;
    width: 100%;
    max-height: 300px;
    overflow-y: auto;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    z-index: 1000;
    display: none;
}

.search-results-list {
    margin: 0;
}

.customer-item {
    border: none;
    border-bottom: 1px solid #f8f9fa;
}

.customer-item:last-child {
    border-bottom: none;
}

.customer-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.customer-name {
    font-weight: 600;
}

.customer-id {
    font-size: 0.75rem;
}

.selected-customer-card {
    display: none;
}

.selected-customer-card .card {
    margin-top: 10px;
    border-left: 4px solid #28a745 !important;
}

.position-relative {
    position: relative;
}

/* Scrollbar styling */
.search-results-list::-webkit-scrollbar {
    width: 6px;
}

.search-results-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.search-results-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.search-results-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection
