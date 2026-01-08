<div class="modal fade" id="add_group_with_worker_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 id="add_group_with_worker_modal_title">
                    Add {{$worker['first_name']}} to group
                </h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="add_group_with_worker_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="add_group_with_worker_form">
                @csrf
                <input type="hidden" name="worker_id" id="worker_id" value="{{$worker['id']}}" />
                <input type="hidden" name="create_type" id="create_type" value="0" /> <!-- 0 = NOT CREATE, 1 = CREATE -->
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row" id="add_group_with_worker_form_section">
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Select group</label>
                                        <select name="group_name" id="group_name" class="form-select form-select-lg" data-control="select2" data-placeholder="Select group" data-allow-clear="true">
                                            <option value="">Select..</option>
                                            @if($group)
                                                @foreach($group as $group_row)
                                                    <option value="{{$group_row['id']}}">{{$group_row['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error text-danger" id="group_name_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-none" id="add_group_with_worker_confirm_job_section">
                                <div class="col-lg-12">
                                    <p class="mb-5 fs-4">The group <span id="selected_group_name"></span> is already linked to the following job(s)</p>
                                    <p class="fs-4" id="selected_group_linked_job_name_section"></p>
                                    <p class="mt-5 fs-4">Do you want to link the {{ $worker['first_name'] }} to these job(s) or only to jobs you link with the group in the future?</p>
                                </div>
                                <div class="col-lg-12 mt-5">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <div>
                                            <label class="form-check-inline me-5">
                                                <input type="radio" name="link_worker_to_job_using_group_type" id="link_to_existing_job" value="link_to_existing_job" checked>
                                                <span class="fw-bold fs-5">Link to existing job(s)</span>
                                            </label>
                                            <label class="form-check-inline">
                                                <input type="radio" name="link_worker_to_job_using_group_type" id="only_link_to_new_job" value="only_link_to_new_job">
                                                <span class="fw-bold fs-5">Only link to new job(s)</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" name="add_group_with_worker_form_submit_btn" id="add_group_with_worker_form_submit_btn" class="btn btn-primary float-end">Add worker to group</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="add_group_with_worker_form_process_btn" id="add_group_with_worker_form_process_btn">
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
@section('add_group_with_worker_js')
    <script>
        $(document).ready(function() {
            $("#group_name").select2({
                dropdownParent: $("#add_group_with_worker_form")
            });
        });

        function add_group_modal_reset() {
            $("#add_group_with_worker_modal_title").text('Add {{ $worker['first_name'] }} to group');
            $("#add_group_with_worker_form_section").removeClass('d-none');
            $("#add_group_with_worker_confirm_job_section").addClass('d-none');
            $("#selected_group_name").html('');
            $("#selected_group_linked_job_name_section").html('');
            $("#add_group_with_worker_form_submit_btn").text('Add worker to group');
            $("#create_type").val('0');
        }

        $("#add_group_with_worker_modal_btn").on('click', function () {
            $(".error").html('');
            add_group_modal_reset();
            $("#add_group_with_worker_form").trigger('reset');
            $("#group_name").val('').trigger('change');
            $("#add_group_with_worker_modal").modal('show');
        });

        $("#add_group_with_worker_modal_close_btn").on('click', function () {
            add_group_modal_reset();
            $("#add_group_with_worker_form").trigger('reset');
            $("#group_name").val('').trigger('change');
            $("#add_group_with_worker_modal").modal('hide');
        })

        $("#add_group_with_worker_form").on('submit', function (e) {

            $("#add_group_with_worker_form_submit_btn").addClass('d-none');
            $("#add_group_with_worker_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-group-with-worker-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {

                    $("#add_group_with_worker_form_submit_btn").removeClass('d-none');
                    $("#add_group_with_worker_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        if (response.message === 'selected_group_details_fetched') {
                            $("#add_group_with_worker_modal_title").text('Link {{ $worker['first_name'] }} to existing jobs?');
                            $("#add_group_with_worker_form_section").addClass('d-none');
                            $("#add_group_with_worker_confirm_job_section").removeClass('d-none');
                            $("#selected_group_name").html(response.data.group_name);
                            $("#selected_group_linked_job_name_section").html(response.data.job_name);
                            $("#add_group_with_worker_form_submit_btn").text('Continue')
                            $("#create_type").val('1');
                        } else {
                            toastr.success(response.message);
                            $("#add_group_with_worker_modal_close_btn").click();
                            setTimeout(function () {
                                location.reload();
                            }, 1500)
                        }
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                    } else {
                        const keys = Object.keys(response.data);
                        keys.forEach((key, index) => {
                            const inputId = key.replace(/\./g, '_');
                            if (index === 0) {
                                $("#" + inputId).focus();
                            }
                            $("#" + inputId + "_error").empty().append(response.data[key][0]);
                        });
                    }
                },
                error   : function (response) {
                    $("#add_group_with_worker_form_submit_btn").removeClass('d-none');
                    $("#add_group_with_worker_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
