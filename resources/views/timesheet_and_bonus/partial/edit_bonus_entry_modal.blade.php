<div class="modal fade" id="edit_bonus_entry_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Edit entry for <span id="worker_name_and_date"></span></h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_edit_bonus_entry_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                </div>
            </div>
            <form id="edit_bonus_entry_form">
                @csrf
                <div class="modal-body scroll-y m-5">
                    <div class="fv-row row">
                        <div class="col-lg-12">
                            <div class="fv-row fv-plugins-icon-container">
                                <label for="bonus_type" class="fs-6 fw-bold required">Bonus type</label>
                                <select name="bonus_type" id="bonus_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                    <option value="">Select...</option>
                                    <option value="attendance_bonus">Attendance bonus</option>
                                    <option value="production_bonus">Production bonus</option>
                                    <option value="one_off_bonus">One off bonus</option>
                                    <option value="loyalty_bonus">Loyalty bonus</option>
                                    <option value="weekend_bonus">Weekend bonus</option>
                                    <option value="referral_bonus">Referral bonus</option>
                                    <option value="other_bonus">Other bonus</option>
                                </select>
                                <span class="text-danger error" id="bonus_type_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="fv-row mt-10 fv-plugins-icon-container">
                                <label for="bonus_amount" class="fs-6 fw-bold required">Bonus amount</label>
                                <input class="form-control" name="bonus_amount" id="bonus_amount" type="text" value="0" placeholder="Enter bonus amount">
                                <input name="bonus_id" id="bonus_id" type="hidden" value="0">
                                <span class="text-danger error" id="bonus_amount_error"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" name="update_bonus_entry_submit_btn" id="update_bonus_entry_submit_btn" class="btn btn-primary float-end">Update bonus entry</button>
                                <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" name="update_bonus_entry_process_btn" id="update_bonus_entry_process_btn">
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

@section('edit_bonus_entry_js')
    <script>
        $(document).on('click', '#edit_bonus', function () {
            $("#worker_name_and_date").text($(this).attr('data-worker'));
            $("#bonus_type").val($(this).attr('data-bonus_type')).trigger('change');
            $("#bonus_amount").val($(this).attr('data-bonus_amount'));
            $("#bonus_id").val($(this).attr('data-id'));
            $("#edit_bonus_entry_modal").modal('show');
        });

        $("#cls_btn_edit_bonus_entry_modal").on('click', function (){
            $("#worker_name_and_date").text('');
            $("#edit_bonus_entry_form").trigger('reset');
            $("#edit_bonus_entry_modal").modal('hide');
        });

        $("#edit_bonus_entry_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#update_bonus_entry_submit_btn").addClass('d-none');
            $("#update_bonus_entry_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('edit-bonus-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);

                    $("#update_bonus_entry_submit_btn").removeClass('d-none');
                    $("#update_bonus_entry_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#worker_name_and_date").text('');
                        $("#edit_bonus_entry_form").trigger('reset');
                        $("#edit_bonus_entry_modal").modal('hide');
                        $("#filter_btn").click()
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#update_bonus_entry_submit_btn").removeClass('d-none');
                    $("#update_bonus_entry_process_btn").addClass('d-none');
                }
            });
        });
    </script>
@endsection