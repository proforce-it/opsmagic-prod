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
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post"> <!--d-flex-->
                        <div id="kt_content_container" class=""> <!--container-xxl-->
                            <div class="card">
                                <div class="card-body py-4">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div class="row mb-5">
                                                <div class="col-lg-12">
                                                    <div id="filter_detail_Section" class="alert alert-custom" role="alert" style="background-color: #FFFFFF; padding: 0">
                                                        <div class="alert-text text-center">
                                                            <div class="row">
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
                                                <th colspan="2">Totals</th>
                                                <th rowspan="2" class="float-end"></th>
                                            </tr>
                                            <tr class="fs-5 fw-boldest text-gray-700 border-bottom-1 border-gray-400">
                                                @foreach($weekDays as $key => $day_title)
                                                    <th id="{{ $key }}"
                                                        class="{{ ($key == 'day_7_title') ? 'border-right-4 border-gray-400' : '' }} text-center" >{!! $day_title !!}</th>
                                                @endforeach
                                                <th class="textarea">Fulfilment</th>
                                                <th class="textarea">Extras</th>
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
                url     : '{{ url('get-booking-overview') }}',
                data    : {
                    _token  : '{{ csrf_token() }}',
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

                    /* ================================ ORDER FULFILMENT ================================ */
                    let fulfilmentRow = response.data.find(r => r.title === 'Order fulfilment');
                    if (fulfilmentRow) {
                        html += `<tr class="border-bottom-1 border-gray-400"><td class="fs-5 fw-bolder text-start">${fulfilmentRow.title}</td>`;
                        days.forEach(day => {
                            let sundayClass = day === 'sunday'
                                ? 'border-right-4 border-gray-400'
                                : '';

                            html += `<td id="${day}" class="${sundayClass}">${renderFulfilmentCell(
                                fulfilmentRow.title,
                                fulfilmentRow.data[day]?.total_no_of_joined_worker ?? 0,
                                fulfilmentRow.data[day]?.total_no_of_worker ?? 0
                            )}</td>`;
                        });

                        html += `<td>${renderFulfilmentCell(
                            fulfilmentRow.title,
                            fulfilmentRow.data.fulfilment?.total_no_of_joined_worker ?? 0,
                            fulfilmentRow.data.fulfilment?.total_no_of_worker ?? 0
                        )}</td>`;

                        html += `<td>${renderExtrasCell(
                            fulfilmentRow.data.extras?.total_no_of_worker ?? 0
                        )}</td>`;

                        html += `</tr>`;
                    }

                    /* ================================ OVERBOOKING (EXTRAS) ================================ */
                    let extrasRow = response.data.find(r => r.title === 'Overbooking (extras)');
                    if (extrasRow) {
                        html += `<tr class="border-bottom-4 border-gray-400"><td class="fs-5 fw-bolder text-start">${extrasRow.title}</td>`;
                        days.forEach(day => {
                            let extrasRowSundayClass = day === 'sunday'
                                ? 'border-right-4 border-gray-400'
                                : '';

                            html += `<td id="${day}_extra" class="${extrasRowSundayClass}">${renderExtrasCell(extrasRow.data[day]?.total_no_of_worker ?? 0)}</td>`;
                        });
                        html += `</tr>`;
                    }

                    /* ================================ BY CLIENT (basic render) ================================ */
                    let byClientRow = response.data.find(r => r.title === 'By client');
                    if (byClientRow) {
                        html += `<tr class="border-bottom-1 border-gray-400"><td class="fs-5 fw-bolder text-start border-right-4 border-gray-400" colspan="8">By client</td><td colspan="3"></td></tr>`
                        byClientRow.clients.forEach(client => {
                            html += `<tr class="border-bottom-1 border-gray-400">
                                <td class="fs-5 fw-bolder text-start">
                                    ${client.title} <br>
                                    <i class="las la-map-marker fs-5"></i> <span class="text-muted">${client.site_count}</span>
                                    <i class="las la-tools fs-5 ms-2"></i> <span class="text-muted">${client.job_count}</span>
                                </td>`;

                            days.forEach(day => {
                                let byClientRowSundayClass = day === 'sunday'
                                    ? 'border-right-4 border-gray-400'
                                    : '';

                                html += `<td id="by_client_${day}" class="${byClientRowSundayClass}">${renderFulfilmentCell(
                                    byClientRow.title,
                                    client[day]?.total_no_of_joined_worker ?? 0,
                                    client[day]?.total_no_of_worker ?? 0
                                )}</td>`;

                            });

                            html += `<td>${renderFulfilmentCell(
                                byClientRow.title,
                                client.fulfilment?.total_no_of_joined_worker ?? 0,
                                client.fulfilment?.total_no_of_worker ?? 0
                            )}</td>`;

                            html += `<td>${renderExtrasCell(
                                client.extras?.total_no_of_worker ?? 0
                            )}</td>`;

                            html += `<td class="float-end">
                                <a href="/booking-overview-by-client/${client.client_id}?week=${$('#selected_week_number').val()}_${$('#selected_week_year').val()}&cost_center=${$('#cost_center').val()}" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" id="view_client_wise_booking_details" data-client_id="${client.client_id}">
                                    <i class="fs-2 las la-arrow-right"></i>
                                </a>
                            </td>`;

                            html += `</tr>`;
                        });
                    }

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

            if (type === 'Order fulfilment') {
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
