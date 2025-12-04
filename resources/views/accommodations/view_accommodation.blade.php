@extends('theme.page')

@section('title', 'Add accommodation')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="mb-10">
                                <div class="bg-gray-500 p-10 fs-3 text-white rounded" id="screen_title">
                                    <h4 class="mb-0 fw-boldest text-white fs-1">{{ $accommodation['name'] }}</h4>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form class="w-100 pb-10" novalidate="novalidate" id="update_accommodation_form">
                                        <div class="table-responsive">
                                            <div class="p-5">
                                                <div class="current" data-kt-stepper-element="content">
                                                    <div class="w-100">
                                                        <div class="fv-row">
                                                            <div class="row mb-5">
                                                                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">BASIC DETAILS</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                                                        <label for="name" class="fs-6 fw-bold required">Name</label>
                                                                        <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ $accommodation['name'] }}" />
                                                                        <span class="text-danger error" id="name_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="number_of_spaces" class="fs-6 fw-bold required">Number of spaces</label>
                                                                        <input type="text" name="number_of_spaces" id="number_of_spaces" class="form-control" placeholder="Number of spaces" value="{{ $accommodation['number_of_spaces'] }}" />
                                                                        <span class="text-danger error" id="number_of_spaces_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="description" class="fs-6 fw-bold required">Description</label>
                                                                        <textarea name="description" id="description" rows="3" class="form-control" placeholder="Description">{{ $accommodation['description'] }}</textarea>
                                                                        <span class="text-danger error" id="description_error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-5">
                                                                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">COST CENTRE(S) <span class="text-danger">*</span></div>
                                                                <div class="alert alert-custom alert-primary mt-5" role="alert">
                                                                    <div class="alert-text fs-4">
                                                                        <i class="las la-info-circle text-primary fs-xl-2"></i> Tick the cost centre(s) that workers staying at this accommodation are most likey to work within
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                                                            @php
                                                                                $selected_cost_center = explode(', ', $accommodation['cost_center'])
                                                                            @endphp

                                                                            @if($costCentre)
                                                                                @foreach($costCentre as $center)
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="checkbox" name="cost_center[]" value="{{ $center['id'] }}" id="cc_{{ $center['id'] }}"
                                                                                        {{ in_array($center['id'], $selected_cost_center) ? 'checked' : '' }} />
                                                                                        <label class="form-check-label" for="cc_{{ $center['id'] }}">{{ $center['short_code'] }}</label>
                                                                                    </div>
                                                                                @endforeach
                                                                            @endif
                                                                        </div>
                                                                        <span class="text-danger error" id="cost_center_error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-5">
                                                                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">LOCATION</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="address_line_one" class="fs-6 fw-bold required">Address line 1</label>
                                                                        <input type="text" name="address_line_one" id="address_line_one" class="form-control" placeholder="Address line 1" value="{{ $accommodation['address_line_one'] }}" />
                                                                        <span class="text-danger error" id="address_line_one_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="postcode" class="fs-6 fw-bold required">Postcode</label>
                                                                        <input type="text" name="postcode" id="postcode" class="form-control" placeholder="Postcode" value="{{ $accommodation['postcode'] }}" />
                                                                        <span class="text-danger error" id="postcode_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="address_line_two" class="fs-6 fw-bold">Address line 2</label>
                                                                        <input type="text" name="address_line_two" id="address_line_two" class="form-control" placeholder="Address line 2" value="{{ $accommodation['address_line_two'] }}" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="country_option" class="fs-6 fw-bold">Country</label>
                                                                    <select name="country" id="country" class="form-select form-select-lg" data-control="select2" data-placeholder="Please select..." data-allow-clear="true">
                                                                        <option {{ ($accommodation['country'] == 'United Kingdom') ? 'selected' : '' }} value="United Kingdom">United Kingdom</option>
                                                                    </select>
                                                                    <span class="text-danger error" id="country_error"></span>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="city" class="fs-6 fw-bold required">City</label>
                                                                        <input type="text" name="city" id="city" class="form-control" placeholder="City" value="{{ $accommodation['city'] }}" />
                                                                        <span class="text-danger error" id="city_error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="what_three_words_locator" class="fs-6 fw-bold">
                                                                            <span class="required">What3Words locator</span>
                                                                            <a href="https://what3words.com/" target="_blank" id="go_to_w3_location">go to site <i class="las la-external-link-square-alt text-primary"></i></a>
                                                                        </label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text" id="basic-addon3">///</span>
                                                                            </div>
                                                                            <input type="text" class="form-control" name="what_three_words_locator" id="what_three_words_locator" aria-describedby="basic-addon3" placeholder="what.three.words" value="{{ $accommodation['what_three_words_locator'] }}">
                                                                            <div class="input-group-prepend" id="view_w3_location">
                                                                                <span class="input-group-text" id="basic-addon3"><a href="https://what3words.com/{{$accommodation['what_three_words_locator']}}" id="view_w3_location_href" target="_blank"><i class="las la-map fs-2"></i></a></span>
                                                                            </div>
                                                                        </div>
                                                                        <span class="text-danger error" id="what_three_words_locator_error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-5">
                                                                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">CONTACT DETAILS</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="contact_name" class="fs-6 fw-bold required">Contact name</label>
                                                                        <input type="text" name="contact_name" id="contact_name" class="form-control" placeholder="Jane Smith" value="{{ $accommodation['contact_name'] }}" />
                                                                        <span class="text-danger error" id="contact_name_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="contact_number" class="fs-6 fw-bold required">Contact Number</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text" id="basic-addon3"><i class="las la-mobile fs-xxl-2"></i></span>
                                                                            </div>
                                                                            <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="Telephone" value="{{ $accommodation['contact_number'] }}">
                                                                        </div>
                                                                        <span class="text-danger error" id="contact_number_error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()['user_type'] == 'Admin')
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <hr>
                                                                    </div>
                                                                    <div class="text-center mt-4">
                                                                        <input type="hidden" name="store_id" id="store_id" value="{{ $accommodation['id'] }}">
                                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBIcqb6PHyEOPWFQVJ_8-Z1V21dPfUhVCI&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
    <script src={{ asset('js/client/autocomplete_address.js') }}></script>
    <script>
        activeMenu('/accommodation-list');
        $("#header_additional_info").html("({{ $accommodation['name'] }})")

        $(document).ready(function () {
            what3WordChanges('{{ $accommodation['what_three_words_locator'] }}');
        })

        $("#what_three_words_locator").on('input', function () {
            what3WordChanges($(this).val());
        });

        function what3WordChanges(what3WordValue) {
            if (what3WordValue !== '') {
                $("#go_to_w3_location").addClass('d-none');
                $('#view_w3_location_href').attr('href', `https://what3words.com/${what3WordValue}`);
                $("#view_w3_location").removeClass('d-none');
            } else {
                $("#go_to_w3_location").removeClass('d-none');
                $("#view_w3_location").addClass('d-none');
            }
        }

        $("#update_accommodation_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-accommodation') }}',
                data        : new FormData($("#update_accommodation_form")[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);
                    if(response.code === 200) {
                        setTimeout(function() { window.location.href='{{ url('accommodation-list') }}'; }, 1500);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            })
        });

        document.addEventListener("DOMContentLoaded", function () {
            const userType = "{{ Auth::user()['user_type'] }}";
            if (userType === "Standard") {
                document.querySelectorAll("input, textarea").forEach(function(input) {
                    input.setAttribute("readonly", true);
                    if (input.type !== "checkbox") {
                        input.classList.add("bg-secondary", "text-gray-500");
                    }
                });

                document.querySelectorAll("input[type='checkbox']").forEach(function(checkbox) {
                    checkbox.disabled = true;
                });

                document.querySelectorAll(".select2").forEach(function(selectBox) {
                    const realSelect = $(selectBox).closest('.select2-container').prev('select');
                    if (realSelect.length) {
                        realSelect.prop('disabled', true).addClass("bg-secondary text-gray-500");
                    }
                    $(realSelect).select2("destroy");
                });
            }
        });
    </script>
@endsection
