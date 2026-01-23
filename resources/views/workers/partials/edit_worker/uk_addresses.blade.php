<div class="tab-pane fade" id="kt_table_widget_5_tab_11">
    <div class="table-responsive">
        <form id="uk_addresses_form">
            @csrf
            <div class="p-5">
                @if(!$worker['accommodation_type'] && !$worker['proforce_transport'])
                    <div class="alert alert-custom alert-warning" role="alert">
                        <div class="alert-text fs-4">
                            <i class="las la-exclamation-triangle text-warning fs-xl-2"></i> This tab has missing data that must be completed before the worker can be made active
                        </div>
                    </div>
                @endif
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">UK ADDRESS</div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="accommodation_type" class="fs-6 required">Accommodation type</label>
                            <select name="accommodation_type" id="accommodation_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                <option {{ ($worker['accommodation_type'] == "") ? 'selected' : '' }} value=""></option>
                                <option {{ ($worker['accommodation_type'] == "arranged_by_worker") ? 'selected' : '' }} value="arranged_by_worker">Arranged by worker</option>
                                <option {{ ($worker['accommodation_type'] == "supplied_by_pro_force") ? 'selected' : '' }} value="supplied_by_pro_force">Supplied by Pro-Force</option>
                            </select>
                            <span class="error text-danger" id="accommodation_type_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row {{ ($worker['accommodation_type'] == "supplied_by_pro_force") ? '' : 'd-none' }}" id="accommodation_site_section">
                    <div class="col-lg-12">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="accommodation_site" class="fs-6 required">Site</label>
                            <select name="accommodation_site" id="accommodation_site" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                <option {{ ($worker['accommodation_site'] == "") ? 'selected' : '' }} value=""></option>
                                @if($accommodation_site)
                                    @foreach($accommodation_site as $as)
                                        <option {{ ($worker['accommodation_site'] == $as['id']) ? 'selected' : '' }} value="{{ $as['id'] }}">
                                            {{ $as['name'] }} - ///{{ $as['what_three_words_locator'] }} - {{ $as['postcode'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="error text-danger" id="accommodation_site_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row {{ ($worker['accommodation_type'] == "arranged_by_worker") ? '' : 'd-none' }}" id="accommodation_address_section">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="current_address_line_one" class="fs-6 required">Address line 1</label>
                            <input type="text" name="current_address_line_one" id="current_address_line_one" class="form-control" value="{{ $worker['current_address_line_one'] }}" />
                            <span class="error text-danger" id="current_address_line_one_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="current_state" class="fs-6">County</label>
                            <input type="text" name="current_state" id="current_state" class="form-control" value="{{ $worker['current_state'] }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="current_address_line_two" class="fs-6">Address line 2</label>
                            <input type="text" name="current_address_line_two" id="current_address_line_two" class="form-control" value="{{ $worker['current_address_line_two'] }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="current_post_code" class="fs-6 required">Postcode</label>
                            <input type="text" name="current_post_code" id="current_post_code" class="form-control" value="{{ $worker['current_post_code'] }}" />
                            <span class="error text-danger" id="current_post_code_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="current_city" class="fs-6 required">City</label>
                            <input type="text" name="current_city" id="current_city" class="form-control" value="{{ $worker['current_city'] }}" />
                            <span class="error text-danger" id="current_city_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="current_country" class="fs-6">Country</label>
                            <select name="current_country" id="current_country" class="form-select form-select-lg" data-control="select2" data-placeholder="Please select..." data-allow-clear="true">
                                <option value="">Please select...</option>
                                @if($country)
                                    @foreach($country as $country_row)
                                        <option {{ ($worker['current_country'] == $country_row['name']) ? 'selected' : '' }} value="{{ $country_row['name'] }}">{{ $country_row['name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">TRANSPORT TO SITE</div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="proforce_transport" class="fs-6 required">ProForce Transport</label>
                            <select name="proforce_transport" id="proforce_transport" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                <option {{ ($worker['proforce_transport'] == "") ? 'selected' : '' }} value=""></option>
                                <option {{ ($worker['proforce_transport'] == "Yes") ? 'selected' : '' }} value="Yes">Yes</option>
                                <option {{ ($worker['proforce_transport'] == "No") ? 'selected' : '' }} value="No">No</option>
                            </select>
                            <span class="error text-danger" id="proforce_transport_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row {{ ($worker['proforce_transport'] == "Yes") ? '' : 'd-none' }}" id="preferred_pickup_point_section">
                    <div class="col-lg-12">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="preferred_pick_up_point_id" class="fs-6 required">Preferred pick up point id</label>
                            <select name="preferred_pick_up_point_id" id="preferred_pick_up_point_id" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                <option {{ ($worker['preferred_pick_up_point_id'] == "") ? 'selected' : '' }}></option>
                                @if($pickup_point)
                                    @foreach($pickup_point as $pp)
                                        <option {{ ($worker['preferred_pick_up_point_id'] == $pp['id']) ? 'selected' : '' }} value="{{ $pp['id'] }}">
                                            {{ $pp['name'] }} - {{ $pp['postcode'] }} - ///{{ $pp['what_three_words_locator'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="error text-danger" id="preferred_pick_up_point_id_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Address in home country</div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-10 fv-row fv-plugins-icon-container d-flex float-start">
                            <label class="form-check form-check-inline is-invalid">
                                <input type="checkbox" class="form-check-input" name="same_as_current_address" id="same_as_current_address" {{ ($worker['same_as_current_address']) ? 'checked' : ''  }} value="1">
                                <span class="fs-6">Same as UK address</span>
                                <span class="error text-danger" id="same_as_current_address_error"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row  {{ ($worker['same_as_current_address']) ? 'd-none' : ''  }}" id="address_in_home_country">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="permanent_address_line_one" class="fs-6 required">Address line 1</label>
                            <input type="text" name="permanent_address_line_one" id="permanent_address_line_one" class="form-control" value="{{ $worker['permanent_address_line_one'] }}" />
                            <span class="error text-danger" id="permanent_address_line_one_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="permanent_state" class="fs-6">County</label>
                            <input type="text" name="permanent_state" id="permanent_state" class="form-control" value="{{ $worker['permanent_state'] }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="permanent_address_line_two" class="fs-6">Address line 2</label>
                            <input type="text" name="permanent_address_line_two" id="permanent_address_line_two" class="form-control" value="{{ $worker['permanent_address_line_two'] }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="permanent_post_code" class="fs-6">Postcode</label>
                            <input type="text" name="permanent_post_code" id="permanent_post_code" class="form-control" value="{{ $worker['permanent_post_code'] }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="permanent_city" class="fs-6 required">City</label>
                            <input type="text" name="permanent_city" id="permanent_city" class="form-control" value="{{ $worker['permanent_city'] }}" />
                            <span class="error text-danger" id="permanent_city_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="permanent_country" class="fs-6 required">Country</label>
                            <select name="permanent_country" id="permanent_country" class="form-select form-select-lg" data-control="select2" data-placeholder="Please select..." data-allow-clear="true">
                                <option value="">Please select...</option>
                                @if($country)
                                    @foreach($country as $country_row)
                                        <option {{ ($worker['permanent_country'] == $country_row['name']) ? 'selected' : '' }} value="{{ $country_row['name'] }}">{{ $country_row['name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
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
                            <label for="next_of_kin_first_name" class="fs-6 required">First Name</label>
                            <input type="text" name="next_of_kin_first_name" id="next_of_kin_first_name" class="form-control" value="{{ $worker['next_of_kin_first_name'] }}" />
                            <span class="error text-danger" id="next_of_kin_first_name_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_last_name" class="fs-6 required">Last Name</label>
                            <input type="text" name="next_of_kin_last_name" id="next_of_kin_last_name" class="form-control" value="{{ $worker['next_of_kin_last_name'] }}" />
                            <span class="error text-danger" id="next_of_kin_last_name_error"></span>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_mobile" class="fs-6 required">Mobile</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="las la-mobile fs-xxl-2"></i>
                                </span>
                                </div>
                                <input type="text" name="next_of_kin_mobile" id="next_of_kin_mobile" class="form-control" value="{{ $worker['next_of_kin_mobile'] }}" />
                            </div>
                            <span class="error text-danger" id="next_of_kin_mobile_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_email" class="fs-6 required">Email</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="las la-paper-plane fs-xxl-2"></i>
                                </span>
                                </div>
                                <input type="text" name="next_of_kin_email" id="next_of_kin_email" class="form-control" value="{{ $worker['next_of_kin_email'] }}" />
                            </div>
                            <span class="error text-danger" id="next_of_kin_email_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_relationship" class="fs-6">Relationship</label>
                            <select name="next_of_kin_relationship" id="next_of_kin_relationship" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                <option {{ ($worker['next_of_kin_relationship'] == "") ? 'selected' : '' }} value=""></option>
                                <option {{ ($worker['next_of_kin_relationship'] == "Mother/Father") ? 'selected' : '' }} value="Mother/Father">Mother/Father</option>
                                <option {{ ($worker['next_of_kin_relationship'] == "Brother/Sister") ? 'selected' : '' }} value="Brother/Sister">Brother/Sister</option>
                                <option {{ ($worker['next_of_kin_relationship'] == "Wife/Husband/Spouse") ? 'selected' : '' }} value="Wife/Husband/Spouse">Wife/Husband/Spouse</option>
                                <option {{ ($worker['next_of_kin_relationship'] == "Friend") ? 'selected' : '' }} value="Friend">Friend</option>
                                <option {{ ($worker['next_of_kin_relationship'] == "Other") ? 'selected' : '' }} value="Other">Other</option>
                            </select>
                            <span class="error text-danger" id="next_of_kin_relationship_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
                </div>

                <div class="row">
                    <div class="col-lg-12 text-center">
                        <input type="hidden" name="update_uk_addresses_id" id="update_uk_addresses_id" value="{{ $worker['id'] }}">
                        <button type="submit" name="addresses_submit" id="addresses_submit" class="btn btn-primary btn-lg">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@section('edit_worker_uk_addresses_js')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBIcqb6PHyEOPWFQVJ_8-Z1V21dPfUhVCI&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
    <script src="{{ asset('js/worker/autocomplete_address.js') }}"></script>

    <script>
        $("#accommodation_type").on('change', function () {
           if ($(this).val() === 'arranged_by_worker') {
               $("#accommodation_site_section").addClass('d-none');
               $("#accommodation_address_section").removeClass('d-none');
           } else {
               $("#accommodation_site_section").removeClass('d-none');
               $("#accommodation_address_section").addClass('d-none');
           }
        });

        $("#proforce_transport").on('change', function () {
            if ($(this).val() === 'Yes') {
                $("#preferred_pickup_point_section").removeClass('d-none');
            } else {
                $("#preferred_pickup_point_section").addClass('d-none');
            }
        });

        $("#uk_addresses_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-worker-uk-address') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);
                    if(response.code === 200) {
                        setTimeout(function() { location.reload(); }, 1500);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#same_as_current_address").on('click', function () {
            $("#permanent_country").val('');
            $("#permanent_state").val('');
            $("#permanent_city").val('');
            $("#permanent_address_line_one").val('');
            $("#permanent_address_line_two").val('');
            $("#permanent_post_code").val('');

            if($(this).prop("checked") === true) {
                $("#address_in_home_country").addClass('d-none');
            } else {
                $("#address_in_home_country").removeClass('d-none');
            }
        });
    </script>
@endsection
