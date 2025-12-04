<div class="tab-pane fade" id="kt_table_widget_5_tab_3">
    <!--begin::Table container-->
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="w-100">
                    <div class="row mb-5">
                        <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">PRIMARY CLIENT CONTACT</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <form>
                                <div class="fv-row fv-plugins-icon-container align-items-center d-flex">
                                    <div class="flex-grow-1">
                                        <select name="primary_contact" id="primary_contact" class="form-select form-select-lg" data-control="select2" data-placeholder="Select primary contact" data-allow-clear="true">
                                            @if (!empty($primaryContact))
                                                @foreach($primaryContact as $contactData)
                                                    <option value="{{$contactData->id}}" {{ ($contactData['primary_contact'] == 1) ? 'selected' : '' }} >{{$contactData->first_name}} {{$contactData->last_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger error" id="primary_contact_error"></span>
                                    </div>
                                    <div class="ms-3">
                                        <button class="btn btn-primary" type="button" id="update_primary_contact_btn">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row mb-5 mt-15">
                        <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">ALL CONTACTS</div>
                        <div class="col-lg-12">
                            <div class="card-header border-0 p-0">
                                <div class="card-title">
                                    <div class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <i class="fs-2 las la-search"></i>
                                        </span>
                                        <input type="text" data-kt-client-contact-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find contact" />
                                    </div>
                                </div>
                                <div class="card-toolbar">
                                    <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                        <label class="btn btn-outline btn-light-primary btn-active-primary active status-radio-btn-padding" data-kt-button="true">
                                            <input class="btn-check contact_status" type="radio" name="contact_status" checked="checked" value="0"/>
                                            Active
                                        </label>
                                        <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                            <input class="btn-check contact_status" type="radio" name="contact_status" value="1" />
                                            Archived
                                        </label>
                                        <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                            <input class="btn-check contact_status" type="radio" name="contact_status" value="All" />
                                            All
                                        </label>
                                    </div>
                                    <div class="float-end">
                                        <a href="javascript:;" id="add_new_contact">
                                            <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                        </a>
                                        <a href="javascript:;" class="close_contact_form d-none" id="close_contact_form">
                                            <i class="fs-xxl-2qx las la-times-circle text-primary"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="contact_form_section" style="display: none">
                        <div class="w-100 mt-5 mb-5 border-bottom border-4"></div>
                        <div class="col-lg-12 border border-1 p-5 border-dark rounded-3">
                            <form id="contact_details_form">
                                @csrf
                                <div class="fv-row">
                                    <div class="row mb-7">
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="contact_first_name" class="fs-6 fw-bold required">First name</label>
                                                <input type="text" name="contact_first_name" id="contact_first_name" class="form-control" placeholder="First name" value="" />
                                                <span class="text-danger error" id="contact_first_name_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="contact_last_name" class="fs-6 fw-bold required">Last name</label>
                                                <input type="text" name="contact_last_name" id="contact_last_name" class="form-control" placeholder="Last name" value="" />
                                                <span class="text-danger error" id="contact_last_name_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="contact_telephone_one" class="fs-6 fw-bold required">Telephone 1</label>
                                                <input type="text" name="contact_telephone_one" id="contact_telephone_one" class="form-control" placeholder="Telephone 1" value="" />
                                                <span class="text-danger error" id="contact_telephone_one_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="contact_telephone_two" class="fs-6 fw-bold">Telephone 2</label>
                                                <input type="text" name="contact_telephone_two" id="contact_telephone_two" class="form-control" placeholder="Telephone 2" value="" />
                                                <span class="text-danger error" id="contact_telephone_two_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="contact_email" class="fs-6 fw-bold required">Email</label>
                                                <input type="text" name="contact_email" id="contact_email" class="form-control" placeholder="Email" value="" />
                                                <span class="text-danger error" id="contact_email_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="contact_site" class="required fs-6 fw-bold">Site(s)</label>
                                                <div class="d-flex align-items-center" id="site_checkbox_section"></div>
                                                <span class="text-danger error" id="contact_site_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <input type="hidden" name="contact_id" id="contact_id" value="0">
                                            <input type="hidden" name="contact_client_id" id="contact_client_id" value="{{ $client['id'] }}">
                                            <button type="submit" name="contact_form_submit" id="contact_form_submit" class="btn btn-primary float-end"><span id="contact_submit_btn_text">Add contact</span></button>
                                            <button type="reset" name="contact_form_cancel_btn" class="btn btn-dark float-end me-1 close_contact_form">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="w-100 mb-5 border-bottom border-dashed border-1"></div>
                <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="contact_datatable">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th>Name</th>
                        <th>Telephone 1</th>
                        <th>Telephone 2</th>
                        <th>Email</th>
                        <th>Site(s)</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold"></tbody>
                </table>
            </div>
        </div>
    </div>
    <!--end::Table-->
</div>

@section('edit_client_contacts_detail_js')
    <script>
        $("#add_new_contact").on('click', function () {

            $.ajax({
                type        : 'post',
                url         : '{{ url('get-site-for-contact') }}',
                data        : {
                    _token      : $("#_token").val(),
                    client_id   : '{{ $client['id'] }}',
                },
                success     : function (response) {
                    if(response.code === 200) {
                        let section  = $("#contact_form_section");

                        $(".error").html('');
                        $("#contact_details_form").trigger('reset');
                        section.slideUp(600);


                        $("#contact_submit_btn_text").empty().append('Add contact')
                        $("#site_checkbox_section").empty().append(response.data.site_checkbox)
                        section.slideDown(600);

                        $("#add_new_contact").addClass('d-none');
                        $("#close_contact_form").removeClass('d-none');
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(document).on('click', '.close_contact_form', function (){
            $("#contact_form_section").slideUp(600);
            $("#add_new_contact").removeClass('d-none');
            $("#close_contact_form").addClass('d-none');
        })

        let tableNameContactDatatable = $('#contact_datatable')
        let contact_table = tableNameContactDatatable.DataTable();

        $(document).on('click', '#Contacts_button', function () {
            contact_table.destroy()
            contact_table = tableNameContactDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-client-contact') }}',
                    "data": function (d){
                        d._token      = "{{ csrf_token() }}";
                        d.client_id   = '{{ $client['id'] }}';
                        d.status = $('input[name="contact_status"]:checked').val()
                    }
                },
                "columns": [
                    {"data": "name", "width": "15%"},
                    {"data": "primary_contact_number", "width": "10%"},
                    {"data": "secondary_contact_number", "width": "10%"},
                    {"data": "email", "width": "5%"},
                    {"data": "site"},
                    {"data": "action", "sClass": "text-center", "width": "10%"}
                ]
            });
        } );

        const client_contact_filterSearch = document.querySelector('[data-kt-client-contact-table-filter="search"]');
        client_contact_filterSearch.addEventListener('keyup', function (e) {
            contact_table.search(e.target.value).draw();
        });

        $("#contact_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $.ajax({
                type        : 'post',
                url         : '{{ url('store-client-contact-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)
                    if(response.code === 200) {
                        contact_table.ajax.reload();
                        $("#contact_details_form").trigger('reset');
                        $("#site_checkbox_section").empty();
                        $("#close_contact_form").click();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(document).on('click', '#delete_client_contact', function () {
            let id = $(this).attr('data-contact_id');
            sweetAlertArchived('You want to archive this contact!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-client-contact-action') }}'+'/'+id,
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                contact_table.ajax.reload();
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

        $("#update_primary_contact_btn").on('click', function (){

            let primaryContactId = $('#primary_contact').val();

            $.ajax({
                type        : 'post',
                url         : '{{ url('update-primary-contact') }}',
                data        : {
                    _token      : $("#_token").val(),
                    client_id   : '{{ $client['id'] }}',
                    primary_contact_id: primaryContactId,

                },
                success     : function (response) {
                    if(response.code === 200) {
                        toastr.success(response.message);
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        })
        $(document).on('change', '.contact_status', function () {
            contact_table.ajax.reload();
        })


    </script>
@endsection
