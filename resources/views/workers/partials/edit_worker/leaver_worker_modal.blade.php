<div class="modal fade" id="leaver_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Add a leaving date for {{$worker['first_name'].' '.$worker['last_name']}}</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="leaver_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <div class="modal-body scroll-y m-5">
                <div class="w-100">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label class="text-muted fs-6 fw-bold required">Leaving date</label>
                                    <div class="position-relative d-flex align-items-center">
                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                    <i class="fs-2 las la-calendar"></i>
                                                </span>
                                        <input class="form-control ps-12 flatpickr-input date_input" name="leaving_date" id="leaving_date" type="text" placeholder="Select leaving date">
                                    </div>
                                    <span class="error text-danger" id="leaving_date_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-lg-12">
                        <button type="button" name="leaver_form_submit_btn" id="leaver_form_submit_btn" class="btn btn-primary float-end">Submit</button>
                        <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="leaver_form_process_btn" id="leaver_form_process_btn">
                            <span>Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('add_worker_leaver_date_js')
    <script>
        flatpickr('#leaving_date', {
            dateFormat: 'd-m-Y'
        });

        $("#leaver_modal_close_btn").on('click', function (){
            $("#leaver_modal").modal('hide');
        })

        $("#leaver_form_submit_btn").on('click', function () {

            $("#leaver_form_submit_btn").addClass('d-none');
            $("#leaver_form_process_btn").removeClass('d-none');

            $(".error").html('');
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-leaving-status') }}',
                data        : {
                    _token      : '{{ csrf_token() }}',
                    worker_id   : '{{ $worker['id'] }}',
                    status      : $('input[name="worker_status"]:checked').val(),
                    leaving_date: $("#leaving_date").val(),
                },
                success     : function (response) {
                    decodeResponse(response)

                    $("#leaver_form_submit_btn").removeClass('d-none');
                    $("#leaver_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $(".error").html('');
                        $("#add_leaver_date_form").trigger('reset');
                        $("#leaver_modal").modal('hide');
                        setTimeout(function() { location.reload(); }, 1500);
                    }
                },
                error   : function (response) {
                    $("#leaver_form_submit_btn").removeClass('d-none');
                    $("#leaver_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
