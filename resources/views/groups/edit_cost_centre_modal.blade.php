<div class="modal fade" id="edit_cost_centre_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 class="modal-title">Edit cost centre</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="edit_cost_centre_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="edit_cost_centre_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Name</label>
                                        <input type="text" name="edit_name" id="edit_name" class="form-control" value="" placeholder="Enter name">
                                        <span class="error text-danger" id="edit_name_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Short code</label>
                                        <input type="text" name="edit_short_code" id="edit_short_code" class="form-control" value="" placeholder="Enter short code">
                                        <span class="error text-danger" id="edit_short_code_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="edit_cost_centre_id" id="edit_cost_centre_id" value="0">
                            <button type="submit" name="edit_cost_centre_form_submit_btn" id="edit_cost_centre_form_submit_btn" class="btn btn-primary float-end">Update</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="edit_cost_centre_form_process_btn" id="edit_cost_centre_form_process_btn">
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

@section('edit_cost_centre_js')
    <script>
        $(document).on('click', '#edit_cost_centre', function () {
            $(".error").html('');
            let costCentreName = $(this).data('cost-centre-name');
            $('.modal-title').text('Edit cost centre: ' + costCentreName);
            $("#edit_cost_centre_form").trigger('reset');

            $.ajax({
                type        : 'get',
                url         : '{{ url('get-single-cost-centre') }}'+'/'+$(this).attr('data-cost-centre_id'),
                success     : function (response) {
                    if(response.code === 200) {

                        $("#edit_cost_centre_id").val(response.data.cost_centre_details.id);
                        $("#edit_name").val(response.data.cost_centre_details.name);
                        $("#edit_short_code").val(response.data.cost_centre_details.short_code);

                        $("#edit_cost_centre_modal").modal('show');
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#edit_cost_centre_modal_close_btn").on('click', function (){
            $("#edit_cost_centre_modal").modal('hide');
        })

        $("#edit_cost_centre_form").on('submit', function (e) {

            $("#edit_cost_centre_form_submit_btn").addClass('d-none');
            $("#edit_cost_centre_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('edit-cost-centre-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#edit_cost_centre_form_submit_btn").removeClass('d-none');
                    $("#edit_cost_centre_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        cost_centre_table.ajax.reload();

                        $("#edit_cost_centre_form").trigger('reset');
                        $("#edit_cost_centre_modal").modal('hide');
                    }
                },
                error   : function (response) {
                    $("#edit_cost_centre_form_submit_btn").removeClass('d-none');
                    $("#edit_cost_centre_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
