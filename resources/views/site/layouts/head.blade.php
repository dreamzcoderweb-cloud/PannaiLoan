<!-- Meta Tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pannai</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="description" content="SmartHR - An advanced Bootstrap 5 admin dashboard template for HRM and CRM.">
<meta name="keywords"
    content="HR dashboard template, HRM admin template, Bootstrap 5 HR dashboard, workforce management, employee records">
<meta name="author" content="Dreams Technologies">
<meta name="robots" content="index, follow">

<!-- Apple Touch Icon -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">

<!-- Favicon -->
<link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">
<!-- jQuery -->
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<!-- Theme Script JS (Loads before CSS) -->
<script src="{{ asset('assets/js/theme-script.js') }}"></script>

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

<!-- Feather Icons -->
<link rel="stylesheet" href="{{ asset('assets/plugins/icons/feather/feather.css') }}">

<!-- Tabler Icons -->
<link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

<!-- Select2 CSS -->
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

<!-- FontAwesome CSS -->
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">

<!-- Datetimepicker CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">

<!-- Bootstrap Tagsinput CSS -->
<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">

<!-- Summernote CSS -->
<link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-lite.min.css') }}">

<!-- Daterangepicker CSS -->
<link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

<!-- Color Picker CSS -->
<link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/@simonwep/pickr/themes/nano.min.css') }}">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<!-- Main CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
<style>
    .toast-success {
        background-color: #51A351 !important;
    }

    .toast-error {
        background-color: #BD362F !important;
    }

    .toast-info {
        background-color: #2F96B4 !important;
    }

    .toast-warning {
        background-color: #F89406 !important;
    }

    /* DataTable Length Select Fix */
    div.dataTables_wrapper div.dataTables_length select {
        width: 62px !important;
       display: inline-block !important;
    }

    .flex-wrap{
        margin-bottom: 1rem;
    }
</style>
