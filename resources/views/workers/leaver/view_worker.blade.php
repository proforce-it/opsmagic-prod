@extends('theme.page')

@section('title', 'Dashboard')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar" id="kt_toolbar">
            <!--begin::Container-->
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <!--begin::Page title-->
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <!--begin::Title-->
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">View worker detail
                        <!--begin::Separator-->
                        <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span>
                        <!--end::Separator-->
                        <!--begin::Description-->
                        <small class="text-muted fs-7 fw-bold my-1 ms-1"></small>
                        <!--end::Description-->
                    </h1>
                    <!--end::Title-->
                </div>
                <!--end::Page title-->

                <!--begin::Actions-->
                <div class="d-flex align-items-center py-1">
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <!--begin::Card header-->
                                <div class="card-header border-0 pt-6">
                                    {{--<h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder fs-3 mb-1">Latest Products</span>
                                        <span class="text-muted mt-1 fw-bold fs-7">More than 400 new products</span>
                                    </h3>--}}
                                    <div class="card-toolbar">
                                        <ul class="nav">
                                            <li class="nav-item">
                                                <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder active px-4  me-1" data-bs-toggle="tab" href="#kt_table_widget_5_tab_1">Basic details</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4 me-1" data-bs-toggle="tab" href="#kt_table_widget_5_tab_2">Other details</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4" data-bs-toggle="tab" href="#kt_table_widget_5_tab_3">Experience/Skills</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4" data-bs-toggle="tab" href="#kt_table_widget_5_tab_4">Documents</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body py-4">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="kt_table_widget_5_tab_1">
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Worker number</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['worker_no'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">First name</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['first_name'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Middle Name</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['middle_name'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Last name</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['last_name'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Date of birth</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ date('d-m-Y', strtotime($worker['date_of_birth'])) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Email address</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['email_address'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Mobile number</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['mobile_number'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Marital status</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['marital_status'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Nationality</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['nationality'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">National Insurance Number</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">@@ {{ substr($worker['national_insurance_number'], 2, -1) }} @</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Name of partner</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['name_of_partner'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">ID number of partner</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['id_number_of_partner'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">UK Address</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                {{ $worker['current_address_line_one'] ? $worker['current_address_line_one'].', ' : '' }}
                                                                {{ $worker['current_address_line_two'] ? $worker['current_address_line_two'].', ' : '' }}
                                                                {{ $worker['current_city_details'] ? $worker['current_city_details']['name'].', ' : '' }}
                                                                {{ $worker['current_state_details'] ? $worker['current_state_details']['name'].' ' : '' }}
                                                                {{ $worker['current_zip_code'] ? ' - '.$worker['current_zip_code'].', ' : '' }}
                                                                {{ $worker['current_country_details'] ? $worker['current_country_details']['name'].'. ' : '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Address in Home Country</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                {{ $worker['permanent_address_line_one'] ? $worker['permanent_address_line_one'].', ' : '' }}
                                                                {{ $worker['permanent_address_line_two'] ? $worker['permanent_address_line_two'].', ' : '' }}
                                                                {{ $worker['permanent_city_details'] ? $worker['permanent_city_details']['name'].', ' : '' }}
                                                                {{ $worker['permanent_state_details'] ? $worker['permanent_state_details']['name'].' ' : '' }}
                                                                {{ $worker['permanent_zip_code'] ? ' - '.$worker['permanent_zip_code'].', ' : '' }}
                                                                {{ $worker['permanent_country_details'] ? $worker['permanent_country_details']['name'].'. ' : '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_2">
                                            <!--begin::Table container-->
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row mb-10">
                                                        <div class="col-lg-4">
                                                            <div class="card-title align-items-start flex-column">
                                                                <div class="fw-bolder fs-3 mb-1">Right to Work Route</div>
                                                                @if($worker['right_to_work'])
                                                                    @foreach(explode('~~~~~', $worker['right_to_work']) as $rtw)
                                                                        <div class="fw-bold fs-7">
                                                                            <div class="badge badge-success fw-bolder fs-6 mt-1">
                                                                                {{ $rtw }}
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    -
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="card-title align-items-start flex-column">
                                                                <div class="fw-bolder fs-3 mb-1">Die Diligence Checklist</div>
                                                                @if($worker['die_diligence'])
                                                                    @foreach(explode('~~~~~', $worker['die_diligence']) as $ddc)
                                                                        <div class="fw-bold fs-7">
                                                                            <div class="badge badge-danger fw-bolder fs-6 mt-1">
                                                                                {{ $ddc }}
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    -
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="card-title align-items-start flex-column">
                                                                <div class="fw-bolder fs-3 mb-1">Compliance</div>
                                                                @if($worker['compliance'])
                                                                    @foreach(explode('~~~~~', $worker['compliance']) as $cr)
                                                                        <div class="fw-bold fs-7">
                                                                            <div class="badge badge-warning fw-bolder fs-6 mt-1">
                                                                                {{ $cr }}
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    -
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-10">
                                                        <div class="col-lg-12">
                                                            <div class="fw-bolder fs-3 mb-1">Region</div>
                                                            @if($worker['region']) <div class="badge badge-primary fw-bolder fs-2 mt-1"> {{ $worker['region'] }}</div> @else - @endif
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="fw-bolder fs-3 mb-1">Visa details</div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">UK Visa type</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['visa_type'] ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Visa reference number</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['visa_reference_number'] ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Start date</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ ($worker['visa_start_date']) ? date('d-m-Y', strtotime($worker['visa_start_date'])) : '-' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">End Date</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ ($worker['visa_end_date']) ? date('d-m-Y', strtotime($worker['visa_end_date'])) : '-' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="fw-bolder fs-3 mb-1">Bank Details</div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Account Number</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['bank_account_number'] ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-10">
                                                        <label class="col-lg-4 fw-bold text-muted">Sort Code</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ ($worker['bank_ifsc_code']) ? 'xx-xx-'.$worker['bank_ifsc_code'] : '-' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Bank Name</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $worker['bank_name'] ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-10">
                                                        <div class="col-lg-12">
                                                            <div class="fw-bolder fs-3 mb-1">Medical Issue / Disability (If Any)</div>
                                                            <div class="text-muted mt-1 fw-bold fs-7">{{ $worker['medical_issue_details'] ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-10">
                                                        <div class="col-lg-12">
                                                            <div class="fw-bolder fs-3 mb-1">Criminal Conviction (If Any)</div>
                                                            <div class="text-muted mt-1 fw-bold fs-7">{{ $worker['criminal_conviction_details'] ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Table-->
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_3">
                                            <!--begin::Table container-->
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Status</label>
                                                        <div class="col-lg-8">
                                                            @if($worker['status'] == 'Active')
                                                                <span class="badge badge-light-success fs-7 fw-bolder">Active</span>
                                                            @elseif($worker['status'] == 'Inactive')
                                                                <span class="badge badge-light-dark fs-7 fw-bolder">Inactive</span>
                                                            @elseif($worker['status'] == 'Leaver')
                                                                <span class="badge badge-light-warning fs-7 fw-bolder">Leaver</span>
                                                            @elseif($worker['status'] == 'Do Not Return')
                                                                <span class="badge badge-light-danger fs-7 fw-bolder">Do Not Return</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="fw-bolder fs-3 mb-1">Skill & Experience</div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Skills</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ ($worker['skill']) ? implode(', ', explode('~~~~~', $worker['skill'])) : '-' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-10">
                                                        <label class="col-lg-4 fw-bold text-muted">Experience</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ ($worker['experience']) ? $worker['experience'].' Year' : '-' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="fw-bolder fs-3 mb-1">Work experience</div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-10">
                                                        <div class="col-lg-12">
                                                            <table class="table table-flush align-middle table-row-bordered table-row-solid gy-4">
                                                                <thead class="border-gray-200 fs-5 fw-bold bg-lighten">
                                                                <tr>
                                                                    <th class="min-w-250px">Job Title / position</th>
                                                                    <th class="min-w-100px">Company / Organization</th>
                                                                    <th class="min-w-150px">Start date</th>
                                                                    <th class="min-w-150px">End date</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody class="fw-6 fw-bold text-gray-600">
                                                                @if($worker['job_title'])
                                                                    @php
                                                                        $job_title = explode('~~~~~', $worker['job_title']);
                                                                        $company_name = explode('~~~~~', $worker['company_name']);
                                                                        $work_start_date = explode('~~~~~', $worker['work_start_date']);
                                                                        $work_end_date = explode('~~~~~', $worker['work_end_date']);
                                                                        $current_working_here = $worker['current_working_here'];
                                                                    @endphp
                                                                    @foreach($job_title as $key => $we_row)
                                                                        @php($no = $key + 1)
                                                                        <tr>
                                                                            <td>
                                                                                <span href="#" class="text-hover-primary text-gray-600">{{ $we_row }}</span>
                                                                            </td>
                                                                            <td>{{ $company_name[$key] }}</td>
                                                                            <td>
                                                                                <span class="badge badge-light-primary fs-7 fw-bolder">{{ date('d-m-Y', strtotime($work_start_date[$key])) }}</span>
                                                                            </td>
                                                                            <td>
                                                                                @if($no == $current_working_here)
                                                                                    <span class="badge badge-light-success fs-7 fw-bolder">Current working</span>
                                                                                @else
                                                                                    <span class="badge badge-light-danger fs-7 fw-bolder">{{ date('d-m-Y', strtotime($work_end_date[$key])) }}</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td>-</td>
                                                                        <td>-</td>
                                                                        <td>-</td>
                                                                        <td>-</td>
                                                                    </tr>
                                                                @endif
                                                                </tbody>
                                                                <!--end::Tbody-->
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="fw-bolder fs-3 mb-1">Interview Records</div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-10">
                                                        @if($worker['interview_date'])

                                                            @php($interview_date = explode('~~~~~', $worker['interview_date']))
                                                            @php($interview_status = explode('~~~~~', $worker['interview_status']))
                                                            @php($interview_details = explode('~~~~~', $worker['interview_details']))

                                                            @foreach($interview_date as $key => $interview_row)
                                                                <div class="col-lg-4">
                                                                    <div class="row mb-2">
                                                                        <label class="col-lg-6 fw-bold text-muted">Interview date</label>
                                                                        <div class="col-lg-6">
                                                                            <span class="fw-bolder fs-6 badge badge-light-primary">{{ date('d-m-Y', strtotime($interview_row)) }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <label class="col-lg-6 fw-bold text-muted">Interview status</label>
                                                                        <div class="col-lg-6">
                                                                            @if($interview_status[$key] == 'Pass')
                                                                                <span class="fw-bolder fs-6 badge badge-light-success">Pass</span>
                                                                            @else
                                                                                <span class="fw-bolder fs-6 badge badge-light-danger">Failed</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <label class="col-lg-12 fw-bold text-muted">Interview description</label>
                                                                        <div class="col-lg-12 text-justify">
                                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $interview_details[$key] }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Table-->
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_4">
                                            <!--begin::Table container-->
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="fw-bolder fs-3 mb-1">Documents</div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <!--begin::Col-->
                                                        @if($worker['document_one'])
                                                            <div class="col-md-6 col-lg-4 col-xl-3 mb-10">
                                                                <div class="card card-bordered bg-light-dark card-shadow h-100">
                                                                    <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                                        <a href="{{ asset('workers/document/'.$worker['document_one']) }}" class="text-gray-800 text-hover-primary d-flex flex-column" target="_blank">
                                                                            <div class="symbol symbol-60px mb-5">
                                                                                @if($worker['document_one_type'] == 'application/pdf')
                                                                                    <img src="{{ asset('assets/media/svg/files/dark/pdf.svg') }}" alt="">
                                                                                @else
                                                                                    <img src="{{ asset('workers/document/'.$worker['document_one']) }}" alt="">
                                                                                @endif
                                                                            </div>
                                                                            <div class="fs-5 fw-bolder mb-2">{{ ($worker['document_one_title']) ? $worker['document_one_title'] : $worker['document_one'] }}</div>
                                                                            <div class="fs-7 fw-bold text-gray-400">First document - view</div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if($worker['document_two'])
                                                            <div class="col-md-6 col-lg-4 col-xl-3 mb-10">
                                                                <div class="card card-bordered bg-light-dark card-shadow h-100">
                                                                    <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                                        <a href="{{ asset('workers/document/'.$worker['document_two']) }}" class="text-gray-800 text-hover-primary d-flex flex-column" target="_blank">
                                                                            <div class="symbol symbol-60px mb-5">
                                                                                @if($worker['document_two_type'] == 'application/pdf')
                                                                                    <img src="{{ asset('assets/media/svg/files/dark/pdf.svg') }}" alt="">
                                                                                @else
                                                                                    <img src="{{ asset('workers/document/'.$worker['document_two']) }}" alt="">
                                                                                @endif
                                                                            </div>
                                                                            <div class="fs-5 fw-bolder mb-2">{{ ($worker['document_two_title']) ? $worker['document_two_title'] : $worker['document_two'] }}</div>
                                                                            <div class="fs-7 fw-bold text-gray-400">Second document - view</div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if($worker['document_three'])
                                                            <div class="col-md-6 col-lg-4 col-xl-3 mb-10">
                                                                <div class="card card-bordered bg-light-dark card-shadow h-100">
                                                                    <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                                        <a href="{{ asset('workers/document/'.$worker['document_three']) }}" class="text-gray-800 text-hover-primary d-flex flex-column" target="_blank">
                                                                            <div class="symbol symbol-60px mb-5">
                                                                                @if($worker['document_three_type'] == 'application/pdf')
                                                                                    <img src="{{ asset('assets/media/svg/files/dark/pdf.svg') }}" alt="">
                                                                                @else
                                                                                    <img src="{{ asset('workers/document/'.$worker['document_three']) }}" alt="">
                                                                                @endif
                                                                            </div>
                                                                            <div class="fs-5 fw-bolder mb-2">{{ ($worker['document_three_title']) ? $worker['document_three_title'] : $worker['document_three'] }}</div>
                                                                            <div class="fs-7 fw-bold text-gray-400">Third document - view</div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if($worker['document_four'])
                                                            <div class="col-md-6 col-lg-4 col-xl-3 mb-10">
                                                                <div class="card card-bordered bg-light-dark card-shadow h-100">
                                                                    <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                                        <a href="{{ asset('workers/document/'.$worker['document_four']) }}" class="text-gray-800 text-hover-primary d-flex flex-column" target="_blank">
                                                                            <div class="symbol symbol-60px mb-5">
                                                                                @if($worker['document_four_type'] == 'application/pdf')
                                                                                    <img src="{{ asset('assets/media/svg/files/dark/pdf.svg') }}" alt="">
                                                                                @else
                                                                                    <img src="{{ asset('workers/document/'.$worker['document_four']) }}" alt="">
                                                                                @endif
                                                                            </div>
                                                                            <div class="fs-5 fw-bolder mb-2">{{ ($worker['document_four_title']) ? $worker['document_four_title'] : $worker['document_four'] }}</div>
                                                                            <div class="fs-7 fw-bold text-gray-400">Fourth document - view</div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if($worker['document_five'])
                                                            <div class="col-md-6 col-lg-4 col-xl-3 mb-10">
                                                                <div class="card card-bordered bg-light-dark card-shadow h-100">
                                                                    <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                                        <a href="{{ asset('workers/document/'.$worker['document_five']) }}" class="text-gray-800 text-hover-primary d-flex flex-column" target="_blank">
                                                                            <div class="symbol symbol-60px mb-5">
                                                                                @if($worker['document_five_type'] == 'application/pdf')
                                                                                    <img src="{{ asset('assets/media/svg/files/dark/pdf.svg') }}" alt="">
                                                                                @else
                                                                                    <img src="{{ asset('workers/document/'.$worker['document_five']) }}" alt="">
                                                                                @endif
                                                                            </div>
                                                                            <div class="fs-5 fw-bolder mb-2">{{ ($worker['document_five_title']) ? $worker['document_five_title'] : $worker['document_five'] }}</div>
                                                                            <div class="fs-7 fw-bold text-gray-400">Fifth document - view</div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if($worker['document_six'])
                                                            <div class="col-md-6 col-lg-4 col-xl-3 mb-10">
                                                                <div class="card card-bordered bg-light-dark card-shadow h-100">
                                                                    <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                                        <a href="{{ asset('workers/document/'.$worker['document_six']) }}" class="text-gray-800 text-hover-primary d-flex flex-column" target="_blank">
                                                                            <div class="symbol symbol-60px mb-5">
                                                                                @if($worker['document_six_type'] == 'application/pdf')
                                                                                    <img src="{{ asset('assets/media/svg/files/dark/pdf.svg') }}" alt="">
                                                                                @else
                                                                                    <img src="{{ asset('workers/document/'.$worker['document_six']) }}" alt="">
                                                                                @endif
                                                                            </div>
                                                                            <div class="fs-5 fw-bolder mb-2">{{ ($worker['document_six_title']) ? $worker['document_six_title'] : $worker['document_six'] }}</div>
                                                                            <div class="fs-7 fw-bold text-gray-400">Sixth document - view</div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    @endif
                                                    <!--end::Col-->
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Table-->
                                        </div>
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
@endsection

@section('js')
    <script>
        activeMenu('/leaver-worker-management');

    </script>
@endsection
