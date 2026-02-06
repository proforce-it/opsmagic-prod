@extends('theme.page')
@php($view_type = \Illuminate\Support\Facades\Request::input('view_type'))
@php($title = 'Job - '.$job['name'].' (ID '.$job['id'].')')
@section('title', $title)
@section('content')
    <style>
        .text-center{
            white-space: nowrap;
        }
        .btn-light-primary-custom{
            background-color: #DAECF9;
        }

        .active {
            background-color: #FFF;
            color: #333;
        }
        .mid-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 120px;
        }
        .mid-btn p{
            color: #181c32;
        }
        .alert-text a{
            color: #665000;
            text-decoration: underline;
        }
        .alert-text{
            font-size: 15px;
        }
    </style>
<!--    <style>
        .calendar-table {
            table-layout: fixed;
        }
        .calendar-table th {
            border: 1px solid #ddd;
            text-align: center;
            font-size: 18px;
        }
        .calendar-table td {
            border: 1px solid #ddd;
        }
    </style>-->
    <div class="d-flex flex-column flex-column-fluid" id="kt_content"> <!--content -->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post"> <!--d-flex-->
                        <div id="kt_content_container" class=""> <!--container-xxl-->

                            @include('job.job_detail_page_card')

                            <ul class="nav ms-10">
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm active" data-bs-toggle="tab" href="#kt_table_widget_5_tab_1" id="basic_details_button">Job info</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_2" id="pay_rates_button">Pay rates</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_9" id="lines_button">Lines</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_3" id="workers_button">Associates</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_11" id="groups_button">Groups</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_8" id="workers_transport">Transport</a>
                                </li>
<!--                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_10" id="worker_availability">Associate availability</a>
                                </li>-->
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_4" id="bookings_button">Bookings</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_5" id="timesheet_button">Timesheets</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_7" data-note_type="job" id="notes_button">Notes</a>
                                </li>
                            </ul>
                            <div class="card mt-n1">
                                <!--begin::Card body-->
                                <div class="card-body py-4">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="kt_table_widget_5_tab_1">
                                            <div class="table-responsive">
                                                <form id="basic_details_form">
                                                    @csrf
                                                    <div class="p-5">
                                                        <div class="fv-row">
                                                            <div class="row mb-7">
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_name" class="fs-6 fw-bold required">Name</label>
                                                                        <input type="text" name="job_name" id="job_name" class="form-control" placeholder="Name" value="{{ $job['name'] }}" />
                                                                        <span class="text-danger error" id="job_name_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_site" class="fs-6 fw-bold required">Site</label>
                                                                        <select name="job_site" id="job_site" class="form-select form-select-lg" data-control="select2" data-placeholder="Select site" data-allow-clear="true">
                                                                            <option {{ ($job['site_id'] == '') ? 'selected' : '' }} value="">Select site</option>
                                                                            @if($site)
                                                                                @foreach($site as $s_row)
                                                                                    <option {{ ($job['site_id'] == $s_row['id']) ? 'selected' : '' }} value="{{ $s_row['id'] }}">{{ $s_row['site_name'] }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                        <span class="text-danger error" id="job_site_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label class="fs-6 fw-bold required">Assignment schedule
                                                                            @if($job['assignment_schedule'])
                                                                                <a href="{{ asset('workers/client_job/'.$job['assignment_schedule']) }}" target="_blank">View</a>
                                                                            @endif
                                                                        </label>
                                                                        <input type="file" name="assignment_schedule" id="assignment_schedule" class="form-control"  accept="application/pdf"/>
                                                                        <span class="text-danger error" id="assignment_schedule_error"></span>
                                                                        <label class="fw-bold text-muted">PDF FORMAT. (Max. 10MB)</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label class="fs-6 fw-bold">Client job Ref.</label>
                                                                        <input type="text" name="reference" id="reference" class="form-control" placeholder="Client job reference"  value="{{ $job['reference'] }}" />
                                                                        <span class="text-danger error" id="reference_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_description" class="fs-6 fw-bold required">Description</label>
                                                                        <textarea name="job_description" id="job_description" rows="5" class="form-control" placeholder="Enter job description">{{ $job['description'] }}</textarea>
                                                                        <span class="text-danger error" id="job_description_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_health_and_safety_information" class="fs-6 fw-bold required">Health and safety information</label>
                                                                        <textarea name="job_health_and_safety_information" id="job_health_and_safety_information" rows="5" class="form-control" placeholder="Enter health and safety information">{{ $job['health_and_safety_information'] }}</textarea>
                                                                        <span class="text-danger error" id="job_health_and_safety_information_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_directions" class="fs-6 fw-bold">Directions</label>
                                                                        <textarea name="job_directions" id="job_directions" rows="5" class="form-control" placeholder="Enter directions">{{ $job['directions'] }}</textarea>
                                                                        <span class="text-danger error" id="job_directions_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_start_date" class="fs-6 fw-bold required">Start date</label>
                                                                        <div class="position-relative d-flex align-items-center">
                                                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                                <i class="fs-2 las la-calendar"></i>
                                                                            </span>
                                                                            <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select start date" name="job_start_date" id="job_start_date" type="text" readonly="readonly" value="{{ date('d-m-Y', strtotime($job['start_date'])) }}">
                                                                        </div>
                                                                        <span class="text-danger error" id="job_start_date_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_end_date" class="fs-6 fw-bold">End date</label>
                                                                        <div class="position-relative d-flex align-items-center">
                                                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                                <i class="fs-2 las la-calendar"></i>
                                                                            </span>
                                                                            <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select end date" name="job_end_date" id="job_end_date" type="text" readonly="readonly" value="{{ ($job['end_date']) ? date('d-m-Y', strtotime($job['end_date'])) : '' }}" disabled>
                                                                        </div>
                                                                        <span class="text-danger error" id="job_end_date_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_default_shift_time" class="fs-6 fw-bold required">Default shift time</label>
                                                                        <div class="position-relative d-flex align-items-center">
                                                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                                <i class="fs-2 las la-clock"></i>
                                                                            </span>
                                                                            <input class="form-control ps-12 flatpickr-input time_input" placeholder="Select shift time" name="job_default_shift_time" id="job_default_shift_time" type="text" readonly="readonly" value="{{ $job['default_shift_start_time'] }}">
                                                                        </div>
                                                                        <span class="text-danger error" id="job_default_shift_time_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_default_shift_length_hr" class="fs-6 fw-bold required">Default shift length</label>
                                                                        <div class="input-group">
                                                                            <input class="form-control" name="job_default_shift_length_hr" id="job_default_shift_length_hr" type="text" value="{{ $job['default_shift_length_hr'] }}">
                                                                            <div class="input-group-prepend"><span class="input-group-text">hr</span></div>
                                                                        </div>
                                                                        <span class="text-danger error" id="job_default_shift_length_hr_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_default_shift_length_min" class="fs-6 fw-bold"></label>
                                                                        <div class="input-group">
                                                                            <input class="form-control" name="job_default_shift_length_min" id="job_default_shift_length_min" type="text" value="{{ $job['default_shift_length_min'] }}">
                                                                            <div class="input-group-prepend"><span class="input-group-text">min</span></div>
                                                                        </div>
                                                                        <span class="text-danger error" id="job_default_shift_length_min_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="job_default_number_workers" class="fs-6 fw-bold">Default number of workers</label>
                                                                        <input type="text" name="job_default_number_workers" id="job_default_number_workers" class="form-control" placeholder="Enter number of workers" value="{{ $job['default_number_workers'] }}" />
                                                                        <span class="text-danger error" id="default_number_workers_error"></span>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="col-lg-12">
                                                                    <input type="hidden" name="job_id" id="job_id" value="{{ $job['id'] }}" />
                                                                    <button type="submit" name="job_form_submit" id="job_form_submit" class="btn btn-primary float-end">Update</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_2">
                                            @if($job['pay_rate_type'] == 'flat_rate' && $job['pay_rate_details'])
                                                @include('clients.partials.edit_client.view_flat_rate')
                                            @elseif($job['pay_rate_type'] == 'pay_rate_map' && $job['pay_rate_details'])
                                                @include('clients.partials.edit_client.view_pay_rate_map')
                                            @else
                                                <div class="mid-btn">
                                                    <p class="fs-6 fw-bold">No pay rate defined for {{ $job['name'] }}</p>
                                                    <a href="javascript:;" class="btn btn-primary btn-lg" id="choose_pay_rate_type_modal_btn">Choose a pay rate type</a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_9">
                                            @include('clients.partials.edit_client.dis_line')
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_3">
                                            <!--begin::Table container-->
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row mb-7">
                                                        <div class="w-100">
                                                            <div class="row border-bottom gs-0 border-4">
                                                                <div class="col-lg-12">
                                                                    <h1>Associates linked to this job</h1>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-5">
                                                                <div class="col-lg-5">
                                                                    <div class="d-flex align-items-center position-relative my-1">
                                                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                                            <i class="las la-search" style="font-size: 24px"></i>
                                                                        </span>
                                                                        <input type="text" data-kt-job-worker-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search worker" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-7">
                                                                    <div style="float:right;">
                                                                        <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding active" data-kt-button="true">
                                                                                <input class="btn-check client_job_worker_status" type="radio" name="client_job_worker_status" checked="checked" value="available"/>
                                                                                Available
                                                                            </label>
                                                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                                                <input class="btn-check client_job_worker_status" type="radio" name="client_job_worker_status" value="unconfirmed"/>
                                                                                Unconfirmed
                                                                            </label>
                                                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                                                <input class="btn-check client_job_worker_status" type="radio" name="client_job_worker_status" value="unavailable" />
                                                                                Unavailable
                                                                            </label>
                                                                            <label class="btn btn-outline btn-light-primary btn-active-primary status-radio-btn-padding" data-kt-button="true">
                                                                                <input class="btn-check client_job_worker_status" type="radio" name="client_job_worker_status" value="All" />
                                                                                All
                                                                            </label>
                                                                        </div>

                                                                        <div class="float-end">
                                                                            <a href="javascript:;" id="add_new_client_job_worker">
                                                                                <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                                                            </a>
                                                                            <a href="javascript:;" class="close_client_job_worker_form d-none" id="close_client_job_worker_form">
                                                                                <i class="fs-xxl-2qx las la-times-circle text-primary"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="row" id="client_job_worker_form_section" style="display: none">
                                                                <div class="w-100 mt-5 mb-5 border-bottom border-4"></div>
                                                                <div class="p-5 border border-1 border-dark rounded-3">
                                                                    <div class="w-100 mb-5">
                                                                        <h2>Select a new worker to add</h2>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <form id="client_job_worker_details_form">
                                                                            @csrf
                                                                            <div class="row">
                                                                                <div class="col-lg-6">
                                                                                    <div class="fv-row fv-plugins-icon-container">
                                                                                        <label for="associated_cost_center" class="fs-6 fw-bold">Cost centre</label> <!--required-->
                                                                                        <select name="associated_cost_center[]" id="associated_cost_center" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Select associated cost center" data-allow-clear="true" multiple>
                                                                                            <!-- <option value="Any">Any</option>-->
                                                                                            @if($costCentre)
                                                                                                @foreach($costCentre as $cc_row)
                                                                                                    <option value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
                                                                                                @endforeach
                                                                                            @endif
                                                                                        </select>
                                                                                        <span class="text-danger error" id="associated_cost_center_error"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-6">
                                                                                    <div class="fv-row fv-plugins-icon-container">
                                                                                        <label for="job_worker_name" class="fs-6 fw-bold required">Worker Name</label>
                                                                                        <select name="job_worker_name[]" id="job_worker_name" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Select worker" data-allow-clear="true" multiple>
                                                                                            <!-- <option value="">Select worker</option> -->
                                                                                        </select>
                                                                                        <span class="text-danger error" id="job_worker_name_error"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-6 mb-10">
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

                                                                            <div class="row">
                                                                                <div class="col-lg-12 fs-4">
                                                                                    <span class="text-danger">Please note:</span> If you add a worker directly, they will be marked as confirmed immediately - you are responsible for ensuring they are happy with the terms contained in the assignment schedule.
                                                                                </div>
                                                                            </div>

                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <input type="hidden" name="job_worker_id" id="job_worker_id" value="{{ $job['id'] }}">
                                                                                    <button type="submit" name="client_job_worker_form_submit" id="client_job_worker_form_submit" class="btn btn-primary float-end">Add worker</button>
                                                                                    <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="client_job_worker_form_process" id="client_job_worker_form_process" style="display: none">
                                                                                        <span>Please wait...
                                                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                                        </span>
                                                                                    </button>
                                                                                    <button type="reset" name="client_job_form_cancel_btn" class="btn btn-dark float-end me-1 close_client_job_worker_form">Cancel</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="w-100 mt-5 mb-5" style="border-top: 1px dashed #dddfe1"></div>
                                                        <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="workers_datatable">
                                                            <thead>
                                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                <th>Worker name</th>
                                                                <th>Worker ID</th>
                                                                <th>Status</th>
                                                                <th>Invited at</th>
                                                                <th>Declined at</th>
                                                                <th>Confirmed at</th>
                                                                <th>RTW Expires</th>
                                                                <th>Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody class="text-gray-600 fw-bold"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Table-->
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_11">
                                            @if($linkedGroups)
                                                @include('job.dis_groups')
                                            @else
                                                <div class="mid-btn">
                                                    <p class="fs-6 fw-bold">There are no groups linked to {{ strtolower($job['name']) }} job</p>
                                                    <a href="javascript:;" class="btn btn-primary btn-lg" id="link_group_modal_btn">Link group(s)</a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_8">
                                            <!--begin::Table container-->
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row mb-7">
                                                        <div class="w-100">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <h1>Workers requiring transport for this job</h1>
                                                                </div>
                                                            </div>
                                                            <div class="mb-5 border-bottom border-4"></div>
                                                        </div>
                                                        <form id="workers_transport_form">
                                                            @csrf
                                                            <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="workers_transport_datatable">
                                                                <thead>
                                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                    <th>Worker</th>
                                                                    <th>PREFERRED PICKUP POINT</th>
                                                                    <th>AGREED PICKUP POINT</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody class="text-gray-600 fw-bold"></tbody>
                                                            </table>
                                                            <div class="row mb-5">
                                                                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
                                                            </div>
                                                            <div class="row text-center">
                                                                <div class="col-lg-12">
                                                                    <button type="submit" name="update_worker_transport_submit_btn" id="update_worker_transport_submit_btn" class="btn btn-primary">Update</button>
                                                                    <button type="button" class="btn btn-lg btn-primary me-3 disabled d-none" name="update_worker_transport_process_btn" id="update_worker_transport_process_btn">
                                                                        <span>Please wait...
                                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Table-->
                                        </div>

                                        {{--@include('job.worker_availability')--}}

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_4">
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row mb-5">
                                                        <div class="col-lg-6 d-flex align-items-center fw-bolder text-muted fs-5 text-uppercase gs-0 border-bottom border-4">Select Bookings to Display</div>
                                                        <div class="col-lg-6 text-end border-bottom gs-0 border-4">
                                                            <div class="float-end">
                                                                <a href="javascript:;" id="add_booking"><i class="las la-chevron-circle-up text-primary fs-xl-2x"></i></a>
                                                                <a href="javascript:;" id="close_booking" class="close_right_to_work d-none"><i class="las la-times-circle text-primary fs-xxl-2qx"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="fv-row">
                                                        <div class="row mb-7">
                                                            <div class="col-lg-12">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="date_range" class="fs-6 fw-bold">Date range</label>
                                                                    <select name="date_range" id="date_range" class="form-select form-select-lg">
                                                                        <option value="">Select a date range...</option>
                                                                        <option value="7" selected>Next 7 days</option>
                                                                        <option value="14">Next 14 days</option>
                                                                        <option value="28">Next 28 days</option>
                                                                        <option value="dates">Between two dates...</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container start_date" style="display:none;">
                                                                    <div class="position-relative d-flex align-items-center ">
                                                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                                <i class="fs-2 las la-calendar"></i>
                                                                            </span>
                                                                        <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select start date..." name="booking_start_date" id="booking_start_date" type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container end_date" style="display:none;">
                                                                    <div class="position-relative d-flex align-items-center ">
                                                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                                <i class="fs-2 las la-calendar"></i>
                                                                            </span>
                                                                        <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select end date..." name="booking_end_date" id="booking_end_date" type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="booking_date" class="fs-6 fw-bold">Show</label><br>
                                                                    <label class="form-check form-check-inline me-2 mb-2 is-invalid pt-2 pe-3 pb-2 ps-12 rounded">
                                                                        <input type="checkbox" class="form-check-input" name="unconfirmed" id="unconfirmed" checked="" value="Unconfirmed">
                                                                        <span class="fw-bold ps-2 fs-6">Unconfirmed</span>
                                                                    </label>
                                                                    <label class="form-check form-check-inline me-2 mb-2 is-invalid pt-2 pe-3 pb-2 ps-12 rounded">
                                                                        <input type="checkbox" class="form-check-input" name="confirmed" id="confirmed" checked="" value="Confirmed">
                                                                        <span class="fw-bold ps-2 fs-6">Confirmed</span>
                                                                    </label>
                                                                    <label class="form-check form-check-inline me-2 mb-2 is-invalid pt-2 pe-3 pb-2 ps-12 rounded">
                                                                        <input type="checkbox" class="form-check-input" name="declined" id="declined" checked="" value="Declined">
                                                                        <span class="fw-bold ps-2 fs-6">Declined</span>
                                                                    </label>
                                                                    <label class="form-check form-check-inline me-2 mb-2 is-invalid pt-2 pe-3 pb-2 ps-12 rounded">
                                                                        <input type="checkbox" class="form-check-input" name="cancelled" id="cancelled" checked="" value="Cancelled">
                                                                        <span class="fw-bold ps-2 fs-6">Cancelled</span>
                                                                    </label>
                                                                    <button type="button" name="booking_form_submit" id="booking_form_submit" class="btn btn-primary float-end">show bookings</button>
                                                                </div>
                                                            </div>
                                                            <div class="w-100 mt-5 mb-5 border-bottom border-4"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center position-relative my-1 mb-5">
                                                             <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                                    <i class="fs-2 las la-search"></i>
                                                                </span>
                                                <input type="text" data-kt-client-booking-filter-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find worker/date" />
                                            </div>
                                            <div class="w-100 mt-5 mb-5 border-bottom border-4"></div>

                                            <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark table-responsive" id="booking_datatable">
                                                <thead>
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    <th width="8%">Date</th>
                                                    <th>Worker name</th>
                                                    <th>Start time</th>
                                                    <th>Duration</th>
                                                    <th>Invited</th>
                                                    <th>Declined</th>
                                                    <th>Confirmed</th>
                                                    <th>Cancelled</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody class="text-gray-600 fw-bold"></tbody>
                                            </table>
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_5">
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row mb-5">
                                                        <div class="col-lg-6 d-flex align-items-center fw-bolder text-muted fs-5 text-uppercase gs-0 border-bottom border-4">Select Timesheets to Display</div>
                                                        <div class="col-lg-6 text-end border-bottom gs-0 border-4">
                                                            <div class="float-end">
                                                                <a href="javascript:;" id="add_timesheet"><i class="las la-chevron-circle-up text-primary fs-xl-2x"></i></a>
                                                                <a href="javascript:;" id="close_timesheet" class="close_right_to_work d-none"><i class="las la-times-circle text-primary fs-xxl-2qx"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="fv-row">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="date_range" class="fs-6 fw-bold">Date range</label>
                                                                    <select name="timesheet_date_range" id="timesheet_date_range" class="form-select form-select-lg">
                                                                        <option value="">Select a date range...</option>
                                                                        <option value="7" selected>Last 7 days</option>
                                                                        <option value="14">Last 14 days</option>
                                                                        <option value="28">Last 28 days</option>
                                                                        <option value="between_dates">Between two dates...</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container timesheet_start_date" style="display:none;">
                                                                    <div class="position-relative d-flex align-items-center ">
                                                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                                <i class="fs-2 las la-calendar"></i>
                                                                            </span>
                                                                        <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select start date..." name="timesheet_start_date" id="timesheet_start_date" type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container timesheet_end_date" style="display:none;">
                                                                    <div class="position-relative d-flex align-items-center ">
                                                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                                <i class="fs-2 las la-calendar"></i>
                                                                            </span>
                                                                        <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select end date..." name="timesheet_end_date" id="timesheet_end_date" type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <button type="button" name="timesheet_form_submit" id="timesheet_form_submit" class="btn btn-primary float-end">show timesheets</button>
                                                                </div>
                                                            </div>
                                                            <div class="w-100 mt-5 mb-5 border-bottom border-4"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center position-relative my-1">
                                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                    <i class="fs-2 las la-search"></i>
                                                </span>
                                                <input type="text" data-kt-client-timesheet-filter-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find worker/date" />
                                            </div>
                                            <div class="w-100 mt-5 mb-5 border-bottom border-4"></div>
                                            <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark table-responsive" id="timesheet_datatable">
                                                <thead>
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    <th>Date</th>
                                                    <th>Worker name</th>
                                                    <th>Start time</th>
                                                    <th>Hours</th>
                                                    <th>Edited</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody class="text-gray-600 fw-bold"></tbody>
                                            </table>
                                        </div>

                                        @include('clients.partials.edit_client.notes_details')
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('timesheet_and_bonus.partial.edit_timesheet_entry_modal')
    @include('clients.partials.edit_client.choose_pay_rate_type_modal')
    @include('clients.partials.edit_client.create_flat_pay_rate_modal')
    @include('clients.partials.edit_client.edit_pay_rate_modal')
    {{--@include('clients.partials.edit_client.view_all_flat_pay_rate')--}}
    @include('clients.partials.edit_client.create_pay_rate_map_modal')
    @include('clients.partials.edit_client.update_pay_rate_map_model')
    @include('job.link_group_model')
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.6/sorting/datetime-moment.js"></script>
    <script>
        $("#last_li_of_header_title").empty().append(`
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('view-client-details/'.$job['client_id']) }}"
                   class="text-muted text-hover-primary text-uppercase">
                    {{ $job['client_details']['company_name'] }}
        </a>
    </li>
    <li class="breadcrumb-item text-gray-500">></li>
    <li class="breadcrumb-item text-dark">
        <span id="header_sub_title">JOB</span>
        <span id="header_additional_info" class="text-uppercase ms-1">
            : {{ $job['name'] }} (ID {{ $job['id'] }})
                </span>
            </li>
        `);

        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', () => {
                localStorage.setItem('viewClientJobActiveTab_'+'{{ $job['id'] }}', tab.id);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = localStorage.getItem('viewClientJobActiveTab_'+'{{ $job['id'] }}');
            if (activeTab) {
                document.getElementById(activeTab).click();
            }
        });
        $("#action_id").val({{ $job['id'] }});
    </script>

    <script>
        activeMenu('/job-management');

        let job_start_date = $("#job_start_date");
        job_start_date.flatpickr({
            dateFormat  : "d-m-Y",
        });

        end_date_disable('{{ date('d-m-Y', strtotime($job['start_date'])) }}', '{{ ($job['end_date']) ? date('d-m-Y', strtotime($job['end_date'])) : '' }}');

        function end_date_disable(start_date, end_date) {
            let end_date_box = $( "#job_end_date" );
            end_date_box.val('');

            let value   = start_date; //$(this).val();
            let dateAr  = value.split('-');
            let date    = dateAr[1] + '-' + dateAr[0] + '-' + dateAr[2];

            let newDate         = new Date(date);
            let currentMonth    = newDate.getMonth();
            let currentDate     = newDate.getDate();
            let currentYear     = newDate.getFullYear();

            end_date_box.prop('disabled', false)
            end_date_box.flatpickr({
                minDate: new Date(currentYear, currentMonth, currentDate),
                dateFormat  : "d-m-Y",
            });

            if (end_date !== '') {
                end_date_box.val(end_date);
            }
        }

        job_start_date.on('change', function () {
            end_date_disable($(this).val(), '');
        });

        $("#job_default_shift_time").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });

        $("#basic_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $.ajax({
                type        : 'post',
                url         : '{{ url('update-client-job-basic-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                //cache       : false,
                success     : function (response) {
                    decodeResponse(response)
                    if(response.code === 200) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        /*--- BEGIN CLIENT JOB WORKERS ---*/
        $("#add_new_client_job_worker").on('click', function () {
            $(".error").html('');

            $("#client_job_worker_details_form").trigger('reset');
            $("#associated_cost_center").val('').trigger('change');
            $("#job_worker_name").val('').trigger('change');

            $("#client_job_worker_form_submit").show();
            $("#client_job_worker_form_process").hide();

            $("#client_job_worker_form_section").slideDown(600);

            $("#add_new_client_job_worker").addClass('d-none');
            $("#close_client_job_worker_form").removeClass('d-none');
        });

        $(document).on('click', '.close_client_job_worker_form', function (){
            $("#client_job_worker_form_section").slideUp(600);
            $("#add_new_client_job_worker").removeClass('d-none');
            $("#close_client_job_worker_form").addClass('d-none');
        })

        $(document).ready(function() {
            $(".form-select-custom").select2({
                // dropdownParent: $("#add_new_client_job_worker_modal")
                dropdownParent : $("#client_job_worker_form_section")
            });
        });

        $(function() {
            $("#job_worker_name").select2({
                // dropdownParent: $("#add_new_client_job_worker_modal"),
                $dropdownParent: $("#client_job_worker_form_section"),
                /*tags: true,
                multiple: true,*/
                ajax: {
                    url: '{{ url('search-client-job-worker') }}',
                    dataType: 'json',
                    type: "POST",
                    data: function (term) {
                        return {
                            _token: '{{ csrf_token() }}',
                            associated_cost_center : $("#associated_cost_center").val(),
                            job_id  : '{{ $job['id'] }}',
                            keyword : term
                        };
                    },
                    processResults: function (data) {
                        console.log(data);
                        return {
                            results: $.map(data.data, function (item) {
                                console.log(item)
                                return {
                                    text: item.name,
                                    id: item.id,
                                }
                            })
                        };
                    }

                }
            });
        });

        $("#client_job_worker_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#client_job_worker_form_submit").hide();
            $("#client_job_worker_form_process").show();

            $.ajax({
                type        : 'post',
                url         : '{{ url('store-client-job-worker-multiple') }}', /*store-client-job-worker*/
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#client_job_worker_form_submit").show();
                    $("#client_job_worker_form_process").hide();

                    if(response.code === 200) {
                        //$("#add_new_client_job_worker_modal").modal('hide');
                        $(".close_client_job_worker_form").click();
                        worker_table.ajax.reload();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        let worker_table;
        $(document).ready(function() {
            worker_table = $('#workers_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-client-job-worker') }}',
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}';
                        d.status = $('input[name="client_job_worker_status"]:checked').val();
                        d.job_id = '{{ $job['id'] }}';
                    },
                },
                "columns": [
                    {"data": "name"},
                    {"data": "worker_id"},
                    {"data": "status"},
                    {"data": "invited_at"},
                    {"data": "declined_at"},
                    {"data": "confirmed_at"},
                    {"data": "rtw_expires"},
                    {"data": "action", "sClass": "text-end w-55px", }
                ]
            });

        });

        const filterSearch = document.querySelector('[data-kt-job-worker-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            worker_table.column(0).search(this.value).draw();
        });

        $(document).on('change', '.client_job_worker_status', function () {
            worker_table.ajax.reload();
        })

        $(document).on('click', '#archive_job_worker', function () {
            let id = $(this).attr('data-job_worker_id');

            let unlink_worker_name = $(this).attr('data-worker_name');
            let nameBeforeSpace = unlink_worker_name.match(/^\S+/);
            let unlink_worker_first_name = nameBeforeSpace ? nameBeforeSpace[0] : unlink_worker_name;

            $.ajax({
                type        : 'post',
                url         : '{{ url('get-client-job-worker-future-confirm-and-invitation-shift') }}',
                data        : {
                    _token  : '{{ csrf_token() }}',
                    worker_id : $(this).attr('data-worker_id'),
                    job_id : '{{ $job['id'] }}'
                },
                success     : function (response) {
                    if(response.code === 200) {
                        let title = 'Unlink '+unlink_worker_name+' from this job?';
                        let text = '<p>You will no longer be able add '+unlink_worker_first_name+' to shifts for this job.</p>'

                        if (response.data.total_count > 0) {
                            text += "<p>Unlinking "+unlink_worker_first_name+" will also cancel "+unlink_worker_first_name+"’s "+response.data.confirmed_shift_count+" future confirmed shifts and "+response.data.invitation_shift_count+" shift invitations for {{ $job['name'] }}.</p>";
                        }

                        sweetAlertUnlink(title, text).then((result) => {
                            if (result.value) {
                                $.ajax({
                                    type    : 'get',
                                    url     : '{{ url('archive-client-job-worker') }}'+'/'+id,
                                    success : function (response) {
                                        decodeResponse(response)
                                        if(response.code === 200) {
                                            worker_table.ajax.reload();
                                        }
                                    },
                                    error : function (response) {
                                        toastr.error(response.statusText);
                                    }
                                });
                            }
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(document).on('click', '#relink_job_worker', function () {
            let id = $(this).attr('data-job_worker_id');
            sweetAlertRelink('Do you want to relink '+$(this).attr('data-worker_name')+' from this job').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('un-archive-client-job-worker') }}'+'/'+id,
                        success : function (response) {
                            decodeResponse(response)
                            if(response.code === 200) {
                                worker_table.ajax.reload();
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $(document).on('click', '#confirmed_job_worker', function () {
            let id = $(this).attr('data-job_worker_id');
            let auth_id = '{{ \Illuminate\Support\Facades\Auth::id() }}';
            Swal.fire({
                text                : 'You want to confirm this worker!',
                icon                : "warning",
                showCancelButton    : true,
                buttonsStyling      : false,
                confirmButtonText   : "Yes, confirm!",
                cancelButtonText    : "No, cancel",
                customClass         : {
                    confirmButton       : "btn fw-bold btn-success",
                    cancelButton        : "btn fw-bold btn-active-light-danger"
                }
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('confirm-client-job-worker-admin') }}'+'/'+id+'/'+auth_id+'/1',
                        success : function (response) {
                            decodeResponse(response)
                            if(response.code === 200) {
                                worker_table.ajax.reload();
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $(document).on('click', '#declined_job_worker', function () {
            let id = $(this).attr('data-job_worker_id');
            let auth_id = '{{ \Illuminate\Support\Facades\Auth::id() }}';
            Swal.fire({
                text                : 'You want to decline this worker!',
                icon                : "warning",
                showCancelButton    : true,
                buttonsStyling      : false,
                confirmButtonText   : "Yes, decline!",
                cancelButtonText    : "No, cancel",
                customClass         : {
                    confirmButton       : "btn fw-bold btn-danger",
                    cancelButton        : "btn fw-bold btn-active-light-danger"
                }
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('confirm-client-job-worker-admin') }}'+'/'+id+'/'+auth_id+'/0',
                        success : function (response) {
                            decodeResponse(response)
                            if(response.code === 200) {
                                worker_table.ajax.reload();
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });
        /*--- END CLIENT JOB WORKERS ---*/

        /*--- BEGIN CLIENT JOB BOOKING ---*/
        let booking_start_date = $("#booking_start_date");
        booking_start_date.flatpickr({
            dateFormat  : "d-m-Y",
        });

        let booking_end_date = $("#booking_end_date");
        booking_end_date.flatpickr({
            dateFormat  : "d-m-Y",
        });

        $('#date_range').change(function() {
            if ($(this).val() == "dates") {
                $('.start_date').show();
                $('.end_date').show();

            } else {
                $('.start_date').hide();
                $('.end_date').hide();

            }
        });

        let tableNameBookingsDatatable = $('#booking_datatable');
        let booking_table = tableNameBookingsDatatable.DataTable();

        $("#booking_form_submit").on('click', function (){
            booking_table.ajax.reload();
        })

        $('#bookings_button').on('click', function () {
            booking_table.destroy()
            tableNameBookingsDatatable.dataTable.moment('DD-MM-YYYY');
            booking_table = tableNameBookingsDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('/get-booking-data') }}',
                    "data": function (d) {
                        console.log(d);
                        d._token = '{{ csrf_token() }}';
                        d.booking_job_id = '{{ $job['id'] }}';
                        d.date_range = $('#date_range').val();
                        d.booking_start_date = $('#booking_start_date').val();
                        d.booking_end_date = $('#booking_end_date').val();

                        if ($('#unconfirmed').prop('checked')) d.unconfirmed = 1;
                        if ($('#confirmed').prop('checked')) d.confirmed = 1;
                        if ($('#declined').prop('checked')) d.declined = 1;
                        if ($('#cancelled').prop('checked')) d.cancelled = 1;

                    },
                },
                "columns": [
                    {"data": "date", "width": "10%"},
                    {"data": "worker_name"},
                    {"data": "start_time"},
                    {"data": "duration"},
                    {"data": "invited_at"},
                    {"data": "declined_at"},
                    {"data": "confirmed_at"},
                    {"data": "cancelled_at"},
                    {"data": "action", "sClass": "text-end"},
                ],
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "type": "date", "targets": 0 }
                ]
            });
        });

        const client_bookings_filterSearch = document.querySelector('[data-kt-client-booking-filter-table-filter="search"]');

        client_bookings_filterSearch.addEventListener('keyup', function (e) {
            let searchText = e.target.value.toLowerCase();
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                let name = data[0].toLowerCase();
                let date = data[1].toLowerCase();
                return name.includes(searchText) || date.includes(searchText);
            });
            booking_table.draw();
            $.fn.dataTable.ext.search.pop();
        });
        /*--- END CLIENT JOB BOOKING ---*/

        /*--- BEGIN CLIENT JOB TIMESHEET ---*/
        let timesheet_start_date = $("#timesheet_start_date");
        timesheet_start_date.flatpickr({
            dateFormat  : "d-m-Y",
        });

        let timesheet_end_date = $("#timesheet_end_date");
        timesheet_end_date.flatpickr({
            dateFormat  : "d-m-Y",
        });
        $('#timesheet_date_range').change(function() {
            if ($(this).val() === "between_dates") {
                $('.timesheet_start_date').show();
                $('.timesheet_end_date').show();
            } else {
                $('.timesheet_start_date').hide();
                $('.timesheet_end_date').hide();
            }
        });

        let tableNameTimesheetsDatatable = $('#timesheet_datatable');
        let timesheet_table = tableNameTimesheetsDatatable.DataTable();

        $("#timesheet_form_submit").on('click', function (){
            timesheet_table.ajax.reload();
        })

        $('#timesheet_button').on('click', function () {
            timesheet_table.destroy()
            timesheet_table = tableNameTimesheetsDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('/get-timesheet-data') }}',
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}';
                        d.timesheet_job_id = '{{ $job['id'] }}';
                        d.timesheet_date_range = $('#timesheet_date_range').val();
                        d.timesheet_start_date = $('#timesheet_start_date').val();
                        d.timesheet_end_date = $('#timesheet_end_date').val();
                    },
                },
                "columns": [
                    {"data": "date", "width": "15%"},
                    {"data": "worker_name", "width": "15%"},
                    {"data": "start_time", "width": "15%"},
                    {"data": "hours", "width": "15%"},
                    {"data": "edited", "width": "15%"},
                    {"data": "action", "width": "10%", "sClass": "text-end"},
                ]
            });
        });

        const client_timesheet_filterSearch = document.querySelector('[data-kt-client-timesheet-filter-table-filter="search"]');

        client_timesheet_filterSearch.addEventListener('keyup', function (e) {
            let searchText = e.target.value.toLowerCase();
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                let name = data[0].toLowerCase();
                let date = data[1].toLowerCase();
                return name.includes(searchText) || date.includes(searchText);
            });
            timesheet_table.draw();
            $.fn.dataTable.ext.search.pop();
        });
        /*--- END CLIENT JOB TIMESHEET ---*/

        $.fn.select2.defaults.set("language", {
            errorLoading: function () {
                return "Start typing to find matching entries.";
            }
        });

        let workers_transport_datatable;
        $(document).ready(function() {
            workers_transport_datatable = $('#workers_transport_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-client-job-worker-transport') }}',
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}';
                        d.status = 'available';
                        d.job_id = '{{ $job['id'] }}';
                    },
                },
                "columns": [
                    {"data": "name", "width": "20%"},
                    {"data": "preferred_pickup_point", "width": "40%"},
                    {"data": "agreed_pickup_point", "width": "40%"}
                ],
                "drawCallback": function () {
                    $('.agreed_pickup_point_drp').select2({
                        dropdownParent: $("#workers_transport_form")
                    });
                }
            });
        });

        let transportUpdates = {};
        $(document).on('change', '.agreed_pickup_point_drp', function () {
            const $row = $(this).closest('tr');
            const rowData = workers_transport_datatable.row($row).data();
            const agreedValue = $(this).val();

            transportUpdates[rowData.record_id] = {
                record_id: rowData.record_id,
                agreed_pickup_point: agreedValue
            };
        });

        $("#workers_transport_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#update_worker_transport_submit_btn").addClass('d-none');
            $("#update_worker_transport_process_btn").removeClass('d-none');

            let formData = new FormData(this);
            formData.append('worker_transport', JSON.stringify(Object.values(transportUpdates)));

            $.ajax({
                type        : 'post',
                url         : '{{ url('update-worker-transport-details') }}',
                data        : formData,
                contentType : false,
                processData : false,
                success     : function (response) {
                    decodeResponse(response)
                    if(response.code === 200) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                    }
                    $("#update_worker_transport_submit_btn").removeClass('d-none');
                    $("#update_worker_transport_process_btn").addClass('d-none');
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#update_worker_transport_submit_btn").removeClass('d-none');
                    $("#update_worker_transport_process_btn").addClass('d-none');
                }
            });
        });
    </script>

    @yield('edit_client_note_js')
    @yield('edit_timesheet_entry_js')
    @yield('choose_pay_rate_type_js')
    @yield('flat_pay_rate_js')
    @yield('edit_pay_rate_js')
    @yield('pay_rate_map_js')
    @yield('choose_update_pay_rate_map_js')
    @yield('view_pay_rate_map_js')
    @yield('job_line_js')
    {{--@yield('worker_availability_js')--}}
    @yield('add_group_link_js')
    @yield('unlink_group_job_js')
@endsection
