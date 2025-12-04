<div class="d-none" id="addresses_tab">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Uk address</div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="current_address_line_one" class="fs-6 required">Address line 1</label>
                        <input type="text" name="current_address_line_one" id="current_address_line_one" class="form-control" />
                        <span class="error text-danger" id="current_address_line_one_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="current_state" class="fs-6">State</label>
                        <input type="text" name="current_state" id="current_state" class="form-control" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="current_address_line_two" class="fs-6">Address line 2</label>
                        <input type="text" name="current_address_line_two" id="current_address_line_two" class="form-control" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="current_zip_code" class="fs-6 required">Postcode</label>
                        <input type="text" name="current_zip_code" id="current_zip_code" class="form-control" />
                        <span class="error text-danger" id="current_zip_code_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="current_city" class="fs-6 required">City</label>
                        <input type="text" name="current_city" id="current_city" class="form-control" />
                        <span class="error text-danger" id="current_city_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="current_country" class="fs-6">Country</label>
                        <input type="text" name="current_country" id="current_country" class="form-control" />
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Address in home country</div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-5 fv-row fv-plugins-icon-container d-flex float-start">
                        <label class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="same_as_current_address" id="same_as_current_address" value="1">
                            <span class="fs-6">Same as UK address</span>
                            <span class="error text-danger" id="same_as_current_address_error"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row" id="address_in_home_country">
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="permanent_address_line_one" class="fs-6 required">Address line 1</label>
                        <input type="text" name="permanent_address_line_one" id="permanent_address_line_one" class="form-control" />
                        <span class="error text-danger" id="permanent_address_line_one_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="permanent_state" class="fs-6">State</label>
                        <input type="text" name="permanent_state" id="permanent_state" class="form-control" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="permanent_address_line_two" class="fs-6">Address line 2</label>
                        <input type="text" name="permanent_address_line_two" id="permanent_address_line_two" class="form-control" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="permanent_zip_code" class="fs-6">Postcode</label>
                        <input type="text" name="permanent_zip_code" id="permanent_zip_code" class="form-control" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="permanent_city" class="fs-6 required">City</label>
                        <input type="text" name="permanent_city" id="permanent_city" class="form-control" />
                        <span class="error text-danger" id="permanent_city_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="permanent_country" class="fs-6 required">Country</label>
                        <input type="text" name="permanent_country" id="permanent_country" class="form-control" />
                        <span class="error text-danger" id="permanent_country_error"></span>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Next of kin</div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="next_of_kin_first_name" class="fs-6 required">First name</label>
                        <input type="text" name="next_of_kin_first_name" id="next_of_kin_first_name" class="form-control" />
                        <span class="error text-danger" id="next_of_kin_first_name_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="next_of_kin_last_name" class="fs-6 required">Surname</label>
                        <input type="text" name="next_of_kin_last_name" id="next_of_kin_last_name" class="form-control" />
                        <span class="error text-danger" id="next_of_kin_last_name_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="next_of_kin_mobile" class="fs-6 required">Mobile</label>
                        <input type="text" name="next_of_kin_mobile" id="next_of_kin_mobile" class="form-control" />
                        <span class="error text-danger" id="next_of_kin_mobile_error"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="next_of_kin_email" class="fs-6 required">Email</label>
                        <input type="text" name="next_of_kin_email" id="next_of_kin_email" class="form-control" />
                        <span class="error text-danger" id="next_of_kin_email_error"></span>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Next of kin address</div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-5 fv-row fv-plugins-icon-container d-flex float-start">
                        <label class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="same_as_current_address_for_next_of_kin" id="same_as_current_address_for_next_of_kin" value="1">
                            <span class="fs-6">Same as UK address</span>
                        </label>

                        <label class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="same_as_worker_home_address" id="same_as_worker_home_address" value="1">
                            <span class="ps-2 fs-6">Same as worker home address</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row" id="next_of_kin_address">
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="next_of_kin_address_line_one" class="fs-6">Address line 1</label>
                        <input type="text" name="next_of_kin_address_line_one" id="next_of_kin_address_line_one" class="form-control" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="next_of_kin_state" class="fs-6">State</label>
                        <input type="text" name="next_of_kin_state" id="next_of_kin_state" class="form-control" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="next_of_kin_address_line_two" class="fs-6">Address line 2</label>
                        <input type="text" name="next_of_kin_address_line_two" id="next_of_kin_address_line_two" class="form-control" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="next_of_kin_zip_code" class="fs-6">Postcode</label>
                        <input type="text" name="next_of_kin_zip_code" id="next_of_kin_zip_code" class="form-control" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="next_of_kin_city" class="fs-6">City</label>
                        <input type="text" name="next_of_kin_city" id="next_of_kin_city" class="form-control" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label for="next_of_kin_country" class="fs-6">Country</label>
                        <input type="text" name="next_of_kin_country" id="next_of_kin_country" class="form-control" />
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start text-muted fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <a href="javascript:;" id="add_worker_next_screen_btn" data-add_worker_screen_name="Right to Work" data-add_worker_no_of_screen="3" data-section="1" class="btn btn-primary btn-lg">Next</a>
                </div>
            </div>
        </div>
    </div>
</div>

@section('add_worker_addresses_js')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBIcqb6PHyEOPWFQVJ_8-Z1V21dPfUhVCI&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
    <script src="{{ asset('js/worker/autocomplete_address.js') }}"></script>

    <script>
        $("#same_as_current_address").on('click', function () {
            if($(this).prop("checked") === true) {
                $("#permanent_country").val($("#current_country").val());
                $("#permanent_state").val($("#current_state").val());
                $("#permanent_city").val($("#current_city").val());
                $("#permanent_address_line_one").val($("#current_address_line_one").val());
                $("#permanent_address_line_two").val($("#current_address_line_two").val());
                $("#permanent_zip_code").val($("#current_zip_code").val());

                $("#address_in_home_country").addClass('d-none');
            } else {
                $("#permanent_country").val('');
                $("#permanent_state").val('');
                $("#permanent_city").val('');
                $("#permanent_address_line_one").val('');
                $("#permanent_address_line_two").val('');
                $("#permanent_zip_code").val('');

                $("#address_in_home_country").removeClass('d-none');
            }
        });

        $("#same_as_worker_home_address").on('click', function () {
            $("#same_as_current_address_for_next_of_kin").prop('checked', false);
            if($(this).prop("checked") === true) {
                $("#next_of_kin_country").val($("#permanent_country").val());
                $("#next_of_kin_state").val($("#permanent_state").val());
                $("#next_of_kin_city").val($("#permanent_city").val());
                $("#next_of_kin_address_line_one").val($("#permanent_address_line_one").val());
                $("#next_of_kin_address_line_two").val($("#permanent_address_line_two").val());
                $("#next_of_kin_zip_code").val($("#permanent_zip_code").val());

                $("#next_of_kin_address").addClass('d-none');
            } else {
                $("#next_of_kin_country").val('');
                $("#next_of_kin_state").val('');
                $("#next_of_kin_city").val('');
                $("#next_of_kin_address_line_one").val('');
                $("#next_of_kin_address_line_two").val('');
                $("#next_of_kin_zip_code").val('');

                $("#next_of_kin_address").removeClass('d-none');
            }
        });

        $("#same_as_current_address_for_next_of_kin").on('click', function () {
            $("#same_as_worker_home_address").prop('checked', false);
            if($(this).prop("checked") === true) {
                $("#next_of_kin_country").val($("#current_country").val());
                $("#next_of_kin_state").val($("#current_state").val());
                $("#next_of_kin_city").val($("#current_city").val());
                $("#next_of_kin_address_line_one").val($("#current_address_line_one").val());
                $("#next_of_kin_address_line_two").val($("#current_address_line_two").val());
                $("#next_of_kin_zip_code").val($("#current_zip_code").val());

                $("#next_of_kin_address").addClass('d-none');
            } else {
                $("#next_of_kin_country").val('');
                $("#next_of_kin_state").val('');
                $("#next_of_kin_city").val('');
                $("#next_of_kin_address_line_one").val('');
                $("#next_of_kin_address_line_two").val('');
                $("#next_of_kin_zip_code").val('');

                $("#next_of_kin_address").removeClass('d-none');
            }
        });

    </script>
@endsection
