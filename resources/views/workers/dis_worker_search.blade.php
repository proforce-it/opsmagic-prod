@extends('theme.page')

@section('title', 'Worker search')

@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <div class="card-body py-4">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="client" class="fs-6 fw-bold">Name / Email / ID</label>
                                                        <input type="text" class="form-control" name="name_email_id" id="name_email_id" value="" placeholder="Enter text">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2"></div>
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="cost_center" class="fs-6 fw-bold">Cost center</label>
                                                        <select name="cost_center[]" id="cost_center" class="form-select form-select-lg" data-control="select2" data-placeholder="Select cost center" data-allow-clear="true" multiple>
                                                            <option value=""></option>
                                                            @if($costCentre)
                                                                @foreach($costCentre as $cc_row)
                                                                    <option value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="status" class="fs-6 fw-bold">Status</label>
                                                        <select name="status" id="status" class="form-select form-select-lg" data-control="select2" data-placeholder="Select status" data-allow-clear="true">
                                                            <option value="Active">Active</option>
                                                            <option value="Archived">Archived</option>
                                                            <option value="Leaver">Leaver</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2"></div>
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="valid_right_to_work" class="fs-6 fw-bold">Valid right to work until at least...</label>
                                                        <div class="position-relative d-flex align-items-center">
                                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                <i class="fs-2 las la-calendar"></i>
                                                            </span>
                                                            <input class="form-control ps-12 flatpickr-input" placeholder="Select date" name="valid_right_to_work" id="valid_right_to_work" type="text" readonly="readonly" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row d-none" id="more_filter_section">
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="has_already_worked_for_client" class="fs-6 fw-bold">Has already worked for client</label>
                                                        <select name="has_already_worked_for_client" id="has_already_worked_for_client" class="form-select form-select-lg" data-control="select2" data-placeholder="Select status" data-allow-clear="true">
                                                            <option value="Any">Any</option>
                                                            @if($client)
                                                                @foreach($client as $c_row)
                                                                    <option value="{{ $c_row['id'] }}">{{ $c_row['company_name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2"></div>
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <label for="age_between_min" class="fs-6 fw-bold required">Age between</label>
                                                                <input type="text" name="age_between_min" id="age_between_min" class="form-control" placeholder="min" value="">
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label for="age_between_max" class="fs-6 fw-bold"></label>
                                                                <input type="text" name="age_between_max" id="age_between_max" class="form-control" placeholder="max" value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="has_already_worked_at_site" class="fs-6 fw-bold">Has already worked at site</label>
                                                        <select name="has_already_worked_at_site" id="has_already_worked_at_site" class="form-select form-select-lg" data-control="select2" data-placeholder="Select status" data-allow-clear="true">
                                                            <option value="Any">Any</option>
                                                            @if($site)
                                                                @foreach($site as $s_row)
                                                                    <option value="{{ $s_row['id'] }}">{{ $s_row['site_name'].' - '.$s_row['client_details']['company_name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2"></div>
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="gender" class="fs-6 fw-bold">Gender</label>
                                                        <select name="gender" id="gender" class="form-select form-select-lg" data-control="select2" data-placeholder="Select gender" data-allow-clear="true">
                                                            <option value="Any">Any</option>
                                                            <option value="Female">Female</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Prefer not to say">Prefer not to say</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="is_already_registered_on_job" class="fs-6 fw-bold">Is already registered on job</label>
                                                        <select name="is_already_registered_on_job" id="is_already_registered_on_job" class="form-select form-select-lg" data-control="select2" data-placeholder="Select status" data-allow-clear="true">
                                                            <option value="Any">Any</option>
                                                            @if($job)
                                                                @foreach($job as $j_row)
                                                                    <option value="{{ $j_row['id'] }}">{{ $j_row['name'].' - '.$j_row['client_details']['company_name'].' ('.$j_row['site_details']['site_name'].')' }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2"></div>
                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="right_to_work_type" class="fs-6 fw-bold">Right to work type</label>
                                                        <select name="right_to_work_type" id="right_to_work_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Select right to work" data-allow-clear="true">
                                                            <option value="">Select right to work type</option>
                                                            <option value="COA">COA</option>
                                                            <option value="Indefinite leave to remain.">Indefinite leave to remain.</option>
                                                            <option value="Pre-Settled Status">Pre-Settled Status</option>
                                                            <option value="Settled Status">Settled Status</option>
                                                            <option value="Tier 2 (skilled visa)">Tier 2 (skilled visa)</option>
                                                            <option value="Tier 4 (student visa)">Tier 4 (student visa)</option>
                                                            <option value="Tier 5 (Seasonal Worker Scheme)">Tier 5 (Seasonal Worker Scheme)</option>
                                                            <option value="Tier 5 (Seasonal Worker Scheme - Poultry)">Tier 5 (Seasonal Worker Scheme - Poultry)</option>
                                                            <option value="Timebound (BRP)">Timebound (BRP)</option>
                                                            <option value="UK Citizen">UK Citizen</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-5">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="last_shift" class="fs-6 fw-bold">Last shift</label>
                                                        <select name="last_shift" id="last_shift" class="form-select form-select-lg" data-control="select2" data-placeholder="Select status" data-allow-clear="true">
                                                            <option value="Any">Any</option>
                                                            <option value="1">< 1 weeks ago</option>
                                                            <option value="2">< 2 weeks ago</option>
                                                            <option value="3">< 3 weeks ago</option>
                                                            <option value="6">< 6 weeks ago</option>
                                                            <option value="12">< 12 weeks ago</option>
                                                            <option value="13">More than 12 weeks ago</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="javascript:;" class="more_filter text-gray-600 text-hover-primary fs-5" data-action="0">Show more filters...</a>
                                    <button type="button" class="float-end btn btn-primary ms-1" id="worker_filter_btn">Find workers</button>
                                    <button type="button" class="float-end btn btn-outline btn-outline-primary btn-active-color-gray-100 ms-1" onclick="location.reload()">Clear filter(s)</button>
                                    <button type="button" class="float-end btn btn-outline btn-outline-primary btn-active-color-gray-100" id="load_search_btn">
                                            <span class="svg-icon svg-icon-2 svg-icon-primary">
                                                <i class="fs-2 las la-search" style="color: #009ef7"></i>
                                            </span>
                                        Load search
                                    </button>
                                </div>
                            </div>

                            <div class="card mt-5 d-none" id="worker_table_section">
                                <div class="card-header border-0 pt-6">
                                    <div class="card-title">
                                       <span id="totalRecords" class="me-1">0</span> Workers matching current filters
                                        <input type="hidden" name="request_data" id="request_data" value="">
                                    </div>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body py-4">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="fv-row fv-plugins-icon-container">
                                                <label for="with_selected" class="fs-6 fw-bold">With selected</label>
                                                <div class="d-flex align-self-center">
                                                    <div class="flex-grow-1 me-3">
                                                        <select name="with_selected" id="with_selected" class="form-select" data-control="select2" data-placeholder="Choose..." data-hide-search="true">
                                                            <option value="">Choose...</option>
                                                            <option value="Add to a job">Add to a job</option>
                                                            <option value="add_to_existing_group">Add to existing group</option>
                                                            <option value="create_a_new_group">Create a new group</option>
                                                            <option value="Leaver">Set status to leaver</option>
                                                            <option value="Archived">Set status to archived</option>
                                                        </select>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-icon flex-shrink-0" id="with_selected_btn">Go</button>
                                                    <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="with_selected_process_btn" id="with_selected_process_btn" style="display: none">
                                                    <span>Please wait...
                                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                    </span>
                                                    </button>
                                                </div>
                                                <span class="text-danger error" id="with_selected_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href="javascript:;" class="btn btn-outline btn-outline-primary text-hover-gray-100 mt-6 ms-1 float-end" id="download_csv">
                                                <span class="svg-icon svg-icon-2 svg-icon-primary">
                                                    <i class="fs-2 las la-file-download" style="color: #009ef7"></i>
												</span>
                                                Download .csv
                                            </a>
                                            <a href="javascript:;" class="btn btn-outline btn-outline-primary text-hover-gray-100 mt-6 float-end" id="save_search">
                                                <span class="svg-icon svg-icon-2 svg-icon-primary">
                                                    <i class="fs-2 las la-save" style="color: #009ef7"></i>
                                                </span>
                                                Save search
                                            </a>
                                        </div>
                                    </div>
                                    <hr>

                                    <table class="table align-middle table-row-dashed fs-7 gy-3" id="worker_datatable">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>
                                                <label class="form-check form-check-inline me-5 is-invalid">
                                                    <input type="checkbox" class="form-check-input" name="worker_ids[]" id="worker_ids" value="All">
                                                </label>
                                            </th>
                                            <th>Worker name</th>
                                            <th>status</th>
                                            <th>RTW type</th>
                                            <th>RTW expires</th>
                                            <th>Mobile</th>
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

    <div class="modal fade" id="save_search_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Save current search filters</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_save_search_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y m-5">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="search_title" class="fs-6 fw-bold required">Give your search a memorable name</label>
                                    <input type="text" name="search_title" id="search_title" class="form-control" placeholder="Add a name" value="">
                                    <span class="text-danger error" id="search_title_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="button" name="save_search_submit_btn" id="save_search_submit_btn" class="btn btn-primary float-end">Save search</button>
                                <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="save_search_process_btn" id="save_search_process_btn" style="display: none">
                                        <span>Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="load_search_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Saved searches</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_lead_search_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y m-5">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table align-middle table-row-dashed fs-7 gy-3" id="worker_datatable">
                                    <thead>
                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                        <th>Name</th>
                                        <th style="width: 24%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        @if($searchData)
                                            @foreach($searchData as $ws_row)
                                                <tr id="tr_{{ $ws_row['id'] }}">
                                                    <td>{{ $ws_row['name'] }}</td>
                                                    <td>
                                                        <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" id="delete_worker_search_request" data-id="{{ $ws_row['id'] }}">
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black"></path>
                                                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black"></path>
                                                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black"></path>
                                                                </svg>
                                                            </span>
                                                        </a>

                                                        <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="load_saved_request_data" data-filter="{{ $ws_row['request_data'] }}">
                                                            <span class="svg-icon svg-icon-2">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black"></rect>
                                                                    <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_to_job_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Add <span class="selected_worker_count"></span> selected workers to a job</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_add_to_job_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y m-5">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12 mb-10">
                                    <span class="fs-4 fw-lighter"><span class="fw-bold text-danger">Please note:</span> If you add workers directly, you are responsible for them receiving and accepting the assignment schedule</span>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label for="client" class="fs-6 fw-bold">Client</label>
                                        <input type="hidden" name="_token" id="_token" value="{{ @csrf_token() }}">
                                        <select name="client" id="client" class="form-select form-select-lg" data-control="select2" data-placeholder="Select client" data-allow-clear="true">
                                            <option value=""></option>
                                            @if($client)
                                                @foreach($client as $row)
                                                    <option value="{{ $row['id'] }}">{{ $row['company_name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger error" id="client_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label for="site" class="fs-6 fw-bold">Site</label>
                                        <select name="site" id="site" class="form-select form-select-lg" data-control="select2" data-placeholder="Select a client first" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                        <span class="text-danger error" id="site_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label for="job" class="fs-6 fw-bold">Job</label>
                                        <select name="job" id="job" class="form-select form-select-lg" data-control="select2" data-placeholder="Select a site first" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                        <span class="text-danger error" id="job_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label for="invitation_type" class="fs-6 fw-bold"></label>
                                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-1 row-cols-xl-2 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                            <div class="col">
                                                <label class="d-flex text-start" data-kt-button="true">
                                                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                        <input class="form-check-input" type="radio" name="invitation_type" id="invitation_type_1" value="1" checked="checked">
                                                        </span>
                                                    <span class="ms-5">
                                                            <span class="fs-4 fw-bolder text-gray-800 d-block">Send invitation</span>
                                                        </span>
                                                </label>
                                            </div>

                                            <div class="col">
                                                <label class="d-flex text-start" data-kt-button="true">
                                                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                        <input class="form-check-input" type="radio" name="invitation_type" id="invitation_type_2" value="2">
                                                        </span>
                                                    <span class="ms-5">
                                                            <span class="fs-4 fw-bolder text-gray-800 d-block">Add directly</span>
                                                        </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" name="client_job_worker_form_submit" id="client_job_worker_form_submit" class="btn btn-primary float-end">Add worker</button>
                                <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="client_job_worker_form_process" id="client_job_worker_form_process" style="display: none">
                                        <span>Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="leaver_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Set <span class="selected_worker_count"></span> selected workers to a leaver</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="leaver_modal_close_btn">
                        <i class="fs-2 las la-times"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y m-5">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Leaving date</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <i class="fs-2 las la-calendar"></i>
                                            </span>
                                            <input class="form-control ps-12 flatpickr-input date_input" name="leaving_date" id="leaving_date" type="text" placeholder="Select leaving date">
                                        </div>
                                        <span class="error text-danger" id="leaving_date_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="button" name="leaver_form_submit_btn" id="leaver_form_submit_btn" class="btn btn-primary float-end">Submit</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="leaver_form_process_btn" id="leaver_form_process_btn">
                            <span>Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="existing_group_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Add <span class="selected_worker_count"></span> workers to existing group</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="existing_group_modal_close_btn">
                        <i class="fs-2 las la-times"></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label for="existing_group_name" class="fs-6 fw-bold">Select group</label>
                                        <select name="existing_group_name" id="existing_group_name" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                            <option value=""></option>
                                            @if($groups)
                                                @foreach($groups as $g_row)
                                                    <option value="{{ $g_row['id'] }}">{{ $g_row['name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger error" id="existing_group_name_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="button" name="existing_group_submit_btn" id="existing_group_submit_btn" class="btn btn-primary float-end">Add <span class="selected_worker_count"></span> workers to group</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="existing_group_process_btn" id="existing_group_process_btn">
                                <span>Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="create_group_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Create a new group</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="create_group_modal_close_btn">
                        <i class="fs-2 las la-times"></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Team</label>
                                        <select name="create_group_team" id="create_group_team" class="form-select form-select-lg" data-control="select2" data-placeholder="Select a team" data-allow-clear="true">
                                            <option value="">Select a team</option>
                                            @if($teams)
                                                @foreach($teams as $row)
                                                    <option value="{{$row['id']}}">{{$row['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error text-danger" id="create_group_team_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Group name</label>
                                        <input type="text" name="create_group_name" id="create_group_name" class="form-control" value="" placeholder="Enter group name">
                                        <span class="error text-danger" id="create_group_name_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" name="create_group_form_submit_btn" id="create_group_form_submit_btn" class="btn btn-primary float-end">Create group with <span class="selected_worker_count"></span> workers</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="create_group_form_process_btn" id="create_group_form_process_btn">
                                <span>Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $("#existing_group_name").select2({
                dropdownParent: $("#existing_group_modal")
            });

            $("#create_group_team").select2({
                dropdownParent: $("#create_group_modal")
            });
        });

        $("#valid_right_to_work").flatpickr({
            dateFormat  : "d-m-Y",
        });

        $(".more_filter").on('click', function (){
            if ($(this).attr('data-action') === '0') {
                $(this).attr('data-action', '1');
                $(this).text('Show fewer filters...');
                $("#more_filter_section").removeClass('d-none')
            } else {
                $(this).attr('data-action', '0');
                $(this).text('Show more filters...');
                $("#more_filter_section").addClass('d-none')
            }
        });

        let worker_datatable;
        let csvTableData;
        function createDataTable() {
            let workerDtaTableID = $('#worker_datatable');
            workerDtaTableID.DataTable().destroy();
            worker_datatable = workerDtaTableID.DataTable({
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('worker-search-action') }}',
                    "data": function (d) {
                        d._token = $('#_token').val();
                        d.filter_type = $(".more_filter").attr('data-action');
                        d.name_email_id = $('#name_email_id').val();
                        d.cost_center = $('#cost_center').val();
                        d.status = $('#status').val();
                        d.valid_right_to_work = $('#valid_right_to_work').val();
                        d.has_already_worked_for_client = $('#has_already_worked_for_client').val();
                        d.age_between_min = $('#age_between_min').val();
                        d.age_between_max = $('#age_between_max').val();
                        d.has_already_worked_at_site = $('#has_already_worked_at_site').val();
                        d.gender = $('#gender').val();
                        d.is_already_registered_on_job = $('#is_already_registered_on_job').val();
                        d.right_to_work_type = $('#right_to_work_type').val();
                        d.last_shift = $('#last_shift').val();
                    },
                },
                "columns": [
                    {"data": "checkbox", "width": "5%"},
                    {"data": "name"},
                    {"data": "status", "width": "7%"},
                    {"data": "rtw_type", "width": "15%"},
                    {"data": "rtw_expires", "width": "10%"},
                    {"data": "mobile", "width": "10%"},
                    {"data": "flags", "width": "15%"},
                    {"data": "actions", "width": "12%"}
                ],
                "order": [[ 1, "asc" ]],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],
                "drawCallback": function(settings) {
                    if (settings.json && settings.json.recordsFiltered !== undefined) {
                        $('#totalRecords').empty().append(settings.json.recordsFiltered);
                        $("#request_data").empty().val(settings.json.request_data);
                        csvTableData = settings.json.csvTableData
                    } else {
                        $('#totalRecords').empty().append('0');
                    }
                }
            });
        }

        $("#worker_filter_btn").on('click', function () {
            $("#worker_table_section").removeClass('d-none')
            createDataTable();
        });

        $("#save_search").on('click', function (){
            $("#search_title").val('');
            $("#save_search_modal").modal('show');
        });

        $("#cls_btn_save_search_modal").on('click', function (){
            $("#save_search_modal").modal('hide');
        });

        $("#save_search_submit_btn").on('click', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-worker-search-request') }}',
                data        : {
                    _token          : '{{ @csrf_token() }}',
                    search_title    : $("#search_title").val(),
                    request_data    : $("#request_data").val(),
                },
                success     : function (response) {
                    decodeResponse(response)
                    if(response.code === 200) {
                        $("#search_title").val('');
                        $("#save_search_modal").modal('hide');
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#load_search_btn").on('click', function () {
            $("#load_search_modal").modal('show');
        })

        $("#cls_btn_lead_search_modal").on('click', function (){
            $("#load_search_modal").modal('hide');
        });

        $(document).on('click', '#load_saved_request_data', function() {
            $("#load_search_modal").modal('hide');

            const jsonData = JSON.parse($(this).attr('data-filter'));
            $('#name_email_id').val(jsonData.name_email_id);
            $('#status').val(jsonData.status).trigger('change');
            $('#valid_right_to_work').val(jsonData.valid_right_to_work);
            $('#has_already_worked_for_client').val(jsonData.has_already_worked_for_client).trigger('change');
            $('#age_between_min').val(jsonData.age_between_min);
            $('#age_between_max').val(jsonData.age_between_max);
            $('#has_already_worked_at_site').val(jsonData.has_already_worked_at_site).trigger('change');
            $('#gender').val(jsonData.gender).trigger('change');
            $('#is_already_registered_on_job').val(jsonData.is_already_registered_on_job).trigger('change');
            $('#right_to_work_type').val(jsonData.right_to_work_type).trigger('change');
            $('#last_shift').val(jsonData.last_shift).trigger('change');

            if (jsonData.cost_center) {
                $('#cost_center').val(jsonData.cost_center).trigger('change');
            }

            let more_filter = $(".more_filter");
            if (jsonData.filter_type === '1') {
                more_filter.attr('data-action', '1');
                more_filter.text('Show fewer filters...');
                $("#more_filter_section").removeClass('d-none')
            } else {
                more_filter.attr('data-action', '0');
                more_filter.text('Show more filters...');
                $("#more_filter_section").addClass('d-none')
            }

            $("#worker_table_section").removeClass('d-none')
            createDataTable();
        });

        $(document).on('click', '#delete_worker_search_request', function () {
            let id = $(this).attr('data-id');
            sweetAlertConfirmDelete('You want to delete this request!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-worker-search-request-data') }}'+'/'+id,
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                $("#tr_"+id).remove();
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

        $('#download_csv').on('click', function() {
            let csv = csvTableData.map(row => row.join(',')).join('\n');
            let csvFile;
            let downloadLink;

            csvFile = new Blob([csv], { type: 'text/csv' });
            downloadLink = document.createElement('a');
            downloadLink.download = 'worker.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
        });

        $(document).ready(function() {
            $('#worker_ids').change(function() {
                let isChecked = $(this).is(':checked');
                $('.rowCheckbox').prop('checked', isChecked);
            });

            $(document).on('change', '.rowCheckbox', function() {
                let allChecked = $('.rowCheckbox:checked').length === $('.rowCheckbox').length;
                $('#worker_ids').prop('checked', allChecked);
            });
        });

        $("#with_selected_btn").on('click', function () {
            let drpValue = $("#with_selected").val();
            const checkedValues = $('.rowCheckbox:checked').map(function() {
                return this.value;
            }).get();

            if (checkedValues.length !== 0) {
                if(drpValue === '') {
                    toastr.error('Please select a dropdown action.');
                } else if(drpValue === 'Add to a job') {
                    $(".selected_worker_count").empty().append(checkedValues.length);
                    $("#add_to_job_modal").modal('show');
                } else if (drpValue === 'add_to_existing_group') {
                    $(".selected_worker_count").empty().append(checkedValues.length);
                    $("#existing_group_modal").modal('show');
                } else if (drpValue === 'create_a_new_group') {
                    $(".selected_worker_count").empty().append(checkedValues.length);
                    $("#create_group_modal").modal('show');
                } else if (drpValue === 'Leaver') {
                    $(".selected_worker_count").empty().append(checkedValues.length);
                    $("#add_leaver_date_form").trigger('reset');
                    $("#leaver_modal").modal('show');
                } else {
                    $("#with_selected_btn").hide();
                    $("#with_selected_process_btn").show();

                    $.ajax({
                        type        : 'post',
                        url         : '{{ url('worker-status-bulk-action') }}',
                        data        : {
                            _token     : '{{ @csrf_token() }}',
                            worker_ids : checkedValues,
                            status     : $("#with_selected").val()
                        },
                        success     : function (response) {
                            decodeResponse(response)

                            if(response.code === 200) {
                                $("#with_selected").val('').trigger('change');
                                createDataTable();
                            }

                            $("#with_selected_btn").show();
                            $("#with_selected_process_btn").hide();
                        },
                        error   : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            } else {
                toastr.error('Please selected a worker to perform action.')
            }
        });

        $("#cls_btn_add_to_job_modal").on('click', function (){
            $("#add_to_job_modal").modal('hide');
        });

        $("#client").on('change', function (){
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-site-using-client') }}',
                data    : {
                    _token    : '{{ csrf_token() }}',
                    client_id : $("#client").val(),
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#site").empty().append(response.data.site_option);
                        $("#job").empty();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#site").on('change', function (){
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-job-using-site') }}',
                data    : {
                    _token  : '{{ csrf_token() }}',
                    site_id : $("#site").val(),
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#job").empty().append(response.data.job_option);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#client_job_worker_form_submit").on('click', function () {
            $(".error").html('');

            let client = $("#client").val();
            let site = $("#site").val();
            let job = $("#job").val();

            if(client === '')
                $("#client_error").empty().append('The client field is required.');

            if(site === '')
                $("#site_error").empty().append('The site field is required.');

            if(job === '')
                $("#job_error").empty().append('The job field is required.');

            if(client !== '' && site !== '' && job !== '') {

                $("#client_job_worker_form_submit").hide();
                $("#client_job_worker_form_process").show();

                const worker_ids = $('.rowCheckbox:checked').map(function() {
                    return this.value;
                }).get();
                const invitation_type = $("input[name='invitation_type']:checked").val();

                $.ajax({
                    type        : 'post',
                    url         : '{{ url('store-client-job-worker-multiple') }}',
                    data        : {
                        _token          : '{{ @csrf_token() }}',
                        job_worker_name : worker_ids,
                        invitation_type : invitation_type,
                        job_worker_id   : job,
                    },
                    success     : function (response) {
                        decodeResponse(response)

                        $("#client_job_worker_form_submit").show();
                        $("#client_job_worker_form_process").hide();

                        if(response.code === 200) {
                            $("#with_selected").val('').trigger('change');
                            createDataTable();

                            $("#client").val('').trigger('change');
                            $("#site").val('').trigger('change');
                            $("#job").val('').trigger('change');

                            $("input[name='invitation_type']").first().prop("checked", true);
                            $("#add_to_job_modal").modal('hide');
                        }
                    },
                    error   : function (response) {
                        $("#client_job_worker_form_submit").show();
                        $("#client_job_worker_form_process").hide();

                        toastr.error(response.statusText);
                    }
                });
            }
        });

        $(document).on('click', '#archived_btn', function () {
            let worker_id = $(this).attr('data-worker-id');
            sweetAlertArchived('You want to archived this worker!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type        : 'post',
                        url         : '{{ url('worker-status-bulk-action') }}',
                        data        : {
                            _token     : '{{ @csrf_token() }}',
                            worker_ids : worker_id.split('').map(Number),
                            status     : 'Archived'
                        },
                        success     : function (response) {
                            decodeResponse(response)

                            if(response.code === 200) {
                                createDataTable();
                            }
                        },
                        error   : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        /*--- BEGIN WORKER LEAVING ---*/
        flatpickr('#leaving_date', {
            dateFormat: 'd-m-Y'
        });
        $("#leaver_modal_close_btn").on('click', function (){
            $("#leaver_modal").modal('hide');
        })
        $("#leaver_form_submit_btn").on('click', function () {

            $("#leaver_form_submit_btn").addClass('d-none');
            $("#leaver_form_process_btn").removeClass('d-none');

            const leaverWorkerId = $('.rowCheckbox:checked').map(function() {
                return this.value;
            }).get();

            $(".error").html('');
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-bulk-leaving-status') }}',
                data        : {
                    _token      : '{{ csrf_token() }}',
                    worker_id   : leaverWorkerId,
                    leaving_date: $("#leaving_date").val(),
                },
                success     : function (response) {
                    decodeResponse(response)

                    $("#leaver_form_submit_btn").removeClass('d-none');
                    $("#leaver_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $(".error").html('');
                        $("#add_leaver_date_form").trigger('reset');
                        $("#leaver_modal").modal('hide');
                        $("#with_selected").val('').trigger('change');
                        createDataTable();
                    }
                },
                error   : function (response) {
                    $("#leaver_form_submit_btn").removeClass('d-none');
                    $("#leaver_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
        /*--- END WORKER LEAVING ---*/

        /*--- BEGIN WORKER ADD IN EXISTING GROUP ---*/
        $("#existing_group_modal_close_btn").on('click', function () {
            $(".error").html('');
            $("#existing_group_name").val('').trigger('change');
            $("#existing_group_modal").modal('hide');
        });

        $("#existing_group_submit_btn").on('click', function () {

            $("#existing_group_submit_btn").addClass('d-none');
            $("#existing_group_process_btn").removeClass('d-none');

            const addExistingGroupWorkerId = $('.rowCheckbox:checked').map(function() {
                return this.value;
            }).get();

            $(".error").html('');
            $.ajax({
                type        : 'post',
                url         : '{{ url('add-worker-to-existing-group') }}',
                data        : {
                    _token      : '{{ csrf_token() }}',
                    worker_id   : addExistingGroupWorkerId,
                    existing_group_name  : $("#existing_group_name").val(),
                },
                success     : function (response) {
                    decodeResponse(response)

                    $("#existing_group_submit_btn").removeClass('d-none');
                    $("#existing_group_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#existing_group_modal_close_btn").click();
                        $("#with_selected").val('').trigger('change');
                        createDataTable();
                    }
                },
                error   : function (response) {
                    $("#existing_group_submit_btn").removeClass('d-none');
                    $("#existing_group_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
        /*--- END WORKER ADD IN EXISTING GROUP ---*/

        /*--- BEGIN WORKER ADD IN CREATING NEW GROUP ---*/
        $("#create_group_modal_close_btn").on('click', function () {
            $(".error").html('');
            $("#create_group_team").val('').trigger('change');
            $("#create_group_name").val('');
            $("#create_group_modal").modal('hide');
        });

        $("#create_group_form_submit_btn").on('click', function () {

            $("#create_group_form_submit_btn").addClass('d-none');
            $("#create_group_form_process_btn").removeClass('d-none');

            const addCreatedGroupWorkerId = $('.rowCheckbox:checked').map(function() {
                return this.value;
            }).get();

            $(".error").html('');
            $.ajax({
                type        : 'post',
                url         : '{{ url('add-worker-to-new-created-group') }}',
                data        : {
                    _token      : '{{ csrf_token() }}',
                    worker_id   : addCreatedGroupWorkerId,
                    create_group_team  : $("#create_group_team").val(),
                    create_group_name  : $("#create_group_name").val(),
                },
                success     : function (response) {
                    decodeResponse(response)

                    $("#create_group_form_submit_btn").removeClass('d-none');
                    $("#create_group_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#create_group_modal_close_btn").click();
                        $("#with_selected").val('').trigger('change');
                        createDataTable();
                    }
                },
                error   : function (response) {
                    $("#create_group_form_submit_btn").removeClass('d-none');
                    $("#create_group_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
        /*--- END WORKER ADD IN CREATING NEW GROUP ---*/
    </script>
@endsection
