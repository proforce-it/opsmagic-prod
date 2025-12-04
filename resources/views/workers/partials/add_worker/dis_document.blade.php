<div class="d-none" id="document_tab">
    <!--begin::Table container-->
    <div class="table-responsive">
        <div class="p-5">
            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">ID DOCUMENT</div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Document type</label>
                        <select name="id_document_type" id="id_document_type" data-section="1" class="form-select form-select-lg rtw_drp" data-placeholder="Select right to work" data-allow-clear="true">
                            <option value="">Select...</option>
                            <option value="Passport">Passport</option>
                            <option value="ID card">ID card</option>
                        </select>
                        <span class="text-danger error" id="id_document_type_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Document number</label>
                        <input type="text" name="id_document_number" id="id_document_number" class="form-control" value="">
                        <span class="error text-danger" id="id_document_number_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Expiry date</label>
                        <div class="position-relative d-flex align-items-center">
                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                <i class="fs-2 las la-calendar"></i>
                            </span>
                            <input class="form-control ps-12 flatpickr-input date_input" name="id_document_expiry_date" id="id_document_expiry_date" type="text" readonly="readonly" value="">
                        </div>
                        <span class="error text-danger" id="id_document_expiry_date_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Document scan</label>
                        <input type="file" name="id_document_file" id="id_document_file" class="form-control"  accept="image/jpg, image/png, image/jpeg, application/pdf"/>
                        <span class="text-danger error" id="id_document_file_error"></span>
                        <label class="fw-bold text-muted">PNG, JPG, JPEG or PDF (Max. 10MB)</label>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4 required">REGISTRATION PACK</div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <input type="file" name="registration_file" id="registration_file" class="form-control"  accept="image/jpg, image/png, image/jpeg, application/pdf"/>
                        <span class="text-danger error" id="registration_file_error"></span>
                        <label class="fw-bold text-muted">PNG, JPG, JPEG or PDF (Max. 10MB)</label>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <button type="submit" name="add_worker_submit_btn" id="add_worker_submit_btn" class="btn btn-primary btn-lg">Add worker</button>
                    <button type="button" class="btn btn-lg btn-primary disabled d-none" data-kt-stepper-action="submit" name="add_worker_form_process" id="add_worker_form_process">
                        <span>Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Table-->
</div>

@section('add_worker_document_js')
    <script>
        $("#id_document_expiry_date").flatpickr({
            dateFormat  : "d-m-Y",
        });
    </script>
@endsection
