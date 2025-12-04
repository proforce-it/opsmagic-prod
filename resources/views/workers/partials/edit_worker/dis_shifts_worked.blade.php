<div class="tab-pane fade" id="kt_table_widget_5_tab_6">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <i class="fs-2 las la-search"></i>
                        </span>
                        <input type="text" data-kt-shifts-worked-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search timesheets" />
                    </div>
                </div>

                <div class="col-lg-9 float-end">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <a href="javascript:;" class="btn btn-outline btn-outline-primary text-hover-gray-100 float-end" id="download_csv_shifts_worked">
                            <span class="svg-icon svg-icon-2 svg-icon-primary">
                                <i class="fs-2 las la-file-download" style="color: #009ef7"></i>
                            </span>
                            Download .csv
                        </a>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-12 text-start text-muted fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="shifts_worked_datatable">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Job Name</th>
                            <th>Site</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Payroll week</th>
                            <th>Start time</th>
                            <th>Hours</th>
                            <th>Edited</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('timesheet_and_bonus.partial.edit_timesheet_entry_modal')

@section('edit_worker_shifts_worked_js')
    @yield('edit_timesheet_entry_js')
    <script>
        let tableNameShiftsWorkedDatatable = $('#shifts_worked_datatable');
        let shifts_worked_table = tableNameShiftsWorkedDatatable.DataTable();
        let shiftWorkedCsvTableData;

        $(document).on('click', '#kt_table_widget_5_tab_6_menu', function () {
            $.fn.dataTable.ext.type.order['date-pre'] = function (data) {
                var dateParts = data.split('-');
                return new Date(dateParts[2], dateParts[1] - 1, dateParts[0]).getTime();
            };

            shifts_worked_table.destroy();
            shifts_worked_table = tableNameShiftsWorkedDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-worker-shifts-worked') }}',
                    "data": function (d) {
                        d._token    = $('#_token').val();
                        d.worker_id = '{{ $worker['id'] }}';
                    },
                },
                "columns": [
                    {"data": "name"},
                    {"data": "site"},
                    {"data": "client"},
                    {"data": "date", "type": "date"},
                    {"data": "payroll_week"},
                    {"data": "start_time"},
                    {"data": "hours"},
                    {"data": "edited", "width": "5%"},
                    {"data": "action", "width":"10%", "sClass":"text-end"},
                ],
                "drawCallback": function(settings) {
                    if (settings.json && settings.json.recordsFiltered !== undefined) {
                        shiftWorkedCsvTableData = settings.json.shiftWorkedCsvTableData
                    }
                },
                "order": [[ 3, "desc" ]],
            });
        })

        const filterShiftsWorkSearch = document.querySelector('[data-kt-shifts-worked-table-filter="search"]');
        filterShiftsWorkSearch.addEventListener('keyup', function (e) {
            shifts_worked_table.search(e.target.value).draw();
        });

        $('#download_csv_shifts_worked').on('click', function() {
            let shiftsWorkedCsv = shiftWorkedCsvTableData.map(row_sw => row_sw.join(',')).join('\n');
            let shiftsWorkedCsvFile;
            let shiftsWorkedDownloadLink;

            shiftsWorkedCsvFile = new Blob([shiftsWorkedCsv], { type: 'text/csv' });
            shiftsWorkedDownloadLink = document.createElement('a');
            shiftsWorkedDownloadLink.download = '{{ $worker['first_name'].'_'.$worker['middle_name'].'_'.$worker['last_name'] }}'+'_shifts_worked.csv';
            shiftsWorkedDownloadLink.href = window.URL.createObjectURL(shiftsWorkedCsvFile);
            shiftsWorkedDownloadLink.style.display = 'none';
            document.body.appendChild(shiftsWorkedDownloadLink);
            shiftsWorkedDownloadLink.click();
        });
    </script>
@endsection
