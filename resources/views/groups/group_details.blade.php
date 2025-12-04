@extends('theme.page')

@section('title', 'Group workers management')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">

        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card mb-5">
                                <div class="card-body py-4">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-4 col-sm-4 text-center">
                                            <div class="fs-1 fw-bold align-middle d-flex align-items-center gap-2">
                                                <i class="fs-xxl-3x las la-users text-dark"></i>
                                                {{ $group['name'] }}
                                            </div>
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
                                            <input type="text" data-kt-group-worker-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find worker" />
                                        </div>
                                    </div>

                                    <div class="card-toolbar">
                                        <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                            <label class="btn btn-outline btn-light-primary btn-active-primary active status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check group_worker_status" type="radio" name="group_worker_status" checked="checked" value="Active"/>
                                                Active
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check group_worker_status" type="radio" name="group_worker_status" value="Leaver"/>
                                                Leaver
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check group_worker_status" type="radio" name="group_worker_status" value="Archived" />
                                                Archived
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check group_worker_status" type="radio" name="group_worker_status" value="All" />
                                                All
                                            </label>
                                        </div>

                                        <div class="float-end">
                                            <a href="javascript:;" id="add_group_worker_modal_btn">
                                                <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body py-4">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="group_worker_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>Worker Name</th>
                                            <th>Worker status</th>
                                            <th>RTW expires</th>
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

    @include('groups.add_group_workers_modal')
@endsection

@section('js')
    <script>
        $("#header_sub_title").empty().append('{{ $group['name'] }}');

        let group_worker_datatable;
        $(document).ready(function() {
            group_worker_datatable = $('#group_worker_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-group-workers') }}',
                    "data": function (d) {
                        d._token    = $('#_token').val();
                        d.group_id  = '{{ $group['id'] }}';
                        d.status    = $('input[name="group_worker_status"]:checked').val();
                    },
                },
                "columns": [
                    {"data": "worker_name"},
                    {"data": "worker_status"},
                    {"data": "rtw_expires"},
                    {"data": "actions", "width": "10%", "sClass": "text-end"}
                ],
                "order": [[ 0, "asc" ]],
            });
        });

        $(document).on('change', '.group_worker_status', function () {
            group_worker_datatable.ajax.reload();
        })

        const filterSearch = document.querySelector('[data-kt-group-worker-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            group_worker_datatable.search(e.target.value).draw();
        });

        $(document).on('click', '.unlink_group', function () {
            sweetAlertConfirmDelete('You want to unlink worker to this group?').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('unlink-group-with-worker') }}'+'/'+$(this).attr('data-group-with-worker_id'),
                        data    : {
                            _token: '{{ csrf_token() }}'
                        },
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                group_worker_datatable.ajax.reload();
                                setTimeout(function () {
                                    location.reload();
                                }, 1500)
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

    @yield('add_group_worker_js')
@endsection
