<div class="" id="basic_details_tab">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Name</div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Title</label>
                        <select name="title" id="title" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                            <option></option>
                            <option value="Mr">Mr</option>
                            <option value="Ms">Ms</option>
                            <option value="Mrs">Mrs</option>
                            <option value="Miss">Miss</option>
                        </select>
                        <span class="error text-danger" id="title_error"></span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">First name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" value="">
                        <span class="error text-danger" id="first_name_error"></span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name" class="form-control" value="">
                        <span class="error text-danger" id="middle_name_error"></span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Last name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" value="">
                        <span class="error text-danger" id="last_name_error"></span>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Reference number</div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Associate ID number</label>
                        <input type="text" name="worker_no" id="worker_no" class="form-control bg-secondary" placeholder="Worker no" value="{{ $work_number['worker_number'] }}" readonly />
                        <input type="hidden" name="worker_number_year" id="worker_number_year" value="{{ $work_number['worker_number_year'] }}" />
                        <input type="hidden" name="worker_number_month" id="worker_number_month" value="{{ $work_number['worker_number_month'] }}" />
                        <input type="hidden" name="worker_number_sequence" id="worker_number_sequence" value="{{ $work_number['worker_number_sequence'] }}" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6">Client reference no.</label> <!--Client reference-->
                        <input type="text" name="client_reference" id="client_reference" class="form-control" value="">
                        <span class="error text-danger" id="client_reference_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6">National Insurance Number</label>
                        <input type="text" name="national_insurance_number" id="national_insurance_number" maxlength="13" class="form-control" value="" />
                        <span class="error text-danger" id="national_insurance_number_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6">Payroll reference number</label>
                        <input type="text" name="payroll_reference" id="payroll_reference" class="form-control" value="">
                        <span class="error text-danger" id="payroll_reference_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Cost center</label>
                        <select name="cost_center" id="cost_center" class="form-select form-select-lg" data-control="select2" data-allow-clear="true" data-placeholder="Select...">
                            <option></option>
                            @if($costCentre)
                                @foreach($costCentre as $cc_row)
                                    <option value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="error text-danger" id="cost_center_error"></span>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Contact details</div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Mobile number</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="las la-mobile fs-xxl-2"></i>
                                </span>
                            </div>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="">
                        </div>
                        <span class="error text-danger" id="mobile_number_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Email address</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="las la-paper-plane fs-xxl-2"></i>
                                </span>
                            </div>
                            <input type="text" name="email_address" id="email_address" class="form-control" value="">
                        </div>
                        <span class="error text-danger" id="email_address_error"></span>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Demographic data</div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Date of birth</label>
                        <div class="position-relative d-flex align-items-center">
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <i class="fs-2 las la-calendar"></i>
                                </span>
                            <input class="form-control ps-12 flatpickr-input date_input" name="date_of_birth" id="date_of_birth" type="text" readonly="readonly" value="">
                        </div>
                        <span class="error text-danger" id="date_of_birth_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Gender</label>
                        <select name="gender" id="gender" class="form-select form-select-lg" data-control="select2" data-allow-clear="true" data-placeholder="Select...">
                            <option></option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <span class="error text-danger" id="gender_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Marital status</label>
                        <select name="marital_status" id="marital_status" class="form-select form-select-lg" data-control="select2" data-allow-clear="true" data-placeholder="Select...">
                            <option></option>
                            <option value="Married">Married</option>
                            <option value="Single">Single</option>
                            <option value="Co-habiting">Co-habiting</option>
                        </select>
                        <span class="error text-danger" id="marital_status_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6 required">Nationality</label>
                        <select name="nationality" id="nationality" class="form-select form-select-lg" data-control="select2" data-allow-clear="true" data-placeholder="Select...">
                            <option></option>
                            @if($nationality)
                                @foreach($nationality as $nRow)
                                    <option value="{{ $nRow['nationality'] }}">{{ $nRow['nationality'] }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="error text-danger" id="nationality_error"></span>
                    </div>
                </div>
<!--                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6">Partner name</label>
                        <input type="text" name="name_of_partner" id="name_of_partner" class="form-control" value="">
                        <span class="error text-danger" id="name_of_partner_error"></span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6">Partner worker no.</label>
                        <input type="text" name="id_number_of_partner" id="id_number_of_partner" class="form-control" value="">
                        <span class="error text-danger" id="id_number_of_partner_error"></span>
                    </div>
                </div>-->
            </div>

<!--            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Bank Details</div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6">Bank Sort Code</label>
                        <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control" value="" minlength="8" maxlength="8" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6">Bank Account Number</label>
                        <input type="text" name="bank_account_number" id="bank_account_number" class="form-control" value="" minlength="8" maxlength="8" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="fs-6">Bank Account Name</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" value="" />
                    </div>
                </div>
            </div>-->

            <div class="row mb-5">
                <div class="col-lg-12 text-start text-muted fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
<!--                    <a href="javascript:;" id="add_worker_next_screen_btn" data-add_worker_screen_name="Addresses" data-add_worker_no_of_screen="2" data-section="0" class="btn btn-primary btn-lg">Next</a>-->
                    <button type="submit" id="add_worker_submit_btn" class="btn btn-lg btn-primary">
                        <span class="indicator-label">Create worker</span>
                    </button>
                    <button type="button" class="btn btn-lg btn-primary disabled d-none" data-kt-stepper-action="submit" id="add_worker_process_btn">
                        <span>
                            Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@section('add_worker_basic_details_js')
    <script>

    </script>
@endsection
