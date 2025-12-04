<div class="modal fade" id="group_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Create a new group</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="group_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="add_group_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Group name</label>
                                        <input type="text" name="group_name" id="group_name" class="form-control" value="" placeholder="Enter group name">
                                        <span class="error text-danger" id="group_name_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="team" id="team" value="{{ $teamId }}">
                            <button type="submit" name="group_form_submit_btn" id="group_form_submit_btn" class="btn btn-primary float-end">Create group</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="group_form_process_btn" id="group_form_process_btn">
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

@section('add_group_js')
    <script>
        $("#add_group_modal_btn").on('click', function () {
            $(".error").html('');
            $("#add_group_form").trigger('reset');
            $("#group_modal").modal('show');
        });

        $("#group_modal_close_btn").on('click', function (){
            $("#add_group_form").trigger('reset');
            $("#group_modal").modal('hide');
        })

        $("#add_group_form").on('submit', function (e) {

            $("#group_form_submit_btn").addClass('d-none');
            $("#group_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-group-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#group_form_submit_btn").removeClass('d-none');
                    $("#group_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        group_table.ajax.reload();
                        $("#group_modal_close_btn").click();
                    }
                },
                error   : function (response) {
                    $("#group_form_submit_btn").removeClass('d-none');
                    $("#group_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
