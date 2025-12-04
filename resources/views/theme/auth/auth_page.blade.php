@extends('theme.auth.auth_master')

@section('body')
<div class="d-flex flex-column flex-root">
<!--begin::Authentication-->
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">

        <!--begin::Body-->
        @yield('form_content')
        <!--end::Body-->
    </div>

    <!--end::Authentication-->
</div>
@endsection
