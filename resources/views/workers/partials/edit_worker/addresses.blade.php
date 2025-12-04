<div class="tab-pane fade" id="kt_table_widget_5_tab_2">
    <div class="table-responsive">
        <form id="addresses_form">
            @csrf
            <div class="p-5">
                @if(((!$worker['same_as_current_address'] && (!$worker['permanent_address_line_one'] || !$worker['permanent_city'] || !$worker['permanent_country']))) || (!$worker['next_of_kin_first_name'] || !$worker['next_of_kin_last_name'] || !$worker['next_of_kin_mobile'] || !$worker['next_of_kin_email']))
                    <div class="alert alert-custom alert-warning" role="alert">
                        <div class="alert-text fs-4">
                            <i class="las la-exclamation-triangle text-warning fs-xl-2"></i> This tab has missing data that must be completed before the worker can be made active
                        </div>
                    </div>
                @endif
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Address in home country</div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-5 fv-row fv-plugins-icon-container d-flex float-start">
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
                            <label for="next_of_kin_first_name" class="fs-6 required">Name</label>
                            <input type="text" name="next_of_kin_first_name" id="next_of_kin_first_name" class="form-control" value="{{ $worker['next_of_kin_first_name'] }}" />
                            <span class="error text-danger" id="next_of_kin_first_name_error"></span>
                        </div>
                    </div>
<!--                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_last_name" class="fs-6 required">Surname</label>
                            <input type="text" name="next_of_kin_last_name" id="next_of_kin_last_name" class="form-control" value="{{ $worker['next_of_kin_last_name'] }}" />
                            <span class="error text-danger" id="next_of_kin_last_name_error"></span>
                        </div>
                    </div>-->
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
                </div>

                <!-- <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Next of kin address</div>
                </div>-->
                <!--<div class="row">
                    <div class="col-lg-12">
                        <div class="mb-5 fv-row fv-plugins-icon-container d-flex float-start">
                            <label class="form-check form-check-inline is-invalid">
                                <input type="checkbox" class="form-check-input" name="same_as_current_address_for_next_of_kin" id="same_as_current_address_for_next_of_kin" {{ ($worker['same_as_current_address_for_next_of_kin']) ? 'checked' : ''  }} value="1">
                                <span class="fs-6">Same as UK address</span>
                            </label>

                            <label class="form-check form-check-inline is-invalid">
                                <input type="checkbox" class="form-check-input" name="same_as_worker_home_address" id="same_as_worker_home_address" {{ ($worker['same_as_worker_home_address']) ? 'checked' : ''  }} value="1">
                                <span class="ps-2 fs-6">Same as worker home address</span>
                            </label>
                        </div>
                    </div>
                </div>-->
                <!--<div class="row {{ ($worker['same_as_worker_home_address'] || $worker['same_as_current_address_for_next_of_kin']) ? 'd-none' : ''  }}" id="next_of_kin_address">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_address_line_one" class="fs-6">Address line 1</label>
                            <input type="text" name="next_of_kin_address_line_one" id="next_of_kin_address_line_one" class="form-control" value="{{ $worker['next_of_kin_address_line_one'] }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_state" class="fs-6">County</label>
                            <input type="text" name="next_of_kin_state" id="next_of_kin_state" class="form-control" value="{{ $worker['next_of_kin_state'] }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_address_line_two" class="fs-6">Address line 2</label>
                            <input type="text" name="next_of_kin_address_line_two" id="next_of_kin_address_line_two" class="form-control" value="{{ $worker['next_of_kin_address_line_two'] }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_post_code" class="fs-6">Postcode</label>
                            <input type="text" name="next_of_kin_post_code" id="next_of_kin_post_code" class="form-control" value="{{ $worker['next_of_kin_post_code'] }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_city" class="fs-6">City</label>
                            <input type="text" name="next_of_kin_city" id="next_of_kin_city" class="form-control" value="{{ $worker['next_of_kin_city'] }}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="next_of_kin_country" class="fs-6">Country</label>
                            <select name="next_of_kin_country" id="next_of_kin_country" class="form-select form-select-lg" data-control="select2" data-placeholder="Please select..." data-allow-clear="true">
                                <option value="">Please select...</option>
                                @if($country)
                                    @foreach($country as $country_row)
                                        <option {{ ($worker['next_of_kin_country'] == $country_row['name']) ? 'selected' : '' }} value="{{ $country_row['name'] }}">{{ $country_row['name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>-->

                <!-- <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
                </div>-->
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <input type="hidden" name="update_addresses_id" id="update_addresses_id" value="{{ $worker['id'] }}">
                        <button type="submit" name="addresses_submit" id="addresses_submit" class="btn btn-primary btn-lg">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@section('edit_worker_addresses_js')
    <script>
        $("#addresses_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-worker-addresses') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)
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
                /*$("#permanent_country").val($("#current_country").val());
                $("#permanent_state").val($("#current_state").val());
                $("#permanent_city").val($("#current_city").val());
                $("#permanent_address_line_one").val($("#current_address_line_one").val());
                $("#permanent_address_line_two").val($("#current_address_line_two").val());
                $("#permanent_post_code").val($("#current_zip_code").val());*/

                $("#address_in_home_country").addClass('d-none');
            } else {
                /*$("#permanent_country").val('');
                $("#permanent_state").val('');
                $("#permanent_city").val('');
                $("#permanent_address_line_one").val('');
                $("#permanent_address_line_two").val('');
                $("#permanent_post_code").val('');*/

                $("#address_in_home_country").removeClass('d-none');
            }
        });

        $("#same_as_worker_home_address").on('click', function () {
            $("#same_as_current_address_for_next_of_kin").prop('checked', false);

            $("#next_of_kin_country").val('');
            $("#next_of_kin_state").val('');
            $("#next_of_kin_city").val('');
            $("#next_of_kin_address_line_one").val('');
            $("#next_of_kin_address_line_two").val('');
            $("#next_of_kin_post_code").val('');

            if($(this).prop("checked") === true) {
                /*$("#next_of_kin_country").val($("#permanent_country").val());
                $("#next_of_kin_state").val($("#permanent_state").val());
                $("#next_of_kin_city").val($("#permanent_city").val());
                $("#next_of_kin_address_line_one").val($("#permanent_address_line_one").val());
                $("#next_of_kin_address_line_two").val($("#permanent_address_line_two").val());
                $("#next_of_kin_post_code").val($("#permanent_post_code").val());*/

                $("#next_of_kin_address").addClass('d-none');
            } else {
                /*$("#next_of_kin_country").val('');
                $("#next_of_kin_state").val('');
                $("#next_of_kin_city").val('');
                $("#next_of_kin_address_line_one").val('');
                $("#next_of_kin_address_line_two").val('');
                $("#next_of_kin_post_code").val('');*/

                $("#next_of_kin_address").removeClass('d-none');
            }
        });

       $("#same_as_current_address_for_next_of_kin").on('click', function () {
            /*$("#same_as_worker_home_address").prop('checked', false);

            $("#next_of_kin_country").val('');
            $("#next_of_kin_state").val('');
            $("#next_of_kin_city").val('');
            $("#next_of_kin_address_line_one").val('');
            $("#next_of_kin_address_line_two").val('');
            $("#next_of_kin_post_code").val(''); */

            if($(this).prop("checked") === true) {
                /*$("#next_of_kin_country").val($("#current_country").val());
                $("#next_of_kin_state").val($("#current_state").val());
                $("#next_of_kin_city").val($("#current_city").val());
                $("#next_of_kin_address_line_one").val($("#current_address_line_one").val());
                $("#next_of_kin_address_line_two").val($("#current_address_line_two").val());
                $("#next_of_kin_post_code").val($("#current_zip_code").val());*/

                $("#next_of_kin_address").addClass('d-none');
            } else {
                /*$("#next_of_kin_country").val('');
                $("#next_of_kin_state").val('');
                $("#next_of_kin_city").val('');
                $("#next_of_kin_address_line_one").val('');
                $("#next_of_kin_address_line_two").val('');
                $("#next_of_kin_post_code").val('');*/

                $("#next_of_kin_address").removeClass('d-none');
            }
        });
    </script>
@endsection
