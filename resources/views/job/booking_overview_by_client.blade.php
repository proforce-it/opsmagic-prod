@extends('theme.page')

@section('title', 'Booking overview')
@section('content')
    <style>
        .booking-table {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
        }
        .booking-table th {
            vertical-align: top;
            text-align: center;
        }
        .booking-table td {
            vertical-align: top;
            text-align: center;
            padding: 6px;
        }
        .booking-table th:first-child,
        .booking-table td:first-child {
            width: 350px;
            min-width: 350px;
            max-width: 350px;
            text-align: left;
        }
    </style>
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="">
                            <div class="card">
                                <div class="card-body py-4">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div class="row mb-5">
                                                <div class="col-lg-12">
                                                    <div id="filter_detail_Section" class="alert alert-custom" role="alert" style="background-color: #FFFFFF; padding: 0">
                                                        <div class="alert-text text-center">
                                                            <div class="row">
                                                                <div class="col-12 mb-5">
                                                                    @if($client['company_logo'])
                                                                        <img src="{{ asset('workers/client_document/'.$client['company_logo']) }}" alt="No image." class="h-125px" style="object-fit: contain; object-position: center; margin-left: auto; display: block; margin-right: auto;">
                                                                    @else
                                                                        <div>
                                                                            <i class="fs-xxl-2hx las la-industry bg-gray-200 rounded-3 p-2"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="col-5">
                                                                    <a href="javascript:;" id="payroll_week_backward_btn" class="me-5 change_week float-end" data-type="backward">
                                                                        <i class="las la-arrow-circle-left" style="color: #181c32;font-size: 24px;"></i>
                                                                    </a>
                                                                </div>
                                                                <div class="col-2">
                                                                    <span class="fs-2">
                                                                        <span class="ms-1">w/c</span>
                                                                        <span id="selected_week_date" class="ms-1">
                                                                            {{ \Carbon\Carbon::make($payroll_week->monday_payroll_start)->format('d M Y') }}
                                                                        </span>
                                                                        <input type="hidden" name="payroll_week_id" id="payroll_week_id" value="{{ $payroll_week['id'] }}">
                                                                        <input type="hidden" name="selected_week_number" id="selected_week_number" value="{{ $payroll_week['payroll_week_number'] }}">
                                                                        <input type="hidden" name="selected_week_year" id="selected_week_year" value="{{ $payroll_week['year'] }}">
                                                                    </span>
                                                                </div>
                                                                <div class="col-5">
                                                                    <a href="javascript:;" id="payroll_week_forward_btn" class="ms-5 change_week float-start" data-type="forward">
                                                                        <i class="las la-arrow-circle-right" style="color: #181c32;font-size: 24px;"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="fv-row fv-plugins-icon-container">
                                                        <select name="cost_center" id="cost_center" class="form-select form-select-lg get_job_shift_overview" data-control="select2" data-placeholder="All cost centres" data-allow-clear="true">
                                                            <option selected value="Any">All cost centres</option>
                                                            @if($costCentre)
                                                                @foreach($costCentre as $cc_row)
                                                                    <option value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-5">
                                <div class="card-body py-4">
                                    <table class="booking-table">
                                        <thead>
                                        <tr class="fs-5 fw-bold text-muted border-top-1 border-gray-400">
                                            <th rowspan="2"></th>
                                            <th>Mon</th>
                                            <th>Tue</th>
                                            <th>Wed</th>
                                            <th>Thu</th>
                                            <th>Fri</th>
                                            <th>Sat</th>
                                            <th class="border-right-4 border-gray-400">Sun</th>
                                            <th colspan="2" class="border-right-4 border-gray-400">Totals</th>
                                            <th rowspan="2" class="float-end">Action</th>
                                        </tr>
                                        <tr class="fs-5 fw-boldest text-gray-700 border-bottom-1 border-gray-400">
                                            @foreach($weekDays as $key => $day_title)
                                                <th id="{{ $key }}"
                                                    class="{{ ($key == 'day_7_title') ? 'border-right-4 border-gray-400' : '' }} text-center" >{!! $day_title !!}</th>
                                            @endforeach
                                            <th class="textarea">Fulfilment</th>
                                            <th class="textarea border-right-4 border-gray-400">Extras</th>
                                        </tr>
                                        </thead>
                                        <tbody id="by_client_body"></tbody>
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
    <script>
        activeMenu('/booking-overview')
        $("#last_li_of_header_title").empty().append(`
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('booking-overview') }}" class="text-muted text-hover-primary text-uppercase">
                    SHIFT OVERVIEW
                </a>
            </li>
            <li class="breadcrumb-item text-gray-500">></li>
            <li class="breadcrumb-item text-dark">
                <span id="header_sub_title"></span>
                <span id="header_additional_info" class="text-uppercase ms-1">
                    {{ $client['company_name'] }}
                </span>
            </li>
        `);

        $(document).on('click', '.change_week', function () {
            $.ajax({
                type    : 'post',
                url     : '{{ url('change-week-booking-overview') }}',
                data    : {
                    _token  : '{{ csrf_token() }}',
                    type : $(this).attr('data-type'),
                    selected_week_number : $("#selected_week_number").val(),
                    selected_week_year : $("#selected_week_year").val()
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#payroll_week_id").val(response.data.payroll_week_id);
                        $("#selected_week_date").text(response.data.selected_week_date);
                        $("#selected_week_number").val(response.data.selected_week_number);
                        $("#selected_week_year").val(response.data.selected_week_year);
                        $.each(response.data.week_days, function (key, value) {
                            $('#' + key).html(value);
                        });
                        getJobShiftOverview();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(document).ready(function () {
            getJobShiftOverview();
        });

        let cost_center = $("#cost_center");
        cost_center.on('change', function (){
            getJobShiftOverview();
        });

        function getJobShiftOverview() {
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-booking-overview-by-client') }}',
                data    : {
                    _token  : '{{ csrf_token() }}',
                    client_id : '{{ $client['id'] }}',
                    payroll_week_id : $("#payroll_week_id").val(),
                    cost_center : cost_center.val()
                },
                success: function (response) {
                    if (response.code !== 200) {
                        toastr.error(response.message);
                        return;
                    }

                    let html = '';
                    const days = ['monday','tuesday','wednesday', 'thursday','friday','saturday','sunday'];

                    /* ================================ BY SITE AND JOB (basic render) ================================ */
                    response.data.forEach(site => {

                        /* ================= SITE ROW ================= */
                        html += `<tr class="border-bottom-1 border-gray-400">
                            <td class="fs-5 fw-bolder text-start border-right-4 border-gray-400" colspan="8">
                                ${site.site_name}
                            </td>
                            <td colspan="2" class="border-right-4 border-gray-400"></td>
                            <td></td>
                        </tr>`;

                        /* ================= JOB ROWS ================= */
                        site.job_data.forEach(job => {

                            html += `<tr class="border-bottom-1 border-gray-300">
                                <td class="fs-5 text-start">${job.title}</td>`;

                            days.forEach(day => {
                                const borderClass = day === 'sunday'
                                    ? 'border-right-4 border-gray-400'
                                    : '';

                                html += `<td class="${borderClass}">
                                    ${renderFulfilmentCell(
                                        'job',
                                        job[day]?.total_no_of_joined_worker ?? 0,
                                        job[day]?.total_no_of_worker ?? 0
                                    )}</td>`;
                                });

                            /* ================= TOTALS ================= */
                            html += `<td>
                                ${renderFulfilmentCell(
                                    'total',
                                    job.fulfilment?.total_no_of_joined_worker ?? 0,
                                    job.fulfilment?.total_no_of_worker ?? 0
                                )}</td>

                                <td class="border-right-4 border-gray-400">
                                    ${renderExtrasCell(job.extras?.total_no_of_worker ?? 0)}
                                </td>

                                <td class="float-end">
                                    <a href="/worker-availability/${job.job_id}?view_type=week" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                                        <i class="fs-2 las la-calendar-week"></i>
                                    </a>
                                    <a href="/view-client-job/${job.job_id}?view_type=details" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm">
                                        <i class="fs-2 las la-arrow-right"></i>
                                    </a>
                                </td>
                            `;

                            html += `</tr>`;
                        });
                    });

                    $('#by_client_body').html(html);
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        }

        function renderFulfilmentCell(type, fulfilled, requested) {

            let isFulfilled = fulfilled >= requested; // >= important
            let isOverFulfilled = fulfilled > requested;

            let bgClassZero = '';
            let bgClass = '';
            let textClass = '';
            let iconClass = '';

            if (type === 'total') {
                bgClassZero = '';
                bgClass   = isFulfilled ? 'bg-confirm-past' : 'bg-booking-gray';
                textClass = isFulfilled ? 'past-confirm-text' : 'bg-booking-gray-text';
                iconClass = isFulfilled ? 'text-success' : 'text-danger';
            } else {
                bgClassZero = 'border-dashed';
                bgClass   = isFulfilled ? 'booking-green-no-border' : 'booking-gray-no-border';
                textClass = isFulfilled ? 'past-confirm-text' : 'bg-booking-gray-text';
                iconClass = isFulfilled ? 'text-success' : 'text-danger';
            }

            if (!requested || requested === 0) {
                return `<div class="rounded-1 border border-1 ${bgClassZero} border-gray-600 text-gray-600">
                    <div class="position-relative d-flex p-1">
                        <label class="fs-5 fw-bold ms-2">
                            <i class="las la-id-badge fs-3 text-gray-600"></i> 0/0
                        </label>
                    </div>
                </div>`;
            }

            return `<div class="${bgClass} rounded-1">
                <div class="position-relative d-flex p-1">
                    <label class="fs-5 ms-2 ${textClass}">
                        <i class="las la-id-badge fs-3 ${iconClass}"></i>
                        <span class="${isOverFulfilled ? 'fw-boldest' : ''}">${fulfilled}</span>/${requested}
                    </label>
                </div>
            </div>`;
        }

        function renderExtrasCell(extras) {
            if (!extras || extras === 0) {
                return `<div class="rounded-1 border border-1 border-gray-600 text-gray-600">
                    <div class="position-relative d-flex p-1">
                        <label class="fs-5 fw-bold ms-2">
                            <i class="las la-id-badge fs-3 text-gray-600"></i> 0
                        </label>
                    </div>
                </div>`;
            }

            return `<div class="bg-confirm-past rounded-1">
                <div class="position-relative d-flex p-1">
                    <label class="fs-5 fw-bold ms-2 past-confirm-text">
                        <i class="las la-id-badge fs-3 text-success"></i> ${extras}
                    </label>
                </div>
            </div>`;
        }
    </script>
@endsection
