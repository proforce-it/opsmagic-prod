<div class="tab-pane fade" id="kt_table_widget_5_tab_2">
    <!--begin::Table container-->
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="w-100">
                    <div class="row">
                        @if(!$client['client_site_details'])
                            <div class="col-lg-12" id="site_warning_alert">
                                <div class="alert alert-custom alert-warning" role="alert">
                                    <div class="alert-text fs-4">
                                        <i class="las la-exclamation-triangle text-warning fs-xl-2"></i>
                                        At least one work site must be created before <strong>{{ $client['company_name'] }}</strong> can be made active (and jobs and workers assigned)
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-12">
                            <div class="card-header border-0 p-0">
                                <div class="card-title">
                                    <div class="d-flex align-items-center position-relative my-1">
                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                        <i class="fs-2 las la-search"></i>
                                    </span>
                                        <input type="text" data-kt-client-site-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find site" />
                                    </div>
                                </div>
                                <div class="card-toolbar">
                                    <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                        <label class="btn btn-outline btn-light-primary btn-active-primary active status-radio-btn-padding" data-kt-button="true">
                                            <input class="btn-check site_status" type="radio" name="site_status" checked="checked" value="0"/>
                                            Active
                                        </label>
                                        <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                            <input class="btn-check site_status" type="radio" name="site_status" value="1" />
                                            Archived
                                        </label>
                                        <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                            <input class="btn-check site_status" type="radio" name="site_status" value="All" />
                                            All
                                        </label>
                                    </div>
                                    <div class="float-end">
                                        <a href="javascript:;" id="add_new_site">
                                            <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                        </a>
                                        <a href="javascript:;" class="close_site_form d-none" id="close_site_form">
                                            <i class="fs-xxl-2qx las la-times-circle text-primary"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="site_form_section" style="display: none">
                        <div class="w-100 mt-5 mb-5 border-bottom border-4"></div>
                        <div class="col-lg-12 border border-1 p-5 border-dark rounded-3">
                            <form id="site_details_form">
                                @csrf
                                <div class="fv-row">
                                    <div class="row mb-7">
                                        <div class="col-lg-12">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="site_name" class="fs-6 fw-bold required">Name</label>
                                                <input type="text" name="site_name" id="site_name" class="form-control" placeholder="Name" value="" />
                                                <span class="text-danger error" id="site_name_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="site_description" class="fs-6 fw-bold">Description</label>
                                                <textarea name="site_description" id="site_description" class="form-control" placeholder="Description"></textarea>
                                                <span class="text-danger error" id="site_description_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="cost_center" class="fs-6 fw-bold">Cost center</label> <!-- required-->
                                                <select name="cost_center" id="cost_center" class="form-select form-select-lg" data-control="select2" data-placeholder="Select cost center" data-allow-clear="true">
                                                    <option value="">Select cost center</option>
                                                    @if($costCentre)
                                                        @foreach($costCentre as $cc_row)
                                                            <option value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span class="text-danger error" id="cost_center_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="site_address_line_one" class="fs-6 fw-bold required">Address line 1</label>
                                                <input type="text" name="site_address_line_one" id="site_address_line_one" class="form-control" placeholder="Address line 1" value="" />
                                                <input type="hidden" name="site_address_latitude" id="site_address_latitude" value="">
                                                <input type="hidden" name="site_address_longitude" id="site_address_longitude" value="">
                                                <span class="text-danger error" id="site_address_line_one_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="site_address_line_two" class="fs-6 fw-bold">Address line 2</label>
                                                <input type="text" name="site_address_line_two" id="site_address_line_two" class="form-control" placeholder="Address line 2" value="" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="site_city" class="fs-6 fw-bold required">City</label>
                                                <input type="text" name="site_city" id="site_city" class="form-control" placeholder="City" value="" />
                                                <span class="text-danger error" id="site_city_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="site_country" class="fs-6 fw-bold required">County</label>
                                                <input type="text" name="site_country" id="site_country" class="form-control" placeholder="County" value="" />
                                                <span class="text-danger error" id="site_country_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="site_postcode" class="fs-6 fw-bold">Postcode</label>
                                                <input type="text" name="site_postcode" id="site_postcode" class="form-control" placeholder="Postcode" value="" />
                                                <span class="text-danger error" id="site_postcode_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="site_telephone" class="fs-6 fw-bold">Telephone</label>
                                                <input type="text" name="site_telephone" id="site_telephone" class="form-control" placeholder="Telephone" value="" />
                                                <span class="text-danger error" id="site_telephone_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="what_three_words_locator" class="fs-6 fw-bold">What3words <!--required-->
                                                <a href="https://what3words.com/" target="_blank" id="go_to_w3_site">go to site <i class="las la-external-link-square-alt text-primary"></i></a></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon3">///</span>
                                                    </div>
                                                    <input type="text" name="what_three_words_locator" id="what_three_words_locator" class="form-control" placeholder="what.three.words" value="" />
                                                </div>
                                                <span class="text-danger error" id="what_three_words_locator_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="fv-row fv-plugins-icon-container">
                                                <div id="client_site_map"></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <input type="hidden" name="site_id" id="site_id" value="0">
                                            <input type="hidden" name="site_client_id" id="site_client_id" value="{{ $client['id'] }}">
                                            <input type="hidden" name="site_latitude" id="location_latitude" value="0" />
                                            <input type="hidden" name="site_longitude" id="location_longitude" value="0" />
                                            <button type="submit" name="site_form_submit" id="site_form_submit" class="btn btn-primary float-end"><span id="site_submit_btn_text">Add site</span></button>
                                            <button type="reset" name="site_form_cancel_btn" class="btn btn-dark float-end me-1 close_site_form">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="w-100 mt-5 mb-5 border-bottom border-dashed border-1"></div>
                <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="site_datatable">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th style="width: 20%">Site name</th>
                        <th>Location</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 10%">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold"></tbody>
                </table>
            </div>
        </div>
    </div>
    <!--end::Table-->
</div>

@section('edit_client_sites_detail_js')
    <script>
        $("#add_new_site").on('click', function () {
            let section  = $("#site_form_section");

            $(".error").html('');
            $("#site_details_form").trigger('reset');
            $("#cost_center").val('').trigger('change');
            section.slideUp(600);

            $("#site_submit_btn_text").empty().append('Add site')
            section.slideDown(600);

            $("#add_new_site").addClass('d-none');
            $("#close_site_form").removeClass('d-none');
        });

        $(document).on('click', '.close_site_form', function (){
            $('#site_form_section').slideUp(600);
            $("#add_new_site").removeClass('d-none');
            $("#close_site_form").addClass('d-none');
        })

        let tableNameSiteDatatable = $('#site_datatable');
        let site_table = tableNameSiteDatatable.DataTable();
        $(document).on('click', '#Sites_button', function () {
            site_table.destroy();
            site_table = tableNameSiteDatatable.DataTable({
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-client-site') }}',
                    "data": function (d){
                        d._token      = "{{ csrf_token() }}";
                        d.client_id   = $("#site_client_id").val();
                        d.status = $('input[name="site_status"]:checked').val()
                    }
                },
                "columns": [
                    {"data": "site_name", "width": "20%"},
                    {"data": "location"},
                    {"data": "status", "width": "10%"},
                    {"data": "action", "sClass": "text-center", "width": "10%"}
                ]
            });
        } );

        const client_site_filterSearch = document.querySelector('[data-kt-client-site-table-filter="search"]');
        client_site_filterSearch.addEventListener('keyup', function (e) {
            site_table.search(e.target.value).draw();
        });

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
                        site_table.ajax.reload();
                        $("#site_details_form").trigger('reset');
                        $("#cost_center").val('').trigger('change');
                        $('.close_site_form').click();

                        $("#site_warning_alert").addClass('d-none');
                        $("#header_site_flag").addClass('d-none');
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(document).on('click', '#delete_client_site', function () {
            let id = $(this).attr('data-site_id');
            sweetAlertArchived('You want to archive this site!').then((result) => {
                if (result.value) {

                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-client-site-action') }}'+'/'+id,
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                site_table.ajax.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $(document).on('change', '.site_status', function () {
            site_table.ajax.reload();
        })
    </script>
@endsection
