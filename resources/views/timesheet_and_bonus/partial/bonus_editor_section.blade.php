<div class="tab-pane fade" id="bonus_nav">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="col-lg-12" id="timesheet_editor_section">
                    <div id="bonusData" class="d-none">
                        <div class="p-5">
                            <div class="row gs-0 border-bottom border-4">
                                <div class="col-lg-8 mb-5">
                                    <div class="text-start text-muted fw-bolder fs-5" id="b_company_and_site_name">-</div>
                                    <div class="fw-bolder fs-3">
                                        <i class="fs-2 las la-money-bill"></i>
                                        Bonus editor (<span class="b_period_date"></span>)
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-5 text-end">
                                    <img id="b_company_logo" src="" alt="No image." style="object-fit: contain; object-position: right 50%; width: 200px; height: 75px; display: block;"> <!--width: 100px; object-fit: contain; object-position: right;-->
                                </div>
                            </div>
                            <div class="row gs-0 border-bottom border-4 mt-4 d-none" id="ignored_bonus_entry_section">
                                <div class="col-lg-12 gs-0">
                                    <div class="alert alert-danger fw-boldest" role="alert">
                                        The following bonus entries were added after the payroll report was processed and have been ignored
                                    </div>

                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="ignored_bonus_entry_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>WORKER</th>
                                            <th>JOB</th>
                                            <th>BONUS TYPE</th>
                                            <th>CHARGE</th>
                                            <th>PAY</th>
                                            <th>EDITED</th>
                                            <th>ACTION</th>
                                        </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold"></tbody>
                                    </table>

                                    <div class="mt-4 mb-4 text-center">
                                        <button type="button" id="delete_bonus_entry" class="btn btn-outline btn-outline-danger text-hover-white delete-all-ignore-entry" data-type="bonus">Delete 0 entry</button>
                                        <span id="add_or_update_timesheet_report_btn"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row gs-0 border-bottom border-4 mt-4 payroll_already_created_show_section d-none">
                                <div class="col-lg-12 gs-0">
                                    <div class="alert alert-success fw-boldest" role="alert">
                                        The following bonus entries have been locked and added to a <a href="javascript:;" class="view_payroll_report_href text-decoration-underline">payroll report</a> generated <span class="payroll_created_at"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4 gs-0 border-bottom border-4 payroll_already_created_hide_section">
                                <div class="col-lg-6 mb-5">
                                    <div class="text-start text-muted fw-bolder fs-5">Period</div>
                                    <div class="fw-bolder fs-3" id="b_period_date"><span class="b_period_date">-</span> <span id="b_week_number">-</span></div>
                                </div>
                                <div class="col-lg-2 mb-5">
                                    <div class="text-start text-muted fw-bolder fs-5">Pay date</div>
                                    <div class="fw-bolder fs-3" id="b_pay_date">-</div>
                                </div>
                                <div class="col-lg-2 mb-5">
                                    <div class="text-start text-muted fw-bolder fs-5">Total Bonus Pay</div>
                                    <div class="fw-bolder fs-3" id="b_total_bonus_pay">-</div>
                                </div>
                                <div class="col-lg-2 mb-5">
                                    <div class="text-start text-muted fw-bolder fs-5">Total Bonus charge</div>
                                    <div class="fw-bolder fs-3" id="b_total_bonus_charge">-</div>
                                </div>
                            </div>
                            <div class="row mt-4 gs-0 border-bottom border-4">
                                <div class="col-lg-12 mb-5">
                                    <div class="text-start text-muted fw-bolder fs-5">View</div>
                                    <div>
                                        <label class="form-check-inline me-5">
                                            <input type="radio" name="bonus_editor_datatable_view" id="bonus_editor_view_1" value="view_1" checked>
                                            <span class="fw-bold fs-5">line items</span>
                                        </label>
                                        <label class="form-check-inline">
                                            <input type="radio" name="bonus_editor_datatable_view" id=bonus_editor_"view_2" value="view_2">
                                            <span class="fw-bold fs-5">Worker summary</span>
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
                                        <input type="text" data-kt-bonus-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find worker" />
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-5">
                                    <a href="javascript:;" class="btn btn-primary float-end" id="download_bonus_csv">
                                        <i class="fs-2 las la-file-download"></i>
                                        Download .csv
                                    </a>
                                </div>
                            </div>
                            <div class="row mt-5 gs-0">
                                <div class="col-lg-12">
                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="bonus_editor_shift_line_item_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>WORKER</th>
                                            <th>JOB</th>
                                            <th>BONUS TYPE</th>
                                            <th>CHARGE</th>
                                            <th>PAY</th>
                                            <th>EDITED</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold"></tbody>
                                    </table>

                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark d-none" id="bonus_editor_total_hour_per_worker_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>WORKER ID</th>
                                            <th>WORKER NAME</th>
                                            <th>NO. BONUSES</th>
                                            <th>CHARGE</th>
                                            <th>PAY</th>
                                            <th>EDITED</th>
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

@section('bonus_editor_section_js')
    <script>
        /*--- LOAD DATATABLE ---*/
        let bonus_shift_line_item_table;
        let bonus_editor_shift_line_item_datatable = $('#bonus_editor_shift_line_item_datatable');

        let bonus_total_hour_per_worker_table;
        let bonus_editor_total_hour_per_worker_datatable = $('#bonus_editor_total_hour_per_worker_datatable');

        let bonus_ignored_entry_table;
        let bonus_ignored_entry_datatable = $('#ignored_bonus_entry_datatable');

        $(document).ready(function() {
            bonus_shift_line_item_table = bonus_editor_shift_line_item_datatable.DataTable();
            bonus_total_hour_per_worker_table = bonus_editor_total_hour_per_worker_datatable.DataTable();
            bonus_ignored_entry_table = bonus_ignored_entry_datatable.DataTable();
        })

        /*--- GET DATA FROM SERVER AND PREPARED VIEW ---*/
        function preparedBonusView(client, site, pwn) {
            $("#bonusData").removeClass('d-none');

            bonus_editor_shift_line_item_datatable.DataTable().destroy();
            bonus_editor_total_hour_per_worker_datatable.DataTable().destroy();

            bonus_shift_line_item_table = bonus_editor_shift_line_item_datatable.DataTable({
                "processing"    : false,
                "serverSide"    : false,
                "ajax"          : {
                    "type"      : "post",
                    "url"       : '{{ url('get-bonus-editor-line-item-data') }}',
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
                    {"data": "job"},
                    {"data": "bonus_type"},
                    {"data": "charge"},
                    {"data": "pay"},
                    {"data": "edited"},
                    {"data": "action"},
                ],
                "initComplete": function(settings, json) {
                    $('#b_company_and_site_name').text(json.client_and_site_name);
                    $('#b_company_logo').attr('src', json.client_logo_url);
                    $('.b_period_date').text(json.period_date);
                    $('#b_week_number').text(json.week_number);
                    $('#b_pay_date').text(json.pay_date);
                    $('#b_total_bonus_pay').text(json.total_bonus_pay);
                    $('#b_total_bonus_charge').text(json.total_bonus_charge);
                    $('#add_or_update_bonus_report_btn').html(json.add_or_update_bonus_report_btn);
                    payroll_already_created_section_hide_show(json.payroll_created_at, json.view_report_url_href)
                }
            });

            bonus_total_hour_per_worker_table = bonus_editor_total_hour_per_worker_datatable.DataTable({
                "processing"    : false,
                "serverSide"    : false,
                "ajax"          : {
                    "type"      : "post",
                    "url"       : '{{ url('get-bonus-editor-worker-summary-data') }}',
                    "data"      : function (d) {
                        d._token = '{{ csrf_token() }}';
                        d.client = client;
                        d.site   = site;
                        d.pwn    = pwn;
                    },
                },
                "columns": [
                    {"data": "worker_id"},
                    {"data": "worker_name"},
                    {"data": "no_bonuses"},
                    {"data": "charge"},
                    {"data": "pay"},
                    {"data": "edited"},
                ]
            });

            $("#bonus_editor_total_hour_per_worker_datatable").removeClass('d-none');
            $('#bonus_editor_total_hour_per_worker_datatable_wrapper').addClass('d-none');
            changeBonusView()
        }

        /*--- FILTER SINGLE SEARCH TEXT BOX ACTION ---*/
        const bonusFilterSearch = document.querySelector('[data-kt-bonus-table-filter="search"]');
        bonusFilterSearch.addEventListener('keyup', function (e) {
            bonus_shift_line_item_table.search(e.target.value).draw();
            bonus_total_hour_per_worker_table.search(e.target.value).draw();
        });

        /*--- CLICK ON BONUS NAV MENU ---*/
        $(document).on('click', '#bonus_nav_menu', function () {
            if($("#client").val() && $("#site").val() && $("#payroll_week_number").val()) {
                preparedBonusIgnoreEntry();
                $("#filter_btn").click();
            }
        });

        /*--- BONUS EDITOR DATATABLE CHANGE ---*/
        $('input[name="bonus_editor_datatable_view"]').change(function() {
            changeBonusView()
        });

        function changeBonusView() {
            var checkedValue = $('input[name="bonus_editor_datatable_view"]:checked').val();
            if(checkedValue === 'view_1') {
                $('#bonus_editor_shift_line_item_datatable_wrapper').removeClass('d-none');
                $('#bonus_editor_total_hour_per_worker_datatable_wrapper').addClass('d-none');
            } else {
                $('#bonus_editor_total_hour_per_worker_datatable_wrapper').removeClass('d-none');
                $('#bonus_editor_shift_line_item_datatable_wrapper').addClass('d-none');
            }
        }

        /*--- DELETE BONUS ENTRY ACTION ---*/
        $(document).on('click', '#delete_bonus', function () {
            sweetAlertConfirmDelete('Do you want to delete this bonus entry!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-bonus-entry') }}'+'/'+$(this).attr('data-id'),
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

        /*--- GET IGNORED BONUS ENTRY SECTION ---*/
        $(document).ready(function () {
            preparedBonusIgnoreEntry()
        })

        function preparedBonusIgnoreEntry() {
            if (localStorage.getItem('viewTimesheetAndBonusActiveTab') === 'bonus_nav_menu') {
                ignored_entry_section = window.bonusIgnoredEntrySection;
            }
        }

        window.bonusIgnoredEntrySection = function() {
            bonus_ignored_entry_datatable.DataTable().destroy();
            bonus_ignored_entry_table = bonus_ignored_entry_datatable.DataTable({
                "processing"    : false,
                "serverSide"    : false,
                "paging"        : false,
                "lengthChange"  : false,
                "info"          : false,
                "ajax"          : {
                    "type"      : "post",
                    "url"       : '{{ url('get-bonus-editor-line-item-data') }}',
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
                    {"data": "job","sClass":"text-danger"},
                    {"data": "bonus_type","sClass":"text-danger"},
                    {"data": "charge","sClass":"text-danger"},
                    {"data": "pay","sClass":"text-danger"},
                    {"data": "edited","sClass":"text-danger"},
                    {"data": "action","sClass":"text-danger"}
                ],
                "initComplete": function(settings, json) {
                    $('#ignored_bonus_entry_datatable thead').addClass('text-muted');
                    $('#ignored_bonus_entry_datatable thead th').removeClass('text-danger');

                    if (json.data.length > 0) {
                        $("#ignored_bonus_entry_section").removeClass('d-none');
                        $("#delete_bonus_entry")
                            .text("Delete "+json.data.length+" entries")
                            .attr('data-ids', json.array_ids);
                    }
                }
            });
        }

        /*--- EXPORT BONUS ENTRIES ---*/
        $(document).on('click', '#download_bonus_csv', function () {
            const site = $("#site").val();
            const payroll_week = $('#payroll_week_number').val();
            window.location.href = `export-bonus-entry?site=${site}&payroll_week=${payroll_week}`;
        });
    </script>
@endsection
