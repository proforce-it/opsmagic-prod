<!DOCTYPE html>

<html lang="en">
<!--begin::Head-->
<head><base href="../../../">

    <title>
        @yield('title_prefix', config('metronic.title_prefix', ''))
        @yield('title', config('metronic.title', ''))
        @yield('title_postfix', config('metronic.title_postfix', ''))
    </title>

    <meta name="description" content="Worker"/>
    <meta name="keywords" content="Worker"/>

    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->

    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

</head>
<!--end::Head-->

<!--begin::Body-->
<body id="kt_body" class="bg-dark">
    {{-- Body Content --}}
    @yield('body')

    <!--begin::Javascript-->
    <script>var hostUrl = "assets/";</script>
    <!--begin::Global Javascript Bundle(used by all pages)-->

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->

    <!--begin::Page Custom Javascript(used by this page)-->
    <script src="{{ asset('assets/js/custom/authentication/sign-in/general.js') }}"></script>
    <!--end::Page Custom Javascript-->

    <!--end::Javascript-->

    @yield('forget_password_js')
    @yield('reset_password_js')
    @yield('create_password_js')
    @yield('reset_worker_password_js')
</body>
<!--end::Body-->

</html>
