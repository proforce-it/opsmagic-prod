@extends('theme.page')

@section('title', 'Cost centres management')
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
                                            <input type="text" data-kt-cost-centre-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search cost centre" />
                                        </div>
                                        <!--end::Search-->
                                    </div>
                                    <!--begin::Card title-->

                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding active" data-kt-button="true">
                                                <input class="btn-check cost_centre_status" type="radio" name="cost_centre_status" checked="checked" value="Active"/>
                                                Active
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check cost_centre_status" type="radio" name="cost_centre_status" value="Archived" />
                                                Archived
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check cost_centre_status" type="radio" name="cost_centre_status" value="All" />
                                                All
                                            </label>
                                        </div>

                                        <div class="float-end">
                                            <a href="javascript:;" id="add_cost_centre_modal_btn">
                                                <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>

                                <div class="card-body py-4">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="cost_centre_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>Name</th>
                                            <th>Short code</th>
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

    @include('groups.add_cost_centre_modal')
    @include('groups.edit_cost_centre_modal')
@endsection

@section('js')
    <script>
        let cost_centre_table;

        $(document).ready(function() {
            cost_centre_table = $('#cost_centre_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-cost-centre') }}',
                    "data": function (d) {
                        d._token    = $('#_token').val();
                        d.status    = $('input[name="cost_centre_status"]:checked').val();
                    },
                },
                "columns": [
                    {"data": "name"},
                    {"data": "short_code", "width":"15%"},
                    {"data": "action", "width": "10%", "sClass": "text-end"}
                ],
                "order": [[ 0, "asc" ]],
            });
        });

        $(document).on('change', '.cost_centre_status', function () {
            cost_centre_table.ajax.reload();
        })

        const filterSearch = document.querySelector('[data-kt-cost-centre-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            cost_centre_table.search(e.target.value).draw();
        });

        $(document).on('click', '.archive_un_archived_cost_centre', function () {
            let status = $(this).attr('data-status');
            let sweetAlert;
            if(status === 'archived') {
                sweetAlert = sweetAlertArchived('You want to archive this cost centre?');
            } else {
                sweetAlert = sweetAlertUnarchived('You want to un-archived this cost centre?')
            }
            sweetAlert.then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('archived-un-archived-cost-centre-action') }}',
                        data    : {
                            _token  : $("#_token").val(),
                            id      : $(this).attr('data-cost-centre_id'),
                            status  : $(this).attr('data-status'),
                        },
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                cost_centre_table.ajax.reload();
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
    </script>

    @yield('add_cost_centre_js')
    @yield('edit_cost_centre_js')
@endsection
