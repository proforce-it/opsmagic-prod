@extends('theme.page')

@section('title', 'Timesheet editor')

@section('content')
    <style>
        .loader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 160px;
            background-color: #F5F8FA;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .loader {
            border: 4px solid #909FAF;
            border-top: 8px solid #019EF7;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loader-text {
            margin-top: 15px;
            font-size: 18px;
            color: #333;
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
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="client" class="text-muted fs-6 fw-bold">Client</label>
                                                        <select name="client" id="client" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                                            <option value="">Select...</option>
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
                                                        <label for="site" class="text-muted fs-6 fw-bold">Site</label>
                                                        <select name="site" id="site" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                                            <option value="">Select...</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="site" class="text-muted fs-6 fw-bold">Payroll Week No.</label>
                                                        <select name="payroll_week_number" id="payroll_week_number" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                                            <option value="">Select...</option>
                                                            @if($payroll_week_number)
                                                                @foreach($payroll_week_number as $pwn_row)
                                                                    <option value="{{ $pwn_row['payroll_week_number'].'_'.$pwn_row['year'] }}">{{ $pwn_row['payroll_week_number'].' - '.$pwn_row['year'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <button type="submit" name="timesheet_filter_btn" id="timesheet_filter_btn" class="btn btn-primary mt-7 w-100">Go</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <a href="{{ url('timesheet-editor') }}" id="reset_search_form"  class="btn btn-dark mt-7 w-100">Reset</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-5" id="timesheet_editor_section">
                                <div class="card-body py-4">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div id="timesheetData" class="d-none">
                                                <div class="p-5">
                                                    <div class="row gs-0 border-bottom border-4">
                                                        <div class="col-lg-8 mb-5">
                                                            <div class="text-start text-muted fw-bolder fs-5" id="company_and_site_name">-</div>
                                                            <div class="fw-bolder fs-3">
                                                                <span class="svg-icon svg-icon-2">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"/>
                                                                            <path d="M12,22 C7.02943725,22 3,17.9705627 3,13 C3,8.02943725 7.02943725,4 12,4 C16.9705627,4 21,8.02943725 21,13 C21,17.9705627 16.9705627,22 12,22 Z" fill="#000000" opacity="0.3"/>
                                                                            <path d="M11.9630156,7.5 L12.0475062,7.5 C12.3043819,7.5 12.5194647,7.69464724 12.5450248,7.95024814 L13,12.5 L16.2480695,14.3560397 C16.403857,14.4450611 16.5,14.6107328 16.5,14.7901613 L16.5,15 C16.5,15.2109164 16.3290185,15.3818979 16.1181021,15.3818979 C16.0841582,15.3818979 16.0503659,15.3773725 16.0176181,15.3684413 L11.3986612,14.1087258 C11.1672824,14.0456225 11.0132986,13.8271186 11.0316926,13.5879956 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z" fill="#000000"/>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                Timesheet editor (<span class="period_date"></span>)
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 mb-5 text-end">
                                                            <img id="company_logo" src="" alt="No image." style="width: 100px; object-fit: contain; object-position: right;">
                                                        </div>
                                                        <div class="col-lg-12 gs-0 d-none" id="ignored_timesheet_entry_section">
                                                            <div class="alert alert-danger fw-boldest" role="alert">
                                                                The following timesheet entries were added after the payroll report was processed and have been ignored
                                                            </div>

                                                            <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="ignored_timesheet_entry_datatable">
                                                                <thead>
                                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                        <th>WORKER</th>
                                                                        <th>DATE</th>
                                                                        <th>JOB</th>
                                                                        <th>START</th>
                                                                        <th>STOP</th>
                                                                        <th>HOURS</th>
                                                                        <th>ACTION</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="text-gray-600 fw-bold"></tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-lg-12 gs-0 payroll_already_created_show_section d-none">
                                                            <div class="alert alert-success fw-boldest" role="alert">
                                                                The following timesheet entries have been locked and added to a <a href="javascript:;" class="view_payroll_report_href text-decoration-underline">payroll report</a> generated <span class="payroll_created_at"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-5 gs-0 border-bottom border-4 payroll_already_created_hide_section">
                                                        <div class="col-lg-6 mb-5">
                                                            <div class="text-start text-muted fw-bolder fs-5">Period</div>
                                                            <div class="fw-bolder fs-3" id="period_date"><span class="period_date">-</span> <span id="week_number">-</span></div>
                                                        </div>
                                                        <div class="col-lg-3 mb-5">
                                                            <div class="text-start text-muted fw-bolder fs-5">Pay date</div>
                                                            <div class="fw-bolder fs-3" id="pay_date">-</div>
                                                        </div>
                                                        <div class="col-lg-3 mb-5">
                                                            <div class="text-start text-muted fw-bolder fs-5">Total Hrs</div>
                                                            <div class="fw-bolder fs-3" id="total_hrs">-</div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-5 gs-0 border-bottom border-4">
                                                        <div class="col-lg-12 mb-5">
                                                            <div class="text-start text-muted fw-bolder fs-5">View</div>
                                                            <div>
                                                                <label class="form-check-inline me-5">
                                                                    <input type="radio" name="timesheet_editor_datatable_view" id="timesheet_editor_view_1" value="view_1" checked>
                                                                    <span class="fw-bold fs-5">Shift line items</span>
                                                                </label>
                                                                <label class="form-check-inline">
                                                                    <input type="radio" name="timesheet_editor_datatable_view" id=timesheet_editor_"view_2" value="view_2">
                                                                    <span class="fw-bold fs-5">Total hours per worker</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-5 gs-0 border-bottom border-4">
                                                        <div class="col-lg-6 mb-5">
                                                            <div class="d-flex align-items-center position-relative my-1">
                                                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                                                    </svg>
                                                                </span>
                                                                <input type="text" data-kt-timesheet-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find worker" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 mb-5">
                                                            <a href="javascript:;" class="btn btn-primary float-end ms-2" id="create_payroll_report_btn">
                                                                <span class="svg-icon svg-icon-2">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <mask fill="white">
                                                                                <use xlink:href="#path-1"/>
                                                                            </mask>
                                                                            <g/>
                                                                            <path d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" fill="#000000"/>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                Create payroll report
                                                            </a>
                                                            <a href="javascript:;" class="btn btn-primary float-end" id="download_csv">
                                                                <span class="svg-icon svg-icon-2">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                                            <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                                            <path d="M14.8875071,11.8306874 L12.9310336,11.8306874 L12.9310336,9.82301606 C12.9310336,9.54687369 12.707176,9.32301606 12.4310336,9.32301606 L11.4077349,9.32301606 C11.1315925,9.32301606 10.9077349,9.54687369 10.9077349,9.82301606 L10.9077349,11.8306874 L8.9512614,11.8306874 C8.67511903,11.8306874 8.4512614,12.054545 8.4512614,12.3306874 C8.4512614,12.448999 8.49321518,12.5634776 8.56966458,12.6537723 L11.5377874,16.1594334 C11.7162223,16.3701835 12.0317191,16.3963802 12.2424692,16.2179453 C12.2635563,16.2000915 12.2831273,16.1805206 12.3009811,16.1594334 L15.2691039,12.6537723 C15.4475388,12.4430222 15.4213421,12.1275254 15.210592,11.9490905 C15.1202973,11.8726411 15.0058187,11.8306874 14.8875071,11.8306874 Z" fill="#000000"/>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                Download .csv
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-5 gs-0 border-bottom border-4">
                                                        <div class="col-lg-12">
                                                            <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="timesheet_editor_shift_line_item_datatable">
                                                                <thead>
                                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                        <th>WORKER</th>
                                                                        <th>DATE</th>
                                                                        <th>JOB</th>
                                                                        <th>START</th>
                                                                        <th>STOP</th>
                                                                        <th>HOURS</th>
                                                                        <th>EDITED</th>
                                                                        <th>ACTION</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="text-gray-600 fw-bold"></tbody>
                                                            </table>

                                                            <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark d-none" id="timesheet_editor_total_hour_per_worker_datatable">
                                                                <thead>
                                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                    <th>WORKER</th>
                                                                    <th>ID</th>
                                                                    <th>NO. JOBS</th>
                                                                    <th>NO. SHIFTS</th>
                                                                    <th>TOT. HOURS</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody class="text-gray-600 fw-bold"></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('payroll_report.partial.create_payroll_report_process')
                            @include('payroll_report.partial.create_payroll_report_success')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_timesheet_entry_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Edit entry for <span id="worker_name_and_date"></span></h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_edit_timesheet_entry_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <form id="edit_timesheet_entry_form">
                    @csrf
                    <div class="modal-body scroll-y m-5">
                        <div class="fv-row row">
                            <div class="col-lg-12">
                                <div class="fv-row fv-plugins-icon-container">
                                    <label for="hours_worked" class="fs-6 fw-bold required">Hours worked</label>
                                    <input class="form-control" name="hours_worked" id="hours_worked" type="text" value="0" placeholder="Enter hours worked">
                                    <input name="timesheet_id" id="timesheet_id" type="hidden" value="0">
                                    <span class="text-danger error" id="hours_worked_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <input class="form-control" name="job_id" id="job_id" type="hidden" value="">
                                    <button type="submit" name="update_timesheet_entry_submit_btn" id="update_timesheet_entry_submit_btn" class="btn btn-primary float-end">Update timesheet entry</button>
                                    <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" name="update_timesheet_entry_process_btn" id="update_timesheet_entry_process_btn">
                                        <span>Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('payroll_report.partial.create_payroll_modal')
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
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        let timesheet_shift_line_item_table;
        let timesheet_editor_shift_line_item_datatable = $('#timesheet_editor_shift_line_item_datatable');

        let timesheet_total_hour_per_worker_table;
        let timesheet_editor_total_hour_per_worker_datatable = $('#timesheet_editor_total_hour_per_worker_datatable');

        let ignored_entry_table;
        let ignored_entry_datatable = $('#ignored_timesheet_entry_datatable');

        $(document).ready(function() {
            timesheet_shift_line_item_table = timesheet_editor_shift_line_item_datatable.DataTable();
            timesheet_total_hour_per_worker_table = timesheet_editor_total_hour_per_worker_datatable.DataTable();
            ignored_entry_table = ignored_entry_datatable.DataTable();
        })

        $("#timesheet_filter_btn").on('click', function() {

            let client = $("#client").val();
            let site   = $("#site").val();
            let pwn    = $("#payroll_week_number").val();

            if (!client || !site || !pwn) {
                toastr.error('Please fill in all filter fields before submitting.');
                return;
            }

            $("#timesheetData").removeClass('d-none');

            timesheet_editor_shift_line_item_datatable.DataTable().destroy();
            timesheet_editor_total_hour_per_worker_datatable.DataTable().destroy();

            timesheet_shift_line_item_table = timesheet_editor_shift_line_item_datatable.DataTable({
                "processing"    : false,
                "serverSide"    : false,
                "ajax"          : {
                    "type"      : "post",
                    "url"       : '{{ url('get-timesheet-editor-shift-line-item-data') }}',
                    "data"      : function (d) {
                        d._token = '{{ csrf_token() }}';
                        d.client = client;
                        d.site   = site;
                        d.pwn    = pwn;
                        d.type   = 'lock';
                    },
                },
                "columns": [
                    {"data": "worker_name"},
                    {"data": "date"},
                    {"data": "job"},
                    {"data": "start"},
                    {"data": "stop"},
                    {"data": "hours"},
                    {"data": "edited"},
                    {"data": "action"},
                ],
                "initComplete": function(settings, json) {
                    $('#company_and_site_name').text(json.client_and_site_name);
                    $('#company_logo').attr('src', json.client_logo_url);
                    $('.period_date').text(json.period_date);
                    $('#week_number').text(json.week_number);
                    $('#pay_date').text(json.pay_date);
                    $('#total_hrs').text(json.total_hrs);
                    payroll_already_created_section_hide_show(json.payroll_created_at, json.view_report_url_href)
                }
            });

            timesheet_total_hour_per_worker_table = timesheet_editor_total_hour_per_worker_datatable.DataTable({
                "processing"    : false,
                "serverSide"    : false,
                "ajax"          : {
                    "type"      : "post",
                    "url"       : '{{ url('get-timesheet-editor-total-hour-per-worker-data') }}',
                    "data"      : function (d) {
                        d._token = '{{ csrf_token() }}';
                        d.client = client;
                        d.site   = site;
                        d.pwn    = pwn;
                    },
                },
                "columns": [
                    {"data": "worker_name"},
                    {"data": "worker_id"},
                    {"data": "no_jobs"},
                    {"data": "no_shifts"},
                    {"data": "total_hrs"},
                ]
            });

            $("#timesheet_editor_total_hour_per_worker_datatable").removeClass('d-none');
            $('#timesheet_editor_total_hour_per_worker_datatable_wrapper').addClass('d-none');
            changeView();
        });

        const filterSearch = document.querySelector('[data-kt-timesheet-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            console.log("called");
            timesheet_shift_line_item_table.search(e.target.value).draw();
            timesheet_total_hour_per_worker_table.search(e.target.value).draw();
        });

        $(document).on('click', '#edit_timesheet', function () {
            $("#worker_name_and_date").text($(this).attr('data-worker'));
            $("#hours_worked").val($(this).attr('data-hours'));
            $("#timesheet_id").val($(this).attr('data-id'));
            $("#edit_timesheet_entry_modal").modal('show');
        });

        $("#cls_btn_edit_timesheet_entry_modal").on('click', function (){
            $("#worker_name_and_date").text('');
            $("#edit_timesheet_entry_form").trigger('reset');
            $("#edit_timesheet_entry_modal").modal('hide');
        });

        $("#edit_timesheet_entry_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#update_timesheet_entry_submit_btn").addClass('d-none');
            $("#update_timesheet_entry_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('edit-timesheet-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);

                    $("#update_timesheet_entry_submit_btn").removeClass('d-none');
                    $("#update_timesheet_entry_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#worker_name_and_date").text('');
                        $("#edit_timesheet_entry_form").trigger('reset');
                        $("#edit_timesheet_entry_modal").modal('hide');
                        $("#timesheet_filter_btn").click()
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#update_timesheet_entry_submit_btn").removeClass('d-none');
                    $("#update_timesheet_entry_process_btn").addClass('d-none');
                }
            });
        });

        $(document).on('click', '#delete_timesheet', function () {
            sweetAlertConfirmDelete('Do you want to delete this timesheet entry!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-timesheet-entry') }}'+'/'+$(this).attr('data-id'),
                        success : function (response) {
                            decodeResponse(response);

                            if(response.code === 200) {
                                $("#timesheet_filter_btn").click()
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $('input[name="timesheet_editor_datatable_view"]').change(function() {
            changeView();
        });

        function changeView() {
            var checkedValue = $('input[name="timesheet_editor_datatable_view"]:checked').val();

            if(checkedValue === 'view_1') {
                $('#timesheet_editor_shift_line_item_datatable_wrapper').removeClass('d-none');
                $('#timesheet_editor_total_hour_per_worker_datatable_wrapper').addClass('d-none');
            } else {
                $('#timesheet_editor_total_hour_per_worker_datatable_wrapper').removeClass('d-none');
                $('#timesheet_editor_shift_line_item_datatable_wrapper').addClass('d-none');
            }
        }

        function ignored_entry_section() {
            ignored_entry_datatable.DataTable().destroy();
            ignored_entry_table = ignored_entry_datatable.DataTable({
                "processing"    : false,
                "serverSide"    : false,
                "ajax"          : {
                    "type"      : "post",
                    "url"       : '{{ url('get-timesheet-editor-shift-line-item-data') }}',
                    "data"      : function (d) {
                        d._token = '{{ csrf_token() }}';
                        d.client = $("#client").val();
                        d.site   = $("#site").val();
                        d.pwn    = $("#payroll_week_number").val();
                        d.type   = 'unlock';
                    },
                },
                "columns": [
                    {"data": "worker_name","sClass":"text-danger"},
                    {"data": "date","sClass":"text-danger"},
                    {"data": "job","sClass":"text-danger"},
                    {"data": "start","sClass":"text-danger"},
                    {"data": "stop","sClass":"text-danger"},
                    {"data": "hours","sClass":"text-danger"},
                    {"data": "action","sClass":"text-danger"},
                ],
                "initComplete": function(settings, json) {
                    $('#ignored_timesheet_entry_datatable thead').addClass('text-muted');
                    $('#ignored_timesheet_entry_datatable thead th').removeClass('text-danger');
                    if (json.data.length > 0) {
                        $("#ignored_timesheet_entry_section").removeClass('d-none');
                    }
                }
            });
        }

        $(document).on('click', '#download_csv', function () {
            const site = $("#site").val();
            const payroll_week = $('#payroll_week_number').val();
            window.location.href = `export-timesheet-entry?site=${site}&payroll_week=${payroll_week}`;
        });
    </script>

    {{--BEGIN PAYROLL SCRIPT--}}
    @yield('create_payroll_script')
    @yield('success_payroll_script')
    {{--END PAYROLL SCRIPT--}}
@endsection
