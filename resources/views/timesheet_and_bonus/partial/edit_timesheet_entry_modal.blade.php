<div class="modal fade" id="edit_timesheet_entry_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Edit entry for <span id="worker_name_and_date"></span></h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_edit_timesheet_entry_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                </div>
            </div>
            <form id="edit_timesheet_entry_form">
                @csrf
                <div class="modal-body scroll-y m-5">
                    <div class="fv-row row">
                        <div class="col-lg-12">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="timesheet_start_time" class="fs-6 fw-bold">Start time</label>
                                <div class="position-relative d-flex align-items-center">
                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                        <i class="fs-2 las la-clock"></i>
                                    </span>
                                    <input class="form-control ps-12 flatpickr-input time_input" placeholder="Select timesheet start time" name="timesheet_start_time" id="timesheet_start_time" type="text" readonly="readonly" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="fv-row fv-plugins-icon-container">
                                <label for="hours_worked" class="fs-6 fw-bold required">Hours worked</label>
                                <input class="form-control" name="hours_worked" id="hours_worked" type="text" value="0" placeholder="Enter hours worked">
                                <input name="timesheet_id" id="timesheet_id" type="hidden" value="0">
                                <span class="text-danger error" id="hours_worked_error"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <input class="form-control" name="job_id" id="job_id" type="hidden" value="">
                                <button type="submit" name="update_timesheet_entry_submit_btn" id="update_timesheet_entry_submit_btn" class="btn btn-primary float-end">Update timesheet entry</button>
                                <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" name="update_timesheet_entry_process_btn" id="update_timesheet_entry_process_btn">
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

@section('edit_timesheet_entry_js')
    <script>
        $("#timesheet_start_time").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });

        $(document).on('click', '#edit_timesheet', function () {
            $("#worker_name_and_date").text($(this).attr('data-worker'));
            $("#hours_worked").val($(this).attr('data-hours'));
            $("#timesheet_start_time").val($(this).attr('data-start_time'));
            $("#timesheet_id").val($(this).attr('data-id'));
            $("#edit_timesheet_entry_modal").modal('show');
        });

        $("#cls_btn_edit_timesheet_entry_modal").on('click', function (){
            $("#worker_name_and_date").text('');
            $("#edit_timesheet_entry_form").trigger('reset');
            $("#edit_timesheet_entry_modal").modal('hide');
        });

        $("#edit_timesheet_entry_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#update_timesheet_entry_submit_btn").addClass('d-none');
            $("#update_timesheet_entry_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('edit-timesheet-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);

                    $("#update_timesheet_entry_submit_btn").removeClass('d-none');
                    $("#update_timesheet_entry_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#worker_name_and_date").text('');
                        $("#edit_timesheet_entry_form").trigger('reset');
                        $("#edit_timesheet_entry_modal").modal('hide');
                        $("#filter_btn").click()
                        $('#kt_table_widget_5_tab_6_menu').click();
                        $('#timesheet_button').click();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#update_timesheet_entry_submit_btn").removeClass('d-none');
                    $("#update_timesheet_entry_process_btn").addClass('d-none');
                }
            });
        });
    </script>
@endsection