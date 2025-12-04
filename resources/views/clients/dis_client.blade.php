@extends('theme.page')
@section('title', 'Clients')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
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
                                            <input type="text" data-kt-client-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find client" />
                                        </div>
                                        <!--end::Search-->
                                    </div>
                                    <!--begin::Card title-->

                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                            <label class="btn btn-outline btn-light-primary btn-active-primary active status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check client_status" type="radio" name="client_status" checked="checked" value="Active"/>
                                                Active
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check client_status" type="radio" name="client_status" value="Prospect" />
                                                Prospect
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check client_status" type="radio" name="client_status" value="Archived" />
                                                Archived
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check client_status" type="radio" name="client_status" value="All" />
                                                All
                                            </label>
                                        </div>
                                        <div class="float-end">
                                            <a href="{{ url('create-client') }}" id="add_new_job">
                                                <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body py-4">
                                    <div class="border border-top-2"></div>
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <table class="table align-middle table-row-bordered fs-7 gy-3" id="datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>Logo</th>
                                            <th>Client Name</th>
                                            <th>Status</th>
                                            <th>No.Sites</th>
                                            <th>Flags</th>
                                            <th>Actions</th>
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
@endsection

@section('js')
    <script src="{{ asset('js/client/datatable.js') }}"></script>
@endsection
