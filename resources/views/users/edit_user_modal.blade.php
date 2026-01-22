<div class="modal fade" id="edit_user_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Edit system user</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="edit_user_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="edit_user_form">
                @csrf
                <div class="modal-body scroll-y m-5">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <div id="image_input_main_div" class="image-input image-input-outline image-input-empty" data-kt-image-input="true" style="background-image: url('{{ asset('assets/media/avatars/worker-square.png') }}');">
                                            <div id="image_input_child_div" class="image-input-wrapper w-lg-200px h-lg-200px" style="background-image: url('{{ asset('assets/media/avatars/worker-square.png') }}');">
                                            </div>
                                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                                <i class="bi bi-pencil-fill fs-7"></i>
                                                <input type="file" id="edit_profile_pic" name="edit_profile_pic" accept=".png, .jpg, .jpeg" />
                                                <input type="hidden" name="edit_user_avatar_remove" id="edit_user_avatar_remove" />
                                            </label>
                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                                <i class="bi bi-x fs-2"></i>
                                            </span>
                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                                <i class="bi bi-x fs-2"></i>
                                            </span>
                                        </div>
                                        <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Name</label>
                                        <input type="text" name="edit_name" id="edit_name" class="form-control" value="" placeholder="Enter name">
                                        <span class="error text-danger" id="edit_name_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Email</label>
                                        <input type="text" name="edit_email" id="edit_email" class="form-control" value="" placeholder="Enter email">
                                        <span class="error text-danger" id="edit_email_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">User type</label>
                                        <select name="edit_user_type" id="edit_user_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                            <option></option>
                                            <option value="Admin">Admin</option>
                                            <option value="Payroll">Payroll</option>
                                            <option value="Standard">Standard</option>
                                        </select>
                                        <span class="error text-danger" id="edit_user_type_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold">Team</label>
                                        <select name="edit_team_members" id="edit_team_members" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                            <option value="">Select..</option>
                                            @if($teamMembers)
                                                @foreach($teamMembers as $teamMembers_row)
                                                    <option value="{{$teamMembers_row['id']}}">{{$teamMembers_row['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="edit_user_id" id="edit_user_id" value="0">
                            <button type="submit" name="edit_user_form_submit_btn" id="edit_user_form_submit_btn" class="btn btn-primary float-end">Edit user</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="edit_user_form_process_btn" id="edit_user_form_process_btn">
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

@section('edit_user_js')
    <script>
        $(document).ready(function() {
            $("#edit_user_type").select2({
                dropdownParent: $("#edit_user_form")
            });
            $('#edit_team_members').select2({
                dropdownParent: $("#edit_user_form")
            });
        });

        $(document).on('click', '#edit_user', function () {
            $(".error").html('');

            $("#edit_user_form").trigger('reset');
            $("#edit_user_type").trigger('change');
            $("#edit_team_members").val('').trigger('change');

            $.ajax({
                type        : 'get',
                url         : '{{ url('get-single-user') }}'+'/'+$(this).attr('data-user_id'),
                success     : function (response) {
                    if(response.code === 200) {

                        $("#edit_user_id").val(response.data.user_details.id);
                        $("#edit_name").val(response.data.user_details.name);
                        $("#edit_email").val(response.data.user_details.email);
                        $("#edit_user_type").val(response.data.user_details.user_type).trigger('change');
                        $("#edit_team_members").val(response.data.user_details.team_id).trigger('change');


                        if (!response.data.user_details.profile_pic) {
                            $("#image_input_main_div").addClass('image-input-empty');
                        } else {
                            let file_path = '{{ asset('workers/users') }}'+'/'+response.data.user_details.profile_pic
                            $("#image_input_main_div").removeClass('image-input-empty');
                            $("#image_input_child_div").css('background-image', 'url('+file_path+')');
                        }

                        $("#edit_user_modal").modal('show');
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#edit_user_modal_close_btn").on('click', function (){
            $("#edit_user_modal").modal('hide');
        })

        $("#edit_user_form").on('submit', function (e) {

            $("#edit_user_form_submit_btn").addClass('d-none');
            $("#edit_user_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('edit-user-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#edit_user_form_submit_btn").removeClass('d-none');
                    $("#edit_user_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        user_table.ajax.reload();

                        $("#edit_user_form").trigger('reset');
                        $("#edit_user_type").trigger('change');
                        $("#edit_team").trigger('change');
                        $("#edit_user_modal").modal('hide');
                    }
                },
                error   : function (response) {
                    $("#edit_user_form_submit_btn").removeClass('d-none');
                    $("#edit_user_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
