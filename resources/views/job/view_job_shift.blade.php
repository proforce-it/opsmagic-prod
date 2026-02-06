@extends('theme.page')
@php
    $view_type = \Illuminate\Support\Facades\Request::input('view_type');
    $title = ($view_type == 'day')
        ? 'Job - '.$shift['client_job_details']['name'].' (ID '.$shift['client_job_details']['id'].')'
        : 'Bookings - '.date('d M', strtotime($shift['date'])).' '.$shift['client_job_details']['name'];
@endphp
@section('title', $title)
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post">
                        <div id="kt_content_container">

                            @if($view_type == 'day')
                                @include('job.job_detail_page_card')

                                <div class="card mb-n5">
                                    <div class="card-body py-4">
                                        <div class="table-responsive">
                                            <div class="p-5">
                                                <div class="fv-row">
                                                    <div class="row">
                                                        <div class="col-lg-12 text-center d-flex justify-content-center align-items-center fs-2 mb-3">
                                                            <a href="{{ url('view-job-shift/'.$previous_shift_td.'?view_type=day') }}"
                                                               class="{{ $previous_shift_td == 0 ? 'disabled' : '' }} me-5">
                                                                <i class="las la-arrow-circle-left fs-1 {{ $previous_shift_td == 0 ? 'text-muted' : 'text-dark' }}"></i>
                                                            </a>
                                                            Date: {{ date('d M Y', strtotime($shift['date'])) }}
                                                            <a href="{{ url('view-job-shift/'.$next_shift_td.'?view_type=day') }}"
                                                               class="{{ $next_shift_td == 0 ? 'disabled' : '' }} ms-5">
                                                                <i class="las la-arrow-circle-right fs-1 {{ $next_shift_td == 0 ? 'text-muted' : 'text-dark' }}"></i>
                                                            </a>
                                                        </div>
                                                        <div class="col-lg-12 text-center d-flex justify-content-center align-items-center">
                                                            <div class="bg-gray-300 p-5 fs-3 me-3 rounded-2">
                                                                <span class="me-3">Start: {{ date('H:i', strtotime($shift['start_time'])) }}</span>
                                                                Duration: {{ $shift['shift_length_hr'] }}hr {{ $shift['shift_length_min'] }}min
                                                            </div>
                                                            <a href="javascript:;" class="btn btn-sm btn-outline btn-outline-primary text-hover-gray-100 p-5 me-3 cursor-pointer" id="edit_booking_detail_btn" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-original-title="Edit details for current shift">
                                                                <i class="fs-xxl-1 las la-pencil-alt text-primary"></i>
                                                            </a>
                                                            <a href="javascript:;" class="btn btn-sm btn-outline btn-outline-primary text-hover-gray-100 p-5 me-3 cursor-pointer" id="copy_job_shift_modal_btn" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-original-title="Copy shift">
                                                                <i class="fs-xxl-1 las la-copy text-primary"></i>
                                                            </a>
                                                            <a href="{{ url('export-booking-calendar-sheet-confirm-worker/'.$shift['id']) }}" class="btn btn-sm btn-outline btn-outline-primary text-hover-gray-100 p-5 cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-original-title="Download booking sheet for this shift">
                                                                <i class="fs-xxl-1 las la-file-download text-primary"></i>
                                                            </a>
                                                        </div>
                                                        <div class="col-lg-12 border-bottom-dashed border-1 mt-2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card mb-5">
                                    <div class="card-body py-4">
                                        <div class="row">
                                            <div class="col-8 d-flex align-items-center">
                                                <div class="symbol symbol-circle pe-5">
                                                    <a href="{{ url('assignment-management?tag='.$shift['client_job_details']['client_id'].'.'.$shift['client_job_details']['site_id'].'.'.$shift['client_job_details']['id']) }}">
                                                        <i class="las la-chevron-circle-left text-primary fs-xxl-3qx"></i>
                                                    </a>
                                                </div>
                                                <div class="fw-bold">
                                                    <span class="fs-3 text-muted">
                                                        {{ $shift['client_job_details']['client_details']['company_name'] }} > {{ $shift['client_job_details']['site_details']['site_name'] }}
                                                    </span> <br>
                                                    <span class="fs-1">
                                                        <a href="{{ url('view-client-job/'.$shift['client_job_details']['id']) }}">{{ $shift['client_job_details']['name'] }}</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-4 text-end">
                                                @if($shift['client_job_details']['client_details']['company_logo'])
                                                    <img src="{{ asset('workers/client_document/'.$shift['client_job_details']['client_details']['company_logo']) }}" alt="No image." class="w-300px h-100px" style="object-fit: contain; object-position: right;">
                                                @else
                                                    <div>
                                                        <i class="fs-xxl-2hx las la-industry bg-gray-200 rounded-3 p-2"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-n6 mb-5">
                                    <div class="col-lg-12 text-center d-flex justify-content-center align-items-center">
                                        <div class="bg-secondary p-5 me-1 fs-3" style="border-bottom-left-radius: 10px;">
                                            <div>
                                                <a href="{{ url('view-job-shift', $previous_shift_td) }}"
                                                   class="{{ $previous_shift_td == 0 ? 'disabled' : '' }}">
                                                    <i class="las la-arrow-circle-left fs-2 {{ $previous_shift_td == 0 ? 'text-muted' : 'text-dark' }}"></i>
                                                </a>
                                                Date: {{ date('d M Y', strtotime($shift['date'])) }}
                                                <a href="{{ url('view-job-shift', $next_shift_td) }}"
                                                   class="{{ $next_shift_td == 0 ? 'disabled' : '' }}">
                                                    <i class="las la-arrow-circle-right fs-2 {{ $next_shift_td == 0 ? 'text-muted' : 'text-dark' }}"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="bg-secondary p-5 me-1 fs-3">Start: {{ date('H:i', strtotime($shift['start_time'])) }}</div>
                                        <div class="bg-secondary p-5 me-1 fs-3">Length: {{ $shift['shift_length_hr'] }}hr {{ $shift['shift_length_min'] }}min</div>
                                        <div class="btn-primary p-4 me-1 cursor-pointer" id="edit_booking_detail_btn">
                                            <i class="fs-xxl-1 las la-pencil-alt text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-original-title="Edit details for current shift"></i>
                                        </div>
                                        <div class="btn-primary p-4 me-1" id="copy_job_shift_modal_btn">
                                            <i class="fs-xxl-1 las la-copy text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-original-title="Copy shift"></i>
                                        </div>
                                        <div class="btn-primary p-4 me-1 cursor-pointer rounded-bottom-end" style="border-bottom-right-radius: 10px;" onclick="window.location.href='{{ url('export-booking-calendar-sheet-confirm-worker/'.$shift['id']) }}'">
                                            <i class="fs-xxl-1 las la-file-download text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-original-title="Download booking sheet for this shift"></i>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @php
                                $shiftDate = \Carbon\Carbon::parse($shift['date']);
                                $currentDate = \Carbon\Carbon::now();
                            @endphp

                            @if ($shiftDate->isBefore($currentDate))
                                @include('job.past_job_shift')
                            @else
                                @include('job.current_and_future_job_shift')
                            @endif
                            @include('job.copy_job_shift_modal')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @if($view_type == 'day')
        <script>
            $("#header_info_second_a_tag").html('<a href="{{ url('client-management') }}" class="text-muted text-hover-primary" id="header_info_second_a_tag_title">CLIENT MANAGEMENT</a>')
            $("#last_li_of_header_title").html(`
                <li class="breadcrumb-item text-muted">
                    <a href="{{ url('view-client-details/'.$shift['client_job_details']['client_id']) }}"
                       class="text-muted text-hover-primary text-uppercase">
                        {{ $shift['client_job_details']['client_details']['company_name'] }}
                </a>
            </li>
            <li class="breadcrumb-item text-gray-500">></li>
            <li class="breadcrumb-item text-dark">
                <span id="header_sub_title">JOB</span>
                <span id="header_additional_info" class="text-uppercase ms-1">
                : {{ $shift['client_job_details']['name'] }} (ID {{ $shift['client_job_details']['id'] }})
                        </span>
                </li>
                `);
            activeMenu('/job-management');
        </script>
    @else
        <script>
            $("#header_info_second_a_tag").html('<a href="{{ url('assignment-management?tag='.$shift['client_job_details']['client_id'].'.'.$shift['client_job_details']['site_id'].'.'.$shift['client_job_details']['id']) }}" class="text-muted text-hover-primary" id="header_info_second_a_tag_title">BOOKINGS CALENDAR</a>')
            activeMenu('/assignment-management');
        </script>
    @endif
    @yield('past_job_shift_js')
    @yield('current_and_future_job_shift_js')
    @yield('copy_job_shift_js')
@endsection
