<div class="tab-pane fade active show" id="timesheet_nav">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="col-lg-12" id="timesheet_editor_section">
                    <div id="timesheetData" class="d-none">
                        <div class="p-5">
                            <div class="row gs-0 border-bottom border-4">
                                <div class="col-lg-8 mb-5">
                                    <div class="text-start text-muted fw-bolder fs-5" id="company_and_site_name">-</div>
                                    <div class="fw-bolder fs-3">
                                        <i class="fs-2 las la-clock"></i>
                                        Timesheet editor (<span class="period_date"></span>)
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-5 text-end">
                                    <img id="company_logo" src="" alt="No image." style="object-fit: contain; object-position: right 50%; width: 200px; height: 75px; display: block;"> <!--width: 100px; object-fit: contain; object-position: right;-->
                                </div>
                            </div>
                            <div class="row gs-0 border-bottom border-4 mt-4 d-none" id="ignored_timesheet_entry_section">
                                <div class="col-lg-12 gs-0">
                                    <div class="alert alert-danger fw-boldest" role="alert">
                                        The following timesheet entries were added after the payroll report was processed and have been ignored
                                    </div>

                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="ignored_timesheet_entry_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>WORKER</th>
                                            <th>DATE</th>
                                            <th>JOB</th>
                                            <th>START TIME</th>
                                            <th>HOURS</th>
                                            <th>ACTION</th>
                                        </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold"></tbody>
                                    </table>

                                    <div class="mt-4 mb-4 text-center">
                                        <button type="button" id="delete_timesheet_entry" class="btn btn-outline btn-outline-danger text-hover-white delete-all-ignore-entry" data-type="timesheet">Delete 0 entry</button>
                                        <span id="add_or_update_timesheet_report_btn"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row gs-0 border-bottom border-4 mt-4 payroll_already_created_show_section d-none">
                                <div class="col-lg-12 gs-0">
                                    <div class="alert alert-success fw-boldest" role="alert">
                                        The following timesheet entries have been locked and added to a <a href="javascript:;" class="view_payroll_report_href text-decoration-underline">payroll report</a> generated <span class="payroll_created_at"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4 gs-0 border-bottom border-4 payroll_already_created_hide_section">
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
                            <div class="row mt-4 gs-0 border-bottom border-4">
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
                            <div class="row mt-4 gs-0 border-bottom border-4">
                                <div class="col-lg-6 mb-5">
                                    <div class="d-flex align-items-center position-relative my-1">
                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                        <i class="fs-2 las la-search"></i>
                                    </span>
                                        <input type="text" data-kt-timesheet-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find worker" />
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-5">
                                    <a href="javascript:;" class="btn btn-primary float-end" id="download_timesheet_csv">
                                        <i class="fs-2 las la-file-download"></i>
                                        Download .csv
                                    </a>
                                </div>
                            </div>
                            <div class="row mt-5 gs-0">
                                <div class="col-lg-12">
                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="timesheet_editor_shift_line_item_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>WORKER</th>
                                            <th>DATE</th>
                                            <th>JOB</th>
                                            <th>START TIME</th>
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
</div>

@section('timesheet_editor_section_js')
    <script>
        /*--- LOAD DATATABLE ---*/
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

        /*--- GET DATA FROM SERVER AND PREPARED VIEW ---*/
        function preparedTimesheetView(client, site, pwn) {
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
                    {"data": "start_time"},
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
                    $('#add_or_update_timesheet_report_btn').html(json.add_or_update_timesheet_report_btn);
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
            changeTimesheetView();
        }

        /*--- FILTER SINGLE SEARCH TEXT BOX ACTION ---*/
        const filterSearch = document.querySelector('[data-kt-timesheet-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            timesheet_shift_line_item_table.search(e.target.value).draw();
            timesheet_total_hour_per_worker_table.search(e.target.value).draw();
        });

        /*--- CLICK ON TIMESHEET NAV MENU ---*/
        $(document).on('click', '#timesheet_nav_menu', function () {
            if($("#client").val() && $("#site").val() && $("#payroll_week_number").val()) {
                preparedTimesheetIgnoreEntry();
                $("#filter_btn").click();
            }
        });

        /*--- TIMESHEET EDITOR DATATABLE CHANGE ---*/
        $('input[name="timesheet_editor_datatable_view"]').change(function() {
            changeTimesheetView();
        });

        function changeTimesheetView() {
            var checkedValue = $('input[name="timesheet_editor_datatable_view"]:checked').val();

            if(checkedValue === 'view_1') {
                $('#timesheet_editor_shift_line_item_datatable_wrapper').removeClass('d-none');
                $('#timesheet_editor_total_hour_per_worker_datatable_wrapper').addClass('d-none');
            } else {
                $('#timesheet_editor_total_hour_per_worker_datatable_wrapper').removeClass('d-none');
                $('#timesheet_editor_shift_line_item_datatable_wrapper').addClass('d-none');
            }
        }

        /*--- DELETE TIMESHEET ENTRY ACTION ---*/
        $(document).on('click', '#delete_timesheet', function () {
            sweetAlertConfirmDelete('Do you want to delete this timesheet entry!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-timesheet-entry') }}'+'/'+$(this).attr('data-id'),
                        success : function (response) {
                            decodeResponse(response);

                            if(response.code === 200) {
                                $("#filter_btn").click()
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        /*--- GET IGNORED TIMESHEET ENTRY SECTION ---*/
        $(document).ready(function () {
            preparedTimesheetIgnoreEntry()
        })

        function preparedTimesheetIgnoreEntry() {
            if (!localStorage.getItem('viewTimesheetAndBonusActiveTab')) {
                localStorage.setItem('viewTimesheetAndBonusActiveTab', 'timesheet_nav_menu');
                document.getElementById("timesheet_nav_menu").click()
            }
            if (localStorage.getItem('viewTimesheetAndBonusActiveTab') === 'timesheet_nav_menu') {
                ignored_entry_section = window.timesheetIgnoredEntrySection;
            }
        }

        window.timesheetIgnoredEntrySection = function() {
            ignored_entry_datatable.DataTable().destroy();
            ignored_entry_table = ignored_entry_datatable.DataTable({
                "processing"    : false,
                "serverSide"    : false,
                "paging"        : false,
                "lengthChange"  : false,
                "info"          : false,
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
                    {"data": "start_time"},
                    {"data": "hours","sClass":"text-danger"},
                    {"data": "action","sClass":"text-danger"},
                ],
                "initComplete": function(settings, json) {
                    $('#ignored_timesheet_entry_datatable thead').addClass('text-muted');
                    $('#ignored_timesheet_entry_datatable thead th').removeClass('text-danger');
                    if (json.data.length > 0) {
                        $("#ignored_timesheet_entry_section").removeClass('d-none');
                        $("#delete_timesheet_entry")
                            .text("Delete "+json.data.length+" entries")
                            .attr('data-ids', json.array_ids);
                    }
                }
            });
        }

        /*--- EXPORT TIMESHEETS ENTRIES ---*/
        $(document).on('click', '#download_timesheet_csv', function () {
            const site = $("#site").val();
            const payroll_week = $('#payroll_week_number').val();
            window.location.href = `export-timesheet-entry?site=${site}&payroll_week=${payroll_week}`;
        });
    </script>
@endsection
