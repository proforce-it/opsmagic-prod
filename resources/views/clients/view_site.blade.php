@extends('theme.page')

@php($title = 'Site - '.$site['site_name'])
@section('title', $title)
@section('content')
    <style>
        .text-center{
            white-space: nowrap;
        }
        .btn-light-primary-custom{
            background-color: #DAECF9;
        }

        .active {
            background-color: #FFF;
            color: #333;
        }
    </style>
    <div class="d-flex flex-column flex-column-fluid" id="kt_content"> <!--content -->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post"> <!--d-flex-->
                        <div id="kt_content_container" class=""> <!--container-xxl-->
                            <div class="card mb-5">
                                <div class="card-body py-4">
                                    <div class="row">
                                        <div class="col-8 d-flex align-items-center">
                                            <div class="fs-1 fw-bold text-uppercase">
                                                {{ $site['site_name'] }}
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            @if($site['client_details']['company_logo'])
                                                <img src="{{ asset('workers/client_document/'.$site['client_details']['company_logo']) }}" alt="No image." style="width: 300px; height: 100px; object-fit: contain; object-position: right;">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="nav ms-10">
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm active" data-bs-toggle="tab" href="#kt_table_widget_5_tab_1" id="site_info_button">Site info</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_2" id="map_and_direction">Map & Directions</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_7" data-note_type="site" id="notes_button">Notes</a>
                                </li>
                            </ul>
                            <div class="card mt-n1">
                                <!--begin::Card body-->
                                <div class="card-body py-4">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="kt_table_widget_5_tab_1">
                                            <div class="table-responsive">
                                                <form id="site_details_form">
                                                    @csrf
                                                    <div class="p-5">
                                                        <div class="row mb-5">
                                                            <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Site Info</div>
                                                        </div>
                                                        <div class="fv-row">
                                                            <div class="row mb-7">
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="site_name" class="fs-6 fw-bold required">Name</label>
                                                                        <input type="text" name="site_name" id="site_name" class="form-control" placeholder="Name" value="{{ $site['site_name'] }}" />
                                                                        <span class="text-danger error" id="site_name_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="site_description" class="fs-6 fw-bold">Description</label>
                                                                        <textarea name="site_description" id="site_description" class="form-control" placeholder="Description">{{ $site['site_description'] }}</textarea>
                                                                        <span class="text-danger error" id="site_description_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="cost_center" class="fs-6 fw-bold">Cost center</label> <!-- required-->
                                                                        <select name="cost_center" id="cost_center" class="form-select form-select-lg" data-control="select2" data-placeholder="Select cost center" data-allow-clear="true">
                                                                            <option {{ ($site['cost_center'] == '') ? 'selected' : '' }} value="">Select cost center</option>
                                                                            @if($costCentre)
                                                                                @foreach($costCentre as $cc_row)
                                                                                    <option value="{{ $cc_row['id'] }}" {{ ($site['cost_center'] == $cc_row['id']) ? 'selected' : '' }}>{{ $cc_row['short_code'] }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                        <span class="text-danger error" id="cost_center_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="site_address_line_one" class="fs-6 fw-bold required">Address line 1</label>
                                                                        <input type="text" name="site_address_line_one" id="site_address_line_one" class="form-control" placeholder="Address line 1" value="{{ $site['address_line_1'] }}" />
                                                                        <input type="hidden" name="site_address_latitude" id="site_address_latitude" value="{{ $site['site_address_latitude'] }}">
                                                                        <input type="hidden" name="site_address_longitude" id="site_address_longitude" value="{{ $site['site_address_longitude'] }}">
                                                                        <span class="text-danger error" id="site_address_line_one_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="site_address_line_two" class="fs-6 fw-bold">Address line 2</label>
                                                                        <input type="text" name="site_address_line_two" id="site_address_line_two" class="form-control" placeholder="Address line 2" value="{{ $site['address_line_2'] }}" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="site_city" class="fs-6 fw-bold required">City</label>
                                                                        <input type="text" name="site_city" id="site_city" class="form-control" placeholder="City" value="{{ $site['city'] }}" />
                                                                        <span class="text-danger error" id="site_city_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="site_country" class="fs-6 fw-bold required">County</label>
                                                                        <input type="text" name="site_country" id="site_country" class="form-control" placeholder="County" value="{{ $site['country'] }}" />
                                                                        <span class="text-danger error" id="site_country_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="site_postcode" class="fs-6 fw-bold">Postcode</label>
                                                                        <input type="text" name="site_postcode" id="site_postcode" class="form-control" placeholder="Postcode" value="{{ $site['postcode'] }}" />
                                                                        <span class="text-danger error" id="site_postcode_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="site_telephone" class="fs-6 fw-bold">Telephone</label>
                                                                        <input type="text" name="site_telephone" id="site_telephone" class="form-control" placeholder="Telephone" value="{{ $site['site_telephone'] }}" />
                                                                        <span class="text-danger error" id="site_telephone_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="what_three_words_locator" class="fs-6 fw-bold">What3words  <a href="https://what3words.com/" target="_blank" id="go_to_w3_location">go to site <i class="las la-external-link-square-alt text-primary"></i></a>
                                                                        </label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text" id="basic-addon3">///</span>
                                                                            </div>
                                                                            <input type="text" name="what_three_words_locator" id="what_three_words_locator" class="form-control" placeholder="pretty.needed.chill" value="{{ $site['what_three_words_address'] }}" />
                                                                            <div class="input-group-prepend" id="view_w3_location">
                                                                                <span class="input-group-text" id="basic-addon3"><a href="https://what3words.com/{{$site['what_three_words_address']}}" id="view_w3_location_href" target="_blank"><i class="las la-map fs-2"></i></a></span>
                                                                            </div>
                                                                        </div>
                                                                        <span class="text-danger error" id="what_three_words_locator_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="fv-row fv-plugins-icon-container">
                                                                        <div id="client_site_map"></div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="col-lg-12 text-center">
                                                                    <input type="hidden" name="site_client_id" id="site_client_id" value="{{ $site['client_id'] }}">
                                                                    <input type="hidden" name="site_id" id="site_id" value="{{ $site['id'] }}" />
                                                                    <button type="submit" name="site_form_submit" id="site_form_submit" class="btn btn-primary"><span id="site_submit_btn_text">Update</span></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_2">
                                            <!--begin::Table container-->
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row mb-5">
                                                        <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Map</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div id="site_map" style="height: 400px; width: 100%; margin-top: 10px;"></div>
                                                        </div>
                                                    </div>
                                                    <form id="site_direction_form">
                                                        @csrf
                                                        <div class="row mb-5 mt-10">
                                                            <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Direction info</div>
                                                        </div>
                                                        <div class="fv-row">
                                                            <div class="row mb-7">
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <textarea name="site_direction" id="site_direction" class="form-control" placeholder="Site direction" rows="10">{{ $site['site_direction'] }}</textarea>
                                                                        <span class="text-danger error" id="site_direction_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-1">
                                                                    <input type="hidden" name="site_id_for_direction" id="site_id_for_direction" value="{{ $site['id'] }}" />
                                                                    <button type="submit" name="site_form_submit" id="site_form_submit" class="btn btn-primary"><span id="site_submit_btn_text">Update</span></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <!--end::Table-->
                                        </div>

                                        @include('clients.partials.edit_client.notes_details')

                                    </div>
                                </div>
                                <!--end::Card body-->
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

    <script>
        $("#last_li_of_header_title").empty().append(`
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('view-client-details/'.$site['client_id']) }}"
                   class="text-muted text-hover-primary text-uppercase">
                    CLIENT DETAILS ({{ $site['client_details']['company_name'] }})
                </a>
            </li>
            <li class="breadcrumb-item text-gray-500">></li>
            <li class="breadcrumb-item text-dark">
                <span id="header_sub_title">SITE</span>
                <span id="header_additional_info" class="text-uppercase ms-1">
                    : {{ $site['site_name'] }}
                </span>
            </li>
        `);

        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', () => {
                localStorage.setItem('viewClientSiteActiveTab_'+'{{ $site['id'] }}', tab.id);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = localStorage.getItem('viewClientSiteActiveTab_'+'{{ $site['id'] }}');
            if (activeTab) {
                document.getElementById(activeTab).click();
            }
        });

        $("#action_id").val({{ $site['id'] }});
    </script>

    <script>
        activeMenu('/client-management');

        $("#site_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-client-site-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)
                    if(response.code === 200) {
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500)
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        /*--- BEGIN SITE DIRECTION ---*/
        function initMap() {
            let lat = '{{ $site['site_address_latitude'] }}';
            let lng = '{{ $site['site_address_longitude'] }}';

            const location = { lat: lat, lng: lng };

            const map = new google.maps.Map(document.getElementById("site_map"), {
                center: location,
                zoom: 15
            });

            new google.maps.Marker({
                position: location,
                map: map
            });
        }

        window.onload = () => {
            if (typeof google !== "undefined") {
                initMap();
            } else {
                console.error("Google Maps API failed to load.");
            }
        };

        $("#site_direction_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-site-direction-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
        /*--- END SITE DIRECTION ---*/
    </script>

    @yield('edit_client_note_js')
    <script src={{ asset('js/client/autocomplete_address.js') }}></script>

    <script>
        $(document).ready(function () {
            what3WordChanges('{{ $site['what_three_words_address'] }}');
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
    </script>
@endsection
