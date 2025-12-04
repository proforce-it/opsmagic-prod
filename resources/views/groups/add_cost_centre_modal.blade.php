<div class="modal fade" id="cost_centre_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Add a cost centre</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cost_centre_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="add_cost_centre_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Name</label>
                                        <input type="text" name="name" id="name" class="form-control" value="" placeholder="Enter name">
                                        <span class="error text-danger" id="name_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Short code</label>
                                        <input type="text" name="short_code" id="short_code" class="form-control" value="" placeholder="Enter short code">
                                        <span class="error text-danger" id="short_code_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" name="cost_centre_form_submit_btn" id="cost_centre_form_submit_btn" class="btn btn-primary float-end">Add cost centre</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="cost_centre_form_process_btn" id="cost_centre_form_process_btn">
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

@section('add_cost_centre_js')
    <script>
        $("#add_cost_centre_modal_btn").on('click', function () {
            $(".error").html('');
            $("#add_cost_centre_form").trigger('reset');
            $("#cost_centre_modal").modal('show');
        });

        $("#cost_centre_modal_close_btn").on('click', function (){
            $("#cost_centre_modal").modal('hide');
        })

        $("#add_cost_centre_form").on('submit', function (e) {

            $("#cost_centre_form_submit_btn").addClass('d-none');
            $("#cost_centre_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-cost-centre-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#cost_centre_form_submit_btn").removeClass('d-none');
                    $("#cost_centre_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        cost_centre_table.ajax.reload();

                        $("#add_cost_centre_form").trigger('reset');
                        $("#cost_centre_modal").modal('hide');
                    }
                },
                error   : function (response) {
                    $("#cost_center_form_submit_btn").removeClass('d-none');
                    $("#cost_centre_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
