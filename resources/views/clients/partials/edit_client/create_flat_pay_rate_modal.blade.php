<div class="modal fade" id="flat_pay_rate_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Create flat pay rate</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="flat_pay_rate_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="create_flat_pay_rate_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="base_pay_rate_per_hour" class="fs-6 fw-bold">Base pay rate per hour
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="base_pay_rate_per_hour" id="base_pay_rate_per_hour" class="form-control" placeholder="Enter base pay rate per hour" value="" />
                                        </div>
                                        <span class="text-danger error" id="base_pay_rate_per_hour_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="base_charge_rate_per_hour" class="fs-6 fw-bold">Base charge rate per hour</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="base_charge_rate_per_hour" id="base_charge_rate_per_hour" class="form-control" placeholder="Enter base charge rate per hour" value="" />
                                        </div>
                                        <span class="text-danger error" id="base_charge_rate_per_hour_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="overtime_pay_rate_per_hour" class="fs-6 fw-bold">Overtime pay rate per hour (optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="overtime_pay_rate_per_hour" id="overtime_pay_rate_per_hour" class="form-control" placeholder="Enter overtime pay rate per hour" />
                                        </div>
                                        <span class="text-danger error" id="overtime_pay_rate_per_hour_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="overtime_charge_rate_per_hour" class="fs-6 fw-bold">Overtime charge rate per hour (optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="overtime_charge_rate_per_hour" id="overtime_charge_rate_per_hour" class="form-control" placeholder="Enter overtime charge rate per hour"  />
                                        </div>
                                        <span class="text-danger error" id="overtime_charge_rate_per_hour_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="overtime_paid_after" class="fs-6 fw-bold">Overtime paid after</label>
                                        <div class="input-group">
                                            <input type="text" name="overtime_paid_after" id="overtime_paid_after" class="form-control" placeholder="Overtime paid after"  />
                                        </div>
                                        <span class="text-danger error" id="overtime_paid_after_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="weekly_ot_threshold" class="fs-6 fw-bold"></label>
                                        <select name="overtime_type" id="overtime_type" class="form-select">
                                            <option value="hours_per_week" selected>Hours per week</option>
                                            <option value="hours_per_day">Hours per day</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="pay_rate_valid_from" class="fs-6 fw-bold">pay rate valid from</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <i class="fs-2 las la-calendar"></i>
                                            </span>
                                            <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select pay rate date" name="pay_rate_valid_from" id="pay_rate_valid_from" type="text" readonly="readonly" value="">
                                        </div>
                                        <span class="text-danger error" id="pay_rate_valid_from_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center !important;">
                    <input type="hidden" name="pay_rate_job_id" id="pay_rate_job_id" value="{{ $job['id'] }}" />
                    <button type="submit" name="flat_pay_rate_form_submit_btn" id="flat_pay_rate_form_submit_btn" class="btn btn-primary btn-lg">Next</button>
                    <button type="button" class="btn btn-lg btn-primary me-3 disabled d-none" data-kt-stepper-action="submit" name="flat_pay_rate_form_process_btn" id="flat_pay_rate_form_process_btn">
                        <span>Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('flat_pay_rate_js')
    <script>
        const minDateFromController = new Date("{{ $minDate }}");
        $("#pay_rate_valid_from").flatpickr({
            dateFormat  : "d-m-Y",
            minDate     : minDateFromController //new Date().fp_incr(1)
        });

        $("#flat_pay_rate_modal_close_btn").on('click', function (){
            $("#create_flat_pay_rate_form").trigger('reset');
            $("#flat_pay_rate_modal").modal('hide');
        });

        $("#create_flat_pay_rate_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#flat_pay_rate_form_submit_btn").addClass('d-none');
            $("#flat_pay_rate_form_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('create-flat-pay-rate-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#flat_pay_rate_form_submit_btn").removeClass('d-none');
                    $("#flat_pay_rate_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                        $("#flat_pay_rate_modal_close_btn").click();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#flat_pay_rate_form_submit_btn").removeClass('d-none');
                    $("#flat_pay_rate_form_process_btn").addClass('d-none');
                }
            });
        });
    </script>
@endsection

