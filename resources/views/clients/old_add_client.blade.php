@extends('theme.page')

@section('title', 'Create client')
@section('content')
    <div {{--content --}} class="d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <div class="card-body">
                                    <div class="stepper stepper-links d-flex flex-column pt-15" id="kt_create_account_stepper">
                                        <div class="stepper-nav mb-5">
                                            <div class="stepper-item current" data-kt-stepper-element="nav">
                                                <h3 class="stepper-title">Basic details</h3>
                                            </div>
                                            <div class="stepper-item" data-kt-stepper-element="nav">
                                                <h3 class="stepper-title">Documents</h3>
                                            </div>
                                        </div>

                                        <!--begin::Form-->
                                        <form class="mx-auto mw-1000px w-100 pt-15 pb-10" novalidate="novalidate" id="create_client_form" enctype="multipart/form-data">
                                            <!--begin::Step 1-->
                                            <div class="current" data-kt-stepper-element="content">
                                                <!--begin::Wrapper-->
                                                <div class="w-100">
                                                    <div class="pb-10 pb-lg-5">
                                                        <h1 class="fw-bolder d-flex align-items-center text-dark">Basic Details</h1>
                                                    </div>
                                                    <div class="fv-row">
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                                                    <label for="company_name" class="fs-6 fw-bold required">Company name</label>
                                                                    <input type="text" name="company_name" id="company_name" class="form-control" placeholder="Company name" value="" />
                                                                    <span class="text-danger error" id="company_name_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="company_number" class="fs-6 fw-bold required">Company number</label>
                                                                    <input type="text" name="company_number" id="company_number" class="form-control" placeholder="Company number" value="" />
                                                                    <span class="text-danger error" id="company_number_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="vat_number" class="fs-6 fw-bold">VAT number</label>
                                                                    <input type="text" name="vat_number" id="vat_number" class="form-control" placeholder="VAT number" value="" />
                                                                    <span class="text-danger error" id="vat_number_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="address_line_one" class="fs-6 fw-bold required">Address line 1</label>
                                                                    <input type="text" name="address_line_one" id="address_line_one" class="form-control" placeholder="Address line 1" value="" />
                                                                    <span class="text-danger error" id="address_line_one_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="address_line_two" class="fs-6 fw-bold">Address line 2</label>
                                                                    <input type="text" name="address_line_two" id="address_line_two" class="form-control" placeholder="Address line 2" value="" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="city_town" class="fs-6 fw-bold required">City/Town</label>
                                                                    <input type="text" name="city_town" id="city_town" class="form-control" placeholder="City/Town" value="" />
                                                                    <span class="text-danger error" id="city_town_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="county" class="fs-6 fw-bold required">County</label>
                                                                    <input type="text" name="county" id="county" class="form-control" placeholder="County" value="" />
                                                                    <span class="text-danger error" id="county_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="postcode" class="fs-6 fw-bold required">Postcode</label>
                                                                    <input type="text" name="postcode" id="postcode" class="form-control" placeholder="Postcode" value="" />
                                                                    <span class="text-danger error" id="postcode_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="address_type" class="fs-6 fw-bold required">Address type</label>
                                                                    <select name="address_type" id="address_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Select address type" data-allow-clear="true">
                                                                        <option value="">Select address type</option>
                                                                        <option value="HQ address">This is an administrative/HQ address only</option>
                                                                        <option value="Site address">This address is also a work site</option>
                                                                    </select>
                                                                    <span class="text-danger error" id="address_type_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="company_telephone" class="fs-6 fw-bold required">Company telephone</label>
                                                                    <input type="text" name="company_telephone" id="company_telephone" class="form-control" placeholder="Company telephone" value="" />
                                                                    <span class="text-danger error" id="company_telephone_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="company_email" class="fs-6 fw-bold required">Company email</label>
                                                                    <input type="text" name="company_email" id="company_email" class="form-control" placeholder="Company email" value="" />
                                                                    <span class="text-danger error" id="company_email_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="company_logo" class="fs-6 fw-bold">Company logo</label>
                                                                    <input type="file" name="company_logo" id="company_logo" class="form-control" value=""/>
                                                                    <span class="text-danger error" id="company_logo_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="company_logo" class="fs-6 fw-bold required">Payroll week starts on</label>
                                                                    <select name="payroll_week_starts" id="payroll_week_starts" class="form-select form-select-lg" data-control="select2" data-placeholder="Select start day…" data-allow-clear="true">
                                                                        <option value="">Select start day…</option>
                                                                        <option value="monday">Monday</option>
                                                                        <option value="sunday">Sunday</option>
                                                                        <option value="tuesday">Tuesday</option>
                                                                        <option value="wednesday">Wednesday</option>
                                                                        <option value="thursday">Thursday</option>
                                                                        <option value="friday">Friday</option>
                                                                        <option value="saturday">Saturday</option>
                                                                    </select>
                                                                    <span class="text-danger error" id="payroll_week_starts_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Step 1-->

                                            <!--begin::Step 2-->
<!--                                            <div data-kt-stepper-element="content">
                                                &lt;!&ndash;begin::Wrapper&ndash;&gt;
                                                <div class="w-100">
                                                    <div class="pb-10 pb-lg-5">
                                                        <h1 class="fw-bolder text-dark">Location details</h1>
                                                    </div>
                                                    <div class="fv-row" id="location_section">
                                                        <div>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="location_name_1" class="fs-6 fw-bold required">Name</label>
                                                                        <input type="text" name="location_name[]" id="location_name_1" class="form-control" placeholder="Name" value="" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="location_address_line_one_1" class="fs-6 fw-bold required">Address line 1</label>
                                                                        <input type="text" name="location_address_line_one[]" id="location_address_line_one_1" class="form-control" placeholder="Address line 1" value="" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="location_address_line_two_1" class="fs-6 fw-bold">Address line 2</label>
                                                                        <input type="text" name="location_address_line_two[]" id="location_address_line_two_1" class="form-control" placeholder="Address line 2" value="" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="location_city_town_1" class="fs-6 fw-bold required">City/Town</label>
                                                                        <input type="text" name="location_city_town[]" id="location_city_town_1" class="form-control" placeholder="City/Town" value="" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="location_county_1" class="fs-6 fw-bold required">County</label>
                                                                        <input type="text" name="location_county[]" id="location_county_1" class="form-control" placeholder="County" value="" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="location_latitude_1" class="fs-6 fw-bold required">Latitude</label>
                                                                        <input type="text" name="location_latitude[]" id="location_latitude_1" class="form-control" placeholder="Latitude" readonly value="0" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="location_longitude_1" class="fs-6 fw-bold required">Longitude</label>
                                                                        <input type="text" name="location_longitude[]" id="location_longitude_1" class="form-control" placeholder="Longitude" readonly value="0" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                        </div>
                                                    </div>
                                                    <div class="fv-row">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <input type="hidden" name="total_location_section" id="total_location_section" value="1">
                                                                    <input type="button" name="new_location_row" id="new_location_row" class="btn btn-primary btn-sm" value="Add Row" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                &lt;!&ndash;end::Wrapper&ndash;&gt;
                                            </div>-->
                                            <!--end::Step 2-->

                                            <!--begin::Step 3-->
<!--                                            <div data-kt-stepper-element="content">
                                                &lt;!&ndash;begin::Wrapper&ndash;&gt;
                                                <div class="w-100">
                                                    <div class="fv-row">
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="contact_info_name" class="fs-6 fw-bold required">Name</label>
                                                                    <input type="text" name="contact_info_name" id="contact_info_name" class="form-control" placeholder="Name" value="" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="contact_info_surname" class="fs-6 fw-bold required">Surname</label>
                                                                    <input type="text" name="contact_info_surname" id="contact_info_surname" class="form-control" placeholder="Surname" value="" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="contact_info_email" class="fs-6 fw-bold required">Email</label>
                                                                    <input type="text" name="contact_info_email" id="contact_info_email" class="form-control" placeholder="Email" value="" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="contact_info_phone_number" class="fs-6 fw-bold required">Phone number</label>
                                                                    <input type="text" name="contact_info_phone_number" id="contact_info_phone_number" class="form-control" placeholder="Phone number" value="" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-8">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="contact_info_job_title" class="fs-6 fw-bold required">Job title</label>
                                                                    <input type="text" name="contact_info_job_title" id="contact_info_job_title" class="form-control" placeholder="Job title" value="" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                &lt;!&ndash;end::Wrapper&ndash;&gt;
                                            </div>-->
                                            <!--end::Step 3-->

                                            <!--begin::Step 4-->
                                            <div data-kt-stepper-element="content">
                                                <!--begin::Wrapper-->
                                                <div class="w-100">
                                                    <div class="pb-10 pb-lg-5"> <!--pb-10 pb-lg-15-->
                                                        <h1 class="fw-bolder text-dark">Document File</h1>
<!--                                                        <div class="text-muted fw-bold fs-6">Note : <span class="text-danger">PDF, JPG, JPEG, PNG</span> file will allow to upload & maximum file size is <span class="text-danger">10 MB</span>.</div>-->
                                                    </div>
                                                    <!--begin::Input group-->
                                                    <div class="fv-row">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <input type="file" name="document_file[]" id="document_1" class="form-control"  accept="image/jpg, image/png, image/jpeg, application/pdf"/>
                                                                    <label class="fw-bold text-muted">Note: Maximum file size is 10MB. PNG, JPG, JPEG and PDF files are supported.</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <input type="text" name="document_title[]" id="document_title_1" class="form-control border-primary" placeholder="Enter document title."/>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-1">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <input type="hidden" name="total_document_section" id="total_document_section" value="1">
                                                                    <!--                                                                    <input type="button" name="new_document_row" id="new_document_row" class="btn btn-primary btn-sm" value="Add" />-->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Step 4-->

                                            <!--begin::Actions-->
                                            <div class="d-flex flex-stack pt-15">
                                                <!--begin::Wrapper-->
                                                <div class="mr-2">
                                                    <button type="button" class="btn btn-lg btn-light-primary me-3" data-kt-stepper-action="previous">
                                                        <span class="svg-icon svg-icon-4 me-1">

                                                            <i class="fs-2 las la-long-arrow-alt-left"></i>
														</span>
                                                        Back
                                                    </button>
                                                </div>
                                                <!--end::Wrapper-->

                                                <!--begin::Wrapper-->
                                                <div>
                                                    <button type="submit" class="btn btn-lg btn-primary me-3" data-kt-stepper-action="submit">
                                                        <span class="indicator-label">Submit
                                                            <i class="fs-2 las la-long-arrow-alt-right"></i>
                                                        </span>
                                                        <span class="indicator-progress">Please wait...
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="next">Continue
                                                        <span class="svg-icon svg-icon-4 ms-1 me-0">
                                                            <i class="fs-2 las la-long-arrow-alt-right"></i>

														</span>
                                                    </button>
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
    </div>
@endsection

@section('js')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBIcqb6PHyEOPWFQVJ_8-Z1V21dPfUhVCI&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
    <script src={{ asset('js/client/autocomplete_address.js') }}></script>

    <script src={{ asset('js/client/client.js') }}></script>
    <script>
        activeMenu('/client-management');
        getLatLong();

        $("#new_location_row").on('click', function () {

            let section     = $("#total_location_section");
            let section_no  = section.val();

            if($("#location_name_"+section_no).val() !== '' && $("#location_address_line_one_"+section_no).val() !== '' && $("#location_city_town_"+section_no).val() !== '' && $("#location_county_"+section_no).val() !== ''  && $("#location_latitude_"+section_no).val() !== ''  && $("#location_longitude_"+section_no).val() !== '') {
                $.ajax({
                    type    : 'post',
                    url     : '{{ url('add-new-section-for-location') }}',
                    data    : {
                        _token  : '{{ csrf_token() }}',
                        total_location_section : section_no,
                    },
                    success : function(response) {
                        if(response.code === 200) {
                            $("#location_section").append(response.data.section)
                            section.val(response.data.count)
                            getLatLong();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (response) {
                        toastr.error(response.statusText);
                    }
                });
            } else {
                toastr.error('Please fill in the '+section_no+' location details section before adding a new section.');
            }
        });

        function getLatLong() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                toastr.error("Geolocation is not supported by this browser.")
            }
        }

        function showPosition(position) {
            let last_section = $("#total_location_section").val();
            $("#location_latitude_"+last_section).val(position.coords.latitude);
            $("#location_longitude_"+last_section).val(position.coords.longitude);
        }
    </script>
@endsection
