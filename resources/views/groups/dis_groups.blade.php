@extends('theme.page')

@section('title', 'Groups management')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">

        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            @if($teamId)
                                <div class="card mb-5">
                                    <div class="card-body p-4">
                                        <div class="alert alert-custom alert-info mb-0" role="alert">
                                            <div class="alert-text fs-4">
                                                <i class="las la-info-circle text-info fs-xl-2"></i> You can only see and manage groups that you have created
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header border-0 pt-6">
                                        <div class="card-title">
                                            <div class="d-flex align-items-center position-relative my-1">
                                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                    <i class="fs-2 las la-search"></i>
                                                </span>
                                                <input type="text" data-kt-group-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find lines" />
                                            </div>
                                        </div>

                                        <div class="card-toolbar">
                                            <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                                <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding active" data-kt-button="true">
                                                    <input class="btn-check group_status" type="radio" name="group_status" checked="checked" value="Active"/>
                                                    Active
                                                </label>
                                                <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                    <input class="btn-check group_status" type="radio" name="group_status" value="Archived" />
                                                    Archived
                                                </label>
                                                <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                    <input class="btn-check group_status" type="radio" name="group_status" value="All" />
                                                    All
                                                </label>
                                            </div>

                                            <div class="float-end">
                                                <a href="javascript:;" id="add_group_modal_btn">
                                                    <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body py-4">
                                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                        <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="group_datatable">
                                            <thead>
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                <th>Name</th>
                                                <th>Team</th>
                                                <th># active members</th>
                                                <th># No rtw</th>
                                                <th># leavers</th>
                                                <th># archived</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody class="text-gray-600 fw-bold"></tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="card mb-5">
                                    <div class="card-body p-4">
                                        <div class="alert alert-custom alert-info mb-0" role="alert">
                                            <div class="alert-text fs-4">
                                                <i class="las la-info-circle text-info fs-xl-2"></i> You are currently not assigned to any team.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($teamId)
        @include('groups.add_group_modal')
        @include('groups.edit_group_modal')
    @endif
@endsection

@section('js')
    <script>
        let group_table;
        $(document).ready(function() {
            group_table = $('#group_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-group') }}',
                    "data": function (d) {
                        d._token    = $('#_token').val();
                        d.status    = $('input[name="group_status"]:checked').val();
                    },
                },
                "columns": [
                    {"data": "name"},
                    {"data": "team_name"},
                    {"data": "active_members"},
                    {"data": "no_rtw"},
                    {"data": "leavers"},
                    {"data": "archived"},
                    {"data": "action", "width": "10%", "sClass": "text-end"}
                ],
                "order": [[ 0, "asc" ]],
            });
        });

        $(document).on('change', '.group_status', function () {
            group_table.ajax.reload();
        })

        const filterSearch = document.querySelector('[data-kt-group-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            group_table.search(e.target.value).draw();
        });

        $(document).on('click', '.archive_un_archived_group', function () {
            let status = $(this).attr('data-status');
            let sweetAlert;
            if(status === 'archived') {
                sweetAlert = sweetAlertArchived('You want to archive this group?');
            } else {
                sweetAlert = sweetAlertUnarchived('You want to un-archived this group?')
            }
            sweetAlert.then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('archived-un-archived-group-action') }}',
                        data    : {
                            _token  : $("#_token").val(),
                            id      : $(this).attr('data-group_id'),
                            status  : $(this).attr('data-status'),
                        },
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                group_table.ajax.reload();
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

    @yield('add_group_js')
    @yield('edit_group_js')
@endsection
