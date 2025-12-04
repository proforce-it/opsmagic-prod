<div class="modal fade" id="user_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Add a new system user</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="user_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="add_user_form">
                @csrf
                <div class="modal-body scroll-y m-5">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <div class="image-input image-input-outline image-input-empty" data-kt-image-input="true" style="background-image: url('{{ asset('assets/media/avatars/worker-square.png') }}');">
                                            <div class="image-input-wrapper w-lg-200px h-lg-200px" style="background-image: none">
                                            </div>
                                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                                <i class="bi bi-pencil-fill fs-7"></i>
                                                <input type="file" id="profile_pic" name="profile_pic" accept=".png, .jpg, .jpeg" />
                                                <input type="hidden" name="user_avatar_remove" id="user_avatar_remove" />
                                            </label>
                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                                <i class="bi bi-x fs-2"></i>
                                            </span>
                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                                <i class="bi bi-x fs-2"></i>
                                            </span>
                                        </div>
                                        <div class="form-text">
                                            <span class="error text-danger" id="profile_pic_error"></span> <br>
                                            Allowed file types: png, jpg, jpeg.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Name</label>
                                        <input type="text" name="name" id="name" class="form-control" value="" placeholder="Enter name">
                                        <span class="error text-danger" id="name_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Email</label>
                                        <input type="text" name="email" id="email" class="form-control" value="" placeholder="Enter email">
                                        <span class="error text-danger" id="email_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">User type</label>
                                        <select name="user_type" id="user_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                            <option></option>
                                            <option value="Standard">Standard</option>
                                            <option value="Admin">Admin</option>
                                        </select>
                                        <span class="error text-danger" id="user_type_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold">Team</label>
                                        <select name="team_members" id="team_members" class="form-select form-select-lg select2-hidden-accessible" data-control="select2" data-placeholder="Select..." data-allow-clear="true" data-select2-id="select2-data-title" tabindex="-1" aria-hidden="true">
                                            <option></option>
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
                            <button type="submit" name="user_form_submit_btn" id="user_form_submit_btn" class="btn btn-primary float-end">Add user</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="user_form_process_btn" id="user_form_process_btn">
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

@section('add_user_js')
    <script>
        $(document).ready(function() {
            $("#user_type").select2({
                dropdownParent: $("#add_user_form")
            });
            $('#team_members').select2({
                dropdownParent: $("#add_user_form")
            });
        });

        $("#add_user_modal_btn").on('click', function () {
            $(".error").html('');
            $("#add_user_form").trigger('reset');
            $("#user_type").trigger('change');
            $("#team").val('').trigger('change');
            $("#user_modal").modal('show');
        });

        $("#user_modal_close_btn").on('click', function (){
            $("#team").val('').trigger('change');
            $("#user_modal").modal('hide');
        })

        $("#add_user_form").on('submit', function (e) {

            $("#user_form_submit_btn").addClass('d-none');
            $("#user_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-user-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#user_form_submit_btn").removeClass('d-none');
                    $("#user_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        user_table.ajax.reload();

                        $("#add_user_form").trigger('reset');
                        $("#user_type").trigger('change');
                        $("#team").val('').trigger('change');
                        $("#user_modal").modal('hide');
                    }
                },
                error   : function (response) {
                    $("#user_form_submit_btn").removeClass('d-none');
                    $("#user_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
