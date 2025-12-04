<div class="modal fade" id="prm_pay_map_valid_from_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Set a 'valid from' date</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary prm_pay_map_valid_from_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="prm_calendar_save_form">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-custom alert-warning alert-msg-prm-valid" role="alert">
                        <div class="alert-text">
                            By default, your updated pay map will take effect for
                            shifts starting from 00:00 tomorrow. However, you can
                            also choose a future date for the new rates to come into
                            force
                        </div>
                    </div>
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="pay_map_valid_from_date" class="fs-6 fw-bold">Pay map valid from</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <i class="fs-2 las la-calendar"></i>
                                            </span>
                                            <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select pay map date" name="pay_map_valid_from_date" id="pay_map_valid_from_date" type="text" value="">
                                        </div>
                                        <span class="text-danger error" id="pay_map_valid_from_date_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center !important;">
                    <input type="hidden" name="default_pay_rate_id" id="default_pay_rate_id" value="{{ $pay_rate_map['id'] }}">
                    <button type="submit" name="prm_pay_map_valid_form_submit_btn" id="prm_pay_map_valid_form_submit_btn" class="btn btn-primary btn-lg">Finish</button>
                    <button type="button" class="btn btn-lg btn-primary me-3 disabled d-none" data-kt-stepper-action="submit" name="prm_pay_map_valid_form_process_btn" id="prm_pay_map_valid_form_process_btn">
                        <span>Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>