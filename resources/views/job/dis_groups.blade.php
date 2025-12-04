<div class="table-responsive">
    <div class="p-5">
        <div class="row mb-7">
            <div class="w-100">
                <div class="row">
                    <div class="col-lg-10">
                        <h1>Groups linked to this job</h1>
                    </div>
                    <div class="col-lg-2">
                        <div class="float-end">
                            <a href="javascript:;" id="link_group_modal_btn">
                                <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-100 mt-5 mb-5" style="border-top: 1px dashed #dddfe1"></div>
            <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th>NAME</th>
                        <th style="width: 10%">GROUP STATUS</th>
                        <th style="width: 10%"># ACTIVE MEMBERS</th>
                        <th style="width: 10%"># NO RTW</th>
                        <th style="width: 10%"># LEAVERS</th>
                        <th style="width: 10%"># ARCHIVED</th>
                        <th  style="width: 10%" class="text-end">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @if($linkedGroups)
                        @foreach($linkedGroups as $row)
                            <tr>
                                <td>{{ $row['groups']['name'] }}</td>
                                <td>{{ ($row['groups']['deleted_at']) ? 'Archived' : 'Active' }}</td>
                                <td>{{ $row['groups']['active_members_count'] }}</td>
                                <td>{{ $row['groups']['no_rtw_count'] }}</td>
                                <td>{{ $row['groups']['leavers_count'] }}</td>
                                <td>{{ $row['groups']['archived_count'] }}</td>
                                <td class="text-end">
                                    <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm me-1 unlink_group" data-group-with-job-id="{{ $row['id'] }}" data-group-name="{{ $row['groups']['name']}}" data-group-id="{{ $row['group_id'] }}"
                                       data-job-id="{{ $row['job_id'] }}">
                                        <i class="fs-2 las la-unlink"></i>
                                    </a>

                                    <a href="{{ url('associate-groups-details/'.$row['groups']['id']) }}" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                                        <i class="fs-2 las la-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">No data found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <div class="modal fade" id="unlink_group_job_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header py-7 d-flex justify-content-between">
                            <h2>Unlink group '<span id="unlink_group_name"></span>'  from job</h2>
                            <div class="btn btn-sm btn-icon btn-active-color-primary" id="unlink_group_job_modal_close_btn">
                                <i class="fs-2 las la-times"></i>
                            </div>
                        </div>
                        <form id="unlink_group_job_form">
                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-custom alert-info mb-10" role="alert">
                                    <div class="alert-text fs-4">
                                        <i class="las la-info-circle text-info fs-xl-2"></i>
                                        To keep <b>current</b> group members linked to this job as
                                        individual associates select ‘Unlink group only’. To
                                        unlink a group and all members from this job select
                                        ‘Unlink group and associates’
                                    </div>
                                </div>
                                <div class="w-100">
                                    <div class="fv-row">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="fv-row fv-plugins-icon-container">
                                                    <label for="unlink_groups" class="fs-6 fw-bold"></label>
                                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-1 row-cols-xl-2 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                                        <div class="col">
                                                            <label class="d-flex text-start" data-kt-button="true">
                                                                <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                    <input class="form-check-input" type="radio" name="unlink_groups" id="unlink_groups_1" value="1" checked="checked">
                                                                </span>
                                                                <span class="ms-5">
                                                                    <span class="fs-4 fw-bolder text-gray-800 d-block">Unlink group only</span>
                                                                </span>
                                                            </label>
                                                        </div>

                                                        <div class="col">
                                                            <label class="d-flex text-start" data-kt-button="true">
                                                                <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                    <input class="form-check-input" type="radio" name="unlink_groups" id="unlink_groups_2" value="2">
                                                                </span>
                                                                <span class="ms-5">
                                                                    <span class="fs-4 fw-bolder text-gray-800 d-block">Unlink group and associates</span>
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
                            <div class="modal-footer d-flex justify-content-center">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <input type="hidden" id="unlink_group_id" name="group_id">
                                        <input type="hidden" id="unlink_job_id" name="job_id">
                                        <input type="hidden" id="unlink_record_id" name="record_id">
                                        <button type="submit" name="unlink_group_job_form_submit_btn" id="unlink_group_job_form_submit_btn" class="btn btn-primary">Unlink group(s)</button>
                                        <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="unlink_group_job_form_process_btn" id="unlink_group_job_form_process_btn">
                                        <span>Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@section('unlink_group_job_js')
    <script>
        $(".unlink_group").on('click', function () {
            $(".error").html('');
            let groupName = $(this).data('group-name');
            let jobId = $(this).data('job-id');
            let groupId = $(this).data('group-id');
            let recordId = $(this).data('group-with-job-id');
            $('#unlink_group_name').text(groupName);

            $('#unlink_group_id').val(groupId);
            $('#unlink_job_id').val(jobId);
            $('#unlink_record_id').val(recordId);

            $("#unlink_group_job_modal").modal('show');
        });

        $("#unlink_group_job_modal_close_btn").on('click', function (){
            $("#unlink_group_job_modal").modal('hide');
        });

        $("#unlink_group_job_form").on('submit', function (e) {

            $("#unlink_group_job_form_submit_btn").addClass('d-none');
            $("#unlink_group_job_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('unlink-group-to-job-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#unlink_group_job_form_submit_btn").removeClass('d-none');
                    $("#unlink_group_job_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                        $("#unlink_group_job_modal_close_btn").click();
                    }
                },
                error   : function (response) {
                    $("#unlink_group_job_form_submit_btn").removeClass('d-none');
                    $("#unlink_group_job_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });


    </script>
@endsection
