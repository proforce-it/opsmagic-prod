<link rel="stylesheet" href="{{ asset('assets/css/custom_calendar.css') }}">
<div class="table-responsive">
    <div class="p-5">
        @if($job_line)
            <div class="row mb-7" id="available_job_line">
                <div class="w-100">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="d-flex align-items-center position-relative my-1">
                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                        <i class="las la-search" style="font-size: 24px"></i>
                                    </span>
                                <input type="text" data-kt-job-worker-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find lines" />
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div style="float:right;">
                                <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                    <label class="btn btn-outline btn-light-primary btn-active-primary active status-radio-btn-padding" data-kt-button="true">
                                        <input class="btn-check job_lien_status" type="radio" name="job_lien_status" checked="checked" value="active"/>
                                        Active
                                    </label>
                                    <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                        <input class="btn-check job_lien_status" type="radio" name="job_lien_status" value="archived"/>
                                        Archived
                                    </label>
                                    <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                        <input class="btn-check job_lien_status" type="radio" name="job_lien_status" value="All" />
                                        All
                                    </label>
                                </div>

                                <div class="float-end">
                                    <a href="javascript:;" class="add_line_btn" id="add_new_line_form_btn">
                                        <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-100 mt-5 mb-5" style="border-top: 1px dashed #dddfe1"></div>
                <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="job_line_datatable">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th style="width: 10%">Name</th>
                        <th>Code</th>
                        <th style="width: 10%">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold"></tbody>
                </table>
            </div>
        @else
            <div class="row mb-7" id="not_available_job_line">
                <div class="w-100">
                    <div class="col-lg-12">
                        <div class="alert alert-custom alert-warning" role="alert">
                            <div class="alert-text fs-4">
                                <i class="las la-exclamation-triangle text-warning fs-xl-2"></i>
                                <b>Please note:</b> Lines are only required if you need to track individual lines or departments within a single job. For examples “General workers” at
                                a site might be split into “Packhouse A” and “Packhouse B”.
                            </div>
                        </div>
                    </div>

                    <div class="mid-btn">
                        <p class="fs-6 fw-bold">No lines defined for {{ $job['name'] }}</p>
                        <a href="javascript:;" class="btn btn-primary btn-lg add_line_btn" id="add_new_line_btn">
                            Add lines
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="add_job_line_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 id="job_line_model_title">Add a line</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="job_line_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="job_line_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Name</label>
                                        <input type="text" name="line_name" id="line_name" class="form-control" placeholder="Add a name" value="">
                                        <span class="text-danger error" id="line_name_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Code</label>
                                        <input type="text" name="line_code" id="line_code" class="form-control" placeholder="Add a code" value="">
                                        <span class="text-danger error" id="line_code_error"></span>
                                        <label class="fs-6 fw-bold text-gray-400">Appears when there is insufficient space to display the full line name
                                            (max 6 characters)</label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label for="job_line_color" class="fs-6 fw-bold">Colour</label>
                                        <div class="color-options">
                                            <label>
                                                <input type="radio" name="job_line_color" value="red" checked>
                                                <span class="color-circle red"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="job_line_color" value="pink">
                                                <span class="color-circle pink"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="job_line_color" value="orange">
                                                <span class="color-circle orange"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="job_line_color" value="coffee">
                                                <span class="color-circle coffee"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="job_line_color" value="green">
                                                <span class="color-circle green"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="job_line_color" value="blue">
                                                <span class="color-circle blue"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="job_line_color" value="purple">
                                                <span class="color-circle purple"></span>
                                            </label>
                                        </div>
                                        <span class="text-danger error" id="job_line_color_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center !important;">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" name="store_id" id="store_id" value="0">
                                <input type="hidden" name="job_id" id="job_id" value="{{ $job['id'] }}">
                                <button type="submit" name="job_line_submit_btn" id="job_line_submit_btn" class="btn btn-primary float-end">Create</button>
                                <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" name="job_line_process_btn" id="job_line_process_btn">
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

@section('job_line_js')
    <script>
        $(".add_line_btn").on('click', function () {
            $(".error").html('');
            $('#job_line_form').trigger('reset');
            $("#job_line_model_title").text('Add a line')
            $("#job_line_submit_btn").text('Create');
            $("#add_job_line_modal").modal('show');
        });

        $("#job_line_modal_close_btn").on('click', function () {
            $(".error").html('');
            $('#job_line_form').trigger('reset');
            $("#job_line_model_title").text('Add a line')
            $("#job_line_submit_btn").text('Create');
            $("#add_job_line_modal").modal('hide');
        })

        $("#job_line_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#job_line_submit_btn").addClass('d-none');
            $("#job_line_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('store-job-line') }}',
                data        : new FormData($("#job_line_form")[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    $("#job_line_submit_btn").removeClass('d-none');
                    $("#job_line_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#job_line_modal_close_btn").click();
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                    }

                    decodeResponse(response);
                },
                error   : function (response) {
                    $("#job_line_submit_btn").removeClass('d-none');
                    $("#job_line_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            })
        });

        $(document).on('click', '#edit_job_line', function () {
            $(".error").html('');
            $('#job_line_form').trigger('reset');
            $("#job_line_model_title").text('Edit line '+$(this).attr('data-line_name'));
            $("#job_line_submit_btn").text('Update');

            $("#store_id").val($(this).attr('data-id'));
            $("#line_name").val($(this).attr('data-line_name'));
            $("#line_code").val($(this).attr('data-line_code'));
            $("input[name='job_line_color'][value='" + $(this).attr('data-color_code') + "']").prop('checked', true);

            $("#add_job_line_modal").modal('show');
        });

        let job_line_datatable;
        $(document).ready(function() {
            job_line_datatable = $('#job_line_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-job-line') }}',
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}';
                        d.status = $('input[name="job_lien_status"]:checked').val();
                        d.job_id = '{{ $job['id'] }}';
                    },
                },
                "columns": [
                    {"data": "name", "width": "20%"},
                    {"data": "code"},
                    {"data": "action", "sClass": "text-end", "width": "10%"}
                ]
            });
        });

        const jobLineSearch = document.querySelector('[data-kt-job-worker-table-filter="search"]');
        jobLineSearch.addEventListener('keyup', function (e) {
            job_line_datatable.column(0).search(this.value).draw();
        });

        $(document).on('change', '.job_lien_status', function () {
            job_line_datatable.ajax.reload();
        });

        $(document).on('click', '#archived_job_line', function () {
            let archived_job_line_id = $(this).attr('data-id')
            sweetAlertArchived('You want to archived this job line!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('action-job-line') }}',
                        data    : {
                            _token : '{{ @csrf_token() }}',
                            job_line_id : archived_job_line_id,
                            action_type : 'archived'
                        },
                        success : function (response) {
                            decodeResponse(response)

                            if(response.code === 200) {
                                job_line_datatable.ajax.reload();
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $(document).on('click', '#unarchived_job_line', function () {
            let unarchived_job_line_id = $(this).attr('data-id')
            sweetAlertUnarchived('You want to un-archived this job line!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('action-job-line') }}',
                        data    : {
                            _token : '{{ @csrf_token() }}',
                            job_line_id : unarchived_job_line_id,
                            action_type : 'unarchived'
                        },
                        success : function (response) {
                            decodeResponse(response)

                            if(response.code === 200) {
                                job_line_datatable.ajax.reload();
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