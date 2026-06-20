<!DOCTYPE html>
<html lang="en">

<head>
    @include('site.layouts.head')
    @stack('styles')

</head>

<body>
    <div class="main-wrapper">

        @include('site.layouts.header')

        @include('site.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                    <div class="my-auto">
                        <h3 class="mb-1 fw-bold text-dark">@yield('title', 'Admin Dashboard')</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-primary"><i class="ti ti-smart-home fs-18"></i></a>
                                </li>
                                <li class="breadcrumb-item text-muted">Dashboard</li>
                                <li class="breadcrumb-item active fw-medium">@yield('title', 'Admin Dashboard')</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                @yield('content')

            </div>

            @include('site.layouts.footer')

        </div>

    </div>

    @include('site.layouts.script')
    @stack('scripts')
    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");

        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        // @if ($errors->any())
        //     @foreach ($errors->all() as $error)
        //         toastr.error("{{ $error }}");
        //     @endforeach
        // @endif
    </script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
        };
    </script>
</body>

</html>
