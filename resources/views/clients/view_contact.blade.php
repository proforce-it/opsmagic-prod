@extends('theme.page')

@php($title = 'Contact - '.$contact['first_name'].' '.$contact['last_name'])
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
                                                {{ $contact['first_name'].' '.$contact['last_name'] }}
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            @if($contact['client_details']['company_logo'])
                                                <img src="{{ asset('workers/client_document/'.$contact['client_details']['company_logo']) }}" alt="No image." style="width: 300px; height: 100px; object-fit: contain; object-position: right;">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="nav ms-10">
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm active" data-bs-toggle="tab" href="#kt_table_widget_5_tab_1" id="site_info_button">Contact info</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_7" data-note_type="contact" id="notes_button">Notes</a>
                                </li>
                            </ul>
                            <div class="card mt-n1">
                                <!--begin::Card body-->
                                <div class="card-body py-4">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="kt_table_widget_5_tab_1">
                                            <div class="table-responsive">
                                                <form id="contact_details_form">
                                                    @csrf
                                                    <div class="p-5">
                                                        <div class="fv-row">
                                                            <div class="row mb-7">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="contact_first_name" class="fs-6 fw-bold required">First name</label>
                                                                        <input type="text" name="contact_first_name" id="contact_first_name" class="form-control" placeholder="First name" value="{{ $contact['first_name'] }}" />
                                                                        <span class="text-danger error" id="contact_first_name_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="contact_last_name" class="fs-6 fw-bold required">Last name</label>
                                                                        <input type="text" name="contact_last_name" id="contact_last_name" class="form-control" placeholder="Last name" value="{{ $contact['last_name'] }}" />
                                                                        <span class="text-danger error" id="contact_last_name_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="contact_telephone_one" class="fs-6 fw-bold required">Telephone 1</label>
                                                                        <input type="text" name="contact_telephone_one" id="contact_telephone_one" class="form-control" placeholder="Telephone 1" value="{{ $contact['primary_contact_number'] }}" />
                                                                        <span class="text-danger error" id="contact_telephone_one_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="contact_telephone_two" class="fs-6 fw-bold">Telephone 2</label>
                                                                        <input type="text" name="contact_telephone_two" id="contact_telephone_two" class="form-control" placeholder="Telephone 2" value="{{ $contact['secondary_contact_number'] }}" />
                                                                        <span class="text-danger error" id="contact_telephone_two_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="contact_email" class="fs-6 fw-bold required">Email</label>
                                                                        <input type="text" name="contact_email" id="contact_email" class="form-control" placeholder="Email" value="{{ $contact['email'] }}" />
                                                                        <span class="text-danger error" id="contact_email_error"></span>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="contact_site" class="required fs-6 fw-bold">Site(s)</label>
                                                                        <div class="d-flex align-items-center" id="site_checkbox_section">{!! $site_checkbox !!}</div>
                                                                        <span class="text-danger error" id="contact_site_error"></span>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="col-lg-12 text-center">
                                                                    <input type="hidden" name="contact_id" id="contact_id" value="{{ $contact['id'] }}">
                                                                    <input type="hidden" name="contact_client_id" id="contact_client_id" value="{{ $contact['client_id'] }}">
                                                                    <button type="submit" name="contact_form_submit" id="contact_form_submit" class="btn btn-primary"><span id="contact_submit_btn_text">Update contact</span></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
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
    <script>
        $("#last_li_of_header_title").empty().append(`
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('view-client-details/'.$contact['client_id']) }}"
                   class="text-muted text-hover-primary text-uppercase">
                    CLIENT DETAILS ({{ $contact['client_details']['company_name'] }})
                </a>
            </li>
            <li class="breadcrumb-item text-gray-500">></li>
            <li class="breadcrumb-item text-dark">
                <span id="header_sub_title">CONTACT</span>
                <span id="header_additional_info" class="text-uppercase ms-1">
                    : {{ $contact['first_name'] }} {{ $contact['last_name'] }}
                </span>
            </li>
        `);

        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', () => {
                localStorage.setItem('viewClientContactActiveTab_'+'{{ $contact['id'] }}', tab.id);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = localStorage.getItem('viewClientContactActiveTab_'+'{{ $contact['id'] }}');
            if (activeTab) {
                document.getElementById(activeTab).click();
            }
        });

        $("#action_id").val({{ $contact['id'] }});
    </script>

    <script>
        activeMenu('/client-management');

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
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
    </script>

    @yield('edit_client_note_js')

@endsection
