@extends('theme.page')

@section('title', 'Jobs')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <div class="card-header border-0 pt-6">
                                    <div class="card-title">
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <i class="fs-2 las la-search"></i>
                                            </span>
                                            <input type="text" data-kt-job-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search job" />
                                        </div>
                                    </div>

                                    <div class="card-toolbar">
                                        <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check client_job_status" type="radio" name="client_job_status" value="All" />
                                                All
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check client_job_status" type="radio" name="client_job_status" value="1" />
                                                Archived
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary active status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check client_job_status" type="radio" name="client_job_status" checked="checked" value="0"/>
                                                Active
                                            </label>
                                        </div>

                                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                            <a href="javascript:;" id="add_new_job_form_open_btn">
                                                <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                            </a>
                                            <a href="javascript:;" id="add_new_job_form_close_btn" class="d-none">
                                                <i class="fs-xxl-2qx las la-times-circle text-primary"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="row" id="job_form_section" style="display: none">
                                        <div class="w-100 mt-5 mb-5 border-bottom border-4"></div>
                                        <div class="col-lg-12 border border-1 p-5 border-dark rounded-3">
                                            <form id="job_details_form">
                                                @csrf
                                                <div class="fv-row">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="job_name" class="fs-6 fw-bold required">Name</label>
                                                                <input type="text" name="job_name" id="job_name" class="form-control" placeholder="Name" value="" />
                                                                <span class="text-danger error" id="job_name_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label class="fs-6 fw-bold required">Assignment schedule</label>
                                                                <input type="file" name="assignment_schedule" id="assignment_schedule" class="form-control"  accept="application/pdf"/>
                                                                <span class="text-danger error" id="assignment_schedule_error"></span>
                                                                <label class="fw-bold text-muted">PDF FORMAT. (Max. 10MB)</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="reference" class="fs-6 fw-bold">Client job Ref.</label>
                                                                <input type="text" name="reference" id="reference" class="form-control" placeholder="Reference" value="" />
                                                                <span class="text-danger error" id="reference_no_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="job_description" class="fs-6 fw-bold required">Description</label>
                                                                <textarea name="job_description" id="job_description" rows="5" class="form-control" placeholder="Enter job description"></textarea>
                                                                <span class="text-danger error" id="job_description_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="job_health_and_safety_information" class="fs-6 fw-bold required">Health and safety information</label>
                                                                <textarea name="job_health_and_safety_information" id="job_health_and_safety_information" rows="5" class="form-control" placeholder="Enter health and safety information"></textarea>
                                                                <span class="text-danger error" id="job_health_and_safety_information_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="job_client_id" class="fs-6 fw-bold required">Client</label>
                                                                <select name="job_client_id" id="job_client_id" class="form-select form-select-lg" data-control="select2" data-placeholder="Select client" data-allow-clear="true">
                                                                    <option value="">Select client</option>
                                                                    @if($client)
                                                                        @foreach($client as $c_row)
                                                                            <option value="{{ $c_row['id'] }}">{{ $c_row['company_name'] }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                                <span class="text-danger error" id="job_client_id_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="job_site" class="fs-6 fw-bold required">Site</label>
                                                                <select name="job_site" id="job_site" class="form-select form-select-lg" data-control="select2" data-placeholder="Select site" data-allow-clear="true">
                                                                    <option value="">Select site</option>
                                                                </select>
                                                                <span class="text-danger error" id="job_site_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="job_start_date" class="fs-6 fw-bold required">Start date</label>
                                                                <div class="position-relative d-flex align-items-center">
                                                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                        <i class="fs-2 las la-calendar"></i>
                                                                    </span>
                                                                    <input class="form-control ps-12 flatpickr-input date_input " placeholder="Select start date" name="job_start_date" id="job_start_date" type="text" value="">
                                                                </div>
                                                                <span class="text-danger error" id="job_start_date_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="job_end_date" class="fs-6 fw-bold">End date</label>
                                                                <div class="position-relative d-flex align-items-center">
                                                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                            <i class="fs-2 las la-calendar"></i>
                                                                        </span>
                                                                    <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select end date" name="job_end_date" id="job_end_date" type="text" readonly="readonly" value="" disabled>
                                                                </div>
                                                                <span class="text-danger error" id="job_end_date_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="job_default_shift_time" class="fs-6 fw-bold required">Default shift start time</label>
                                                                <div class="position-relative d-flex align-items-center">
                                                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                            <i class="fs-2 las la-clock"></i>
                                                                        </span>
                                                                    <input class="form-control ps-12 flatpickr-input time_input" placeholder="Select shift time" name="job_default_shift_time" id="job_default_shift_time" type="text" readonly="readonly" value="">
                                                                </div>
                                                                <span class="text-danger error" id="job_default_shift_time_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="job_default_shift_length_hr" class="fs-6 fw-bold required">Default shift length</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" name="job_default_shift_length_hr" id="job_default_shift_length_hr" type="text" value="0">
                                                                    <div class="input-group-prepend"><span class="input-group-text">hr</span></div>
                                                                </div>
                                                                <span class="text-danger error" id="job_default_shift_length_hr_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="job_default_shift_length_min" class="fs-6 fw-bold"></label>
                                                                <div class="input-group">
                                                                    <input class="form-control" name="job_default_shift_length_min" id="job_default_shift_length_min" type="text" value="0">
                                                                    <div class="input-group-prepend"><span class="input-group-text">min</span></div>
                                                                </div>
                                                                <span class="text-danger error" id="job_default_shift_length_min_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <button type="submit" name="job_form_submit" id="job_form_submit" class="btn btn-primary float-end">Add job</button>
                                                            <button type="reset" name="job_form_cancel_btn" id="add_new_job_form_close_btn" class="btn btn-dark float-end me-1">Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="w-100 mt-10 mb-10" style="border-top: 1px dashed #dddfe1"></div>
                                    </div>
                                </div>
                                <div class="card-body py-4">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="jobs_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th style="width: 10%">JOb ID</th>
                                            <th style="width: 20%">Name</th>
                                            <th style="width: 20%">Client</th>
                                            <th style="width: 20%">Site</th>
                                            <th style="width: 15%">Start Date</th>
                                            <th style="width: 10%">Status</th>
                                            <th style="width: 15%">Action</th>
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
@endsection

@section('js')
    <script>
        $("#add_new_job_form_open_btn").on('click', function () {
            $(".error").html('');
            $("#job_details_form").trigger('reset');
            $("#job_site").val('').trigger('change');

            $("#add_new_job_form_open_btn").addClass('d-none');
            $("#add_new_job_form_close_btn").removeClass('d-none');

            $("#job_form_section").slideDown(600);
        });

        $(document).on('click', '#add_new_job_form_close_btn', function () {
            $("#add_new_job_form_open_btn").removeClass('d-none');
            $("#add_new_job_form_close_btn").addClass('d-none');

            $("#job_form_section").slideUp(600);
        })

        let job_start_date = $("#job_start_date");
        job_start_date.flatpickr({
            dateFormat  : "d-m-Y",
            allowInput: true
        });

        job_start_date.on('change', function () {

            let end_date_box = $( "#job_end_date" );
            end_date_box.val('');

            let value   = $(this).val();
            let dateAr  = value.split('-');
            let date    = dateAr[1] + '-' + dateAr[0] + '-' + dateAr[2];

            let newDate         = new Date(date);
            let currentMonth    = newDate.getMonth();
            let currentDate     = newDate.getDate();
            let currentYear     = newDate.getFullYear();

            end_date_box.prop('disabled', false)
            end_date_box.flatpickr({
                minDate: new Date(currentYear, currentMonth, currentDate),
                dateFormat  : "d-m-Y",
                allowInput: true
            });
        });

        $("#job_default_shift_time").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });

        $("#job_client_id").on('change', function (){
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-site-using-client') }}',
                data    : {
                    _token    : '{{ csrf_token() }}',
                    client_id : $("#job_client_id").val(),
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#job_site").empty().append(response.data.site_option);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#job_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $.ajax({
                type        : 'post',
                url         : '{{ url('store-client-job-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)
                    if(response.code === 200) {
                        jobs_table.ajax.reload();
                        $("#job_details_form").trigger('reset');
                        $("#job_form_section").hide();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        let jobs_table;
        $(document).ready(function() {
            jobs_table = $('#jobs_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-client-jobs') }}',
                    "data": function (d) {
                        d._token    = $('#_token').val();
                        d.status    = $('input[name="client_job_status"]:checked').val();
                        d.client_id = 0;
                    },
                },
                "columns": [
                    {"data": "job_id", "width": "8%"},
                    {"data": "name", "width": "20%"},
                    {"data": "client", "width": "20%"},
                    {"data": "site", "width": "20%"},
                    {"data": "start_date", "width": "10%"},
                    {"data": "status", "width": "5%"},
                    {"data": "action", "sClass": "text-center", "width": "15%"}
                ],
                "order": [[ 1, "asc" ]],
            });
        });

        $(document).on('change', '.client_job_status', function () {
            jobs_table.ajax.reload();
        })

        const filterSearch = document.querySelector('[data-kt-job-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            jobs_table.search(e.target.value).draw();
        });

        $(document).on('click', '.archive_action', function () {
            let id = $(this).attr('data-job_id');
            let status = $(this).attr('data-status');
            Swal.fire({
                text                : $(this).attr('data-text'),
                icon                : "warning",
                showCancelButton    : true,
                buttonsStyling      : false,
                confirmButtonText   : $(this).attr('data-btn_text'),
                cancelButtonText    : "No, cancel",
                customClass         : {
                    confirmButton       : "btn fw-bold btn-danger",
                    cancelButton        : "btn fw-bold btn-active-light-danger"
                }
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('archive-client-job-action') }}'+'/'+id+'/'+status,
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                jobs_table.ajax.reload();
                            } else {
                                toastr.error(response.message);
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
