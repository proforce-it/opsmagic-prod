<div class="modal fade" id="edit_pay_rate_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 id="edit_flat_pay_rate_modal_title">Edit Pay rate</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary edit_pay_rate_modal_close_btn" id="edit_pay_rate_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
                @php
                    $lastItem = $job['pay_rate_multiple']->first();
                @endphp
            </div>
            <form id="edit_pay_rate_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_base_pay_rate_per_hour" class="fs-6 fw-bold">Base pay rate per hour
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="edit_base_pay_rate_per_hour" id="edit_base_pay_rate_per_hour" class="form-control" placeholder="Enter base pay rate per hour" value="{{ $lastItem['base_pay_rate'] ?? '' }}" />
                                        </div>
                                        <span class="text-danger error" id="edit_base_pay_rate_per_hour_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_base_charge_rate_per_hour" class="fs-6 fw-bold">Base charge rate per hour</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="edit_base_charge_rate_per_hour" id="edit_base_charge_rate_per_hour" class="form-control" placeholder="Enter base charge rate per hour" value="{{ $lastItem['base_charge_rate'] ?? '' }}" />
                                        </div>
                                        <span class="text-danger error" id="edit_base_charge_rate_per_hour_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_overtime_pay_rate_per_hour" class="fs-6 fw-bold">Overtime pay rate per hour (optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="edit_overtime_pay_rate_per_hour" id="edit_overtime_pay_rate_per_hour" class="form-control" placeholder="Enter overtime pay rate per hour" value="{{ $lastItem['default_overtime_pay_rate'] ?? '' }}" />
                                        </div>
                                        <span class="text-danger error" id="edit_overtime_pay_rate_per_hour_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_overtime_charge_rate_per_hour" class="fs-6 fw-bold">Overtime charge rate per hour (optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="las la-pound-sign" style="font-size: 24px"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="edit_overtime_charge_rate_per_hour" id="edit_overtime_charge_rate_per_hour" class="form-control" placeholder="Enter overtime charge rate per hour" value="{{ $lastItem['default_overtime_charge_rate'] ?? '' }}" />
                                        </div>
                                        <span class="text-danger error" id="edit_overtime_charge_rate_per_hour_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_overtime_paid_after" class="fs-6 fw-bold">Overtime paid after (optional)</label>
                                        <div class="input-group">
                                            <input type="text" name="edit_overtime_paid_after" id="edit_overtime_paid_after" class="form-control" placeholder="Overtime paid after" value="{{ $lastItem['default_overtime_hours_threshold'] ?? '' }}" />
                                        </div>
                                        <span class="text-danger error" id="edit_overtime_paid_after_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="edit_overtime_type" class="fs-6 fw-bold"></label>
                                        <select name="edit_overtime_type" id="edit_overtime_type" class="form-select">
                                            <option value="hours_per_week" {{ (isset($lastItem['overtime_type']) && $lastItem['overtime_type'] == 'hours_per_week') ? 'selected' : '' }}>Hours per week</option>
                                            <option value="hours_per_day" {{ (isset($lastItem['overtime_type']) && $lastItem['overtime_type'] == 'hours_per_day') ? 'selected' : '' }}>Hours per day</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="updated_pay_rate_valid_from" class="fs-6 fw-bold">Updated pay rate valid from</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <i class="fs-2 las la-calendar"></i>
                                            </span>
                                            <input class="form-control ps-12 flatpickr-input date_input pay_rate_date" placeholder="Select pay rate date" name="updated_pay_rate_valid_from" id="updated_pay_rate_valid_from" type="text" readonly="readonly" value="{{ (isset($lastItem['pay_rate_valid_from'])) ? date('d-m-Y', strtotime($lastItem['pay_rate_valid_from'])) : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center !important;">
                    <input type="hidden" name="edit_pay_rate_job_id" id="edit_pay_rate_job_id" value="{{ $job['id'] }}" />`
                    <input type="hidden" name="flat_pay_rate_update_id" id="flat_pay_rate_update_id" value="{{ $lastItem['id'] ?? '' }}" />
                    <input type="hidden" name="action_type" id="action_type" value="" />

                    <button type="button" name="edit_flat_pay_rate_cancel_btn" id="edit_flat_pay_rate_cancel_btn" class="btn btn-secondary btn-lg edit_pay_rate_modal_close_btn">Cancel</button>
                    <button type="submit" name="edit_flat_pay_rate_submit_btn" id="edit_flat_pay_rate_submit_btn" class="btn btn-primary btn-lg">Update</button>
                    <button type="button" class="btn btn-lg btn-primary me-3 disabled d-none" data-kt-stepper-action="submit" name="edit_flat_pay_rate_process_btn" id="edit_flat_pay_rate_process_btn">
                        <span>Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('edit_pay_rate_js')
    <script>
        /*let updated_pay_rate_valid_from = '{{ $job['pay_rate_details']['pay_rate_valid_from'] ?? '' }}';
        let baseDate = new Date(updated_pay_rate_valid_from);
        baseDate.setDate(baseDate.getDate() + 1);*/

        const baseDate = new Date("{{ $minDate }}");
        $(".pay_rate_date").flatpickr({
            dateFormat  : "d-m-Y",
            minDate     : baseDate
        });

        $(".make_changes_flat_rate_btn").on('click', function () {
            $(".error").html('');

            $("#edit_pay_rate_form").trigger('reset');
            $("#edit_flat_pay_rate_submit_btn").removeClass('d-none');
            $("#edit_flat_pay_rate_process_btn").addClass('d-none');

            let action_type = $(this).attr('data-type')
            $("#edit_flat_pay_rate_modal_title").text($(this).attr('data-modal_title'));
            $("#action_type").val(action_type)
            if (action_type === 'UpdateOrCreateUpcoming') {
                $("#updated_pay_rate_valid_from").val('');
            }

            $("#edit_pay_rate_modal").modal('show');
        });

        $(".edit_pay_rate_modal_close_btn").on('click', function () {
            $(".error").html('');
            $("#edit_pay_rate_form").trigger('reset');
            $("#edit_pay_rate_modal").modal('hide');
        })

        $("#edit_pay_rate_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#edit_flat_pay_rate_submit_btn").addClass('d-none');
            $("#edit_flat_pay_rate_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('edit-flat-pay-rate-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#edit_flat_pay_rate_submit_btn").removeClass('d-none');
                    $("#edit_flat_pay_rate_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                        $(".edit_pay_rate_modal_close_btn").click();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#edit_flat_pay_rate_submit_btn").removeClass('d-none');
                    $("#edit_flat_pay_rate_process_btn").addClass('d-none');
                }
            });
        });

        $(document).on('click', '#delete_upcoming_flat_pay_rate_btn', function () {
            let pri = $(this).attr('data-id')
            sweetAlertConfirmDelete('You want to delete this pay rate entry!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-flat-pay-rate-action') }}'+'/'+pri,
                        success : function (response) {
                            decodeResponse(response)

                            if(response.code === 200) {
                                $("#"+pri+"_row").remove();
                                setTimeout(function () {
                                    location.reload();
                                }, 1500)
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });
    </script>
@endsection

