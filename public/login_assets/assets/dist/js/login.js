$(document).ready(function() {

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
    $("#login_form").on('submit', function(e) {
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
        var email = $("#email").val();
        var password = $("#password").val();
//alert(email);
        // empty check validation
        if (email == "") {
            $("#jsalerterror").show();
            $("#jsalerterror").html("Enter Email Address!");
        } else if (password == "") {
            $("#jsalerterror").show();
            $("#jsalerterror").html("Enter Password!");
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



                    $("#submit").html("login")
//alert(data.message);

                    if (data.login_status == 0) {

                          window.location.href = data.redirect_url;

                        // reset the form
                        $("#login_form")[0].reset();
                    } else if ( data.login_status== 1) {

                        $("#jsalerterror").show();
                        $("#jsalerterror").html("Login Failed!");
                    }

                }
            });
            // ajax call end
        }

    });
});
