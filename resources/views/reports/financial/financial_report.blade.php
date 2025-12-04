@extends('theme.page')

@section('title', 'Financial report')

@section('content')
    <style>
        .btn-light-primary-custom{
            background-color: #DAECF9;
        }

        .active {
            background-color: #FFF;
            color: #333;
        }

        tfoot {
            position: sticky;
            bottom: 0;
            background-color: white;  /* or your background color */
            z-index: 1;
        }

    </style>

    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <div class="card-header border-0 pt-6">
                                    <div class="card-title">
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <span>Filters</span>
                                        </div>
                                    </div>
                                    <div class="card-toolbar">
                                        <a href="javascript:;" class="btn btn-icon" id="collapsible_content_btn">
                                            <span class="svg-icon svg-icon-1 svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon Angle-double-up">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon id="Shape" points="0 0 24 0 24 24 0 24"/>
                                                        <path d="M8.2928955,10.2071068 C7.90237121,9.81658249 7.90237121,9.18341751 8.2928955,8.79289322 C8.6834198,8.40236893 9.31658478,8.40236893 9.70710907,8.79289322 L15.7071091,14.7928932 C16.085688,15.1714722 16.0989336,15.7810586 15.7371564,16.1757246 L10.2371564,22.1757246 C9.86396402,22.5828436 9.23139665,22.6103465 8.82427766,22.2371541 C8.41715867,21.8639617 8.38965574,21.2313944 8.76284815,20.8242754 L13.6158645,15.5300757 L8.2928955,10.2071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 15.500003) scale(-1, 1) rotate(-90.000000) translate(-12.000003, -15.500003) "/>
                                                        <path d="M6.70710678,12.2071104 C6.31658249,12.5976347 5.68341751,12.5976347 5.29289322,12.2071104 C4.90236893,11.8165861 4.90236893,11.1834211 5.29289322,10.7928968 L11.2928932,4.79289682 C11.6714722,4.41431789 12.2810586,4.40107226 12.6757246,4.76284946 L18.6757246,10.2628495 C19.0828436,10.6360419 19.1103465,11.2686092 18.7371541,11.6757282 C18.3639617,12.0828472 17.7313944,12.1103502 17.3242754,11.7371577 L12.0300757,6.88414142 L6.70710678,12.2071104 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(12.000003, 8.500003) scale(-1, 1) rotate(-360.000000) translate(-12.000003, -8.500003) "/>
                                                    </g>
                                                </svg>

                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon d-none Angle-double-down">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon id="Shape" points="0 0 24 0 24 24 0 24"/>
                                                        <path d="M8.2928955,3.20710089 C7.90237121,2.8165766 7.90237121,2.18341162 8.2928955,1.79288733 C8.6834198,1.40236304 9.31658478,1.40236304 9.70710907,1.79288733 L15.7071091,7.79288733 C16.085688,8.17146626 16.0989336,8.7810527 15.7371564,9.17571874 L10.2371564,15.1757187 C9.86396402,15.5828377 9.23139665,15.6103407 8.82427766,15.2371482 C8.41715867,14.8639558 8.38965574,14.2313885 8.76284815,13.8242695 L13.6158645,8.53006986 L8.2928955,3.20710089 Z" id="Path-94" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 8.499997) scale(-1, -1) rotate(-90.000000) translate(-12.000003, -8.499997) "/>
                                                        <path d="M6.70710678,19.2071045 C6.31658249,19.5976288 5.68341751,19.5976288 5.29289322,19.2071045 C4.90236893,18.8165802 4.90236893,18.1834152 5.29289322,17.7928909 L11.2928932,11.7928909 C11.6714722,11.414312 12.2810586,11.4010664 12.6757246,11.7628436 L18.6757246,17.2628436 C19.0828436,17.636036 19.1103465,18.2686034 18.7371541,18.6757223 C18.3639617,19.0828413 17.7313944,19.1103443 17.3242754,18.7371519 L12.0300757,13.8841355 L6.70710678,19.2071045 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(12.000003, 15.499997) scale(-1, -1) rotate(-360.000000) translate(-12.000003, -15.499997) "/>
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body py-4 collapsible_content">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="payroll_week_number" class="text-muted fs-6 fw-bold required">Payroll week</label>
                                                        <select name="payroll_week_number" id="payroll_week_number" class="form-select form-select-lg" data-control="select2" data-placeholder="Select a payroll week..." data-allow-clear="true">
                                                            <option value="">Select a payroll week...</option>
                                                            @if($payroll_week_number)
                                                                @foreach($payroll_week_number as $pwn_row) {{--{{ ($pwn_row['id'] == '38') ? 'selected' : '' }}--}}
                                                                <option  value="{{ $pwn_row['payroll_week_number'].'_'.$pwn_row['year'] }}">{{ $pwn_row['payroll_week_number'].' - '.$pwn_row['year'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2"></div>
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="cost_center" class="text-muted fs-6 fw-bold">Cost center</label>
                                                        <select name="cost_center" id="cost_center" class="form-select form-select-lg" data-control="select2" data-placeholder="All..." data-allow-clear="true">
                                                            <option value="All">All...</option>
                                                            @if($costCentre)
                                                                @foreach($costCentre as $cc_row)
                                                                    <option value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="client" class="text-muted fs-6 fw-bold">Client</label>
                                                        <select name="client" id="client" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                                            <option value="All">All...</option>
                                                            @if($client)
                                                                @foreach($client as $row)
                                                                    <option value="{{ $row['id'] }}">{{ $row['company_name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2"></div>
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="site" class="text-muted fs-6 fw-bold">Site</label>
                                                        <select name="site" id="site" class="form-select form-select-lg" data-control="select2" data-placeholder="Select a client first..." data-allow-clear="true">
                                                            <option value="">Select a client first...</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="job" class="text-muted fs-6 fw-bold">Job</label>
                                                        <select name="job" id="job" class="form-select form-select-lg" data-control="select2" data-placeholder="Select a site first..." data-allow-clear="true">
                                                            <option value="">Select a site first...</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2"></div>
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="worker" class="text-muted fs-6 fw-bold">Worker</label>
                                                        <select name="worker" id="worker" class="form-select form-select-lg" data-control="select2" data-placeholder="Select status" data-allow-clear="true">
                                                            <option value="All">All...</option>
                                                            @if($worker)
                                                                @foreach($worker as $w_row)
                                                                    <option value="{{ $w_row['id'] }}">{{ $w_row['worker_no'].' - '.$w_row['first_name'].' '.$w_row['middle_name'].' '.$w_row['last_name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer collapsible_content">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <button type="button" class="float-end btn btn-primary ms-1" id="view_summary_btn">View summary</button>
                                    <button type="button" class="float-end btn btn-outline btn-outline-primary btn-active-color-gray-100 ms-1" onclick="location.reload()">Clear filer</button>
                                </div>
                            </div>

                            <ul class="nav ms-10 mt-10">
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm active" data-bs-toggle="tab" href="#kt_table_widget_5_tab_1" id="kt_table_widget_5_tab_1_menu">Site summary</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_2" id="kt_table_widget_5_tab_2_menu">Job summary</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_3" id="kt_table_widget_5_tab_3_menu">Worker summary</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_4" id="kt_table_widget_5_tab_4_menu">Payroll line item</a>
                                </li>
                            </ul>
                            <div class="card mt-n1">
                                <div class="card-body">
                                    @include('reports.financial.partial.filter_detail_tab')
                                    <div class="tab-content">
                                        @include('reports.financial.partial.site_summary')
                                        @include('reports.financial.partial.job_summary')
                                        @include('reports.financial.partial.worker_summary')
                                        @include('reports.financial.partial.payroll_line_item')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @yield('filter_details_js')
    @yield('site_summary_js')
    @yield('job_summary_js')
    @yield('worker_summary_js')
    @yield('payroll_summary_js')
    <script>
        let filter = false;
        localStorage.setItem('financial_report_tab', 'kt_table_widget_5_tab_1_menu');
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', () => {
                localStorage.setItem('financial_report_tab', tab.id);
            });
        });

        $("#client").on('change', function (){
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-site-using-client') }}',
                data    : {
                    _token    : '{{ csrf_token() }}',
                    client_id : $("#client").val(),
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#site").empty().append(response.data.site_option);
                        $("#job").empty();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#site").on('change', function (){
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-job-using-site') }}',
                data    : {
                    _token  : '{{ csrf_token() }}',
                    site_id : $("#site").val(),
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#job").empty().append(response.data.job_option);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $('#collapsible_content_btn').on('click', function() {
            let target = $('.collapsible_content');
            if (target.is(':visible')) {
                target.slideUp(500);
                $(".Angle-double-up").addClass('d-none');
                $(".Angle-double-down").removeClass('d-none');
            } else {
                target.slideDown(500);
                $(".Angle-double-up").removeClass('d-none');
                $(".Angle-double-down").addClass('d-none');
            }
        });

        $("#view_summary_btn").on('click', function () {
            filter = true;
            if($("#payroll_week_number").val() === '') {
                toastr.error('Please select Payroll week number');
                return false;
            }

            $("#"+localStorage.getItem('financial_report_tab')).click();

            let filterValues = getSelectedDropdownOptionText();
            manage_filter_details_section(filterValues);
        });
    </script>
@endsection
