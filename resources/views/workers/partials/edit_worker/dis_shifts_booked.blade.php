<div class="tab-pane fade" id="kt_table_widget_5_tab_5">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <i class="fs-2 las la-search"></i>
                        </span>
                        <input type="text" data-kt-shifts-booked-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search shifts booked" />
                    </div>
                </div>

                <div class="col-lg-9 float-end">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <a href="javascript:;" class="btn btn-outline btn-outline-primary text-hover-gray-100 float-end" id="download_csv">
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
                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="shifts_booked_datatable">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Job Name</th>
                                <th>Site</th>
                                <th>Client</th>
                                <th>Date</th>
<!--                                <th>Start</th>
                                <th>Exp.Dur.</th>-->
                                <th>INV.</th>
                                <th>CONF.</th>
                                <th>DECL.</th>
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

@section('edit_worker_shifts_booked_js')
    <script>
        let tableNameShiftsBookedDatatable = $('#shifts_booked_datatable');
        let shifts_book_table = tableNameShiftsBookedDatatable.DataTable();
        let shiftBookCsvTableData;


        $(document).on('click', '#kt_table_widget_5_tab_5_menu', function () {
            shifts_book_table.destroy();

            $.fn.dataTable.ext.type.order['date-pre'] = function (data) {
                var dateParts = data.split('-');
                return new Date(dateParts[2], dateParts[1] - 1, dateParts[0]).getTime();
            };

            shifts_book_table = tableNameShiftsBookedDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-worker-shifts-booked') }}',
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
                    /*{"data": "start_time"},
                    {"data": "exp_dur"},*/
                    {"data": "invited_at", "width":"4%", "sClass":"text-center"},
                    {"data": "confirmed_at", "width":"4%", "sClass":"text-center"},
                    {"data": "declined_at", "width":"4%", "sClass":"text-center"},
                    {"data": "action", "width":"10%", "sClass":"text-end"},
                ],
                "drawCallback": function(settings) {
                    if (settings.json && settings.json.recordsFiltered !== undefined) {
                        shiftBookCsvTableData = settings.json.shiftBookCsvTableData
                    }
                },
                "order": [[ 3, "desc" ]],
            });
        })

        const filterShiftsBookSearch = document.querySelector('[data-kt-shifts-booked-table-filter="search"]');
        filterShiftsBookSearch.addEventListener('keyup', function (e) {
            shifts_book_table.search(e.target.value).draw();
        });

        $('#download_csv').on('click', function() {
            let shiftsBookedCsv = shiftBookCsvTableData.map(row => row.join(',')).join('\n');
            let shiftsBookedCsvFile;
            let shiftsBookedDownloadLink;

            shiftsBookedCsvFile = new Blob([shiftsBookedCsv], { type: 'text/csv' });
            shiftsBookedDownloadLink = document.createElement('a');
            shiftsBookedDownloadLink.download = '{{ $worker['first_name'].'_'.$worker['middle_name'].'_'.$worker['last_name'] }}'+'_shifts_book.csv';
            shiftsBookedDownloadLink.href = window.URL.createObjectURL(shiftsBookedCsvFile);
            shiftsBookedDownloadLink.style.display = 'none';
            document.body.appendChild(shiftsBookedDownloadLink);
            shiftsBookedDownloadLink.click();
        });
    </script>
@endsection
