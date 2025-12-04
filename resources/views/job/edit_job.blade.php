@extends('theme.page')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('title', 'Edit job')
@section('content')
    <div {{--content --}} class="d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <div class="card-body">
                                    <div class="stepper stepper-links d-flex flex-column pt-15" id="kt_create_account_stepper">
                                        <div class="stepper-nav mb-5">
                                            <div class="stepper-item current" data-kt-stepper-element="nav">
                                                <h3 class="stepper-title">Basic details</h3>
                                            </div>
                                            <div class="stepper-item" data-kt-stepper-element="nav">
                                                <h3 class="stepper-title">Worker details</h3>
                                            </div>
                                        </div>

                                        <!--begin::Form-->
                                        <form class="mx-auto mw-1000px w-100 pt-15 pb-10" novalidate="novalidate" id="create_job_form" enctype="multipart/form-data">
                                            <!--begin::Step 1-->
                                            <div class="current" data-kt-stepper-element="content">
                                                <!--begin::Wrapper-->
                                                <div class="w-100">
                                                    <div class="pb-10 pb-lg-5">
                                                        <h1 class="fw-bolder d-flex align-items-center text-dark">Basic Details</h1>
                                                    </div>
                                                    <div class="fv-row">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">

                                                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                                                    <input type="hidden" name="form_url" id="form_url" value="{{ url('store-assignment') }}" />
                                                                    <input type="hidden" name="redirect_url" id="redirect_url" value="{{ url('assignment-management') }}" />
                                                                    <input type="hidden" name="job_id" id="job_id" value="{{ $job['id'] }}" />

                                                                    <label for="job_title" class="fs-6 fw-bold required">Job title</label>
                                                                    <input type="text" name="job_title" id="job_title" class="form-control" placeholder="Job title" value="{{ $job['job_title'] }}" />
                                                                    <span class="error text-danger" id="job_title_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="client" class="fs-6 fw-bold required">Client</label>
                                                                    <select name="client" id="client" class="form-select form-select-lg" data-control="select2" data-placeholder="Select client" data-allow-clear="true" data-hide-search="true">
                                                                        <option {{ ($job['client'] == '') ? 'selected' : '' }} value=""></option>
                                                                        @if($client)
                                                                            @foreach($client as $row)
                                                                                <option {{ ($job['client'] == $row['id']) ? 'selected' : '' }} value="{{ $row['id'] }}">{{ $row['company_name'] }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                    <span class="error text-danger" id="client_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="category" class="fs-6 fw-bold required">Category</label>
                                                                    <select name="category" id="category" class="form-select form-select-lg" data-control="select2" data-placeholder="Select category" data-allow-clear="true" data-hide-search="true">
                                                                        <option {{ ($job['category'] == '') ? 'selected' : '' }} value=""></option>
                                                                        <option {{ ($job['category'] == 'Category 1') ? 'selected' : '' }} value="Category 1">Category 1</option>
                                                                        <option {{ ($job['category'] == 'Category 2') ? 'selected' : '' }} value="Category 2">Category 2</option>
                                                                    </select>
                                                                    <span class="error text-danger" id="category_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-lg-12">
                                                                <h1 class="fw-bolder">Job Costing</h1>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="job_costing" class="fs-6 fw-bold required">Job costing</label>
                                                                    <select name="job_costing" id="job_costing" class="form-select form-select-lg" data-control="select2" data-placeholder="Select costing type Per day / Per hour / Per week" data-allow-clear="true" data-hide-search="true">
                                                                        <option {{ ($job['job_costing'] == '') ? 'selected' : '' }} value=""></option>
                                                                        <option {{ ($job['job_costing'] == 'Per day') ? 'selected' : '' }} value="Per day">Per Day</option>
                                                                        <option {{ ($job['job_costing'] == 'Per hour') ? 'selected' : '' }} value="Per hour">Per Hour</option>
                                                                        <option {{ ($job['job_costing'] == 'Per week') ? 'selected' : '' }} value="Per week">Per Week</option>
                                                                    </select>
                                                                    <span class="error text-danger" id="job_costing_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="cost" class="fs-6 fw-bold required">Cost (£)</label>
                                                                    <input type="text" name="cost" id="cost" class="form-control" placeholder="Job cost" value="{{ $job['cost'] }}" />
                                                                    <span class="error text-danger" id="cost_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-lg-12">
                                                                <h1 class="fw-bolder">Job Timeline</h1>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="job_timeline" class="fs-6 fw-bold required">Job timeline</label>
                                                                    <input type="text" name="job_timeline" id="job_timeline" class="form-control" placeholder="Job time line" value="{{ $job['job_timeline'] }}" />
                                                                    <span class="error text-danger" id="job_timeline_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="job_start" class="fs-6 fw-bold required">Job start</label>
                                                                    <input type="datetime-local" name="job_start" id="job_start" class="form-control datetime_local" placeholder="Job starting date & time" value="{{ date('Y-m-d\TH:i', strtotime($job['job_start'])) }}" />
                                                                    <span class="error text-danger" id="job_start_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="job_end" class="fs-6 fw-bold required">Job end</label>
                                                                    <input type="datetime-local" name="job_end" id="job_end" class="form-control datetime_local" placeholder="Job end date & time" value="{{ date('Y-m-d\TH:i', strtotime($job['job_end'])) }}" />
                                                                    <span class="error text-danger" id="job_end_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-lg-12">
                                                                <h1 class="fw-bolder">Workers</h1>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="number_of_workers" class="fs-6 fw-bold required">Workers</label>
                                                                    <input type="text" name="number_of_workers" id="number_of_workers" class="form-control" placeholder="Number of workers" value="{{ $job['number_of_workers'] }}" />
                                                                    <span class="error text-danger" id="number_of_workers_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="worker_cost" class="fs-6 fw-bold required">Worker cost (£)</label>
                                                                    <input type="text" name="worker_cost" id="worker_cost" class="form-control" placeholder="Cost per worker" value="{{ $job['worker_cost'] }}" />
                                                                    <span class="error text-danger" id="worker_cost_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-lg-12">
                                                                <h1 class="fw-bolder">Shifts</h1>
                                                            </div>
                                                        </div>
                                                        <div id="shift_section">
                                                            @php
                                                                $shift = explode('~~~~~', $job['shifts']);
                                                                $shift_time = explode('~~~~~', $job['shift_times']);
                                                            @endphp
                                                            <input type="hidden" name="total_shift_section" id="total_shift_section" value="{{ count($shift) }}">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="shift_1" class="fs-6 fw-bold required">Shift 1</label>
                                                                        <input type="text" name="shift[]" id="shift_1" class="form-control" placeholder="Enter shift 1" value="{{ $shift[0] }}" />
                                                                        <span class="error text-danger" id="shift_1_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-5">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <label for="shift_time_1" class="fs-6 fw-bold required">Shift time</label>
                                                                        <input type="text" name="shift_time[]" id="shift_time_1" class="form-control shift_time" placeholder="Enter shift time" value="{{ $shift_time[0] }}" /> {{--date('H').':'.date('m').':'.date('s').' - '.date('H').':'.date('m').':'.date('s')--}}
                                                                        <span class="error text-danger" id="shift_time_1_error"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-1">
                                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                        <button type="button" name="add_shift_section" id="add_shift_section" class="btn btn-primary btn-sm mt-7">
                                                                    <span class="svg-icon svg-icon-2">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="black" />
                                                                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black" />
                                                                        </svg>
                                                                    </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            @foreach($shift as $key => $s_row)
                                                                @if($key != 0)
                                                                    @php($display_key = $key + 1)
                                                                    <div class="row">
                                                                        <div class="col-lg-6">
                                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                                <label for="shift_{{ $display_key }}" class="fs-6 fw-bold required">Shift {{ $display_key }}</label>
                                                                                <input type="text" name="shift[]" id="shift_{{ $display_key }}" class="form-control" placeholder="Enter shift {{ $display_key }}" value="{{ $s_row }}" />
                                                                                <span class="error text-danger" id="shift_'.$count.'_error"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                                <label for="shift_time_{{ $display_key }}" class="fs-6 fw-bold required">Shift time</label>
                                                                                <input type="text" name="shift_time[]" id="shift_time_{{ $display_key }}" class="form-control shift_time" placeholder="Enter shift time" value="{{ $shift_time[$key] }}" />
                                                                                <span class="error text-danger" id="shift_time_{{ $display_key }}_error"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-lg-12">
                                                                <h1 class="fw-bolder">Notes</h1>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="details" class="fs-6 fw-bold required">Details</label>
                                                                    <textarea name="details" id="details" rows="5" placeholder="details" class="form-control">{{ $job['details'] }}</textarea>
                                                                    <span class="error text-danger" id="details_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Step 1-->

                                            <!--begin::Step 2-->
                                            <div data-kt-stepper-element="content">
                                                <!--begin::Wrapper-->
                                                <div class="w-100">
                                                    <div class="pb-10 pb-lg-5">
                                                        <h1 class="fw-bolder text-dark">Worker details</h1>
                                                    </div>
                                                    <div class="fv-row" id="worker_details">
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                    <label for="experience" class="fs-6 fw-bold">Experience</label>
                                                                    <input type="number" name="experience" id="experience" class="form-control" placeholder="Experience" value="{{ $job['filter_experience'] }}" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row pb-10">
                                                            <div class="col-lg-12">
                                                                @php($skills = explode('~~~~~', $job['filter_skill']))
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-check form-check-inline me-5 is-invalid">
                                                                        <input type="checkbox" class="form-check-input skill" name="skill[]" id="skill_check_list_picking" value="picking" {{ (in_array('picking', $skills)) ? 'checked' : '' }}>
                                                                        <span class="fw-bold ps-2 fs-6">Picking</span>
                                                                    </label>
                                                                    <label class="form-check form-check-inline me-5 is-invalid">
                                                                        <input type="checkbox" class="form-check-input skill" name="skill[]" id="skill_check_list_packing" value="packing"  {{ (in_array('packing', $skills)) ? 'checked' : '' }}>
                                                                        <span class="fw-bold ps-2 fs-6">Packing</span>
                                                                    </label>
                                                                    <label class="form-check form-check-inline me-5 is-invalid">
                                                                        <input type="checkbox" class="form-check-input skill" name="skill[]" id="skill_check_list_forklift_truck_driver" value="forklift truck driver"  {{ (in_array('forklift truck driver', $skills)) ? 'checked' : '' }}>
                                                                        <span class="fw-bold ps-2 fs-6">Forklift truck driver</span>
                                                                    </label>
                                                                    <label class="form-check form-check-inline me-5 is-invalid">
                                                                        <input type="checkbox" class="form-check-input skill" name="skill[]" id="skill_check_list_production_line" value="production line"  {{ (in_array('production line', $skills)) ? 'checked' : '' }}>
                                                                        <span class="fw-bold ps-2 fs-6">Production line</span>
                                                                    </label>
                                                                    <label class="form-check form-check-inline me-5 is-invalid">
                                                                        <input type="checkbox" class="form-check-input skill" name="skill[]" id="skill_check_list_other" value="other"  {{ (in_array('other', $skills)) ? 'checked' : '' }}>
                                                                        <span class="fw-bold ps-2 fs-6">Other</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <button type="button" id="filter_worker" name="filter_worker" class="btn btn-primary btn-sm">Filter</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="pb-lg-5">
                                                            <h1 class="fw-bolder text-dark">Worker list</h1>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <table class="table table-bordered align-middle table-row-dashed fs-7 gy-3">
                                                                    <thead>
                                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                        <th></th>
                                                                        <th class="min-w-125px">Name</th>
                                                                        <th>Category</th>
                                                                        <th>On Going Job</th>
                                                                        <th>Status</th>
                                                                        <th>Revenue</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody class="text-gray-600 fw-bold" id="workers_data_body"></tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Step 2-->

                                            <!--begin::Actions-->
                                            <div class="d-flex flex-stack pt-15">
                                                <!--begin::Wrapper-->
                                                <div class="mr-2">
                                                    <button type="button" class="btn btn-lg btn-light-primary me-3" data-kt-stepper-action="previous">
                                                        <span class="svg-icon svg-icon-4 me-1">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<rect opacity="0.5" x="6" y="11" width="13" height="2" rx="1" fill="black" />
																<path d="M8.56569 11.4343L12.75 7.25C13.1642 6.83579 13.1642 6.16421 12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75L5.70711 11.2929C5.31658 11.6834 5.31658 12.3166 5.70711 12.7071L11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25C13.1642 17.8358 13.1642 17.1642 12.75 16.75L8.56569 12.5657C8.25327 12.2533 8.25327 11.7467 8.56569 11.4343Z" fill="black" />
															</svg>
														</span>
                                                        Back
                                                    </button>
                                                </div>
                                                <!--end::Wrapper-->

                                                <!--begin::Wrapper-->
                                                <div>
                                                    <button type="submit" class="btn btn-lg btn-primary me-3" data-kt-stepper-action="submit">
                                                        <span class="indicator-label">Submit
                                                            <span class="svg-icon svg-icon-3 ms-2 me-0">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
                                                                    <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
                                                                </svg>
                                                            </span>
                                                        </span>
                                                        <span class="indicator-progress">Please wait...
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="next">Continue
                                                        <span class="svg-icon svg-icon-4 ms-1 me-0">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
																<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
															</svg>
														</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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
    {{--<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>--}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src={{ asset('js/job/job.js') }}></script>
    <script>
        activeMenu('/assignment-management');

        /*$(document).on('focus', '.datetime_local', function () {
            this.type = 'datetime-local';
            this.focus();
        })*/

        $("#add_shift_section").on('click', function () {
            $.ajax({
                type        : 'post',
                url         : '{{ url('add-shift-section') }}',
                data        : {
                    _token : '{{ csrf_token() }}',
                    total_shift_section : $("#total_shift_section").val(),
                },
                success     : function (response) {
                    if(response.code === 200) {
                        $("#total_shift_section").val(response.data.count)
                        $("#shift_section").append(response.data.section)
                        setTimeRangePicker();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(function() {
            setTimeRangePicker();
            filterWorkers();
        });

        function setTimeRangePicker() {
            $('.shift_time').daterangepicker({
                opens: 'left',
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 1,
                timePickerSeconds: true,
                locale: {
                    format: 'HH:mm:ss'
                }
            }, function(start, end, label) {

            }).on('show.daterangepicker', function (ev, picker) {
                picker.container.find(".calendar-table").hide();
            });
        }

        $("#filter_worker").on('click', function () {
            filterWorkers();
        });

        function filterWorkers() {
            let skillValues = [];
            $('.skill:checked').map(function() {
                skillValues.push($(this).val());
            })

            $.ajax({
                type        : 'post',
                url         : '{{ url('assignment-section-filter-worker') }}',
                data        : {
                    _token      : '{{ csrf_token() }}',
                    experience  : $("#experience").val(),
                    skills      : skillValues,
                    job_id      : '{{ $job['id'] }}'
                },
                success     : function (response) {
                    if(response.code === 200) {
                        $("#workers_data_body").empty().append(response.data)
                    } else {
                        toastr.error(response.message);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        }
    </script>
@endsection
