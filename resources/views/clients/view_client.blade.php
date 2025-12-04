@extends('theme.page')

@php
    $title = 'Client details - '.$client['company_name']
@endphp
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

        .card-shadow {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
        }
        .loader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 160px;
            background-color: #F5F8FA;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .loader {
            border: 4px solid #909FAF;
            border-top: 8px solid #019EF7;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loader-text {
            margin-top: 15px;
            font-size: 18px;
            color: #333;
        }
    </style>
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">

        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post"> <!--d-flex-->
                        <div id="kt_content_container" class=""> <!--container-xxl-->
                            <div class="card mb-5">
                                <div class="card-body py-4">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <div class="fs-1 fw-bold text-uppercase">
                                                {{ $client['company_name']}}
                                            </div>
                                            <div class="d-flex flex-wrap">
                                                {!! $flags !!}
                                            </div>
                                        </div>
                                        <div class="col-5 d-flex justify-content-end">
                                            <div class="btn-group me-3" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                                <label class="btn btn-outline btn-light-success btn-active-success status-radio-btn-padding
                                                    {{ ($client['status'] == 'Prospect' && !$client['deleted_at']) ? 'active' : '' }}
                                                    {{ ($client['status'] == 'Active') ? 'disabled' : '' }}" data-kt-button="true">
                                                    <input class="btn-check client_status_update" type="radio" name="client_status"
                                                           {{ ($client['status'] == 'Prospect' && !$client['deleted_at']) ? 'checked' : '' }}
                                                           value="Prospect"/>
                                                    Prospect
                                                </label>
                                                <label class="btn btn-outline btn-light-success btn-active-success status-radio-btn-padding
                                                        {{ ($client['status'] == 'Active' && !$client['deleted_at']) ? 'active' : '' }}
                                                        {{ ($flags != '') ? 'disabled' : '' }}" data-kt-button="true">
                                                    <input class="btn-check client_status_update" type="radio" name="client_status"
                                                           {{ ($client['status'] == 'Active' && !$client['deleted_at']) ? 'checked' : '' }}
                                                           {{ ($flags != '') ? 'readonly' : '' }}
                                                           value="Active" />
                                                    Active
                                                </label>
                                                <label class="btn btn-outline btn-light-success btn-active-success status-radio-btn-padding
                                                    {{ ($client['deleted_at']) ? 'active' : '' }}" data-kt-button="true">
                                                    <input class="btn-check client_status_update" type="radio" name="client_status"
                                                           {{ ($client['deleted_at']) ? 'checked' : '' }}
                                                           value="Archived" />
                                                    Archived
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3" id="client_pic" style="border-radius: 3px; padding: 4px; border: 1px solid #999;">
                                            @if($client['company_logo'])
                                                <img src="{{ asset('workers/client_document/'.$client['company_logo']) }}" alt="No image." class="w-200px h-75px" style="object-fit: contain; object-position: center; margin-left: auto; display: block; margin-right: auto;">
                                            @else
                                                <div>
                                                    <i class="fs-xxl-2hx las la-industry bg-gray-200 rounded-3 p-2"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('clients.partials.edit_client.client_tabs')
                            <div class="card mt-n1">
                                <div class="card-body">
                                    <div class="tab-content">
                                        @include('clients.partials.edit_client.dashboard')

                                        @include('clients.partials.edit_client.basic_details')

                                        @include('clients.partials.edit_client.pay_details')

                                        @include('clients.partials.edit_client.site_details')

                                        @include('clients.partials.edit_client.contacts_details')

                                        @include('clients.partials.edit_client.job_details')

                                        @include('clients.partials.edit_client.document_details')

                                        @include('clients.partials.edit_client.notes_details')

                                        @include('clients.partials.edit_client.log_details')
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

    @include('clients.partials.edit_client.pic_modal')
@endsection

@section('js')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBIcqb6PHyEOPWFQVJ_8-Z1V21dPfUhVCI&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
    <script src={{ asset('js/client/autocomplete_address.js') }}></script>
    <script>
        activeMenu('/client-management');

        $("#header_additional_info").empty().append('({{ $client['company_name'] }})');

        const tabKey = 'viewClientActiveTab_' + '{{ $client["id"] }}';

        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', () => {
                localStorage.setItem(tabKey, tab.id);
            });
        });

        window.addEventListener('DOMContentLoaded', () => {
            let activeTabId = localStorage.getItem(tabKey);

            if (!activeTabId) {
                activeTabId = 'basic_details_button';
                localStorage.setItem(tabKey, activeTabId);
            }

            const tab = document.getElementById(activeTabId);
            if (tab) {
                tab.click();
            }
        });

        $("#action_id").val({{ $client['id'] }});

        $(document).on('click', '.client_status_update', function () {
            $.ajax({
                type : 'post',
                url : '{{ url('update-client-status') }}',
                data : {
                    _token : '{{ csrf_token() }}',
                    client_id : '{{ $client['id'] }}',
                    status : $('input[name="client_status"]:checked').val(),
                },
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
        })
    </script>

    @yield('edit_client_basic_details_js')
    @yield('edit_client_sites_detail_js')
    @yield('edit_client_contacts_detail_js')
    @yield('edit_client_jobs_detail_js')
    @yield('edit_client_documents_detail_js')
    @yield('edit_client_pic_js')
    @yield('edit_client_note_js')
    @yield('edit_client_dashboard_js')
    @yield('edit_client_pay_detail_js')

    <script>
        //getLatLong();

        /*--- BEGIN BASIC DETAILS SECTION JS ---*/
        /*function getLatLong() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                toastr.error("Geolocation is not supported by this browser.")
            }
        }

        function showPosition(position) {
            $("#location_latitude").val(position.coords.latitude);
            $("#location_longitude").val(position.coords.longitude);
        }*/
        /*--- END BASIC DETAILS SECTION JS ---*/

        /*$("#new_location_row").on('click', function () {

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
        });*/
    </script>
    <script src="{{ asset('js/activity/datatable.js') }}"></script>
@endsection
