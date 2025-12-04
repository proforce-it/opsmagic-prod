<div class="modal fade" id="pay_rate_map_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Pay map step 1: Default pay rate details</h2>

                <div class="btn btn-sm btn-icon btn-active-color-primary" id="pay_rate_map_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="create_pay_rate_map_form">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-custom alert-warning mb-15" role="alert">
                        <div class="alert-text">
                            When you first create a pay map, the rates for all days and times will be set to the ‘default pay rate’ you define on this
                            screen. On the next screen, you can define as many separate pay rates as you need.
                        </div>
                    </div>
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="default_pay_rate_map_per_hour" class="fs-6 fw-bold">Default pay rate per hour</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="default_pay_rate_map_per_hour" id="default_pay_rate_map_per_hour" class="form-control" placeholder="Enter default pay rate per hour" value="" />
                                        </div>
                                        <span class="text-danger error" id="default_pay_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="default_charge_rate_map_per_hour" class="fs-6 fw-bold">Default charge rate per hour</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="default_charge_rate_map_per_hour" id="default_charge_rate_map_per_hour" class="form-control" placeholder="Enter default charge rate per hour" value="" />
                                        </div>
                                        <span class="text-danger error" id="default_charge_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="default_overtime_pay_rate_map_per_hour" class="fs-6 fw-bold">Default overtime pay rate per hour (optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="default_overtime_pay_rate_map_per_hour" id="default_overtime_pay_rate_map_per_hour" class="form-control" placeholder="Enter default overtime pay rate per hour" />
                                        </div>
                                        <span class="text-danger error" id="default_overtime_pay_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="default_overtime_charge_rate_map_per_hour" class="fs-6 fw-bold">Default overtime charge rate per hour (optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="default_overtime_charge_rate_map_per_hour" id="default_overtime_charge_rate_map_per_hour" class="form-control" placeholder="Enter default overtime charge rate per hour"  />
                                        </div>
                                        <span class="text-danger error" id="default_overtime_charge_rate_map_per_hour_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="map_overtime_paid_after" class="fs-6 fw-bold">Overtime paid after (optional)</label>
                                        <div class="input-group">
                                            <input type="text" name="map_overtime_paid_after" id="map_overtime_paid_after" class="form-control" placeholder="Overtime paid after"  />
                                        </div>
                                        <span class="text-danger error" id="map_overtime_paid_after_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="map_overtime_type" class="fs-6 fw-bold"></label>
                                        <select name="map_overtime_type" id="map_overtime_type" class="form-select">
                                            <option value="hours_per_week" selected>Hours per week</option>
                                            <option value="hours_per_day">Hours per day</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center !important;">
                    <input type="hidden" name="pay_rate_map_job_id" id="pay_map_job_id" value="{{ $job['id'] }}" />
                    <button type="submit" name="pay_rate_map_form_submit_btn" id="pay_rate_map_form_submit_btn" class="btn btn-primary btn-lg">Next</button>
                    <button type="button" class="btn btn-lg btn-primary me-3 disabled d-none" data-kt-stepper-action="submit" name="pay_rate_map_form_process_btn" id="pay_rate_map_form_process_btn">
                        <span>Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('pay_rate_map_js')
    <script>
        $("#pay_rate_map_modal_close_btn").on('click', function (){
            $("#create_pay_rate_map_form").trigger('reset');
            $("#pay_rate_map_modal").modal('hide');
        });

       $("#create_pay_rate_map_form").on('submit', function (e) {
           $(".error").html('');
           e.preventDefault();

            $("#pay_rate_map_form_submit_btn").addClass('d-none');
            $("#pay_rate_map_form_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('create-pay-rate-map-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#pay_rate_map_form_submit_btn").removeClass('d-none');
                    $("#pay_rate_map_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        setTimeout(function () {
                            location.href = '{{ url('pay-rate-map-step-2') }}'+'/'+response.data.id;
                        }, 1500);
                        $("#pay_rate_map_modal_close_btn").click();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#pay_rate_map_form_submit_btn").removeClass('d-none');
                    $("#pay_rate_map_form_process_btn").addClass('d-none');
                }
            });
       });
    </script>
@endsection

