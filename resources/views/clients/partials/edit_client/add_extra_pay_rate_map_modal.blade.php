<div class="modal fade" id="add_extra_pay_rate_map_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Add Pay Rate</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="add_extra_pay_rate_map_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="add_extra_pay_rate_map_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="add_extra_pay_rate_map_name" class="fs-6 fw-bold">Pay rate name</label>
                                        <div class="input-group">
                                            <input type="text" name="add_extra_pay_rate_map_name" id="add_extra_pay_rate_map_name" class="form-control" placeholder="Enter pay rate name" value="" />
                                        </div>
                                        <span class="text-danger error" id="add_extra_pay_rate_map_name_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="add_extra_pay_rate_map_short_code" class="fs-6 fw-bold">Pay rate short code (4 characters max)</label>
                                        <div class="input-group">
                                            <input type="text" name="add_extra_pay_rate_map_short_code" id="add_extra_pay_rate_map_short_code" class="form-control" placeholder="Enter pay rate short code" />
                                        </div>
                                        <span class="text-gray-400">This is used to ID the pay rate on the pay matrix</span><br>
                                        <span class="text-danger error" id="add_extra_pay_rate_map_short_code_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="pay_rate_short_code" class="fs-6 fw-bold">Colour</label>
                                        <div class="color-options">
                                            <label>
                                                <input type="radio" name="add_extra_pay_rate_map_bg_color" value="red" checked>
                                                <span class="color-circle red"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="add_extra_pay_rate_map_bg_color" value="pink">
                                                <span class="color-circle pink"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="add_extra_pay_rate_map_bg_color" value="orange">
                                                <span class="color-circle orange"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="add_extra_pay_rate_map_bg_color" value="yellow">
                                                <span class="color-circle yellow"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="add_extra_pay_rate_map_bg_color" value="peach">
                                                <span class="color-circle peach"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="add_extra_pay_rate_map_bg_color" value="lemon">
                                                <span class="color-circle lemon"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="add_extra_pay_rate_map_bg_color" value="green">
                                                <span class="color-circle green"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="add_extra_pay_rate_map_bg_color" value="blue">
                                                <span class="color-circle blue"></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="add_extra_pay_rate_map_bg_color" value="purple">
                                                <span class="color-circle purple"></span>
                                            </label>
                                        </div>
                                        <span class="text-danger error" id="add_extra_pay_rate_map_bg_color_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="add_extra_pay_rate_map_per_hour" class="fs-6 fw-bold">Pay</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input class="form-control" name="add_extra_pay_rate_map_per_hour" id="add_extra_pay_rate_map_per_hour" type="text" placeholder="pay">
                                            <div class="input-group-prepend"><span class="input-group-text" style="font-size: 16px">hr</span></div>
                                        </div>
                                        <span class="text-danger error" id="add_extra_pay_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="add_extra_charge_rate_map_per_hour" class="fs-6 fw-bold">Charge</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="add_extra_charge_rate_map_per_hour" id="add_extra_charge_rate_map_per_hour" class="form-control" placeholder="charge"  />
                                            <div class="input-group-prepend"><span class="input-group-text" style="font-size: 16px">hr</span></div>
                                        </div>
                                        <span class="text-danger error" id="add_extra_charge_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="add_extra_overtime_pay_rate_map_per_hour" class="fs-6 fw-bold">Overtime Pay (optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="add_extra_overtime_pay_rate_map_per_hour" id="add_extra_overtime_pay_rate_map_per_hour" class="form-control" placeholder="overtime pay"  />
                                            <div class="input-group-prepend"><span class="input-group-text" style="font-size: 16px">hr</span></div>

                                        </div>
                                        <span class="text-danger error" id="add_extra_overtime_pay_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="add_extra_overtime_charge_rate_map_per_hour" class="fs-6 fw-bold">Overtime Charge (optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="add_extra_overtime_charge_rate_map_per_hour" id="add_extra_overtime_charge_rate_map_per_hour" class="form-control" placeholder="overtime charge"  />
                                            <div class="input-group-prepend"><span class="input-group-text" style="font-size: 16px">hr</span></div>
                                        </div>
                                        <span class="text-danger error" id="add_extra_overtime_charge_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center !important;">
                    <input type="hidden" name="add_extra_pay_rate_map_job_id" id="add_extra_pay_rate_map_job_id" value="{{ $job['id'] }}" />
                    <input type="hidden" name="add_extra_pay_rate_map_default_pay_rate_id" id="add_extra_pay_rate_map_default_pay_rate_id" value="{{ $pay_rate_map['id'] }}" />
                    <input type="hidden" name="add_extra_pay_rate_map_type" id="add_extra_pay_rate_map_type" value="{{ $type }}" />
                    <button type="submit" name="add_extra_pay_rate_map_form_submit_btn" id="add_extra_pay_rate_map_form_submit_btn" class="btn btn-primary btn-lg">Create pay rate</button>
                    <button type="button" class="btn btn-lg btn-primary me-3 disabled d-none" data-kt-stepper-action="submit" name="add_extra_pay_rate_map_form_process_btn" id="add_extra_pay_rate_map_form_process_btn">
                        <span>Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('add_extra_pay_rate_map_modal_js')
    <script>
        $("#add_extra_pay_rate_map_btn").on('click', function () {
            $("#add_extra_pay_rate_map_modal").modal('show');
        });

        $("#add_extra_pay_rate_map_modal_close_btn").on('click', function () {
            $("#add_extra_pay_rate_map_form").trigger('reset');
            $("#add_extra_pay_rate_map_modal").modal('hide');
        });

        $("#add_extra_pay_rate_map_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#add_extra_pay_rate_map_form_submit_btn").addClass('d-none');
            $("#add_extra_pay_rate_map_form_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('add-extra-pay-rate-map-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#add_extra_pay_rate_map_form_submit_btn").removeClass('d-none');
                    $("#add_extra_pay_rate_map_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#add_extra_pay_rate_map_modal_close_btn").click();
                        const updated = response.data.extra_pay_rate_details;
                        const newHtml = `
                            <div class="lozenge lozenge-${updated.color}" id="lozenge_${updated.id}">
                                <div class="lozenge-checkbox">
                                    <input type="checkbox" class="rate-check" name="selected_extra_pay_rate_map[]"
                                           id="selected_extra_pay_rate_map_${updated.id}" value="${updated.id}"
                                           data-color="${updated.color}"
                                           data-short_code="${updated.pay_rate_short_code}"
                                           data-extra_prm_id="${updated.id}" />
                                </div>
                                <div class="lozenge-rate-details">
                                    <div class="lozenge-rate-name">${updated.pay_rate_name} (${updated.pay_rate_short_code})</div>
                                    <div class="lozenge-rate-amounts">
                                        P: £${parseFloat(updated.base_pay_rate).toFixed(2)}
                                        C: £${parseFloat(updated.base_charge_rate).toFixed(2)}
                                    </div>
                                </div>
                                <div class="lozenge-actions">
                                    <a href="javascript:;">
                                        <i class="las la-edit text-white" id="edit_extra_pay_rate_map_btn" data-id="${updated.id}"></i>
                                    </a>
                                    <a href="javascript:;">
                                        <i class="las la-trash-alt text-white" id="delete_extra_pay_rate_map_btn" data-id="${updated.id}"
                                           data-title="${updated.pay_rate_name} (${updated.pay_rate_short_code})"></i>
                                    </a>
                                </div>
                            </div>
                        `;
                        $('#add_extra_pay_rate_map_btn').before(newHtml);
                        $(".mapped_save_alert_section").removeClass('d-none');
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#add_extra_pay_rate_map_form_submit_btn").removeClass('d-none');
                    $("#add_extra_pay_rate_map_form_process_btn").addClass('d-none');
                }
            });
        });
    </script>
@endsection