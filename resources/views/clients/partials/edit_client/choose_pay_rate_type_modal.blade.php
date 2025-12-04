<div class="modal fade" id="pay_rate_type_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Choose pay rate type for {{ $job['name'] }}</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="pay_rate_type_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <div class="modal-body">
                <div class="w-100">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label class="text-muted fs-6 fw-bold required">Pay rate type</label>
                                    <select name="pay_rate" id="pay_rate" class="form-select form-select-lg" data-control="select2" data-allow-clear="true" data-placeholder="Select...">
                                        <option value="" selected>Select...</option>
                                        <option value="flat_rate">Flat rate (with or without overtime)</option>
                                        <option value="pay_rate_map">Pay rate map</option>
                                    </select>
                                    <span class="text-danger error" id="pay_rate_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="justify-content: center !important;">
                <button type="button" name="pay_rate_form_submit_btn" id="pay_rate_form_submit_btn" class="btn btn-primary btn-lg">Next</button>
            </div>
        </div>
    </div>
</div>
@section('choose_pay_rate_type_js')
    <script>
        $("#choose_pay_rate_type_modal_btn").on('click', function () {
            $('#pay_rate').val('').trigger('change');
            $("#pay_rate_type_modal").modal('show');
        });

        $("#pay_rate_type_modal_close_btn").on('click', function () {
            $(".error").html('');
            $('#pay_rate').val('').trigger('change');
            $("#pay_rate_type_modal").modal('hide');
        })

        $("#pay_rate_form_submit_btn").on('click', function () {
            $(".error").html('');

            let pay_rate = $("#pay_rate").val();
            if (!pay_rate) {
                $("#pay_rate_error").text('The pay rate type field is required.')
                return
            }

            $('#pay_rate').val('').trigger('change');
            $('#pay_rate_type_modal').modal('hide');

            if (pay_rate === 'flat_rate') {
                $('#flat_pay_rate_modal').modal('show');
            } else {
                $('#pay_rate_map_modal').modal('show');
            }
        });
    </script>
@endsection
