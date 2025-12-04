<div class="tab-pane fade" id="kt_table_widget_5_tab_4">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <i class="fs-2 las la-search"></i>
                        </span>
                        <input type="text" data-kt-job-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search jobs" />
                    </div>
                </div>
                <div class="col-lg-9">
                    <a href="javascript:;" class="float-end {{ (!$worker['email_verified_at']) ? 'disabled' : '' }}" id="add_new_job" name="add_new_job">
                        <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                    </a>

                    <div class="btn-group me-3 float-end" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                        <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding active" data-kt-button="true">
                            <input class="btn-check client_job_status" type="radio" name="client_job_status" checked="checked" value="0"/>
                            Active
                        </label>
                        <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                            <input class="btn-check client_job_status" type="radio" name="client_job_status" value="1" />
                            Archived
                        </label>
                        <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                            <input class="btn-check client_job_status" type="radio" name="client_job_status" value="All" />
                            All
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-12 text-start text-muted fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="jobs_datatable">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Job Name</th>
                            <th>Site</th>
                            <th>Client</th>
                            <th>INV.</th>
                            <th>CONF.</th>
                            <th>DECL.</th>
                            <th>Shifts</th>
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

<!-- BEGIN JOB MODAL-->
<div class="modal fade" id="add_to_job_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Add {{ $worker['first_name'].' '.$worker['middle_name'].' '.$worker['last_name'] }} to a job</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_add_to_job_modal">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="worker_job_form">
                <div class="modal-body scroll-y m-5">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12 mb-10">
                                    <span class="fs-4 fw-lighter"><span class="fw-bold text-danger">Please note:</span> If you add workers directly, you are responsible for them receiving and accepting the assignment schedule</span>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label for="client" class="fs-6 fw-bold">Client</label>
                                        <input type="hidden" name="_token" id="_token" value="{{ @csrf_token() }}">
                                        <select name="client" id="client" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Select client" data-allow-clear="true">
                                            <option value=""></option>
                                            @if($client)
                                                @foreach($client as $row)
                                                    <option value="{{ $row['id'] }}">{{ $row['company_name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger error" id="client_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label for="site" class="fs-6 fw-bold">Site</label>
                                        <select name="site" id="site" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Select a client first" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                        <span class="text-danger error" id="site_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label for="job" class="fs-6 fw-bold">Job</label>
                                        <select name="job" id="job" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Select a site first" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                        <span class="text-danger error" id="job_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label for="invitation_type" class="fs-6 fw-bold"></label>
                                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-1 row-cols-xl-2 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                            <div class="col">
                                                <label class="d-flex text-start" data-kt-button="true">
                                                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                        <input class="form-check-input" type="radio" name="invitation_type" id="invitation_type_1" value="1" checked="checked">
                                                    </span>
                                                    <span class="ms-5">
                                                        <span class="fs-4 fw-bolder text-gray-800 d-block">Send invitation</span>
                                                    </span>
                                                </label>
                                            </div>

                                            <div class="col">
                                                <label class="d-flex text-start" data-kt-button="true">
                                                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                        <input class="form-check-input" type="radio" name="invitation_type" id="invitation_type_2" value="2">
                                                    </span>
                                                    <span class="ms-5">
                                                        <span class="fs-4 fw-bolder text-gray-800 d-block">Add directly</span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" name="client_job_worker_form_submit" id="client_job_worker_form_submit" class="btn btn-primary float-end">Add worker</button>
                                <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="client_job_worker_form_process" id="client_job_worker_form_process" style="display: none">
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
<!-- END JOB MODAL-->

@section('edit_worker_job_js')
    <script>
        $(document).ready(function() {
            $(".form-select-custom").select2({
                dropdownParent: $("#worker_job_form")
            });
        });

        let tableNameJobsDatatable = $('#jobs_datatable');
        let jobs_table = tableNameJobsDatatable.DataTable();

        $(document).on('click', '#kt_table_widget_5_tab_4_menu', function () {
            jobs_table.destroy()
            jobs_table = tableNameJobsDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-worker-assigned-jobs') }}',
                    "data": function (d) {
                        d._token    = $('#_token').val();
                        d.worker_id = '{{ $worker['id'] }}';
                        d.status    = $('input[name="client_job_status"]:checked').val();
                    },
                },
                "columns": [
                    {"data": "name"},
                    {"data": "site"},
                    {"data": "client"},
                    {"data": "invited_at", "width":"4%", "sClass":"text-center"},
                    {"data": "confirmed_at", "width":"4%", "sClass":"text-center"},
                    {"data": "declined_at", "width":"4%", "sClass":"text-center"},
                    {"data": "no_shift", "width":"4%", "sClass":"text-center"},
                    {"data": "action", "width":"10%", "sClass":"text-end"},
                ]
            });
        })

        $(document).on('change', '.client_job_status', function () {
            jobs_table.ajax.reload();
        })

        const filterSearch = document.querySelector('[data-kt-job-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            jobs_table.search(e.target.value).draw();
        });

        $(document).on('click', '.archive_action', function () {
            let id = $(this).attr('data-client_job_worker_id');
            sweetAlertUnlink($(this).attr('data-text')).then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('archive-client-job-worker') }}'+'/'+id,
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

        $("#add_new_job").on('click', function (){
            $("#client").val('').trigger('change');
            $("#site").val('').trigger('change');
            $("#job").val('').trigger('change');

            $("#add_to_job_modal").modal('show');
        });

        $("#cls_btn_add_to_job_modal").on('click', function (){
            $("#add_to_job_modal").modal('hide');
        });

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
                        $("#job").empty();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#site").on('change', function (){
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-job-using-site') }}',
                data    : {
                    _token  : '{{ csrf_token() }}',
                    site_id : $("#site").val(),
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#job").empty().append(response.data.job_option);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#worker_job_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            let client = $("#client").val();
            let site = $("#site").val();
            let job = $("#job").val();

            if(client === '')
                $("#client_error").empty().append('The client field is required.');

            if(site === '')
                $("#site_error").empty().append('The site field is required.');

            if(job === '')
                $("#job_error").empty().append('The job field is required.');

            if(client !== '' && site !== '' && job !== '') {

                $("#client_job_worker_form_submit").hide();
                $("#client_job_worker_form_process").show();

                $.ajax({
                    type        : 'post',
                    url         : '{{ url('store-client-job-worker-multiple') }}',
                    data        : {
                        _token          : '{{ @csrf_token() }}',
                        job_worker_name : ["{{ $worker['id'] }}"],
                        invitation_type : $("input[name='invitation_type']:checked").val(),
                        job_worker_id   : job,
                        add_type        : 'single',
                    },
                    success     : function (response) {
                        decodeResponse(response)

                        $("#client_job_worker_form_submit").show();
                        $("#client_job_worker_form_process").hide();

                        if(response.code === 200) {
                            jobs_table.ajax.reload();

                            $("#client").val('').trigger('change');
                            $("#site").val('').trigger('change');
                            $("#job").val('').trigger('change');

                            $("input[name='invitation_type']").first().prop("checked", true);
                            $("#add_to_job_modal").modal('hide');
                        }
                    },
                    error   : function (response) {
                        $("#client_job_worker_form_submit").show();
                        $("#client_job_worker_form_process").hide();

                        toastr.error(response.statusText);
                    }
                });
            }
        });
    </script>
@endsection
