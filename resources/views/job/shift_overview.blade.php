@extends('theme.page')

@section('title', 'Booking overview')
@section('content')
    <style>
        .calendar-table {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
        }
        .calendar-table th {
            vertical-align: top;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 20px;
        }
        .calendar-table td {
            vertical-align: top;
            border: 1px solid #ddd;
        }
        .lozenge {
            border: 2px solid #28a745;
            border-radius: 10px;
            margin-bottom: 5px;
            background-color: #f9f9f9;
            font-size: 14px;
            cursor: pointer;
        }
        .lozenge-text {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .lozenge.red {
            border-color: #dc3545;
        }
        .lozenge .time, .lozenge .slots {
            font-size: 14px;
        }
        .custom-rounded-bottom-9 {
            border-bottom-left-radius: 9px !important;
            border-bottom-right-radius: 9px !important;
        }
        .tooltip-inner {
            max-width: none;
            white-space: normal;
            text-align: left;
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
                                <div class="card-body py-4">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div class="row mb-5">
                                                <div class="col-lg-12">
                                                    <div id="filter_detail_Section" class="alert alert-custom" role="alert" style="background-color: #FFFFFF; padding: 0">
                                                        <div class="alert-text text-center">
                                                            <div class="row">
                                                                <div class="col-5">
                                                                    <a href="javascript:;" id="payroll_week_backward_btn" class="me-5 change_week float-end" data-type="backward">
                                                                        <i class="las la-arrow-circle-left" style="color: #181c32;font-size: 24px;"></i>
                                                                    </a>
                                                                </div>
                                                                <div class="col-2">
                                                                    <span class="fs-2">
                                                                        <span class="ms-1">w/c</span>
                                                                        <span id="selected_week_date" class="ms-1">
                                                                            {{ \Carbon\Carbon::make($payroll_week->monday_payroll_start)->format('d M Y') }}
                                                                        </span>
                                                                        <input type="hidden" name="payroll_week_id" id="payroll_week_id" value="{{ $payroll_week['id'] }}">
                                                                        <input type="hidden" name="selected_week_number" id="selected_week_number" value="{{ $payroll_week['payroll_week_number'] }}">
                                                                        <input type="hidden" name="selected_week_year" id="selected_week_year" value="{{ $payroll_week['year'] }}">
                                                                    </span>
                                                                </div>
                                                                <div class="col-5">
                                                                    <a href="javascript:;" id="payroll_week_forward_btn" class="ms-5 change_week float-start" data-type="forward">
                                                                        <i class="las la-arrow-circle-right" style="color: #181c32;font-size: 24px;"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="cost_center" class="fs-6 fw-bold">Cost center</label>
                                                        <select name="cost_center" id="cost_center" class="form-select form-select-lg get_job_shift_overview" data-control="select2" data-placeholder="Any" data-allow-clear="true">
                                                            <option selected value="Any">Any</option>
                                                            @if($costCentre)
                                                                @foreach($costCentre as $cc_row)
                                                                    <option value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="client" class="fs-6 fw-bold">Client</label>
                                                        <select name="client" id="client" class="form-select form-select-lg get_job_shift_overview" data-control="select2" data-placeholder="Any" data-allow-clear="true">
                                                            <option value="Any">Any</option>
                                                            @if($client)
                                                                @foreach($client as $row)
                                                                    <option value="{{ $row['id'] }}">{{ $row['company_name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="site" class="fs-6 fw-bold">Site</label>
                                                        <select name="site" id="site" class="form-select form-select-lg get_job_shift_overview" data-control="select2" data-placeholder="Select a client first" data-allow-clear="true">
                                                            <option value="">Select a client first</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="job" class="fs-6 fw-bold">Job</label>
                                                        <select name="job" id="job" class="form-select form-select-lg go_to_booking_calendar" data-control="select2" data-placeholder="Select a site first" data-allow-clear="true">
                                                            <option value="">Select a site first</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <a href="{{ url('shift-overview') }}" id="reset_search_form"  class="btn btn-dark mt-7 w-100">Reset</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-5">
                                <div class="card-body py-4">
                                    <table class="calendar-table" cellpadding="5">
                                        <thead>
                                            <tr class="text-gray-500 fw-normal">
                                                <th>Mon</th>
                                                <th>Tue</th>
                                                <th>Wed</th>
                                                <th>Thu</th>
                                                <th>Fri</th>
                                                <th>Sat</th>
                                                <th>Sun</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td id="monday"></td>
                                            <td id="tuesday"></td>
                                            <td id="wednesday"></td>
                                            <td id="thursday"></td>
                                            <td id="friday"></td>
                                            <td id="saturday"></td>
                                            <td id="sunday"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
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

        $(document).on('click', '.change_week', function (){
            $.ajax({
                type    : 'post',
                url     : '{{ url('change-week') }}',
                data    : {
                    _token  : '{{ csrf_token() }}',
                    type : $(this).attr('data-type'),
                    selected_week_number : $("#selected_week_number").val(),
                    selected_week_year : $("#selected_week_year").val()
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#payroll_week_id").val(response.data.payroll_week_id);
                        $("#selected_week_date").text(response.data.selected_week_date);
                        $("#selected_week_number").val(response.data.selected_week_number);
                        $("#selected_week_year").val(response.data.selected_week_year);
                        getJobShiftOverview();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(document).ready(function (){
            getJobShiftOverview();
        });

        $(document).on('change', '.get_job_shift_overview', function (){
            getJobShiftOverview();
        });

        function getJobShiftOverview() {
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-job-shift-overview') }}',
                data    : {
                    _token  : '{{ csrf_token() }}',
                    payroll_week_id : $("#payroll_week_id").val(),
                    cost_center : $("#cost_center").val(),
                    client : $("#client").val(),
                    site : $("#site").val(),
                },
                success: function (response) {
                    if (response.code === 200) {
                        Object.entries(response.data).forEach(([day, html]) => {
                            $('#' + day).html(html);
                        });
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        }

        $('.go_to_booking_calendar').on('change', function () {
            let job_id = $("#job").val();
            if (job_id !== '') {
                window.location.href = '{{ url('assignment-management') }}'+'?tag='+$("#client").val()+'.'+$("#site").val()+'.'+job_id;
            } else {
                getJobShiftOverview();
            }
        });

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl, {
                html: true
            });
        });
    </script>
@endsection
