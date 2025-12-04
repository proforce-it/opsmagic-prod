@extends('theme.page')

@section('title', 'Create job')

@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->

        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <!--begin::Card header-->
                                <div class="card-header pt-10">
                                    <div class="d-flex align-items-center">
                                        <!--begin::Icon-->
                                        <div class="symbol symbol-circle me-5">
                                            <div class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
                                                <!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm007.svg-->
                                                <span class="svg-icon svg-icon-2x svg-icon-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"/>
                                                            <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                                                            <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                                                            <rect fill="#000000" opacity="0.3" x="10" y="9" width="7" height="2" rx="1"/>
                                                            <rect fill="#000000" opacity="0.3" x="7" y="9" width="2" height="2" rx="1"/>
                                                            <rect fill="#000000" opacity="0.3" x="7" y="13" width="2" height="2" rx="1"/>
                                                            <rect fill="#000000" opacity="0.3" x="10" y="13" width="7" height="2" rx="1"/>
                                                            <rect fill="#000000" opacity="0.3" x="7" y="17" width="2" height="2" rx="1"/>
                                                            <rect fill="#000000" opacity="0.3" x="10" y="17" width="7" height="2" rx="1"/>
                                                        </g>
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </div>
                                        </div>
                                        <!--end::Icon-->
                                        <!--begin::Title-->
                                        <div class="d-flex flex-column">
                                            <h2 class="mb-1">Create job</h2>
                                            <div class="text-muted fw-bolder">
                                                Please fill in all mandatory fields (<b class="text-danger">*</b>)</div>
                                        </div>
                                        <!--end::Title-->
                                    </div>
                                </div>
                                <form class="form" id="job_form" method="post">
                                    @csrf
                                    <div class="card-body py-10 px-lg-17">
                                        <div class="scroll-y me-n7 pe-7" id="kt_modal_add_customer_scroll" > {{--data-kt-scroll-offset="300px"--}}
                                            <div class="fv-row">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="job_title" class="fs-6 fw-bold required">Job title</label>
                                                            <input type="text" name="job_title" id="job_title" class="form-control" placeholder="Job title" value="" />
                                                            <span class="error text-danger" id="job_title_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="client" class="fs-6 fw-bold required">Client</label>
                                                            <select name="client" id="client" class="form-select form-select-lg" data-control="select2" data-placeholder="Select client" data-allow-clear="true" data-hide-search="true">
                                                                <option></option>
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
                                                                <option></option>
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
                                                                <option></option>
                                                            </select>
                                                            <span class="error text-danger" id="job_costing_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="cost" class="fs-6 fw-bold required">Cost</label>
                                                            <input type="text" name="cost" id="cost" class="form-control" placeholder="Job cost" value="" />
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
                                                            <input type="text" name="job_timeline" id="job_timeline" class="form-control" placeholder="Job time line" value="" />
                                                            <span class="error text-danger" id="job_timeline_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="job_start" class="fs-6 fw-bold required">Job start</label>
                                                            <input type="text" name="job_start" id="job_start" class="form-control datetime_local" placeholder="Job starting date & time" value="" />
                                                            <span class="error text-danger" id="job_start_error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="job_end" class="fs-6 fw-bold required">Job end</label>
                                                            <input type="text" name="job_end" id="job_end" class="form-control datetime_local" placeholder="Job end date & time" value="" />
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
                                                            <input type="text" name="number_of_workers" id="number_of_workers" class="form-control" placeholder="Number of workers" value="" />
                                                            <span class="error text-danger" id="number_of_workers_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="worker_cost" class="fs-6 fw-bold required">Worker cost</label>
                                                            <input type="text" name="worker_cost" id="worker_cost" class="form-control" placeholder="Cost per worker" value="" />
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
                                                    <input type="hidden" name="total_shift_section" id="total_shift_section" value="1">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="shift_1" class="fs-6 fw-bold required">Shift 1</label>
                                                                <input type="text" name="shift[]" id="shift_1" class="form-control" placeholder="Enter shift 1" value="" />
                                                                <span class="error text-danger" id="shift_1_error"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                                <label for="shift_time_1" class="fs-6 fw-bold required">Shift time</label>
                                                                <input type="text" name="shift_time[]" id="shift_time_1" class="form-control" placeholder="Enter shift time" value="" />
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
                                                            <textarea name="details" id="details" rows="5" placeholder="details" class="form-control"></textarea>
                                                            <span class="error text-danger" id="details_error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-center">
                                        <button type="button" id="kt_form_client_cancel" class="btn btn-light me-3 close_modal">Discard</button>
                                        <button type="submit" id="kt_form_client_submit" class="btn btn-primary">
                                            <span class="indicator-label"> Create</span>
                                            <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                </form>
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
        activeMenu('/assignment-management');

        $(document).on('focus', '.datetime_local', function () {
            this.type = 'datetime-local';
            this.focus();
        })

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
                    } else {
                        toastr.error(response.message);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#job_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-assignment') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);
                    if(response.code === 200) {
                        setTimeout(function() { window.location.href='client-management'; }, 1500);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
