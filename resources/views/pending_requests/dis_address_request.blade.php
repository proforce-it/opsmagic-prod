@extends('theme.page')

@section('title', 'Address Requests management')
@section('content')
    {{--content--}}<div class="d-flex flex-column flex-column-fluid" id="kt_content">

        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <!--begin::Card header-->
                                <div class="card-header border-0 pt-6">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <!--begin::Search-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <i class="fs-2 las la-search"></i>
                                            </span>
                                            <input type="text" data-kt-address-request-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search address requests" />
                                        </div>
                                        <!--end::Search-->
                                    </div>
                                </div>

                                <div class="card-body py-4">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="address_request_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>Associate Name</th>
                                            <th>Contact No.</th>
                                            <th>Generated at</th>
                                            <th>Details</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold"></tbody>
                                    </table>
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view_address_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>View <span id="worker_name"></span> addresses</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_address_modal_btn">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12 text-uppercase gs-0 border-bottom-dashed border-secondary border-1 mb-3">
                                <label class="fs-5 fw-bolder text-muted">Current details</label>
                            </div>
                            <div class="col-lg-12">
                                <div class="fv-row fv-plugins-icon-container">
                                    <label class="fs-5 fw-bold" id="current_address"></label>
                                    <label class="mt-4 fs-5 fw-bold" id="current_mobile_number"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-12 text-uppercase gs-0 border-bottom-dashed border-secondary border-1 mb-3">
                                <label class="fs-5 fw-bolder text-muted">Requested details</label>
                            </div>
                            <div class="col-lg-12">
                                <div class="fv-row fv-plugins-icon-container">
                                    <label class="fs-5 fw-bold" id="requested_address"></label>
                                    <label class="mt-4 fs-5 fw-bold" id="requested_mobile_number"></label>
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
    <script>
        let address_request_table;
        $(document).ready(function() {
            address_request_table = $('#address_request_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('address-request') }}',
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}';
                    },
                },
                "columns": [
                    {"data": "worker_name", "sClass":"text-left"},
                    {"data": "mobile_number", "width": "20%", "sClass":"text-center"},
                    {"data": "generated_at", "width": "20%", "sClass":"text-center"},
                    {"data": "details", "width": "5%", "sClass": "text-center"},
                    {"data": "action", "width": "10%", "sClass": "text-center"}
                ],
                "order": [[ 0, "desc" ]],
            });
        });

        const filterSearch = document.querySelector('[data-kt-address-request-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            address_request_table.search(e.target.value).draw();
        });

        $(document).on('click', '#approve_request', function () {
            sweetAlertApproved('You want to approved this address request!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('approved-pending-request') }}',
                        data    : {
                            _token : '{{ csrf_token() }}',
                            id : $(this).attr('data-id'),
                            type : 'worker_addresses'
                        },
                        success : function (response) {
                            decodeResponse(response)
                            if(response.code === 200) {
                                address_request_table.ajax.reload();
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $(document).on('click', '#declined_request', function () {
            sweetAlertUnapproved('You want to reject this address request!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('declined-pending-request') }}',
                        data    : {
                            _token : '{{ csrf_token() }}',
                            id : $(this).attr('data-id'),
                        },
                        success : function (response) {
                            decodeResponse(response)
                            if(response.code === 200) {
                                address_request_table.ajax.reload();
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });
        
        $(document).on('click', '#view_addresses', function () {
            $("#worker_name").text($(this).attr('data-worker_name'));
            $("#current_address").text($(this).attr('data-current_address'));
            $("#requested_address").text($(this).attr('data-requested_address'));

            let current_mobile_number = $(this).attr('data-current_mobile_number');
            let requested_mobile_number = $(this).attr('data-requested_mobile_number');
            if (current_mobile_number === requested_mobile_number) {
                $("#current_mobile_number").addClass('d-none');
                $("#requested_mobile_number").addClass('d-none');
            } else {
                $("#current_mobile_number").removeClass('d-none').text(current_mobile_number);
                $("#requested_mobile_number").removeClass('d-none').text(requested_mobile_number);
            }

            $("#view_address_modal").modal('show');
        });

        $("#cls_address_modal_btn").on('click', function (){
            $("#view_address_modal").modal('hide');
        });
    </script>
@endsection
