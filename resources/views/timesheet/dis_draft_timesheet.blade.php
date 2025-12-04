@extends('theme.page')

@php
    $title = 'Timesheets - '.date('d M', strtotime($shift['date'])).' '.$shift['client_job_details']['name']
@endphp
@section('title', $title)
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post">
                        <div id="kt_content_container">
                            <div class="card mb-5">
                                <div class="card-body py-4">
                                    <div class="row">
                                        <div class="col-8 d-flex align-items-center">
                                            <div class="symbol symbol-circle pe-5">
                                                <a href="{{ url('view-job-shift/'.$shift['id']) }}">
                                                    <i class="las la-chevron-circle-left text-primary fs-xxl-3qx"></i>
                                                </a>
                                            </div>
                                            <div class="fw-bold">
                                                <span class="fs-3 text-muted">
                                                    {{ $shift['client_job_details']['client_details']['company_name'] }} > {{ $shift['client_job_details']['site_details']['site_name'] }}
                                                </span> <br>
                                                <span class="fs-1">
                                                    {{ $shift['client_job_details']['name'] }} | {{ date('d M Y', strtotime($shift['date'])) }}
                                                </span>

                                                @if($shift['cancelled_at'])
                                                    <span class="fs-3 text-danger">(Shift cancelled)</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            @if($shift['client_job_details']['client_details']['company_logo'])
                                                <img src="{{ asset('workers/client_document/'.$shift['client_job_details']['client_details']['company_logo']) }}" alt="No image." class="w-300px h-100px" style="object-fit: contain; object-position: right;">
                                            @else
                                                <div>
                                                    <i class="fs-xxl-2hx las la-industry bg-gray-200 rounded-3 p-2"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-5">
                                <div class="card-body py-4">
                                    <div class="table-responsive">
                                        <div class="p-5">
                                            <div class="current" data-kt-stepper-element="content">
                                                <div class="w-100">
                                                    <div class="fv-row">
                                                        <div class="row mb-5">
                                                            <div class="col-lg-12 text-start fw-bolder fs-2 text-uppercase gs-0 border-bottom border-4">
                                                                Confirm timesheet entries for shift
                                                                <a href="javascript:;" id="add_draft_timesheet">
                                                                    <i class="fs-xxl-2qx las la-plus-circle text-primary float-end"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <table class="table align-middle table-row-dashed fs-7 gy-3" id="datatable">
                                                                    <thead>
                                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                        <th>WORKER</th>
                                                                        <th>START TIME</th>
                                                                        <th>HOURS WORKED</th>
                                                                        <th>ACTIONS</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody class="text-gray-600 fw-bold">

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="row border-top-dashed border-1 mt-5">
                                                            <div class="col-lg-12 text-center mt-5">
                                                                <a href="javascript:;" id="create_confirm_timesheet_entries_btn" class="btn btn-primary text-hover-white btn-lg">
                                                                    Confirm timesheets entries
                                                                </a>
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
    </div>

    <div class="modal fade" id="add_draft_timesheet_entry_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Add draft timesheet entry for {{ $shift['client_job_details']['name'] }} ({{ date('d/m/Y', strtotime($shift['date'])) }})</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_add_draft_timesheet_entry_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <form id="add_draft_timesheet_entry_form">
                    @csrf
                    <div class="modal-body scroll-y">
                        <div class="fv-row row">
                            <div class="col-lg-12">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="add_timesheet_worker" class="fs-6 fw-bold required">Select worker</label>
                                    <select name="add_timesheet_worker" id="add_timesheet_worker" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Choose a worker" data-allow-clear="true">
                                        <option value=""></option>
                                        @if($clientJobWorker)
                                            @foreach($clientJobWorker as $cjw)
                                                @php($cjWorker = $cjw['worker'])
                                                <option value="{{ $cjWorker['id'] }}">
                                                    {{ $cjWorker['first_name'].' '.$cjWorker['middle_name'].' '.$cjWorker['last_name'].' - '.date('d/m/Y', strtotime($cjWorker['date_of_birth'])) }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger error" id="add_timesheet_worker_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="add_timesheet_start_time" class="fs-6 fw-bold required">Start time</label>
                                    <div class="position-relative d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                            <i class="fs-2 las la-clock"></i>
                                        </span>
                                        <input class="form-control ps-12 flatpickr-input time_input" placeholder="Select timesheet start time" name="add_timesheet_start_time" id="add_timesheet_start_time" type="text" readonly="readonly" value="{{ $shift['start_time'] }}">
                                        <span class="text-danger error" id="add_timesheet_start_time_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="fv-row fv-plugins-icon-container">
                                    <label for="hours_worked" class="fs-6 fw-bold required">Hours worked</label>
                                    <input class="form-control" name="add_hours_worked" id="add_hours_worked" type="text" value="{{ $hoursWorked }}" placeholder="Enter hours worked">
                                    <span class="text-danger error" id="add_hours_worked_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="hidden" name="job_shift_id" id="job_shift_id" value="{{ $shift['id'] }}">
                                    <input type="hidden" name="job_shift_date" id="job_shift_date" value="{{ $shift['date'] }}">
                                    <input type="hidden" name="job_id" id="job_id" value="{{ $shift['job_id'] }}">
                                    <button type="submit" name="add_draft_timesheet_entry_submit_btn" id="add_draft_timesheet_entry_submit_btn" class="btn btn-primary">Add entry</button>
                                    <button type="button" class="btn btn-lg btn-primary disabled d-none" name="add_draft_timesheet_entry_process_btn" id="add_draft_timesheet_entry_process_btn">
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
    <div class="modal fade" id="edit_draft_timesheet_entry_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Edit draft timesheet entry for <span id="worker_name_and_date"></span></h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_edit_draft_timesheet_entry_modal">
                                                <span class="svg-icon svg-icon-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                                    </svg>
                                                </span>
                    </div>
                </div>
                <form id="edit_draft_timesheet_entry_form">
                    @csrf
                    <div class="modal-body scroll-y">
                        <div class="fv-row row">
                            <div class="col-lg-12">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="timesheet_start_time" class="fs-6 fw-bold">Start time</label>
                                    <div class="position-relative d-flex align-items-center">
                                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                    <i class="fs-2 las la-clock"></i>
                                                                </span>
                                        <input class="form-control ps-12 flatpickr-input time_input" placeholder="Select timesheet start time" name="timesheet_start_time" id="timesheet_start_time" type="text" readonly="readonly" value="">
                                    </div>
                                </div>
                            </div>
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

                    <div class="modal-footer justify-content-center">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" name="update_draft_timesheet_entry_submit_btn" id="update_draft_timesheet_entry_submit_btn" class="btn btn-primary">Update timesheet entry</button>
                                    <button type="button" class="btn btn-lg btn-primary disabled d-none" name="update_draft_timesheet_entry_process_btn" id="update_draft_timesheet_entry_process_btn">
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
    <div class="modal fade" id="confirm_timesheet_entry_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2 class="text-uppercase">Confirm timesheet entries for shift ({{ $shift['client_job_details']['name'] }} | {{ date('d/m/Y', strtotime($shift['date'])) }})</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary cls_btn_confirm_timesheet_entry_modal" id="cls_btn_confirm_timesheet_entry_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <div class="fv-row row">
                        <div class="col-lg-12">
                            <table class="table align-middle table-row-dashed fs-7 gy-3" id="timesheet_validation_datatable">
                                <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th>WORKER</th>
                                    <th>START TIME</th>
                                    <th>HOURS WORKED</th>
                                    <th>Ready for create</th>
                                    <th>ERROR</th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-center">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <a href="javascript:;" id="yes_create_timesheet_entries_btn" class="btn btn-success text-hover-white btn-lg">
                                    Yes, Create!
                                </a>
                                <a href="javascript:;" id="no_cancel_btn" class="btn fw-bold btn-active-light-danger btn-lg cls_btn_confirm_timesheet_entry_modal">
                                    No, cancel
                                </a>
                                <button type="button" class="btn btn-lg btn-primary btn-lg disabled d-none" data-kt-stepper-action="submit" id="yes_create_timesheet_entries_process_btn">
                                    <span>Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
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
        activeMenu('/assignment-management');

        $("#header_info_second_a_tag").empty().append(`
            <a href="{{ url('assignment-management?tag='.$shift['client_job_details']['client_id'].'.'.$shift['client_job_details']['site_id'].'.'.$shift['client_job_details']['id']) }}" class="text-muted text-hover-primary" id="header_info_second_a_tag_title">
                BOOKINGS CALENDAR
            </a>
        `);

        $("#last_li_of_header_title").empty().append(`
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('view-job-shift/'.$shift['id']) }}"
                   class="text-muted text-hover-primary text-uppercase">
                    VIEW BOOKINGS
                </a>
            </li>
            <li class="breadcrumb-item text-gray-500">></li>
            <li class="breadcrumb-item text-dark">
                <span id="header_additional_info" class="text-uppercase ms-1">
                    CREATE TIMESHEETS
                </span>
            </li>
        `);

        let datatable;
        $(document).ready(function() {
            datatable = $('#datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-draft-timesheet-entries') }}',
                    "data": function (d) {
                        d._token = '{{ @csrf_token() }}';
                        d.job_shift_id = '{{ $shift['id'] }}';
                    },
                },
                "columns": [
                    {"data": "worker"},
                    {"data": "start_time", "sClass": "text-center", "width": "10%"},
                    {"data": "hours_worked", "sClass": "text-center", "width": "10%"},
                    {"data": "action", "sClass": "text-center", "width": "10%"}
                ],
                "order": [[ 0, "asc" ]],
            });
        });

        $(document).on('click', '#delete_draft_timesheet', function () {
            sweetAlertConfirmDelete('Do you want to delete this timesheet entry!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('delete-draft-timesheet-entry') }}'+'/'+$(this).attr('data-id'),
                        data    : {
                            _token: '{{ @csrf_token() }}',
                        },
                        success : function (response) {
                            decodeResponse(response);

                            if(response.code === 200) {
                                datatable.ajax.reload();
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $(".time_input").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });

        $(document).ready(function() {
            $(".form-select-custom").select2({
                dropdownParent : $("#add_draft_timesheet_entry_form")
            });
        });

        /*--- BEGIN ADD DRAFT TIMESHEET ---*/
        $('#add_draft_timesheet').on('click', function () {
            $("#add_draft_timesheet_entry_modal").modal('show');
        });

        $("#cls_btn_add_draft_timesheet_entry_modal").on('click', function (){
            $("#add_timesheet_worker").val('').trigger('change');
            $("#add_timesheet_start_time").val('{{ $shift['start_time'] }}');
            $("#add_hours_worked").val('{{ $hoursWorked }}');
            $("#job_shift_id").val('{{ $shift['id'] }}')
            $("#job_shift_date").val('{{ $shift['date'] }}')
            $("#job_id").val('{{ $shift['job_id'] }}')
            $("#add_draft_timesheet_entry_modal").modal('hide');
        });

        $("#add_draft_timesheet_entry_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#add_draft_timesheet_entry_submit_btn").addClass('d-none');
            $("#add_draft_timesheet_entry_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('add-draft-timesheet-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);

                    $("#add_draft_timesheet_entry_submit_btn").removeClass('d-none');
                    $("#add_draft_timesheet_entry_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#cls_btn_add_draft_timesheet_entry_modal").click();
                        datatable.ajax.reload();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#add_draft_timesheet_entry_submit_btn").removeClass('d-none');
                    $("#add_draft_timesheet_entry_process_btn").addClass('d-none');
                }
            });
        });

        /*--- END ADD DRAFT TIMESHEET ---*/

        /*--- BEGIN EDIT DRAFT TIMESHEET ---*/
        $(document).on('click', '#edit_draft_timesheet', function () {
            $("#worker_name_and_date").text($(this).attr('data-worker'));
            $("#hours_worked").val($(this).attr('data-hours'));
            $("#timesheet_start_time").val($(this).attr('data-start_time'));
            $("#timesheet_id").val($(this).attr('data-id'));
            $("#edit_draft_timesheet_entry_modal").modal('show');
        });

        $("#cls_btn_edit_draft_timesheet_entry_modal").on('click', function (){
            $("#worker_name_and_date").text('');
            $("#edit_draft_timesheet_entry_form").trigger('reset');
            $("#edit_draft_timesheet_entry_modal").modal('hide');
        });

        $("#edit_draft_timesheet_entry_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#update_draft_timesheet_entry_submit_btn").addClass('d-none');
            $("#update_draft_timesheet_entry_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('edit-draft-timesheet-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);

                    $("#update_draft_timesheet_entry_submit_btn").removeClass('d-none');
                    $("#update_draft_timesheet_entry_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#cls_btn_edit_draft_timesheet_entry_modal").click();
                        datatable.ajax.reload();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#update_draft_timesheet_entry_submit_btn").removeClass('d-none');
                    $("#update_draft_timesheet_entry_process_btn").addClass('d-none');
                }
            });
        });
        /*--- END EDIT DRAFT TIMESHEET ---*/

        /*--- BEGIN CREATE TIMESHEET ENTRY SECTION ----*/
        let draft_timesheet_record_ids = [];
        let timesheet_validation_datatable = $('#timesheet_validation_datatable').DataTable();

        $('#create_confirm_timesheet_entries_btn').on('click', function () {
            $("#confirm_timesheet_entry_modal").modal('show');
            timesheet_validation_datatable.destroy();

            timesheet_validation_datatable = $('#timesheet_validation_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('check-timesheet-entries-validation') }}',
                    "data": function (d) {
                        d._token = '{{ @csrf_token() }}';
                        d.job_shift_id = '{{ $shift['id'] }}';
                    },
                },
                "initComplete": function(settings, json) {
                    draft_timesheet_record_ids = json.draft_timesheet_ids || [];
                },
                "columns": [
                    {"data": "worker"},
                    {"data": "start_time", "sClass": "text-center", "width": "10%"},
                    {"data": "hours_worked", "sClass": "text-center", "width": "10%"},
                    {"data": "ready_for_create", "sClass": "text-center", "width": "10%"},
                    {"data": "error", "width": "30%"}
                ],
                "order": [[ 0, "asc" ]],
            });
        });

        $(".cls_btn_confirm_timesheet_entry_modal").on('click', function (){
            $("#confirm_timesheet_entry_modal").modal('hide');
        });

        $("#yes_create_timesheet_entries_btn").on('click', function () {
            $("#yes_create_timesheet_entries_btn").addClass('d-none');
            $("#no_cancel_btn").addClass('d-none');
            $("#yes_create_timesheet_entries_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('create-timesheet-using-draft-timesheet-entries') }}',
                data        : {
                    _token : '{{ @csrf_token() }}',
                    draft_timesheet_record_ids : draft_timesheet_record_ids,
                },
                success     : function (response) {
                    decodeResponse(response);

                    $("#yes_create_timesheet_entries_btn").removeClass('d-none');
                    $("#no_cancel_btn").removeClass('d-none');
                    $("#yes_create_timesheet_entries_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $(".cls_btn_confirm_timesheet_entry_modal").click();
                        datatable.ajax.reload();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#yes_create_timesheet_entries_btn").removeClass('d-none');
                    $("#no_cancel_btn").removeClass('d-none');
                    $("#yes_create_timesheet_entries_process_btn").addClass('d-none');
                }
            });
        });
        /*--- END CREATE TIMESHEET ENTRY SECTION ----*/
    </script>
@endsection
