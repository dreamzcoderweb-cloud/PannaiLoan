// loan-assign-validation.js - Reusable validation for both create and edit forms
function initializeLoanAssignValidation() {
    const $interest = $("#interest_id");
    const initialInterestOptions = $interest.html();

    function filterInterestOptionsByCollectionType(collectionTypeId) {
        // Rebuild from the original option set to avoid permanently losing options.
        $interest.html(initialInterestOptions);

        if (!collectionTypeId) {
            $interest.val("");
            $interest.find("option").each(function () {
                if ($(this).val() !== "") {
                    $(this).remove();
                }
            });
            return;
        }

        $interest.find("option").each(function () {
            const optionValue = $(this).val();
            const optionCollectionType = String($(this).data("collection-type") || "");

            // Keep placeholder; keep only matching collection type options.
            if (optionValue !== "" && optionCollectionType !== String(collectionTypeId)) {
                $(this).remove();
            }
        });

        const selectedType = String($interest.find("option:selected").data("collection-type") || "");
        if ($interest.val() && selectedType !== String(collectionTypeId)) {
            $interest.val("");
        }

        if ($interest.hasClass("select2-hidden-accessible")) {
            $interest.trigger("change.select2");
        }
    }

    // Custom method for select validation
    $.validator.addMethod("selectRequired", function (value, element) {
        if ($(element).prop("multiple")) {
            return value && value.length > 0;
        }
        return value !== "";
    }, "Please select an option");

    // Initialize form validation
    $("#loanAssignForm").validate({
        rules: {
            client_id: {
                selectRequired: true,
            },
            loan_assign_id: {
                required: true,
                digits: true
            },
            client_type_id: {
                selectRequired: true,
            },
            address: {
                required: true,
                minlength: 10
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            city: {
                required: true
            },
            pincode: {
                required: true,
                digits: true,
                minlength: 6,
                maxlength: 6
            },
            loan_type_id: {
                selectRequired: true
            },
            "document_type_id[]": {
                selectRequired: true
            },
            collection_type_id: {
                selectRequired: true
            },
            loanassign_date: {
                required: true
            },
            daily_duedays_id: {
                required: function (element) {
                    return $('#collection_type_id').val() == 1;
                }
            },
            week_duedays_id: {
                required: function (element) {
                    return $('#collection_type_id').val() == 2;
                }
            },
            monthlydue_date: {
                required: function (element) {
                    return $('#collection_type_id').val() == 3;
                }
            },
            loan_tenure: {
                required: true,
                digits: true,
                min: 1
            },
            branch_id: {
                selectRequired: true
            },
            route_id: {
                selectRequired: true
            },
            loan_amount: {
                required: true,
                digits: true,
                min: 1
            },
            interest_id: {
                selectRequired: true
            },
            daily_emi: {
                required: function (element) {
                    return $('#collection_type_id').val() == 1;
                }
            },
            weekly_emi: {
                required: function (element) {
                    return $('#collection_type_id').val() == 2;
                }
            },
            monthly_emi: {
                required: function (element) {
                    return $('#collection_type_id').val() == 3;
                }
            },
            total_interest: {
                required: true
            },
            total_payableamt: {
                required: true
            }
        },
        messages: {
            client_id: {
                selectRequired: "The Customer name field is required.",
            },
            loan_assign_id: {
                required: "The Loan Assign ID field is required.",
                digits: "Please enter a valid number"
            },
            client_type_id: {
                selectRequired: "The Customer Type field is required.",
            },
            address: {
                required: "The Address field is required.",
                minlength: "Address must be at least 10 characters"
            },
            phone: {
                required: "The Phone field is required.",
                digits: "Please enter valid phone number",
                minlength: "Phone number must be 10 digits",
                maxlength: "Phone number must be 10 digits"
            },
            city: {
                required: "The City field is required."
            },
            pincode: {
                required: "The Pincode field is required.",
                digits: "Please enter valid pincode",
                minlength: "Pincode must be 6 digits",
                maxlength: "Pincode must be 6 digits"
            },
            loanassign_date: {
                required: "The Loan Assign Date field is required."
            },
            loan_type_id: {
                selectRequired: "The Loan Type field is required."
            },
            "document_type_id[]": {
                selectRequired: "The Document Type field is required."
            },
            collection_type_id: {
                selectRequired: "The Loan Collection Type field is required."
            },
            daily_duedays_id: {
                required: "The Daily Due Day field is required."
            },
            week_duedays_id: {
                required: "The Weekly Due Day field is required."
            },
            monthlydue_date: {
                required: "The Monthly Due Date field is required."
            },
            loan_tenure: {
                required: "The Loan Tenure field is required.",
                digits: "Please enter valid number",
                min: "Loan tenure must be at least 1 month"
            },
            branch_id: {
                selectRequired: "The Branch field is required."
            },
            route_id: {
                selectRequired: "The Route field is required."
            },
            loan_amount: {
                required: "The Loan Amount field is required.",
                digits: "Please enter valid amount",
                min: "Loan amount must be greater than 0"
            },
            interest_id: {
                selectRequired: "The Interest Rate field is required."
            },
            daily_emi: {
                required: "Daily EMI is required for Daily collection type."
            },
            weekly_emi: {
                required: "Weekly EMI is required for Weekly collection type."
            },
            monthly_emi: {
                required: "Monthly EMI is required for Monthly collection type."
            }
        },
        errorPlacement: function (error, element) {
            // For select elements, place error in the error container
            if (element.is("select")) {
                var errorContainer = element.parent().find('.error-container');
                if (errorContainer.length) {
                    errorContainer.html(error);
                } else {
                    error.insertAfter(element);
                }
            }
            // For date input
            else if (element.attr("name") === "monthlydue_date") {
                var errorContainer = element.parent().find('.error-container');
                if (errorContainer.length) {
                    errorContainer.html(error);
                } else {
                    error.insertAfter(element);
                }
            }
            // For other elements
            else {
                error.insertAfter(element);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass(errorClass).removeClass(validClass);
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass(errorClass).addClass(validClass);
            // Clear error container when valid
            if ($(element).is("select") || $(element).attr("name") === "monthlydue_date") {
                var errorContainer = $(element).parent().find('.error-container');
                if (errorContainer.length) {
                    errorContainer.empty();
                }
            }
        },
        submitHandler: function (form) {
            // Prevent double submission
            $('button[type="submit"]').prop('disabled', true);

            // Add loading state
            $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            // Submit the form
            form.submit();
        }
    });

    // Collection type change handler
    $('#collection_type_id').change(function () {
        var CollectionTypeId = $(this).val();
        var validator = $("#loanAssignForm").validate();

        // Reset all dependent fields if collection type changes
        if (CollectionTypeId != 3) {
            $('input[name="monthlydue_date"]').val('');
        }
        if (CollectionTypeId != 2) {
            $('#week_duedays_id').val('');
        }
        if (CollectionTypeId != 1) {
            $('#daily_duedays_id').val('');
        }

        // Keep interest list aligned with selected collection type.
        filterInterestOptionsByCollectionType(CollectionTypeId);

        // Clear EMI fields
        $('input[name="daily_emi"]').val('');
        $('input[name="weekly_emi"]').val('');
        $('input[name="monthly_emi"]').val('');
        $('input[name="total_interest"]').val('');
        $('input[name="total_payableamt"]').val('');

        // Hide all EMI sections first
        $('#daily_div').hide();
        $('#weekly_div').hide();
        $('#monthly_div').hide();
        $('#dailyemi_div').hide();
        $('#weeklyemi_div').hide();
        $('#monthlyemi_div').hide();
        $('#totalinterest_div').hide();
        $('#totalpayable_div').hide();

        // Show relevant sections
        if (CollectionTypeId == 1) {
            $('#daily_div').show();
            $('#loan_tenure_div').show();
        }
        else if (CollectionTypeId == 2) {
            $('#weekly_div').show();
            $('#loan_tenure_div').show();
        }
        else if (CollectionTypeId == 3) {
            $('#monthly_div').show();
            $('#loan_tenure_div').show();
        }

        // Recalculate EMI if we have all required values
        if ($('#collection_type_id').val() && $('#loan_amount').val() && $('#loan_tenure').val() && $('#interest_id').val()) {
            calculateEMI();
        }

        // Trigger validation for the changed field
        validator.element('#collection_type_id');
        validator.element('#interest_id');
    });

    // Clear error messages when selection is made
    $('select').change(function () {
        var validator = $("#loanAssignForm").validate();
        validator.element(this);
    });


    // EMI Calculation function
     function calculateEMI() {
    let collectionType = $("#collection_type_id").val();
    let P = parseFloat($("#loan_amount").val());
    let interestRateElement = $("#interest_id option:selected");
    let interestRate = parseFloat(interestRateElement.data('rate'));
    let interestCollectionType = interestRateElement.data('collection-type');
    let N = parseFloat($("#loan_tenure").val());

    // Clear fields initially
    $("#monthly_emi, #weekly_emi, #daily_emi").val('');
    $("#total_interest").val('');
    $("#total_payableamt").val('');
    $("#total_distribution").val('');

    // Hide all sections initially
    $('#monthlyemi_div, #weeklyemi_div, #dailyemi_div').hide();
    $('#totalinterest_div, #totalpayable_div, #totaldistub_div').hide();

    // Validate inputs
    if (isNaN(P) || isNaN(N) || !collectionType || isNaN(interestRate)) {
        return;
    }

    // For WEEKLY Collection Type (2)
    if (collectionType == 2) {
        // Calculate Weekly EMI = Loan Amount / Loan Tenure
        let weeklyEMI = P / N;
        
        // Calculate Total Interest = Loan Amount × (Interest Rate / 100)
        let totalInterest = P * (interestRate / 100);
        
        // Total Distribution = Loan Amount - Total Interest
        let totalDistribution = P - totalInterest;
        
        // Round values
        let roundedWeeklyEMI = Math.round(weeklyEMI);
        let roundedTotalInterest = Math.round(totalInterest);
        let roundedTotalDistribution = Math.round(totalDistribution);
        
        // Display values
        $("#weekly_emi").val(roundedWeeklyEMI);
        $("#total_distribution").val(roundedTotalDistribution);
        $("#total_interest").val(roundedTotalInterest);
        
        // Show relevant sections
        $('#weeklyemi_div').show();
        $('#totaldistub_div').show();
        $('#totalinterest_div').show();
        
    } 
    // For MONTHLY Collection Type (3)
    else if (collectionType == 3) {
        if (!interestCollectionType) return;
        
        // Normalize interest rate to yearly
        let annualRate = 0;
        if (interestCollectionType == 3) { // Monthly
            annualRate = interestRate * 12;
        } else if (interestCollectionType == 2) { // Weekly
            annualRate = interestRate * 52;
        } else if (interestCollectionType == 1) { // Daily
            annualRate = interestRate * 365;
        }

        // Monthly period rate
        let monthlyRate = annualRate / 12;

        // Calculate totals (flat interest)
        let totalInterest = P * (monthlyRate / 100) * N;
        let totalPayable = P + totalInterest;
        let monthlyEMI = totalPayable / N;

        // Rounding
        let roundedMonthlyEMI = Math.round(monthlyEMI);
        let roundedTotalInterest = Math.round(totalInterest);
        let roundedTotalPayable = Math.round(totalPayable);

        // Display
        $("#monthly_emi").val(roundedMonthlyEMI);
        $("#total_interest").val(roundedTotalInterest);
        $("#total_payableamt").val(roundedTotalPayable);
        
        // Show sections
        $('#monthlyemi_div').show();
        $('#totalinterest_div').show();
        $('#totalpayable_div').show();
    } 
    // For DAILY Collection Type (1)
    else if (collectionType == 1) {
        if (!interestCollectionType) return;
        
        // Normalize interest rate to yearly
        let annualRate = 0;
        if (interestCollectionType == 3) { // Monthly
            annualRate = interestRate * 12;
        } else if (interestCollectionType == 2) { // Weekly
            annualRate = interestRate * 52;
        } else if (interestCollectionType == 1) { // Daily
            annualRate = interestRate * 365;
        }

        // Daily period rate
        let dailyRate = annualRate / 365;

        // Calculate totals (flat interest)
        let totalInterest = P * (dailyRate / 100) * N;
        let totalPayable = P + totalInterest;
        let dailyEMI = totalPayable / N;

        // Rounding
        let roundedDailyEMI = Math.round(dailyEMI);
        let roundedTotalInterest = Math.round(totalInterest);
        let roundedTotalPayable = Math.round(totalPayable);

        // Display
        $("#daily_emi").val(roundedDailyEMI);
        $("#total_interest").val(roundedTotalInterest);
        $("#total_payableamt").val(roundedTotalPayable);
        
        // Show sections
        $('#dailyemi_div').show();
        $('#totalinterest_div').show();
        $('#totalpayable_div').show();
    }

    // Trigger validation
    var validator = $("#loanAssignForm").validate();
    if (collectionType == 2) {
        validator.element("#weekly_emi");
        validator.element("#total_distribution");
        validator.element("#total_interest");
    } else if (collectionType == 3) {
        validator.element("#monthly_emi");
        validator.element("#total_interest");
        validator.element("#total_payableamt");
    } else if (collectionType == 1) {
        validator.element("#daily_emi");
        validator.element("#total_interest");
        validator.element("#total_payableamt");
    }
    }

    // Recalculate on changes
    $("#loan_amount, #loan_tenure, #interest_id, #collection_type_id").on("keyup change", calculateEMI);

    // Apply correct interest options on first load as well.
    filterInterestOptionsByCollectionType($("#collection_type_id").val());

}
