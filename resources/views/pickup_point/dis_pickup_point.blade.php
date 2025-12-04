@extends('theme.page')
@section('title', 'Pick Up Point')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
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
                                            <input type="text" data-kt-pickup-point-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find pick up point" />
                                        </div>
                                    </div>
                                    <div class="card-toolbar">
                                        <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                            <label class="btn btn-outline btn-light-primary btn-active-primary active status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check pickup_point_status" type="radio" name="pickup_point_status" checked="checked" value="Active"/>
                                                Active
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check pickup_point_status" type="radio" name="pickup_point_status" value="Archived" />
                                                Archived
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check pickup_point_status" type="radio" name="pickup_point_status" value="All" />
                                                All
                                            </label>
                                        </div>
                                        <div class="float-end">
                                            @if (Auth::user()['user_type'] == 'Admin')
                                                <a href="{{ url('create-pick-up-point') }}" id="add_pickup-point">
                                                    <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body py-4">
                                    <div class="border border-top-2"></div>
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <table class="table align-middle table-row-bordered fs-7 gy-3" id="datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Address 1</th>
                                            <th>Cost center(s)</th>
                                            <th>WHAT3WORDS</th>
                                            <th>Actions</th>
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
        let table;
        $(document).ready(function() {
            table = $('#datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-pick-up-point') }}',
                    "data": function (d) {
                        d._token    = $('#_token').val();
                        d.status    = $('input[name="pickup_point_status"]:checked').val();
                    },
                },
                "columns": [
                    {"data": "id", "width": "5%"},
                    {"data": "name", "width": "10%"},
                    {"data": "address_line_one", "width": "20%"},
                    {"data": "cost_center"},
                    {"data": "what_three_words_locator", "width": "15%"},
                    {"data": "action", "width": "10%", "sClass": "text-end"}
                ],
                "order": [[ 0, "desc" ]],
            });
        });

        $(document).on('change', '.pickup_point_status', function () {
            table.ajax.reload();
        })

        const filterSearch = document.querySelector('[data-kt-pickup-point-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            table.search(e.target.value).draw();
        });

        $(document).on('click', '#archive_pickup_point', function () {
            let id = $(this).attr('data-id');
            sweetAlertArchived('You want to archive this pick up point!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('archive-pick-up-point') }}',
                        data    : {
                            _token : $("#_token").val(),
                            id : id,
                            status: 'Archived'
                        },
                        success : function (response) {
                            decodeResponse(response);
                            if(response.code === 200) {
                                table.ajax.reload();
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $(document).on('click', '#un_archive_pickup_point', function () {
            let id = $(this).attr('data-id');
            sweetAlertUnarchived('You want to un-archive this pick up point!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('archive-pick-up-point') }}',
                        data    : {
                            _token : $("#_token").val(),
                            id : id,
                            status: 'Active'
                        },
                        success : function (response) {
                            decodeResponse(response);
                            if(response.code === 200) {
                                table.ajax.reload();
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
