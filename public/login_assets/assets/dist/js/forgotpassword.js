$(document).ready(function() {
    alert("test");
        // hide the js alert load the page
        $("#jsalerterror").hide();
        $("#jsalertsuccess").hide();

        // only number input in mobile using jquery start
        $('#mobile').keypress(function(e) {

            var charCode = (e.which) ? e.which : event.keyCode

            if (String.fromCharCode(charCode).match(/[^0-9]/g))

                return false;

        });
        // only number input in mobile using jquery end

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        // click submit button
        $("#signup_form").on('submit', function(e) {
    //alert('ok');
            e.preventDefault();

            // close alert in 5 sec
            setTimeout(function() {
                $('#jsalerterror').fadeOut('slow');
            }, 5000); // <-- time in milliseconds

            setTimeout(function() {
                $('#jsalertsuccess').fadeOut('slow');
            }, 5000); // <-- time in milliseconds

            // get all input values using jquery for empty check validation
            var username = $("#username").val();
            var email = $("#email").val();
            var password = $("#password").val();
            var mobile = $("#mobile").val();

            // empty check validation
            if (username == "") {
                $("#jsalerterror").show();
                $("#jsalerterror").html("Enter Username!");
            } else if (email == "") {
                $("#jsalerterror").show();
                $("#jsalerterror").html("Enter Email Address!");
            } else if (password == "") {
                $("#jsalerterror").show();
                $("#jsalerterror").html("Enter Password!");
            } else if (mobile == "") {
                $("#jsalerterror").show();
                $("#jsalerterror").html("Enter Mobile Number!");
            } else {
                // spinner for loading...
                $("#submit").html("<div class='spinner-border text-light' role='status'></div>")

                // ajax call start
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    processData: false,
                    dataType: 'json',
                    contentType: false,
                    beforeSend: function() {
                        $(document).find('span.error-text').text('');
                    },
                    success: function(data) {


//alert(data.message);
                        $("#submit").html("Signup")
                        //alert(data.message);
                        if (data.message == 1) {
                            $("#jsalertsuccess").show();
                            $("#jsalerterror").hide();
                            $("#jsalertsuccess").html("Signup Success!");

                            // reset the form
                            $("#signup_form")[0].reset();
                        } else if (data.message == 23000) {
                            $("#jsalerterror").show();
                            $("#jsalerterror").html("Duplicate Values!");
                        } else if (data.error['username'] =="The username field is required.") { // server side validation response
                            $("#jsalerterror").show();
                            $("#jsalerterror").html(data.error['username']);
                        } else if (data.error['email'] ==
                            "The email field is required." || data.error['email'] ==
                            "The email field must be a valid email address."
                        ) { // server side validation response
                            $("#jsalerterror").show();
                            $("#jsalerterror").html(data.error['email']);
                        } else if (data.error['password'] ==
                            "The password field is required." || data.error['password'] ==
                            "The password field must be at least 6 characters."
                        ) { // server side validation response
                            $("#jsalerterror").show();
                            $("#jsalerterror").html(data.error['password']);
                        } else if (data.error['mobile'] ==
                            "The mobile field is required." || data.error['mobile'] ==
                            "The mobile field must be 10 digits."
                        ) { // server side validation response
                            $("#jsalerterror").show();
                            $("#jsalerterror").html(data.error['mobile']);
                        } else {
                            $("#jsalerterror").show();
                            $("#jsalerterror").html("Signup Failed!");
                        }

                    }
                });
                // ajax call end
            }

        });
    });

