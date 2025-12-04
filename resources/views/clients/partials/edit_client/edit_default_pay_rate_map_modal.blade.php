<div class="modal fade" id="edit_default_pay_rate_map_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Edit Default Pay Rate</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="edit_default_pay_rate_map_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="edit_default_pay_rate_map_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_default_pay_rate_map_name" class="fs-6 fw-bold">Pay rate name</label>
                                        <div class="input-group">
                                            <input type="text" name="edit_default_pay_rate_map_name" id="edit_default_pay_rate_map_name" class="form-control bg-secondary" value="Default Rate" readonly/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_default_pay_rate_map_short_code" class="fs-6 fw-bold">Pay rate short code (3 characters max)</label>
                                        <div class="input-group">
                                            <input type="text" name="edit_default_pay_rate_map_short_code" id="edit_default_pay_rate_map_short_code" class="form-control bg-secondary" value="DR" readonly/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_default_pay_rate_map_per_hour" class="fs-6 fw-bold">Pay</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input class="form-control" name="edit_default_pay_rate_map_per_hour" id="edit_default_pay_rate_map_per_hour" type="text" placeholder="pay" value="{{ $pay_rate_map['base_pay_rate'] }}">
                                            <div class="input-group-prepend"><span class="input-group-text" style="font-size: 16px">hr</span></div>
                                        </div>
                                        <span class="text-danger error" id="edit_default_pay_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_default_charge_rate_map_per_hour" class="fs-6 fw-bold">Charge</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="edit_default_charge_rate_map_per_hour" id="edit_default_charge_rate_map_per_hour" class="form-control" placeholder="charge"  value="{{ $pay_rate_map['base_charge_rate'] }}"/>
                                            <div class="input-group-prepend"><span class="input-group-text" style="font-size: 16px">hr</span></div>
                                        </div>
                                        <span class="text-danger error" id="edit_default_charge_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_default_overtime_pay_rate_map_per_hour" class="fs-6 fw-bold">Overtime Pay (optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="edit_default_overtime_pay_rate_map_per_hour" id="edit_default_overtime_pay_rate_map_per_hour" class="form-control" placeholder="overtime pay"  value="{{ $pay_rate_map['default_overtime_pay_rate'] }}"/>
                                            <div class="input-group-prepend"><span class="input-group-text" style="font-size: 16px">hr</span></div>
                                        </div>
                                        <span class="text-danger error" id="edit_default_overtime_pay_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_default_overtime_charge_rate_map_per_hour" class="fs-6 fw-bold">Overtime Charge (optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="edit_default_overtime_charge_rate_map_per_hour" id="edit_default_overtime_charge_rate_map_per_hour" class="form-control" placeholder="overtime charge"  value="{{ $pay_rate_map['default_overtime_charge_rate'] }}"/>
                                            <div class="input-group-prepend"><span class="input-group-text" style="font-size: 16px">hr</span></div>
                                        </div>
                                        <span class="text-danger error" id="edit_default_overtime_charge_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center !important;">
                    <input type="hidden" name="edit_default_pay_rate_map_id" id="edit_default_pay_rate_map_id" value="{{ $pay_rate_map['id'] }}" />
                    <input type="hidden" name="edit_prm_type" id="edit_prm_type" value="{{ $type }}" />
                    <button type="submit" name="edit_default_pay_rate_map_form_submit_btn" id="edit_default_pay_rate_map_form_submit_btn" class="btn btn-primary btn-lg">Update</button>
                    <button type="button" class="btn btn-lg btn-primary me-3 disabled d-none" data-kt-stepper-action="submit" name="edit_default_pay_rate_map_form_process_btn" id="edit_default_pay_rate_map_form_process_btn">
                        <span>Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('edit_default_pay_rate_js')
    <script>
        $("#edit_default_pay_rate_map_btn").on('click', function () {
            $("#edit_default_pay_rate_map_modal").modal('show');
        });

        $("#edit_default_pay_rate_map_modal_close_btn").on('click', function () {
            $("#edit_default_pay_rate_map_modal").modal('hide');
        });

        $("#edit_default_pay_rate_map_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#edit_default_pay_rate_map_form_submit_btn").addClass('d-none');
            $("#edit_default_pay_rate_map_form_process_btn").removeClass('d-none');

            let edit_default_pay_rate_map_per_hour = $("#edit_default_pay_rate_map_per_hour").val();
            let edit_default_charge_rate_map_per_hour = $("#edit_default_charge_rate_map_per_hour").val();
            $.ajax({
                type        : 'post',
                url         : '{{ url('edit-default-pay-rate-map-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#edit_default_pay_rate_map_form_submit_btn").removeClass('d-none');
                    $("#edit_default_pay_rate_map_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#edit_default_pay_rate_map_modal_close_btn").click();
                        $("#default_rate_pay_amount").text(edit_default_pay_rate_map_per_hour);
                        $("#default_rate_charge_amount").text(edit_default_charge_rate_map_per_hour);
                        $(".mapped_save_alert_section").removeClass('d-none');
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#edit_default_pay_rate_map_form_submit_btn").removeClass('d-none');
                    $("#edit_default_pay_rate_map_form_process_btn").addClass('d-none');
                }
            });
        });
    </script>
@endsection