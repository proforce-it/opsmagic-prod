<div class="tab-pane fade" id="kt_table_widget_5_tab_1">
    <div class="table-responsive">
        <form id="basic_details_form">
            @csrf
            <div class="p-5">
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">BASIC INFO</div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="required">Company name</label>
                            <input type="hidden" name="basic_details_update_id" id="basic_details_update_id" value="{{ $client['id'] }}">
                            <input type="text" name="company_name" id="company_name" class="form-control" placeholder="Company name" value="{{ $client['company_name'] }}" />
                            <span class="text-danger error" id="company_name_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="required">Company registration number</label>
                            <input type="text" name="company_registration_number" id="company_registration_number" class="form-control" placeholder="Company number" value="{{ $client['company_number'] }}" />
                            <span class="text-danger error" id="company_registration_number_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label>VAT number</label>
                            <input type="text" name="vat_number" id="vat_number" class="form-control" placeholder="VAT number" value="{{ $client['vat_number'] }}" />
                            <span class="text-danger error" id="vat_number_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="sector" class="fs-6 fw-bold required">Sector</label>
                            <select name="sector" id="sector" class="form-select form-select-lg" data-control="select2" data-placeholder="Select sector" data-allow-clear="true">
                                <option value="">Select..</option>
                                <option value="Administration & Office Support" {{$client['sector'] == 'Administration & Office Support' ? 'selected' : '' }}>Administration & Office Support</option>
                                <option value="Agriculture" {{$client['sector'] == 'Agriculture' ? 'selected' : '' }}>Agriculture</option>
                                <option value="Cleaning & Facilities Management" {{$client['sector'] == 'Cleaning & Facilities Management' ? 'selected' : '' }}>Cleaning & Facilities Management</option>
                                <option value="Construction" {{$client['sector'] == 'Construction' ? 'selected' : '' }}>Construction</option>
                                <option value="Customer Service & Call Centres" {{$client['sector'] == 'Customer Service & Call Centres' ? 'selected' : '' }}>Customer Service & Call Centres</option>
                                <option value="Engineering & Technical" {{$client['sector'] == 'Engineering & Technical' ? 'selected' : '' }}>Engineering & Technical</option>
                                <option value="FMCG" {{$client['sector'] == 'FMCG' ? 'selected' : '' }}>FMCG</option>
                                <option value="Food Production" {{$client['sector'] == 'Food Production' ? 'selected' : '' }}>Food Production</option>
                                <option value="Healthcare & Social Care" {{$client['sector'] == 'Healthcare & Social Care' ? 'selected' : '' }}>Healthcare & Social Care</option>
                                <option value="Horticulture" {{$client['sector'] == 'Horticulture' ? 'selected' : '' }}>Horticulture</option>
                                <option value="Hospitality & Catering" {{$client['sector'] == 'Hospitality & Catering' ? 'selected' : '' }}>Hospitality & Catering</option>
                                <option value="Manufacturing" {{$client['sector'] == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="Other" {{$client['sector'] == 'Other' ? 'selected' : '' }}>Other</option>
                                <option value="Retail" {{$client['sector'] == 'Retail' ? 'selected' : '' }}>Retail</option>
                                <option value="Transport & Driving" {{$client['sector'] == 'Transport & Driving' ? 'selected' : '' }}>Transport & Driving</option>
                                <option value="Warehousing & Logistics" {{$client['sector'] == 'Warehousing & Logistics' ? 'selected' : '' }}>Warehousing & Logistics</option>
                            </select>
                            <span class="text-danger error" id="sector_error"></span>
                        </div>
                    </div>

                </div>

                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">PRIMARY CLIENT CONTACT</div>
                    <div class="alert alert-custom alert-primary mt-5" role="alert">
                        <div class="alert-text fs-4">
                            <i class="las la-lightbulb text-primary fs-xl-2"></i> <strong>Note: </strong>This information can be edited on the <strong>contacts tab</strong>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="first_name" class="fs-6 fw-bold required">First name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control bg-secondary text-gray-500" placeholder="First name" value="{{ $primaryContactData['first_name'] ?? '' }}" readonly />
                            <span class="text-danger error" id="first_name_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="last_name" class="fs-6 fw-bold required">Last name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control bg-secondary text-gray-500" placeholder="Last name" value="{{ $primaryContactData['last_name'] ?? '' }}" readonly />
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
                                <input class="form-control bg-secondary text-gray-500" name="telephone_one" id="telephone_one" type="text" placeholder="Telephone 1" value="{{ $primaryContactData['primary_contact_number'] ?? '' }}" readonly />
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
                                <input type="text" name="telephone_two" id="telephone_two" class="form-control bg-secondary text-gray-500" placeholder="Telephone 2" value="{{ $primaryContactData['secondary_contact_number'] ?? '' }}" readonly />
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
                                <input type="text" name="email_address" id="email_address" class="form-control bg-secondary text-gray-500" placeholder="Email address" value="{{ $primaryContactData['email'] ?? '' }}" readonly />
                            </div>
                            <span class="text-danger error" id="email_address_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">COMPANY ADDRESS & WEBSITE</div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="address_line_one" class="fs-6 required">Address line 1</label>
                                <input type="text" name="address_line_one" id="address_line_one" class="form-control" placeholder="Address line 1" value="{{ $client['address_line_one'] }}" />
                                <span class="text-danger error" id="address_line_one_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="city" class="fs-6 required">City</label>
                            <input type="text" name="city" id="city" class="form-control" placeholder="City/Town" value="{{ $client['city_town'] }}" />
                            <span class="text-danger error" id="city_error"></span>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="address_line_two" class="fs-6">Address line 2</label>
                            <input type="text" name="address_line_two" id="address_line_two" class="form-control" placeholder="Address line 2" value="{{ $client['address_line_two'] }}" />
                            <span class="text-danger error" id="address_line_two_error"></span>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="country" class="fs-6 required">Country</label>
                                <select name="country" id="country" class="form-select form-select-lg" data-control="select2" data-placeholder="Please select..." data-allow-clear="true">
                                    <option value="">Please select...</option>
                                    @if($country)
                                        @foreach($country as $country_row)
                                            <option {{ ($country_row['name'] == $client['county']) ? 'selected' : '' }} value="{{ $country_row['name'] }}">{{ $country_row['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="text-danger error" id="county_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="postcode" class="fs-6">Postcode</label>
                            <input type="text" name="postcode" id="postcode" class="form-control" placeholder="Postcode" value="{{ $client['postcode'] }}" />
                            <span class="text-danger error" id="postcode_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="col-lg-4 required">Address type</label>
                            <select name="address_type" id="address_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Select address type" data-allow-clear="true">
                                <option {{ ($client['address_type'] == '') ? 'selected' : '' }} value="">Select address type</option>
                                <option {{ ($client['address_type'] == 'HQ address') ? 'selected' : '' }} value="HQ address">This is an administrative/HQ address only</option>
                                <option {{ ($client['address_type'] == 'Site address') ? 'selected' : '' }} value="Site address">This address is also a work site</option>
                            </select>
                            <span class="text-danger error" id="address_type_error"></span>
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
                                <input type="text" class="form-control" name="website" id="website" aria-describedby="basic-addon3" placeholder="www.example.com" value="{{ $client['website'] }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-5 border-bottom border-4"></div>
                <div class="row mb-7 text-center">
                    <div class="col-lg-12">
                        <button type="submit" name="basic_details_submit" id="basic_details_submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@section('edit_client_basic_details_js')
    <script>
        $("#basic_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-client-basic-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)
                    if(response.code === 200)
                        setTimeout(function() { location.reload(); }, 1500);
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
