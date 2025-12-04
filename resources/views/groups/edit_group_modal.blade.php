<div class="modal fade" id="edit_group_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 class="modal-title">Edit a group</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="edit_group_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="edit_group_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Group name</label>
                                        <input type="text" name="edit_group_name" id="edit_group_name" class="form-control" value="" placeholder="Enter group name">
                                        <span class="error text-danger" id="edit_group_name_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="edit_team" id="edit_team" value="">
                            <input type="hidden" name="edit_group_id" id="edit_group_id" value="0">
                            <button type="submit" name="edit_group_form_submit_btn" id="edit_group_form_submit_btn" class="btn btn-primary float-end">Update</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="edit_group_form_process_btn" id="edit_group_form_process_btn">
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

@section('edit_group_js')
    <script>
        $(document).on('click', '#edit_group', function () {
            $(".error").html('');
            let groupName = $(this).data('group_name');
            $('.modal-title').text('Edit group: ' + groupName);

            $("#edit_group_form").trigger('reset');
            $("#edit_team").val('');

            $.ajax({
                type        : 'get',
                url         : '{{ url('get-single-group') }}'+'/'+$(this).attr('data-group_id'),
                success     : function (response) {
                    if(response.code === 200) {

                        $("#edit_group_id").val(response.data.group_details.id);
                        $("#edit_team").val(response.data.group_details.team_id);
                        $("#edit_group_name").val(response.data.group_details.name);

                        $("#edit_group_modal").modal('show');
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#edit_group_modal_close_btn").on('click', function () {
            $("#edit_group_form").trigger('reset');
            $("#edit_team").val('');
            $("#edit_group_modal").modal('hide');
        })

        $("#edit_group_form").on('submit', function (e) {

            $("#edit_group_form_submit_btn").addClass('d-none');
            $("#edit_group_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('edit-group-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#edit_group_form_submit_btn").removeClass('d-none');
                    $("#edit_group_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        group_table.ajax.reload();
                        $("#edit_group_modal_close_btn").click();
                    }
                },
                error   : function (response) {
                    $("#edit_group_form_submit_btn").removeClass('d-none');
                    $("#edit_group_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
