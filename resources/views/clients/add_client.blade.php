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
                            <div class="mb-10">
                                <div class="bg-gray-500 p-10 fs-3 text-white rounded" id="screen_title">
                                    <div class="small text-uppercase mb-1">ADD NEW CLIENT &gt; STEP 1 OF 1</div>
                                    <h4 class="mb-0 fw-boldest text-white fs-1">Client details</h4>
                                </div>
                                <div class="bg-success p-10 fw-boldest fs-1 text-white rounded d-none" id="client_created_message">
                                    Client created!
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form class="w-100 pt-5" novalidate="novalidate" id="create_client_form" enctype="multipart/form-data">
                                        <div class="table-responsive">
                                            <div class="p-5">
                                                <div class="current" data-kt-stepper-element="content">
                                                    <div class="w-100">
                                                        @include('clients.partials.add_client.basic_info')
                                                        @include('clients.partials.add_client.client_created')
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
        activeMenu('/client-management');

        $("#create_client_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#add_client_submit_btn").addClass('d-none');
            $("#add_client_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('store-client') }}',
                data        : new FormData($("#create_client_form")[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    $("#add_client_submit_btn").removeClass('d-none');
                    $("#add_client_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#basic_details_tab").addClass('d-none');
                        $("#client_created_tab").removeClass('d-none');
                        $("#screen_title").addClass('d-none');
                        $("#client_created_message").removeClass('d-none');

                        $("#created_client_name").text(response.data.client_name);
                        $("#created_client_edit_a").attr('href', `view-client-details/${response.data.client_id}`);
                    }

                    decodeResponse(response);
                },
                error   : function (response) {
                    $("#add_client_submit_btn").removeClass('d-none');
                    $("#add_client_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            })
        });
    </script>
@endsection
