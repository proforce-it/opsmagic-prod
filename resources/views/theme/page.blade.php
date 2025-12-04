@extends('theme.master')

@section('page')
    <div class="page d-flex flex-row flex-column-fluid">

        <!--begin::Aside-->
        @include('theme.partials.navigation_bar')
        <!--end::Aside-->

        <!--begin::Wrapper-->
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

            <!--begin::Header-->
            @include('theme.partials.header')
            <!--end::Header-->

            <!--begin::Content-->
            @yield('content')
            <!--end::Content-->

            <!--begin::Footer-->
            @include('theme.partials.footer')
            <!--end::Footer-->

        </div>
        <!--end::Wrapper-->
    </div>
@endsection
