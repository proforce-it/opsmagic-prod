@extends('theme.page')

@section('title', 'Add Pick up point site')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="mb-10">
                                <div class="bg-gray-500 p-10 fs-3 text-white rounded" id="screen_title">
                                    <h4 class="mb-0 fw-boldest text-white fs-1">Add Pick up point site</h4>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form class="w-100 pb-10" novalidate="novalidate" id="create_pickup_point_form">
                                        <div class="table-responsive">
                                            <div class="p-5">
                                                <div class="current" data-kt-stepper-element="content">
                                                    <div class="w-100">
                                                        <div class="fv-row">
                                                            <div class="row mb-5">
                                                                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">BASIC DETAILS</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                                                        <label for="name" class="fs-6 fw-bold required">Name</label>
                                                                        <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="" />
                                                                        <span class="text-danger error" id="name_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="description" class="fs-6 fw-bold required">Description</label>
                                                                        <textarea name="description" id="description" rows="3" class="form-control" placeholder="Description"></textarea>
                                                                        <span class="text-danger error" id="description_error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-5">
                                                                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">COST CENTRE(S) <span class="text-danger">*</span></div>
                                                                <div class="alert alert-custom alert-primary mt-5" role="alert">
                                                                    <div class="alert-text fs-4">
                                                                        <i class="las la-info-circle text-primary fs-xl-2"></i> Tick the cost centre(s) that workers travelling from this pick up are most likely to work within
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                                                            @if($costCentre)
                                                                                @foreach($costCentre as $center)
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="checkbox" name="cost_center[]" value="{{ $center['id'] }}" id="cc_{{ $center['id'] }}">
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
                                                                        <input type="text" name="address_line_one" id="address_line_one" class="form-control" placeholder="Address line 1" value="" />
                                                                        <span class="text-danger error" id="address_line_one_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="postcode" class="fs-6 fw-bold required">Postcode</label>
                                                                        <input type="text" name="postcode" id="postcode" class="form-control" placeholder="Postcode" value="" />
                                                                        <span class="text-danger error" id="postcode_error"></span>
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
                                                                        <option value="United Kingdom">United Kingdom</option>
                                                                    </select>
                                                                    <span class="text-danger error" id="country_error"></span>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="city" class="fs-6 fw-bold required">City</label>
                                                                        <input type="text" name="city" id="city" class="form-control" placeholder="City" value="" />
                                                                        <span class="text-danger error" id="city_error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="what_three_words_locator" class="fs-6 fw-bold">
                                                                            <span class="required">What3Words locator</span>
                                                                            <a href="https://what3words.com/" target="_blank">go to site <i class="las la-external-link-square-alt text-primary"></i></a>
                                                                        </label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text" id="basic-addon3">///</span>
                                                                            </div>
                                                                            <input type="text" class="form-control" name="what_three_words_locator" id="what_three_words_locator" aria-describedby="basic-addon3" placeholder="what.three.words">
                                                                        </div>
                                                                        <span class="text-danger error" id="what_three_words_locator_error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="text-center mt-4">
                                                                    <input type="hidden" name="store_id" id="store_id" value="0">
                                                                    <button type="submit" class="btn btn-primary">Add</button>
                                                                </div>
                                                            </div>
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
        activeMenu('/pick-up-point-list');
        $("#create_pickup_point_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-pick-up-point') }}',
                data        : new FormData($("#create_pickup_point_form")[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);
                    if(response.code === 200) {
                        setTimeout(function() { window.location.href='{{ url('pick-up-point-list') }}'; }, 1500);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            })
        });
    </script>
@endsection
