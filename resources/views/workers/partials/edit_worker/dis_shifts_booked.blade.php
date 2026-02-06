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

                        <a href="javascript:;" id="manage_worker_shifts_btn" data-job_id="" class="ms-5">
                            <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
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

<!-- BEGIN SHIFT MODAL-->
<div class="modal fade" id="manage_worker_shifts_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Manage shifts for {{ $worker['first_name'] }} (next 14 days)</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_manage_worker_shifts_modal">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="manage_worker_shifts_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label for="manage_worker_shifts_job_select_box" class="text-muted fs-6 fw-bold">Job</label>
                                        <select name="manage_worker_shifts_job_select_box" id="manage_worker_shifts_job_select_box" class="form-select form-select-lg manage-worker-shifts-form-select-custom" data-control="select2" data-placeholder="Select job..." data-allow-clear="true">
                                            <option value=""></option>
                                            @if($assignedJobs)
                                                @foreach($assignedJobs as $assigned_job_row)
                                                    <option value="{{ $assigned_job_row['job']['id'] }}">{{ $assigned_job_row['job']['name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger error" id="manage_worker_shifts_job_select_box_error"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row manage_worker_shifts_table_section d-none">
                                <div class="col-lg-12 text-start text-muted fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
                                <div class="col-lg-12 mt-5">
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <div class="d-flex align-self-center">
                                            <div class="flex-grow-1 me-10">
                                                <select name="worker_availability_action_type_box" id="worker_availability_action_type_box" class="form-select" data-control="select2" data-placeholder="With selected..." data-hide-search="true">
                                                    <option value="">With selected</option>
                                                    <option value="add_to_shift_as_confirmed">Add to shift as confirmed</option>
                                                    <option value="invite_to_shift">Invite to shift</option>
                                                    <option value="mark_invited_as_confirmed">Mark invited as confirmed</option>
                                                    <option value="mark_invited_as_declined">Mark invited as declined</option>
                                                    <option value="unassign_from_shift">Unassign from shift </option>
                                                    <option value="cancel_worker_from_shift">Cancel worker from shift</option>
                                                    <option value="mark_as_rest">Set as rest</option>
                                                    <option value="mark_as_sick">Set as sick</option>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon flex-shrink-0" id="manage_worker_shifts_form_submit_btn">Go</button>
                                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="manage_worker_shifts_form_process_btn" id="manage_worker_shifts_form_process_btn" style="display: none">
                                                <span>Please wait...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                        <span class="text-danger error" id="worker_availability_action_type_box_error"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row manage_worker_shifts_table_section d-none" id="manage_worker_shifts_table_section">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END SHIFT MODAL-->

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

        $(document).ready(function() {
            $(".manage-worker-shifts-form-select-custom").select2({
                dropdownParent: $("#manage_worker_shifts_form")
            });
        });

        $(document).on('click', '#manage_worker_shifts_btn', function () {
            let manage_worker_shifts_job_select_box = $("#manage_worker_shifts_job_select_box")
            if ($(this).hasClass('disabled_manage_worker_shifts_job_select_box')) {
                manage_worker_shifts_job_select_box.prop('disabled', true);
                manage_worker_shifts_job_select_box.next('.select2').find('.select2-selection').css('background-color', '#e4e6ee');
            } else {
                manage_worker_shifts_job_select_box.prop('disabled', false);
                manage_worker_shifts_job_select_box.next('.select2').find('.select2-selection').css('background-color', '');
            }

            manage_worker_shifts_job_select_box.val($(this).attr('data-job_id')).trigger('change');
            $(".manage_worker_shifts_table_section").addClass('d-none');
            $("#manage_worker_shifts_table_section").html('');
            $("#manage_worker_shifts_modal").modal('show');
        });

        $("#cls_btn_manage_worker_shifts_modal").on('click', function () {
            $("#manage_worker_shifts_job_select_box").val('').trigger('change');
            $(".manage_worker_shifts_table_section").addClass('d-none');
            $("#manage_worker_shifts_table_section").html('');
            $("#manage_worker_shifts_modal").modal('hide');
        });

        $("#manage_worker_shifts_job_select_box").on('change', function () {
            let manage_worker_shifts_job_select_box_id = $(this).val()
            if (manage_worker_shifts_job_select_box_id !== '') {
                get_next_14_day_shift(manage_worker_shifts_job_select_box_id);
            }
        });

        function get_next_14_day_shift(manage_worker_shifts_job_select_box_id) {
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-next-14-day-shift') }}',
                data    : {
                    _token : '{{ csrf_token() }}',
                    job_id : manage_worker_shifts_job_select_box_id,
                    worker_id : '{{ $worker['id'] }}'
                },
                success: function (response) {
                    if (response.code === 200) {
                        $(".manage_worker_shifts_table_section").removeClass('d-none');
                        $("#manage_worker_shifts_table_section").html(response.data.html);

                        const tooltipTriggerList = [].slice.call(
                            document.querySelectorAll('#manage_worker_shifts_modal [data-bs-toggle="tooltip"]')
                        );

                        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                            new bootstrap.Tooltip(tooltipTriggerEl);
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

        $("#manage_worker_shifts_form_submit_btn").on('click', function () {
            let worker_availability_action_type = $("#worker_availability_action_type_box").val();
            let worker_availability_checked_worker = [];

            $('input[name="worker_availability_checkbox"]:checked').each(function() {
                worker_availability_checked_worker.push($(this).val());
            });

            if (worker_availability_action_type === '') {
                toastr.error('Please select a action.')
            } else if(worker_availability_checked_worker.length === 0) {
                toastr.error('Please select a workers.')
            } else {
                $("#manage_worker_shifts_form_submit_btn").hide();
                $("#manage_worker_shifts_form_process_btn").show();

                $.ajax({
                    type : 'post',
                    url : '{{ url('action-on-worker-availability') }}',
                    data : {
                        _token : '{{ csrf_token() }}',
                        worker_availability_action_type : worker_availability_action_type,
                        worker_availability_checked_worker : worker_availability_checked_worker,
                    },
                    success : function (response) {

                        $("#manage_worker_shifts_form_submit_btn").show();
                        $("#manage_worker_shifts_form_process_btn").hide();

                        if(response.code === 200) {
                            toastr.success(response.message);
                            $("#worker_availability_action_type_box").val('').trigger('change');
                            get_next_14_day_shift($("#manage_worker_shifts_job_select_box").val());
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error   : function (response) {
                        $("#manage_worker_shifts_form_submit_btn").show();
                        $("#manage_worker_shifts_form_process_btn").hide();

                        toastr.error(response.statusText);
                    }
                });
            }
        });
    </script>
@endsection
