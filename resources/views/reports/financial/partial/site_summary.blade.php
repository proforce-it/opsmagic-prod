<div class="tab-pane fade active show" id="kt_table_widget_5_tab_1">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
{{--                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />--}}
{{--                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />--}}
{{--                            </svg>--}}
                            <i class="fs-2 las la-search"></i>
                        </span>
                        <input type="text" data-kt-site-summary-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search" />
                    </div>
                </div>
                <div class="col-lg-9">
                    <a href="javascript:;" class="btn btn-sm btn-primary float-end" id="download_csv_site_summary_btn">
{{--                        <span class="svg-icon svg-icon-2">--}}
{{--                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">--}}
{{--                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">--}}
{{--                                    <polygon id="Shape" points="0 0 24 0 24 24 0 24"/>--}}
{{--                                    <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" id="Combined-Shape" fill="#000000" fill-rule="nonzero" opacity="0.3"/>--}}
{{--                                    <path d="M14.8875071,11.8306874 L12.9310336,11.8306874 L12.9310336,9.82301606 C12.9310336,9.54687369 12.707176,9.32301606 12.4310336,9.32301606 L11.4077349,9.32301606 C11.1315925,9.32301606 10.9077349,9.54687369 10.9077349,9.82301606 L10.9077349,11.8306874 L8.9512614,11.8306874 C8.67511903,11.8306874 8.4512614,12.054545 8.4512614,12.3306874 C8.4512614,12.448999 8.49321518,12.5634776 8.56966458,12.6537723 L11.5377874,16.1594334 C11.7162223,16.3701835 12.0317191,16.3963802 12.2424692,16.2179453 C12.2635563,16.2000915 12.2831273,16.1805206 12.3009811,16.1594334 L15.2691039,12.6537723 C15.4475388,12.4430222 15.4213421,12.1275254 15.210592,11.9490905 C15.1202973,11.8726411 15.0058187,11.8306874 14.8875071,11.8306874 Z" id="Shape" fill="#000000"/>--}}
{{--                                </g>--}}
{{--                            </svg>--}}

{{--                        </span>--}}
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
                    <table class="table align-middle table-row-dashed fs-7 gy-3" id="site_summary_datatable">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th style="width: 60%;">Site Name</th>
                                <th style="width: 10%;" class="text-end">shifts</th>
                                <th style="width: 10%;" class="text-end">hours</th>
                                <th style="width: 10%;" class="text-end">Bonus pay</th>
                                <th style="width: 10%;" class="text-end">Bonus chg</th>
                                <th style="width: 10%;" class="text-end">pay</th>
                                <th style="width: 10%;" class="text-end">charge</th>
                                <th style="width: 10%;" class="text-end">tot. pay</th>
                                <th style="width: 10%;" class="text-end">tot. chg</th>
                            </tr>
                        </thead>
                        <tfoot>
                        <tr class="text-start text-dark fw-bolder fs-7 text-uppercase gs-0">
                            <td>Total(<span id="s_total_records">0</span>)</td>
                            <td class="text-end"><span id="s_total_shift">0</span></td>
                            <td class="text-end"><span id="s_total_hour">0</span></td>
                            <td class="text-end"><span id="s_total_bonus_pay">0</span></td>
                            <td class="text-end"><span id="s_total_bonus_charge">0</span></td>
                            <td class="text-end"><span id="s_pay">0</span></td>
                            <td class="text-end"><span id="s_charge">0</span></td>
                            <td class="text-end"><span id="s_total_pay">0</span></td>
                            <td class="text-end"><span id="s_total_charge">0</span></td>
                        </tr>
                        </tfoot>
                        <tbody class="text-gray-600 fw-bold"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('site_summary_js')
    <script>
        let site_summary_table_name = $("#site_summary_datatable")
        let site_summary_table = site_summary_table_name.DataTable();

        $(document).on('click', '#kt_table_widget_5_tab_1_menu', function () {

            site_summary_table.destroy()
            site_summary_table = site_summary_table_name.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-financial-site-summary-report') }}',
                    "data": function (d) {
                        d._token = $('#_token').val();
                        d.params = getSelectedDropdownValues();
                    },
                },
                "columns": [
                    {"data": "site_name"},
                    {"data": "shifts", "width":"10%", "sClass":"text-end"},
                    {"data": "hour", "width":"10%", "sClass":"text-end"},
                    {"data": "bonus_pay", "width":"10%", "sClass":"text-end"},
                    {"data": "bonus_charge", "width":"10%", "sClass":"text-end"},
                    {"data": "pay", "width":"10%", "sClass":"text-end"},
                    {"data": "charge", "width":"10%", "sClass":"text-end"},
                    {"data": "total_pay", "width":"10%", "sClass":"text-end"},
                    {"data": "total_charge", "width":"10%", "sClass":"text-end"},
                ],
                "initComplete": function(settings, json) {
                    $('#s_total_records').text(json.recordsTotal);
                    $('#s_total_shift').text(json.total_shift);
                    $('#s_total_hour').text(json.total_hour);
                    $('#s_total_bonus_pay').text(json.bonus_pay);
                    $('#s_total_bonus_charge').text(json.bonus_charge);
                    $('#s_pay').text(json.pay);
                    $('#s_charge').text(json.charge);
                    $('#s_total_pay').text(json.total_pay);
                    $('#s_total_charge').text(json.total_charge);
                }
            });
        })

        const filterSearchSiteSummary = document.querySelector('[data-kt-site-summary-table-filter="search"]');
        filterSearchSiteSummary.addEventListener('keyup', function (e) {
            site_summary_table.search(e.target.value).draw();
        });
    </script>
@endsection
