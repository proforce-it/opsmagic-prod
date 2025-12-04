@extends('theme.page')

@section('title', 'Teams management')
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
                                            <input type="text" data-kt-teams-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search teams" />
                                        </div>
                                        <!--end::Search-->
                                    </div>
                                    <!--begin::Card title-->

                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <div class="float-end">
                                            <a href="javascript:;" id="add_teams_modal_btn">
                                                <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>

                                <div class="card-body py-4">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="teams_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>Name</th>
                                            <th>Cost Centre</th>
                                            <th>Number of consultants</th>
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

    @include('groups.add_teams_modal')
    @include('groups.edit_teams_modal')
@endsection

@section('js')
    <script>
        let teams_table;

        $(document).ready(function() {
            teams_table = $('#teams_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-teams') }}',
                    "data": function (d) {
                        d._token    = $('#_token').val();
                    },
                },
                "columns": [
                    {"data": "name"},
                    {"data": "cost_centre_id", "width":"15%"},
                    {"data": "number_of_consultants", "width":"15%"},
                    {"data": "action", "width": "10%", "sClass": "text-end"}
                ],
                "order": [[ 0, "asc" ]],
            });
        });

        const filterSearch = document.querySelector('[data-kt-teams-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            teams_table.search(e.target.value).draw();
        });
    </script>

    @yield('add_teams_js')
    @yield('edit_teams_js')
@endsection
