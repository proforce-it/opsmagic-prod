<div class="modal fade" id="copy_job_shift_future_week_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Copy confirmed bookings to future a week</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_copy_job_shift_future_week_modal">
                    <i class="las la-times fs-2"></i>
                </div>
            </div>
            <div class="modal-body">
                <div class="mb-10 alert alert-custom alert-warning" role="alert">
                    <div class="alert-text fs-4">
                        <i class="las la-exclamation-triangle text-warning fs-xl-2"></i>
                        <strong>Please note:</strong> This will copy <strong>‘confirmed'</strong> associates booked
                        on shifts this week to corresponding shifts on the week
                        selected below. It will only do so where the associate is
                        available on that day
                    </div>
                </div>
                <div class="fv-row">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="copy_week_commencing_date" class="fs-6 fw-bold">Copy w/c</label>
                                <div class="position-relative d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                            <i class="fs-2 las la-calendar"></i>
                                        </span>
                                    <input class="form-control ps-12 flatpickr-input" placeholder="Select date" name="copy_week_commencing_date" id="copy_week_commencing_date" value="">
                                </div>
                                <span class="text-danger error" id="date_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-center">
                <div class="fv-row">
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" name="copy_job_shift_future_week_form_submit_btn" id="copy_job_shift_future_week_form_submit_btn" class="btn btn-primary">Copy bookings</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="copy_job_shift_future_week_form_process_btn" id="copy_job_shift_future_week_form_process_btn">
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
</div>
@section('copy_job_shift_future_week_js')
    <script>
        let copy_week_commencing_date = $("#copy_week_commencing_date");
        let week_start_day_name = '{{ $job['client_details']['payroll_week_starts'] }}';
        const dayNameToNumber = {
            "sunday": 0,
            "monday": 1,
            "tuesday": 2,
            "wednesday": 3,
            "thursday": 4,
            "friday": 5,
            "saturday": 6
        };

        const flatpickrInstance = copy_week_commencing_date.flatpickr({
            dateFormat  : "d-m-Y",
            minDate: "today",
            disableMobile : true,

            enable: [
                function(date) {
                    return (date.getDay() === dayNameToNumber[week_start_day_name]);
                }
            ]
        });

        /*--- BEGIN COPY JOB SHIFT ---*/
        $("#copy_job_shift_future_week_btn").on('click', function () {
            $(".error").html('');
            $("#copy_week_commencing_date").val('');
            flatpickrInstance.clear();
            $("#copy_job_shift_future_week_modal").modal('show');
        });

        $("#cls_btn_copy_job_shift_future_week_modal").on('click', function () {
            $("#copy_week_commencing_date").val('');
            flatpickrInstance.clear();
            $("#copy_job_shift_future_week_modal").modal('hide');
        })

        $("#copy_job_shift_future_week_form_submit_btn").on('click', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#copy_job_shift_future_week_form_submit_btn").addClass('d-none');
            $("#copy_job_shift_future_week_form_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('copy-job-shift-in-worker-availability') }}',
                data        : {
                    _token : '{{ csrf_token() }}',
                    job_id : '{{$job['id']}}',
                    week_number : wa_week_number,
                    week_year : wa_week_year,
                    payroll_week_starts :week_start_day_name,
                    date : $("#copy_week_commencing_date").val(),
                },
                success     : function (response) {
                    decodeResponse(response)

                    $("#copy_job_shift_future_week_form_submit_btn").removeClass('d-none');
                    $("#copy_job_shift_future_week_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#cls_btn_copy_job_shift_modal").click();
                        setTimeout(function (){
                            location.reload()
                        },1500);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#copy_job_shift_future_week_form_submit_btn").addClass('d-none');
                    $("#copy_job_shift_future_week_form_process_btn").removeClass('d-none');
                }
            });
        });

    </script>
@endsection
