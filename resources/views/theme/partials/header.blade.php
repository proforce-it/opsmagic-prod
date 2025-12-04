<div id="kt_header" style="" class="header align-items-stretch">
    <!--begin::Brand-->
    <div class="header-brand">
        <!--begin::Logo-->
        <a href="javascript:;">
            <img alt="Logo" src="{{ asset('assets/media/logos/new_logo.png') }}" class="h-50px h-lg-50px">
        </a>
        <!--end::Logo-->
        <!--begin::Aside minimize-->
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-dark aside-minimize" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr092.svg-->
            <span class="svg-icon svg-icon-1 svg-icon-white me-n1 minimize-default">
                <i class="text-white fs-2 las la-arrow-circle-left"></i>
                </span>
            <!--end::Svg Icon-->
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr076.svg-->
            <span class="svg-icon svg-icon-1 minimize-active">
                <i class="text-white fs-2 las la-arrow-circle-right"></i>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Aside minimize-->
        <!--begin::Aside toggle-->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
            <div class="btn btn-icon btn-active-color-primary w-30px h-30px" id="kt_aside_mobile_toggle">
                <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black"></path>
                        <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black"></path>
                    </svg>
                </span>
                <!--end::Svg Icon-->
            </div>
        </div>
        <!--end::Aside toggle-->
    </div>
    <!--end::Brand-->
    <div class="toolbar">
        <!--begin::Toolbar-->
        @php($str = explode('_', \Illuminate\Support\Facades\Route::current()->getName()))
        <div class="container-fluid py-6 py-lg-0 d-flex flex-column flex-lg-row align-items-lg-stretch justify-content-lg-between">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column me-5">
                <!--begin::Title-->
<!--                <h1 class="d-flex flex-column text-dark fw-bolder fs-3 mb-0">{{ ucfirst(str_replace('-', ' ', $str[1]))  }}</h1>-->
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 pt-1">

                    @php($title0 = str_replace('-', ' ', strtoupper($str[0])))
                    @php($title1 = str_replace('-', ' ', strtoupper($str[1])))

                    <li class="breadcrumb-item text-muted">
                        <a href="{{ url('dashboard')}}" class="text-muted text-hover-primary">DASHBOARD</a>
                    </li>

                    @if($title1 != 'HOME')
                        <li class="breadcrumb-item text-gray-500">
                            >
                        </li>

                        @if($title0 != 'DASHBOARD')
                            <li class="breadcrumb-item text-muted" id="header_info_second_a_tag">
                                <a href="{{ url($str[0])}}" class="text-muted text-hover-primary" id="header_info_second_a_tag_title">{{ $title0 }}</a>
                            </li>

                            <li class="breadcrumb-item text-gray-500">
                                >
                            </li>
                        @endif

                        <li class="breadcrumb-item text-dark" id="last_li_of_header_title">
                            <span id="header_sub_title">{{ $title1 }}</span> <span id="header_additional_info" class="text-uppercase ms-1"></span>
                        </li>
                    @endif
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->

        </div>
        <!--end::Toolbar-->
    </div>
</div>
