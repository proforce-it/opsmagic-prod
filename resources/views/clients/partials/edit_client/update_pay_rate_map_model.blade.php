<div class="modal fade" id="update_pay_rate_map_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Choose how to update your map</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="update_pay_rate_map_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <div class="modal-body">
                <div class="w-100">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label class="text-muted fs-6 fw-bold required">Label</label>
                                    <select name="update_pay_rate_map" id="update_pay_rate_map" class="form-select form-select-lg" data-control="select2" data-allow-clear="true" data-placeholder="Select...">
                                        <option value="" selected>Select...</option>
                                        <option value="start_from_current_rate_pay_windows">Start from current rates and pay windows</option>
                                        <option value="start_from_new_blank_map">Start from a new blank map </option>
                                    </select>
                                    <span class="text-danger error" id="update_pay_rate_map_error"></span>
                                    <input type="hidden" name="pay_rate_id" id="pay_rate_id" value="{{ ($job['pay_rate_details']) ? $job['pay_rate_details']['id'] : 0 }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="justify-content: center !important;">
                <button type="button" name="update_pay_rate_map_form_submit_btn" id="update_pay_rate_map_form_submit_btn" class="btn btn-primary btn-lg">Next</button>
            </div>
        </div>
    </div>
</div>
@section('choose_update_pay_rate_map_js')
    <script>
        $("#make_changes_pay_rate_map_btn").on('click', function () {
            $('#update_pay_rate_map').val('').trigger('change');
            $("#update_pay_rate_map_modal").modal('show');
        });

        $("#update_pay_rate_map_modal_close_btn").on('click', function () {
            $(".error").html('');
            $('#update_pay_rate_map').val('').trigger('change');
            $("#update_pay_rate_map_modal").modal('hide');
        })

        $("#update_pay_rate_map_form_submit_btn").on('click', function () {

            let update_pay_rate_map = $("#update_pay_rate_map").val();
            let payRateId = $('#pay_rate_id').val();

            $.ajax({
                type : 'post',
                url : '{{ url('create-temporary-upcoming-prm-entry') }}',
                data : {
                    _token : "{{ csrf_token() }}",
                    default_prm_id : payRateId,
                    type : update_pay_rate_map
                },
                success : function (response) {
                    if(response.code === 200) {
                        $("#update_pay_rate_map_modal").modal('hide');
                        window.location.href = '{{ url('create-upcoming-pay-rate-map') }}'+'/'+response.data.tmp_prm_id;
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                    }
                },
                error : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
