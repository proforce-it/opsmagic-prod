<div class="tab-pane fade" id="kt_table_widget_5_tab_4">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <i class="fs-2 las la-search"></i>
                        </span>
                        <input type="text" data-kt-payroll-summary-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search" />
                    </div>
                </div>
                <div class="col-lg-9">
                    <a href="javascript:;" class="btn btn-sm btn-primary float-end" id="download_csv_site_summary_btn">
                        <i class="fs-2 las la-file-download"></i>
                        Download csv
                    </a>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-12 text-start text-muted fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table align-middle table-row-dashed fs-7 gy-3" id="payroll_summary_datatable">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th style="width: 10%;">Date</th>
                            <th style="width: 20%;">Site Name</th>
                            <th style="width: 15%;">Job Name</th>
                            <th style="width: 15%;">Worker Name</th>
                            <th style="width: 10%;">Rate</th>
                            <th style="width: 10%;" class="text-end">hours</th>
                            <th style="width: 10%;" class="text-end">tot. pay</th>
                            <th style="width: 10%;" class="text-end">tot. chg</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr class="text-start text-dark fw-bolder fs-7 text-uppercase gs-0">
                            <td colspan="5">Total(<span id="p_total_records">0</span>)</td>
                            <td class="text-end"><span id="p_total_hour">0</span></td>
                            <td class="text-end"><span id="p_total_pay">0</span></td>
                            <td class="text-end"><span id="p_total_charge">0</span></td>
                        </tr>
                        </tfoot>
                        <tbody class="text-gray-600 fw-bold"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('payroll_summary_js')
    <script>
        let payroll_summary_table_name = $("#payroll_summary_datatable")
        let payroll_summary_table = payroll_summary_table_name.DataTable();

        $(document).on('click', '#kt_table_widget_5_tab_4_menu', function () {
            payroll_summary_table.destroy()
            payroll_summary_table = payroll_summary_table_name.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-financial-payroll-summary-report') }}',
                    "data": function (d) {
                        d._token = $('#_token').val();
                        d.params = getSelectedDropdownValues();
                    },
                },
                "columns": [
                    {"data": "date", "width":"15%"},
                    {"data": "site_name", "width":"20%"},
                    {"data": "job_name", "width":"15%"},
                    {"data": "worker_name", "width":"15%"},
                    {"data": "pay_rate_name", "width":"5%"},
                    {"data": "hour", "width":"10%", "sClass":"text-end"},
                    {"data": "pay", "width":"10%", "sClass":"text-end"},
                    {"data": "charge", "width":"10%", "sClass":"text-end"},
                ],
                "initComplete": function(settings, json) {
                    $('#p_total_records').text(json.recordsTotal);
                    $('#p_total_hour').text(json.total_hour);
                    $('#p_total_pay').text(json.total_pay);
                    $('#p_total_charge').text(json.total_charge);
                }
            });
        })

        const filterSearchPayrollSummary = document.querySelector('[data-kt-payroll-summary-table-filter="search"]');
        filterSearchPayrollSummary.addEventListener('keyup', function (e) {
            payroll_summary_table.search(e.target.value).draw();
        });
    </script>
@endsection
