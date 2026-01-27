@extends('theme.page')

@section('title', 'Absence Requests management')
@section('content')
    {{--content--}}<div class="d-flex flex-column flex-column-fluid" id="kt_content">

        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <div class="card-header border-0 pt-6">
                                    <div class="card-title">
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <i class="fs-2 las la-search"></i>
                                            </span>
                                            <input type="text" data-kt-absence-request-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search holiday requests" />
                                        </div>
                                    </div>
                                    <div class="card-toolbar">
                                        <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                            <label class="btn btn-outline btn-light-primary btn-active-primary active status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check absence_status" type="radio" name="absence_status" checked="checked" value="Active"/>
                                                Active
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check absence_status" type="radio" name="absence_status" value="Approved"/>
                                                Approved
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check absence_status" type="radio" name="absence_status" value="Rejected" />
                                                Rejected
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check absence_status" type="radio" name="absence_status" value="All" />
                                                All
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body py-4">
                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="absence_request_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>Associate Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Reason</th>
                                            <th>Generated at</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold"></tbody>
                                    </table>
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
        let absence_request_table;
        $(document).ready(function() {
            absence_request_table = $('#absence_request_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('absence-request') }}',
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}';
                        d.status    = $('input[name="absence_status"]:checked').val();
                    },
                },
                "columns": [
                    {"data": "worker_name", "sClass":"text-left"},
                    {"data": "start_date", "sClass":"text-center"},
                    {"data": "end_date", "sClass":"text-center"},
                    {"data": "reason", "sClass":"text-center"},
                    {"data": "generated_at", "sClass":"text-center"},
                    {"data": "action", "width": "10%", "sClass": "text-center"}
                ],
                "order": [[ 0, "desc" ]],
            });
        });

        const filterSearch = document.querySelector('[data-kt-absence-request-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            absence_request_table.search(e.target.value).draw();
        });

        $(document).on('change', '.absence_status', function () {
            absence_request_table.ajax.reload();
        })

        $(document).on('click', '#approve_request', function () {
            sweetAlertApproved('You want to approve this holiday request!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('approved-pending-request') }}',
                        data    : {
                            _token : '{{ csrf_token() }}',
                            id : $(this).attr('data-id'),
                            type : 'absence'
                        },
                        success : function (response) {
                            decodeResponse(response)
                            if(response.code === 200) {
                                absence_request_table.ajax.reload();
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
            sweetAlertUnapproved('You want to reject this holiday request!').then((result) => {
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
                                absence_request_table.ajax.reload();
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });
    </script>
@endsection
