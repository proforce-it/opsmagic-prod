@extends('theme.page')
@php
    $view_type = \Illuminate\Support\Facades\Request::input('view_type');
@endphp
@section('title', 'Job - '.$job['name'].' (ID '.$job['id'].')')
@section('content')
    <style>
        .calendar-table {
            table-layout: fixed;
        }
        .calendar-table th {
            border: 1px solid #ddd;
            text-align: center;
            font-size: 18px;
        }
        .calendar-table td {
            border: 1px solid #ddd;
        }
        .week-highlight {
            background-color: #F4B52E !important;
        }

        .week-normal {
            background-color: #A1A5B7 !important;
        }

        .week-highlight,
        .week-normal {
            color: #fff !important;
        }

        .week-highlight div,
        .week-highlight span,
        .week-highlight a,
        .week-highlight a i,
        .week-normal div,
        .week-normal span {
            color: #fff !important;
        }

        .week-left-align {
            text-align: left !important;
            padding-left: 15px;
        }

        .worker-tooltip-img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            object-position: center;
        }
    </style>

    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post">
                        <div id="kt_content_container">
                            @include('job.job_detail_page_card')
                            <div class="card mb-5">
                                <div class="card-body py-4">
                                    <div class="table-responsive">
                                        <div class="p-5">
                                            <div class="row">
                                                <div class="w-100">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div id="filter_detail_Section" class="alert alert-custom" role="alert" style="background-color: #FFFFFF; padding: 0">
                                                                <div class="alert-text text-center">
                                                                    <div class="row">
                                                                        <div class="col-lg-12 text-center d-flex justify-content-center align-items-center fs-2 mb-3">
                                                                            <a href="javascript:;" class="me-5" id="previous_wa_week">
                                                                                <i class="las la-arrow-circle-left fs-1 text-dark"></i>
                                                                            </a>
                                                                            <span class="fw-bolder">PAYROLL WEEK <span id="worker_availability_tab_date"></span></span>
                                                                            <a href="javascript:;" class="ms-5" id="next_wa_week">
                                                                                <i class="las la-arrow-circle-right fs-1 text-dark"></i>
                                                                            </a>
                                                                        </div>
                                                                        <div class="col-lg-12 text-center d-flex justify-content-center align-items-center">
                                                                            <a href="javascript:;" class="btn btn-sm btn-outline btn-outline-primary text-hover-gray-100 p-5 me-3 cursor-pointer" id="copy_job_shift_future_week_btn" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-original-title="Copy shift">
                                                                                <i class="fs-xxl-1 las la-copy text-primary"></i>
                                                                            </a>
                                                                            <a href="javascript:;" class="btn btn-sm btn-outline btn-outline-primary text-hover-gray-100 p-5 cursor-pointer" id="exportBookings" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-original-title="Download booking sheet for this shift">
                                                                                <i class="fs-xxl-1 las la-file-download text-primary"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <div class="fv-row fv-plugins-icon-container bg-secondary border border-dark rounded p-5">
                                                                <div class="d-flex align-self-center">
                                                                    <div class="form-check form-check-sm form-check-custom me-10">
                                                                        <input name="worker_availability_available_worker" id="available_worker" class="form-check-input widget-9-check worker_availability_checkbox" type="checkbox" value="available_worker" />
                                                                        <label for="available_worker" class="fs-6 fw-bold ms-2 text-primary">Available</label>
                                                                    </div>
                                                                    <div class="form-check form-check-sm form-check-custom me-10">
                                                                        <input name="worker_availability_confirmed_worker" id="confirmed_worker" class="form-check-input widget-9-check worker_availability_checkbox" type="checkbox" value="confirmed_worker" />
                                                                        <label for="confirmed_worker" class="fs-6 fw-bold ms-2 text-primary">Confirmed</label>
                                                                    </div>
                                                                    <div class="form-check form-check-sm form-check-custom me-10">
                                                                        <input name="worker_availability_invited_worker" id="invited_worker" class="form-check-input widget-9-check worker_availability_checkbox" type="checkbox" value="invited_worker" />
                                                                        <label for="invited_worker" class="fs-6 fw-bold ms-2 text-primary">Invited</label>
                                                                    </div>
                                                                    <div class="form-check form-check-sm form-check-custom me-10">
                                                                        <input name="worker_availability_deselect_all_worker" id="deselect_all_worker" class="form-check-input widget-9-check worker_availability_checkbox" type="checkbox" value="deselect_all_worker" />
                                                                        <label for="deselect_all_worker" class="fs-6 fw-bold ms-2 text-primary">Deselect all</label>
                                                                    </div>
                                                                    <div class="flex-grow-1 me-10">
                                                                        <select name="worker_availability_action_type" id="worker_availability_action_type" class="form-select" data-control="select2" data-placeholder="With selected..." data-hide-search="true">
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
                                                                    <button type="button" class="btn btn-primary btn-icon flex-shrink-0" id="worker_availability_action_submit_btn">Go</button>
                                                                    <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="worker_availability_action_process_btn" id="worker_availability_action_process_btn" style="display: none">
                                                                        <span>Please wait...
                                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                                <span class="text-danger error" id="assign_selected_workers_via_error"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12 mt-10">
                                                            <table class="calendar-table" cellpadding="5" id="worker_availability_datatable" style="margin-top: 6px !important; margin-bottom: 6px !important;">
                                                                <thead>
                                                                <tr class="text-gray-500 fw-normal">
                                                                    <th></th>
                                                                    <th class="week-left-align week-container" colspan="13"></th>
                                                                </tr>

                                                                <tr class="text-gray-500 fw-normal">
                                                                    <th></th>
                                                                    <th class="fs-7" id="pw_worked_hrs_title"></th>
                                                                    <th id="day_1_th2" class="fs-5"></th>
                                                                    <th id="day_2_th2" class="fs-5"></th>
                                                                    <th id="day_3_th2" class="fs-5"></th>
                                                                    <th id="day_4_th2" class="fs-5"></th>
                                                                    <th id="day_5_th2" class="fs-5"></th>
                                                                    <th id="day_6_th2" class="fs-5"></th>
                                                                    <th id="day_7_th2" class="fs-5"></th>
                                                                    <th id="day_8_th2" class="fs-5"></th>
                                                                    <th id="day_9_th2" class="fs-5"></th>
                                                                    <th id="day_10_th2" class="fs-5"></th>
                                                                    <th id="day_11_th2" class="fs-5"></th>
                                                                    <th id="day_12_th2" class="fs-5"></th>
                                                                    <th id="day_13_th2" class="fs-5"></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody></tbody>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_job_shift_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Add shift(s)</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_add_job_shift_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <form id="job_shift_details_form">
                    @csrf
                    <div class="modal-body scroll-y">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="from_date" class="fs-6 fw-bold required">From</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <i class="fs-2 las la-calendar"></i>
                                            </span>
                                            <input class="form-control ps-12 flatpickr-input" placeholder="Select date" name="from_date" id="from_date" type="text" readonly="readonly" value="">
                                        </div>
                                        <span class="text-danger error" id="from_date_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="how_many_days" class="fs-6 fw-bold required">For</label>
                                        <div class="input-group">
                                            <input class="form-control" name="how_many_days" id="how_many_days" type="text" value="1">
                                            <div class="input-group-prepend"><span class="input-group-text">Days</span></div>
                                        </div>
                                        <span class="text-danger error" id="how_many_days_error"></span>
                                    </div>
                                </div>
                            </div>
                            @if($jobLineTextBox)
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-7 fv-row fv-plugins-icon-container">
                                            <label class="fs-4 fw-bold">Number of associates required (by line)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="number_of_no_line" class="fs-6 fw-bold">No line</label>
                                            <input class="form-control" name="number_of_no_line" id="number_of_no_line" type="text" value="0">
                                            <span class="text-danger error" id="number_of_no_line_error"></span>
                                            <label class="fs-6 fw-bold text-gray-400">You can assign lines to these associates later</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="job_line_text_box_section">
                                    {!! $jobLineTextBox !!}
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="number_of_no_line" class="fs-6 fw-bold required">Number of associates required</label>
                                            <input class="form-control" name="number_of_no_line" id="number_of_no_line" type="text" value="0">
                                            <span class="text-danger error" id="number_of_no_line_error"></span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <input class="form-control" name="wa_job_id" id="wa_job_id" type="hidden" value="">
                                    <button type="submit" name="wa_client_job_worker_form_submit" id="wa_client_job_worker_form_submit" class="btn btn-primary float-end">Add shift(s)</button>
                                    <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="wa_client_job_worker_form_process" id="wa_client_job_worker_form_process">
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
    @include('job.copy_job_shift_future_week_modal')
@endsection

@section('js')
    <script>
        $("#last_li_of_header_title").empty().append(`
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('view-client-details/'.$job['client_id']) }}" class="text-muted text-hover-primary text-uppercase">
                    {{ $job['client_details']['company_name'] }}
                </a>
            </li>
            <li class="breadcrumb-item text-gray-500">></li>
            <li class="breadcrumb-item text-dark">
                <span id="header_sub_title">JOB</span>
                <span id="header_additional_info" class="text-uppercase ms-1">
                    : {{ $job['name'] }} (ID {{ $job['id'] }})
                </span>
            </li>
        `);

        activeMenu('/job-management');
    </script>

    <script>
        $('#worker_availability_datatable_wrapper > div.table-responsive').removeClass('table-responsive');

        let today = moment().format('YYYY-MM-DD');
        let current_week_number = '{{ $week_number }}';
        let wa_week_number = '{{ $week_number }}';
        let wa_week_year = '{{ $week_year }}';
        let week_type = 'current';
        let tableNameWorkerAvailabilityDatatable = $('#worker_availability_datatable');
        let base_date = moment().format('YYYY-MM-DD');

        worker_availability_table = tableNameWorkerAvailabilityDatatable.DataTable({
            "processing": false,
            "serverSide": false,
            "paging"    : false,
            "info"      : false,
            "sorting"   : false,
            "ajax": {
                "type": "post",
                "url": '{{ url('/get-job-worker-availability') }}',
                "data": function (d) {
                    d._token = '{{ csrf_token() }}';
                    d.job_id = '{{ $job['id'] }}';
                    //d.client_id = '{{ $job['client_id'] }}';
                    d.week_type = week_type;
                    d.base_date = base_date;
                    //d.payroll_week_starts = '{{ $job['client_details']['payroll_week_starts'] }}';
                    d.wa_week_number = wa_week_number;
                    d.wa_week_year = wa_week_year;
                },
                "dataSrc": function (json) {
                    base_date = json.base_date;
                    wa_week_number = json.wa_week_number;
                    wa_week_year   = json.wa_week_year;

                    $('#worker_availability_tab_date').text(json.worker_availability_tab_date);
                    $("#pw_worked_hrs_title").html(json.pw_worked_hrs_title);

                    if (json.table_th) {
                        let weekHeaderHtml = '<th colspan="2"></th>';
                        json.week_groups.forEach((week) => {
                            weekHeaderHtml += `
                                <th colspan="${week.count}"
                                    class="${week.is_current_week ? 'week-highlight' : 'week-normal'} week-left-align">
                                    <div class="fw-bold fs-3">
                                        PW${week.week_number}${week.is_current_week ? ' (Current)' : ''}
                                    </div>
                                </th>
                            `;
                        });

                        $('#worker_availability_datatable thead tr:first').html(weekHeaderHtml);

                        for (let i = 0; i < 13; i++) {
                            const day = json.table_th[i];
                            let html = `<div class="fw-bold">${day.title}</div>`;
                            if (day.shiftTrueOrFalse) {
                                html += `<div class="mt-1 small fw-bold text-muted">${day.confirm}c / ${day.invited}I</div>`;
                            }

                            let thId = '#day_' + (i + 1) + '_th2';
                            $(thId).html(html);

                            if (day.date === today) {
                                $(thId).addClass('week-highlight');
                            } else {
                                $(thId).removeClass('week-highlight');
                            }
                        }
                    }

                    return json.data;
                }
            },
            "columns": [
                {
                    "data": "worker_detail",
                    "sClass":"position-relative p-0",
                    "width":"15%",
                    "render": function(data) {
                        let tooltipContent = `
                            <div class="text-center">
                                <img src="${data.profile_pic}" class="worker-tooltip-img" />
                                <div class="fw-bold">${data.nationality}</div>
                                <div class="fw-bold">${data.dob}</div>
                            </div>
                        `;

                        return `<a href="{{ url('view-worker-details/') }}/${data.id}"
                            class="fw-bolder fs-7 p-2 worker-tooltip"
                            target="_blank"
                            data-bs-toggle="tooltip"
                            data-bs-html="true"
                            title='${tooltipContent}'>
                                ${data.name}
                            </a>
                            <span class="ms-2 align-middle d-inline-block">${data.icon}</span>`;
                    }
                },
                {"data": "past_week_hrs_worked", "sClass":"position-relative p-0 text-center"},
                {"data": "day_1", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_2", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_3", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_4", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_5", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_6", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_7", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_8", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_9", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_10", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_11", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_12", "sClass":"position-relative p-0", "width":"10%"},
                {"data": "day_13", "sClass":"position-relative p-0", "width":"10%"},
            ],
            "drawCallback": function() {
                const tooltipTriggerList = [].slice.call(
                    document.querySelectorAll('[data-bs-toggle="tooltip"]')
                );
                tooltipTriggerList.map((el) => new bootstrap.Tooltip(el));
            }
        });

        $("#previous_wa_week").on('click', function (){
            week_type = 'previous';
            worker_availability_table.ajax.reload();
        });

        $("#next_wa_week").on('click', function (){
            week_type = 'next';
            worker_availability_table.ajax.reload();
        });

        $(document).on('change', '.worker_availability_checkbox', function () {
            const value = $(this).val();
            const isChecked = $(this).is(':checked');

            if (value === 'deselect_all_worker') {
                $('input[name="worker_availability_checkbox"]').prop('checked', false);
                $('.worker_availability_checkbox').prop('checked', false);
                return;
            }

            if (value === 'available_worker') {
                $('input[value$="_available"]').prop('checked', isChecked);
            }

            if (value === 'confirmed_worker') {
                $('input[value$="_confirmed"]').prop('checked', isChecked);
            }

            if (value === 'invited_worker') {
                $('input[value$="_invited"]').prop('checked', isChecked);
            }
        });

        $("#worker_availability_action_submit_btn").on('click', function () {
            let worker_availability_action_type = $("#worker_availability_action_type").val();
            let worker_availability_checked_worker = [];

            $('input[name="worker_availability_checkbox"]:checked').each(function() {
                worker_availability_checked_worker.push($(this).val());
            });

            if (worker_availability_action_type === '') {
                toastr.error('Please select a action.')
            } else if(worker_availability_checked_worker.length === 0) {
                toastr.error('Please select a workers.')
            } else {
                $("#worker_availability_action_submit_btn").hide();
                $("#worker_availability_action_process_btn").show();

                $.ajax({
                    type : 'post',
                    url : '{{ url('action-on-worker-availability') }}',
                    data : {
                        _token : '{{ csrf_token() }}',
                        worker_availability_action_type : worker_availability_action_type,
                        worker_availability_checked_worker : worker_availability_checked_worker,
                    },
                    success : function (response) {

                        $("#worker_availability_action_submit_btn").show();
                        $("#worker_availability_action_process_btn").hide();

                        if(response.code === 200) {
                            toastr.success(response.message);
                            $("#worker_availability_action_type").val('').trigger('change');
                            week_type = 'current'
                            worker_availability_table.ajax.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error   : function (response) {
                        $("#worker_availability_action_submit_btn").show();
                        $("#worker_availability_action_process_btn").hide();

                        toastr.error(response.statusText);
                    }
                });
            }
        });

        $('#exportBookings').on('click', function() {
            let data = {
                job_id: '{{ $job['id'] }}',
                client_id: '{{ $job['client_id'] }}',
                payroll_week_starts: '{{ $job['client_details']['payroll_week_starts'] }}',
                week_type: week_type,
                wa_week_number: wa_week_number,
                wa_week_year: wa_week_year,
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: "{{ url('bulk-export-booking-calendar-sheet-confirm-worker') }}",
                type: "POST",
                data: data,
                success: function (response) {
                    if (response.code === 200) {
                        if (Array.isArray(response.data) && response.data.length > 0) {
                            response.data.forEach(function (job_shift_id, index) {
                                let link = document.createElement('a');
                                link.href = "{{ url('export-booking-calendar-sheet-confirm-worker') }}/" + job_shift_id;
                                link.download = '';
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            });
                        } else {
                            toastr.warning('No job shifts found to export.');
                        }
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(document).on('click', '#create_job_shift_th_btn', function () {
            let todayDate = moment().format("YYYY-MM-DD");
            let createShiftDate = $(this).attr('data-create_shift_date')
            let worker_availability_job_id = '{{ $job['id'] }}'
            if (moment(createShiftDate).isBefore(todayDate, 'day')) {
                toastr.error('You cannot create a shift for a past date');
            } else if (worker_availability_job_id === '') {
                toastr.error('Please select a job.');
            } else {
                $("#from_date").val(moment(createShiftDate, "YYYY-MM-DD").format("DD-MM-YYYY")).prop('readonly', true).addClass('bg-secondary');
                $("#wa_job_id").val(worker_availability_job_id)
                $("#how_many_days").prop('readonly', true).addClass('bg-secondary');
                $("#add_job_shift_modal").modal('show');
            }
        })

        $("#cls_btn_add_job_shift_modal").on('click', function (){
            $("#add_job_shift_modal").modal('hide');
        })

        $("#job_shift_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#wa_client_job_worker_form_submit").addClass('d-none');
            $("#wa_client_job_worker_form_process").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('create-job-shift') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#wa_client_job_worker_form_submit").removeClass('d-none');
                    $("#wa_client_job_worker_form_process").addClass('d-none');

                    if(response.code === 200) {
                        $("#job_shift_details_form").trigger('reset');
                        $("#add_job_shift_modal").modal('hide');
                        week_type = 'current'
                        worker_availability_table.ajax.reload();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#wa_client_job_worker_form_submit").removeClass('d-none');
                    $("#wa_client_job_worker_form_process").addClass('d-none');
                }
            });
        });
    </script>
    @yield('copy_job_shift_future_week_js')
@endsection
