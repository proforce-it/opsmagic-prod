<div class="fv-row" id="basic_details_tab">
    <div class="row mb-5">
        <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Basic information</div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                <label for="company_name" class="fs-6 fw-bold required">Company name</label>
                <input type="text" name="company_name" id="company_name" class="form-control" placeholder="Company name" value="" />
                <span class="text-danger error" id="company_name_error"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="company_registration_number" class="fs-6 fw-bold required">Company registration number</label>
                <input type="text" name="company_registration_number" id="company_registration_number" class="form-control" placeholder="Company registration number" value="" />
                <span class="text-danger error" id="company_registration_number_error"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="vat_number" class="fs-6 fw-bold">VAT number</label>
                <input type="text" name="vat_number" id="vat_number" class="form-control" placeholder="VAT number" value="" />
                <span class="text-danger error" id="vat_number_error"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="sector" class="fs-6 fw-bold required">Sector</label>
                <select name="sector" id="sector" class="form-select form-select-lg" data-control="select2" data-placeholder="Select sector" data-allow-clear="true">
                    <option value="">Select..</option>
                    <option value="Administration & Office Support">Administration & Office Support</option>
                    <option value="Agriculture">Agriculture</option>
                    <option value="Cleaning & Facilities Management">Cleaning & Facilities Management</option>
                    <option value="Construction">Construction</option>
                    <option value="Customer Service & Call Centres">Customer Service & Call Centres</option>
                    <option value="Engineering & Technical">Engineering & Technical</option>
                    <option value="FMCG">FMCG</option>
                    <option value="Food Production">Food Production</option>
                    <option value="Healthcare & Social Care">Healthcare & Social Care</option>
                    <option value="Horticulture">Horticulture</option>
                    <option value="Hospitality & Catering">Hospitality & Catering</option>
                    <option value="Manufacturing">Manufacturing</option>
                    <option value="Other">Other</option>
                    <option value="Retail">Retail</option>
                    <option value="Transport & Driving">Transport & Driving</option>
                    <option value="Warehousing & Logistics">Warehousing & Logistics</option>
                </select>
                <span class="text-danger error" id="sector_error"></span>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">PRIMARY CLIENT CONTACT</div>
            <div class="alert alert-custom alert-primary mt-5" role="alert">
                <div class="alert-text fs-4">
                    <i class="las la-lightbulb text-primary fs-xl-2"></i> <strong>Note: </strong>You can add additional contacts once the client is created
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="first_name" class="fs-6 fw-bold required">First name</label>
                <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First name" value="" />
                <span class="text-danger error" id="first_name_error"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="last_name" class="fs-6 fw-bold required">Last name</label>
                <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last name" value="" />
                <span class="text-danger error" id="last_name_error"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="telephone_one" class="fs-6 fw-bold required">Telephone 1</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="las la-mobile fs-xxl-2"></i>
                        </span>
                    </div>
                    <input class="form-control" name="telephone_one" id="telephone_one" type="text" placeholder="Telephone 1">
                </div>
                <span class="text-danger error" id="telephone_one_error"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="telephone_two" class="fs-6 fw-bold">Telephone 2</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="las la-mobile fs-xxl-2"></i>
                        </span>
                    </div>
                    <input type="text" name="telephone_two" id="telephone_two" class="form-control" placeholder="Telephone 2" />
                </div>
                <span class="text-danger error" id="telephone_two_error"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="email_address" class="fs-6 fw-bold required">Email address</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="las la-envelope-open fs-xxl-2"></i>
                        </span>
                    </div>
                    <input type="text" name="email_address" id="email_address" class="form-control" placeholder="Email address" />
                </div>
                <span class="text-danger error" id="email_address_error"></span>
            </div>
        </div>
        <div class="alert alert-custom alert-primary mt-10 mb-10" role="alert">
            <div class="alert-text fs-4">
                <i class="las la-lightbulb text-primary fs-xl-2"></i> <strong>Note: </strong>You can add additional work sites once the client is created
            </div>
        </div>
        <div class="col-lg-12">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="address_type" class="fs-6 fw-bold required">Address type</label>
                <select name="address_type" id="address_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Please select..." data-allow-clear="true">
                    <option value="">Select address type</option>
                    <option value="HQ address">This is an administrative/HQ address only</option>
                    <option value="Site address">This address is also a work site</option>
                </select>
                <span class="text-danger error" id="address_type_error"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="address_line_one" class="fs-6 fw-bold required">Address line 1</label>
                <input type="text" name="address_line_one" id="address_line_one" class="form-control" placeholder="Address line 1" value="" />
                <span class="text-danger error" id="address_line_one_error"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="city" class="fs-6 fw-bold required">City</label>
                <input type="text" name="city" id="city" class="form-control" placeholder="City" value="" />
                <span class="text-danger error" id="city_error"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="address_line_two" class="fs-6 fw-bold">Address line 2</label>
                <input type="text" name="address_line_two" id="address_line_two" class="form-control" placeholder="Address line 2" value="" />
            </div>
        </div>
        <div class="col-lg-6">
            <label for="country_option" class="fs-6 fw-bold">Country</label>
            <select name="country" id="country" class="form-select form-select-lg" data-control="select2" data-placeholder="Please select..." data-allow-clear="true">
                <option value="">Please select...</option>
                @if($country)
                    @foreach($country as $country_row)
                        <option value="{{ $country_row['name'] }}">{{ $country_row['name'] }}</option>
                    @endforeach
                @endif
            </select>
            <span class="text-danger error" id="country_error"></span>
        </div>
        <div class="col-lg-6">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="postcode" class="fs-6 fw-bold">Postcode</label>
                <input type="text" name="postcode" id="postcode" class="form-control" placeholder="Postcode" value="" />
                <span class="text-danger error" id="postcode_error"></span>
            </div>
        </div>

        <div class="col-lg-12">
            <hr>
        </div>
        <div class="col-lg-12">
            <div class="mb-10 fv-row fv-plugins-icon-container">
                <label for="website" class="fs-6 fw-bold">Website</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon3">https://</span>
                    </div>
                    <input type="text" class="form-control" name="website" id="website" aria-describedby="basic-addon3" placeholder="www.example.com">
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <button type="submit" id="add_client_submit_btn" class="btn btn-lg btn-primary">
                <span class="indicator-label">Create client</span>
            </button>
            <button type="button" class="btn btn-lg btn-primary disabled d-none" data-kt-stepper-action="submit" id="add_client_process_btn">
                <span>
                    Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </div>
</div>
