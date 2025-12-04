<div class="tab-pane fade" id="kt_table_widget_5_tab_12">
    <div class="table-responsive">
        <form id="bank_details_form">
            @csrf
            <div class="p-5">
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">BANK DETAILS</div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <div class="d-flex align-items-center" id="site_checkbox_section">
                                <label class="form-check form-check-inline me-5 is-invalid">
                                    <input type="checkbox" class="form-check-input" name="requires_bank_account_setup" id="requires_bank_account_setup" value="Yes" {{ old('proforce_to_open_bank_account', $worker['proforce_to_open_bank_account']) == 'Yes' ? 'checked' : 'No' }}>
                                    <span class="fw-bold ps-2 fs-6">Worker requires Pro-Force to set up a bank account for them</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Bank account name</label>
                            <input type="text" name="bank_account_name" id="bank_account_name" class="form-control" placeholder="Bank account name" value="{{ $worker['bank_account_name'] }}" />
                            <span class="error text-danger" id="bank_account_name_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Bank name</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Bank name" value="{{ $worker['bank_name'] }}" />
                            <span class="error text-danger" id="bank_name_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Bank Sort Code</label>
                            <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control" placeholder="Bank Sort Code" value="{{ $worker['bank_ifsc_code'] }}" minlength="8" maxlength="8" />
                            <span class="error text-danger" id="bank_ifsc_code_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Bank account number</label>
                            <input type="text" name="bank_account_number" id="bank_account_number" class="form-control" placeholder="Bank account number" value="{{ $worker['bank_account_number'] }}" minlength="8" maxlength="8" />
                            <span class="error text-danger" id="bank_account_number_error"></span>
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">OTHER FINANCIAL DATA</div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Tax Treatment</label>
                            <select name="tax_statement" id="tax_statement" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                               <option value=""></option>
                                <option value="first_job"  {{ old('tax_treatment', $worker['tax_treatment']) == 'first_job' ? 'selected' : '' }}>First job</option>
                                <option value="subsequent_job"  {{ old('tax_treatment', $worker['tax_treatment']) == 'subsequent_job' ? 'selected' : '' }}>Subsequent job</option>
                                <option value="multiple_jobs"  {{ old('tax_treatment', $worker['tax_treatment']) == 'multiple_jobs' ? 'selected' : '' }}>Multiple jobs</option>
                                <option value="concurrent_job"  {{ old('tax_treatment', $worker['tax_treatment']) == 'concurrent_job' ? 'selected' : '' }}>Concurrent job</option>
                            </select>
                            <span class="error text-danger" id="tax_statement_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">48 hour opt out</label>
                            <select name="opt_out_48_hour_week" id="opt_out_48_hour_week" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                <option value=""></option>
                                <option value="Yes" {{ old('48_hour_opt_out', $worker['48_hour_opt_out']) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('48_hour_opt_out', $worker['48_hour_opt_out']) == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <span class="error text-danger" id="opt_out_48_hour_week_error"></span>
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <input type="hidden" name="update_bank_details_id" id="update_bank_details_id" value="{{ $worker['id'] }}">
                        <button type="submit" name="bank_details_submit" id="bank_details_submit" class="btn btn-primary btn-lg">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@section('edit_worker_bank_js')
    <script>
        $("#bank_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-worker-bank-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);
                    if(response.code === 200)
                        setTimeout(function() { location.reload(); }, 1500);
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        bank_ifsc_code.addEventListener('keyup',function (e) {
            console.log(e.keyCode);
            if (e.keyCode !== 6) {
                if (this.value.length === 2 || this.value.length === 5) {
                    this.value = this.value += '-';
                }
            }
        });
    </script>
@endsection
