@extends('theme.page')

@section('title', 'Workers')
@section('content')

<!--    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <div class="card-header pt-10">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-circle me-5">
                                            <div class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
                                                <span class="svg-icon svg-icon-2x svg-icon-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"/>
                                                            <path d="M5,4 L19,4 C19.2761424,4 19.5,4.22385763 19.5,4.5 C19.5,4.60818511 19.4649111,4.71345191 19.4,4.8 L14,12 L14,20.190983 C14,20.4671254 13.7761424,20.690983 13.5,20.690983 C13.4223775,20.690983 13.3458209,20.6729105 13.2763932,20.6381966 L10,19 L10,12 L4.6,4.8 C4.43431458,4.5790861 4.4790861,4.26568542 4.7,4.1 C4.78654809,4.03508894 4.89181489,4 5,4 Z" fill="#000000"/>
                                                        </g>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h2 class="mb-1">Search Worker</h2>
                                            <div class="text-muted fw-bolder">
                                                Filter your data</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body py-4">
                                    <div class="current" data-kt-stepper-element="content">
                                        <div class="w-100">
                                            <div class="fv-row">
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Filter by first name" value="" />
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <input type="text" name="surname" id="surname" class="form-control" placeholder="Filter by surname" value="" />
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <input type="text" name="mobile_number" id="mobile_number" class="form-control" placeholder="Filter by mobile number" value="" />
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <input type="text" name="email" id="email" class="form-control" placeholder="Filter by email" value="" />
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <input type="text" name="worker_no" id="worker_no" class="form-control" placeholder="Filter by worker unique number" value="" />
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <select name="status" id="status" class="form-select form-select-lg" data-control="select2" data-placeholder="Select status" data-allow-clear="true" data-dropdown_type="status">
                                                                <option value="">Select status</option>
                                                                <option value="Active">Active</option>
                                                                <option value="Leaver">Leaver</option>
                                                                <option value="Archived">Archived</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex" data-kt-user-table-toolbar="base">
                                        <button type="reset" class="btn btn-primary mr-2" id="filter_worker_button">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M5,4 L19,4 C19.2761424,4 19.5,4.22385763 19.5,4.5 C19.5,4.60818511 19.4649111,4.71345191 19.4,4.8 L14,12 L14,20.190983 C14,20.4671254 13.7761424,20.690983 13.5,20.690983 C13.4223775,20.690983 13.3458209,20.6729105 13.2763932,20.6381966 L10,19 L10,12 L4.6,4.8 C4.43431458,4.5790861 4.4790861,4.26568542 4.7,4.1 C4.78654809,4.03508894 4.89181489,4 5,4 Z" fill="#000000"/>
                                                    </g>
                                                </svg>
                                            </span>
                                            Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>-->

    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">

                            @if($filter != '')
                                <div class="alert alert-custom alert-warning" role="alert">
                                    <div class="alert-text fw-boldest">
                                        <span class="svg-icon svg-icon-warning svg-icon-2x">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"/>
                                                    <path d="M11.1669899,4.49941818 L2.82535718,19.5143571 C2.557144,19.9971408 2.7310878,20.6059441 3.21387153,20.8741573 C3.36242953,20.9566895 3.52957021,21 3.69951446,21 L21.2169432,21 C21.7692279,21 22.2169432,20.5522847 22.2169432,20 C22.2169432,19.8159952 22.1661743,19.6355579 22.070225,19.47855 L12.894429,4.4636111 C12.6064401,3.99235656 11.9909517,3.84379039 11.5196972,4.13177928 C11.3723594,4.22181902 11.2508468,4.34847583 11.1669899,4.49941818 Z" fill="#000000" opacity="0.3"/>
                                                    <rect fill="#000000" x="11" y="9" width="2" height="7" rx="1"/>
                                                    <rect fill="#000000" x="11" y="17" width="2" height="2" rx="1"/>
                                                </g>
                                            </svg>
                                        </span>
                                        Showing: {{ ucfirst(str_replace('-', ' ', $filter)) }}.
                                        <a href="{{ url('worker-management') }}" class="text-warning float-end fw-boldest mt-1 align-middle">Clear</a>
                                    </div>
                                </div>
                            @endif

                            <div class="card">
                                <!--begin::Card header-->
                                <div class="card-header border-0 pt-6">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <!--begin::Search-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
{{--                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />--}}
{{--                                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />--}}
{{--                                                </svg>--}}
                                                <i class="fs-2 las la-search"></i>

                                            </span>
                                            <input type="text" data-kt-worker-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search worker" />
                                        </div>
                                        <!--end::Search-->
                                    </div>
                                    <!--begin::Card title-->

                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check worker_status" type="radio" name="worker_status" value="Prospect" />
                                                Prospect
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary active status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check worker_status" type="radio" name="worker_status" checked="checked" value="Active"/>
                                                Active
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check worker_status" type="radio" name="worker_status" value="Leaver"/>
                                                Leaver
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check worker_status" type="radio" name="worker_status" value="Archived" />
                                                Archived
                                            </label>
                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                <input class="btn-check worker_status" type="radio" name="worker_status" value="All" />
                                                All
                                            </label>
                                        </div>
                                        <div class="float-end">
                                            <a href="{{ url('create-worker') }}" id="add_new_worker">
                                                <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                            </a>
                                        </div>

                                        <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                                            <div class="fw-bolder me-5">
                                                <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
                                            <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
                                        </div>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body py-4">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="filter" id="filter" value="{{ $filter }}">
                                    <input type="hidden" name="cost_center" id="cost_center" value="{{ $cost_center_val }}">
                                    <table class="table align-middle table-row-dashed fs-7 gy-3" id="datatable">
                                        <thead>
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                <th>Worker ID</th>
                                                <th>Worker's name</th>
                                                <th>status</th>
                                                <th>RTW Expires</th>
                                                <th>Mobile number</th>
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
    <script src="{{ asset('js/worker/datatable.js') }}"></script>
@endsection
