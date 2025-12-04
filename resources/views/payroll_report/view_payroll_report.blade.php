@extends('theme.page')

@section('title', 'View payroll report')

@section('content')
    <style>

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
                                                                    <option {{ (isset($selectedData['client_id']) && $row['id'] == $selectedData['client_id']) ? 'selected' : '' }} value="{{ $row['id'] }}">{{ $row['company_name'] }}</option>
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
                                                            @if(isset($selectedData['sites']))
                                                                @foreach($selectedData['sites'] as $s_row)
                                                                    <option {{ ($s_row['id'] == $selectedData['site_id']) ? 'selected' : '' }} value="{{ $s_row['id'] }}">{{ $s_row['site_name'] }}</option>
                                                                @endforeach
                                                            @endif
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
                                                                    @php($value = $pwn_row['payroll_week_number'].'_'.$pwn_row['year'])
                                                                    <option {{ (isset($selectedData['payroll_week']) && $value == $selectedData['payroll_week']) ? 'selected' : '' }} value="{{ $value }}">{{ $pwn_row['payroll_week_number'].' - '.$pwn_row['year'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <button type="submit" name="payroll_filter_btn" id="payroll_filter_btn" class="btn btn-primary mt-7 w-100">Go</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <a href="{{ url('view-payroll-report') }}" id="reset_search_form"  class="btn btn-dark mt-7 w-100">Reset</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-5 d-none" id="payrollViewSection">
                                <div class="card-body py-4">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div class="p-5">
                                                <div class="row gs-0 border-bottom border-4">
                                                    <div class="col-lg-8 mb-5">
                                                        <div class="text-start text-muted fw-bolder fs-5" id="company_and_site_name">-</div>
                                                        <div class="fw-bolder fs-3">
                                                            Payroll report (<span class="payroll_report_date_between"></span>)
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 mb-5 text-end">
                                                        <img id="company_logo" src="" alt="No image." style="width: 200px; height: 75px; object-fit: contain; object-position: right;">
                                                    </div>
                                                </div>
                                                <div class="row gs-0 border-bottom border-4 mt-4 timesheet_and_bonus_new_entry_created d-none">
                                                    <div class="col-lg-12 gs-0">
                                                        <div class="alert alert-danger fw-boldest" role="alert">
                                                            Timesheet and/or bonus entries have been uploaded since this report was produced. <a href="javascript:;" id="timesheet_and_bonus_new_entry_created" class="text-decoration-underline">View them in the editor</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row border-bottom border-4">
                                                    <div class="col-lg-6 pt-5">
                                                        <label class="text-muted fs-4 fw-bold">Client</label>
                                                        <h2 class="fs-4 fw-boldest" id="payroll_report_client_name">-</h2>
                                                    </div>
                                                    <div class="col-lg-6 pt-5">
                                                        <label class="text-muted fs-4 fw-bold">Site</label>
                                                        <h2 class="fs-4 fw-boldest" id="payroll_report_site_name">-</h2>
                                                    </div>
                                                    <div class="col-lg-6 pt-3">
                                                        <label class="text-muted fs-4 fw-bold">Operations payroll week no.</label>
                                                        <h2 class="fs-4 fw-boldest" id="payroll_report_week_number">-</h2>
                                                    </div>
                                                    <div class="col-lg-6 pt-3">
                                                        <label class="text-muted fs-4 fw-bold">Pay date</label>
                                                        <h2 class="fs-4 fw-boldest" id="payroll_report_pay_date">-</h2>
                                                    </div>
                                                    <div class="col-lg-6 pt-3">
                                                        <label class="text-muted fs-4 fw-bold">Client week starts on</label>
                                                        <h2 class="fs-4 fw-boldest" id="payroll_report_client_week_start_on">-</h2>
                                                    </div>
                                                    <div class="col-lg-6 pt-3">
                                                        <label class="text-muted fs-4 fw-bold">Shift data between</label>
                                                        <h2 class="fs-4 fw-boldest payroll_report_date_between">-</h2>
                                                    </div>
                                                    <div class="col-lg-6 pt-3 pb-5">
                                                        <label class="text-muted fs-4 fw-bold">Total charge</label>
                                                        <h2 class="fs-4 fw-boldest">
                                                            &pound;
                                                            <span id="payroll_report_total_charge">-</span>
                                                        </h2>
                                                    </div>
                                                    <div class="col-lg-6 pt-3 pb-5">
                                                        <label class="text-muted fs-4 fw-bold">Total pay</label>
                                                        <h2 class="fs-4 fw-boldest">
                                                            &pound;
                                                            <span id="payroll_report_total_pay">-</span>
                                                        </h2>
                                                    </div>
                                                </div>
                                                <div class="row mt-5 gs-0 border-bottom border-4">
                                                    <div class="col-lg-12 mb-5">
                                                        <div class="d-flex align-items-center position-relative my-1">
                                                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                                    <i class="fs-2 las la-search"></i>
                                                                </span>
                                                            <input type="text" data-kt-payroll-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find worker" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-5 gs-0">
                                                    <div class="col-lg-12">
                                                        <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="payroll_datatable">
                                                            <thead>
                                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                <th>Worker</th>
                                                                <th>Type</th>
                                                                <th>Rate</th>
                                                                <th>Job</th>
                                                                <th>d1</th>
                                                                <th>d2</th>
                                                                <th>d3</th>
                                                                <th>d4</th>
                                                                <th>d5</th>
                                                                <th>d6</th>
                                                                <th>d7</th>
                                                                <th>Total hours</th>
                                                                <th>Total charge</th>
                                                                <th>Total pay</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody class="text-gray-600 fw-bold"></tbody>
                                                            <tfoot>
                                                            <tr class="text-start text-muted fw-bolder fs-5 text-uppercase gs-0">
                                                                <th colspan="11" class="text-end text-dark">Totals</th>
                                                                <th id="footer_total_hours" class="text-end text-dark"></th>
                                                                <th id="footer_total_charge" class="text-end text-dark"></th>
                                                                <th id="footer_total_pay" class="text-end text-dark"></th>
                                                            </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="javascript:;" class="btn btn-primary export_payroll_btn" id="export_payroll_btn">
                                        <span class="svg-icon svg-icon-2">
                                            <i class="fs-2 las la-file-download"></i>
                                        </span>
                                        Export to csv
                                    </a> <br> <br>
                                    <span id="reported_edit_or_not"></span>
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

        $(document).ready(function() {
            let payrollUrlParams = '{{ \Illuminate\Support\Facades\Request::get('payroll') }}';
            if (payrollUrlParams && payrollUrlParams.trim() !== '') {
                $("#payroll_filter_btn").click();
            }
        });

        let payroll_table;
        payroll_datatable = $('#payroll_datatable');

        $(document).ready(function() {
            payroll_table = payroll_datatable.DataTable();
        })

        $("#payroll_filter_btn").on('click', function() {
            let client = $("#client").val();
            let site   = $("#site").val();
            let pwn    = $("#payroll_week_number").val();

            if (!client || !site || !pwn) {
                toastr.error('Please fill in all filter fields before submitting.');
                return;
            }

            let filterParam = "payroll=" + client + "." + site + "." + pwn;
            let newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + filterParam;
            window.history.pushState({ path: newUrl }, '', newUrl);

            $("#payrollViewSection").removeClass('d-none');
            payroll_datatable.DataTable().destroy();
            payroll_table = payroll_datatable.DataTable({
                "processing"    : false,
                "serverSide"    : false,
                "ajax"          : {
                    "type"      : "post",
                    "url"       : '{{ url('get-payroll-data') }}',
                    "data"      : function (d) {
                        d._token = '{{ csrf_token() }}';
                        d.client = client;
                        d.site   = site;
                        d.pwn    = pwn;
                    },
                },
                "columns": [
                    {"data": "worker_name"},
                    {"data": "type"},
                    {"data": "rate"},
                    {"data": "job"},
                    {"data": "day_1", "sClass": "text-end"},
                    {"data": "day_2", "sClass": "text-end"},
                    {"data": "day_3", "sClass": "text-end"},
                    {"data": "day_4", "sClass": "text-end"},
                    {"data": "day_5", "sClass": "text-end"},
                    {"data": "day_6", "sClass": "text-end"},
                    {"data": "day_7", "sClass": "text-end"},
                    {"data": "total_hours", "sClass": "text-end"},
                    {"data": "total_charge", "sClass": "text-end"},
                    {"data": "total_pay", "sClass": "text-end"},
                ],
                "initComplete": function(settings, json) {
                    $("#company_and_site_name").text(json.client_name +' > '+json.site_name);
                    $('#company_logo').attr('src', json.client_logo_url);
                    $("#payroll_report_client_name").text(json.client_name);
                    $("#payroll_report_site_name").text(json.site_name);
                    $("#payroll_report_week_number").text(json.payroll_week_number);
                    $("#payroll_report_pay_date").text(json.pay_date);
                    $("#payroll_report_client_week_start_on").text(json.client_week_start);
                    $(".payroll_report_date_between").text(json.date_between);
                    $("#payroll_report_total_charge").text(json.total_charge);
                    $("#payroll_report_total_pay").text(json.total_pay);
                    $("#reported_edit_or_not").html(json.reported_edit_or_not)

                    if (json.timesheet_and_bonus_new_entry_created !== '') {
                        $(".timesheet_and_bonus_new_entry_created").removeClass('d-none');
                        $("#timesheet_and_bonus_new_entry_created").attr('href', json.timesheet_and_bonus_new_entry_created);
                    }
                },
                "drawCallback": function(settings) {
                    var total_hours = 0;
                    var total_charge = 0;
                    var total_pay = 0;

                    this.api().rows({ page: 'current' }).data().each(function(rowData) {
                        total_hours += parseFloat(rowData.total_hours) || 0;
                        total_charge += parseFloat(rowData.total_charge) || 0;
                        total_pay += parseFloat(rowData.total_pay) || 0;
                    });

                    $('#footer_total_hours').text(total_hours.toFixed(2));
                    $('#footer_total_charge').text(total_charge.toFixed(2));
                    $('#footer_total_pay').text(total_pay.toFixed(2));
                }
            });
        });

        const filterSearch = document.querySelector('[data-kt-payroll-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            payroll_table.search(e.target.value).draw();
        });

        $(document).on('click', '.export_payroll_btn', function () {
            const site = $("#site").val();
            const payroll_week = $('#payroll_week_number').val();
            window.location.href = `export-payroll-item?site=${site}&payroll_week=${payroll_week}`;
        });

        $(document).on('click', '#delete_report', function () {
            let client_id = $("#client").val();
            let site_id = $("#site").val();
            let pwd = $("#payroll_week_number").val();

            sweetAlertConfirmDelete('Do you want to delete this report!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('delete-payroll-report') }}',
                        data    : {
                            _token  : '{{ @csrf_token() }}',
                            site_id : site_id,
                            pwd     : pwd,
                        },
                        success : function (response) {
                            decodeResponse(response);

                            if(response.code === 200) {
                                setTimeout(function () {
                                    window.location.href = '{{ url('timesheet-and-bonus-editor/?filtered=') }}'+client_id+'.'+site_id+'.'+pwd;
                                }, 1500)
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });
    </script>
@endsection
