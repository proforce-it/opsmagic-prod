<div class="modal fade" id="copy_job_shift_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Copy confirmed shift bookings</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_copy_job_shift_modal">
                    <i class="las la-times fs-2"></i>
                </div>
            </div>
            <form id="copy_job_shift_form">
                @csrf
                <div class="modal-body">
                    <div class="mb-10 alert alert-custom alert-warning" role="alert">
                        <div class="alert-text fs-4">
                            <i class="las la-exclamation-triangle text-warning fs-xl-2"></i>
                            <strong>Please note:</strong> This will copy ‘confirmed’ associates booked
                            for the current shift to the date(s) selected below. It will
                            only do so where the associate is available (i.e. it will not
                            overwrite any pre-exisiting bookings)
                        </div>
                    </div>
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="shift_start_date" class="fs-6 fw-bold required">Start date</label>
                                    <div class="position-relative d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <i class="fs-2 las la-calendar"></i>
                                            </span>
                                        <input class="form-control ps-12 flatpickr-input" placeholder="Select date" name="shift_start_date" id="shift_start_date" readonly="readonly" value="">
                                    </div>
                                    <span class="text-danger error" id="shift_start_date_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="fv-row fv-plugins-icon-container">
                                    <label for="shift_end_date" class="fs-6 fw-bold">End date (optional)</label>
                                    <div class="position-relative d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <i class="fs-2 las la-calendar"></i>
                                            </span>
                                        <input class="form-control ps-12 flatpickr-input" placeholder="Select date" name="shift_end_date" id="shift_end_date" readonly="readonly" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-center">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <input class="form-control" name="copy_job_shift_id" id="copy_job_shift_id" type="hidden" value="{{$shift['id']}}">
                                <button type="submit" name="copy_job_shift_form_submit_btn" id="copy_job_shift_form_submit_btn" class="btn btn-primary">Copy bookings</button>
                                <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="copy_job_shift_form_process_btn" id="copy_job_shift_form_process_btn">
                                        <span>Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@section('copy_job_shift_js')
    <script>
        let endDateFP;

        const shiftStartFP  = $("#shift_start_date").flatpickr({
            dateFormat  : "d-m-Y",
            minDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                const end_date_box = $("#shift_end_date");
                end_date_box.val('');

                if (!selectedDates.length) return;

                let newDate = selectedDates[0];
                let currentMonth = newDate.getMonth();
                let currentDate = newDate.getDate();
                let currentYear = newDate.getFullYear();

                end_date_box.prop('disabled', false);
                endDateFP = end_date_box.flatpickr({
                    minDate: new Date(currentYear, currentMonth, currentDate + 1),
                    dateFormat: "d-m-Y",
                });
            }
        });

        /*--- BEGIN COPY JOB SHIFT ---*/
        $("#copy_job_shift_modal_btn").on('click', function () {
            $(".error").html('');
            shiftStartFP.clear();
            if (endDateFP) endDateFP.clear();
            $("#copy_job_shift_form").trigger('reset');
            $("#copy_job_shift_modal").modal('show');
        });

        $("#cls_btn_copy_job_shift_modal").on('click', function () {
            shiftStartFP.clear();
            if (endDateFP) endDateFP.clear();

            $("#copy_job_shift_form").trigger('reset');
            $("#copy_job_shift_modal").modal('hide');
        })

        $("#copy_job_shift_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#copy_job_shift_form_submit_btn").addClass('d-none');
            $("#copy_job_shift_form_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('copy-job-shift') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#copy_job_shift_form_submit_btn").removeClass('d-none');
                    $("#copy_job_shift_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#cls_btn_copy_job_shift_modal").click();
                        setTimeout(function (){
                            location.reload()
                        },1500);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#copy_job_shift_form_submit_btn").addClass('d-none');
                    $("#copy_job_shift_form_process_btn").removeClass('d-none');
                }
            });
        });

    </script>
@endsection
