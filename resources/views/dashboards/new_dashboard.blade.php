@extends('theme.page')

@section('title', 'Dashboard')
@section('content')
    <style>
        .btn-light-primary-custom{
            background-color: #DAECF9;
        }

        .active {
            background-color: #FFF;
            color: #333;
        }
        .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered{
            color: #181c32;
        }

        .card-shadow {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
        }
        .table {
            margin-bottom : 0;
        }
        .table.table-row-bordered tr {
            border-bottom-width: 3px;
            border-bottom-style: solid;
            border-bottom-color: #EFF2F5;
        }

        .loader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 160px;
            background-color: #F5F8FA;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .loader {
            border: 4px solid #909FAF;
            border-top: 8px solid #019EF7;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loader-text {
            margin-top: 15px;
            font-size: 18px;
            color: #333;
        }

        #top_client_chart {
            max-width: 100%;
            height: 300px;
        }

        .top-client-chart-card-body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .top-client-chart-card-body canvas {
            margin-right: 20px;
        }

        .select2-results__group {
            background-color: #E4E6EE !important;
            color: #3F4254 !important;
            padding: 5px 10px !important;
            font-weight: bold !important;
            border-top: 1px solid #3F4254 !important;
            border-bottom: 1px solid #3F4254 !important;
            border-left: none !important;
            border-right: none !important;
        }

    </style>

    <div class="d-flex flex-column flex-column-fluid" id="kt_content">

        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="">

                            <div class="card mb-10">
                                <div class="card-body p-5">
                                    <div class="tab-content">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <table class="table table-sm m-0 p-0">
                                                    <tr>
                                                        <td class="text-uppercase w-250px">
                                                            <span class="fs-6 text-gray-600"><i class="fs-3 las la-calendar text-gray-600"></i> {{ \Illuminate\Support\Carbon::now()->format('l') }}</span><br>
                                                            <span class="fs-2 text-dark fw-bolder">{{ \Illuminate\Support\Carbon::now()->format('d M Y') }}</span>
                                                        </td>
                                                        <td class="w-150px">
                                                            <span class="fs-6 text-gray-600">BOOKINGS</span> <br>
                                                            <span class="fs-2 text-dark fw-bolder">{{ number_format($todayData['totalBooking']) }}</span>
                                                        </td>
                                                        <td class="w-150px">
                                                            <span class="fs-6 text-gray-600">JOBS</span> <br>
                                                            <span class="fs-2 text-dark fw-bolder">{{ number_format($todayData['totalJobs']) }}</span>
                                                        </td>
                                                        <td class="w-150px">
                                                            <span class="fs-6 text-gray-600">SITES</span> <br>
                                                            <span class="fs-2 text-dark fw-bolder">{{ number_format($todayData['totalSites']) }}</span>
                                                        </td>
                                                        <td class="w-150px">
                                                            <span class="fs-6 text-gray-600">CLIENTS</span> <br>
                                                            <span class="fs-2 text-dark fw-bolder">{{ number_format($todayData['totalClients']) }}</span>
                                                        </td>
                                                        <td class="float-end">
                                                            <span class="fs-6 text-gray-600">LATEST PAY DATE</span> <br>
                                                            <span class="fs-2 text-dark fw-bolder">{{ \Illuminate\Support\Carbon::parse($previousPayrollWeek['pay_date'])->format('d M') }} /
                                                            PW {{ $previousPayrollWeek['payroll_week_number']}}</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>

                            <ul class="nav ms-10">
                                @php($tabVar = \Illuminate\Support\Facades\Auth::user()['dashboard_tab'])
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm {{ ($tabVar == 'alerts_and_bookings_tab' || $tabVar == '') ? 'active' : '' }}" data-bs-toggle="tab" data-dashboard_type="bookings_and_alerts" href="#dashboard_tab_1" id="dashboard_tab_1_menu">Bookings & Alerts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm {{ ($tabVar == 'kpis_tab') ? 'active' : '' }}" data-bs-toggle="tab" data-dashboard_type="kpis" href="#dashboard_tab_2" id="dashboard_tab_2_menu">KPIs</a>
                                </li>
                            </ul>
                            <div class="card mt-n1">
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="row mb-4">
                                            <div class="col-lg-12">
                                                <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                                <select name="cost_center" id="cost_center" class="form-select form-select-lg" data-control="select2" data-placeholder="All cost centres" data-allow-clear="true">
                                                    <option value="">All cost centres</option>
                                                    @if($costCentre)
                                                        @foreach($costCentre as $cc_row)
                                                            <option value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        @include('dashboards.partial.processing')
                                        @include('dashboards.partial.error')

                                        @include('dashboards.partials.bookings_and_alerts.bookings_and_alerts')
                                        @include('dashboards.partials.kpis.kpis')
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        /*let auth_id = '{{ \Illuminate\Support\Facades\Auth::id() }}';
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', () => {
                localStorage.setItem('viewDashboardActiveTab_'+auth_id, tab.id);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = localStorage.getItem('viewDashboardActiveTab_'+auth_id);
            if (activeTab) {
                document.getElementById(activeTab).click();
            }
        });*/

        $(document).ready(function () {
            getDashboardData();
        });

        let cost_center_drp = $("#cost_center");
        cost_center_drp.on('change', function () {
            const cost_center = $(this).val();

            $(".url_modified").each(function () {
                let href = $(this).attr('href');
                href = href.replace(/&cost_center=[^&]*/g, '');
                if (cost_center) {
                    href += `&cost_center=${cost_center}`;
                }
                $(this).attr('href', href);
            });

            $("#alert_cost_center").val(cost_center);
            getDashboardData();
        });

        function getDashboardData() {
            let dashboard_type = document.querySelector(".nav-link.active").getAttribute("data-dashboard_type");
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-new-dashboard-data') }}',
                data    : {
                    _token : '{{ csrf_token() }}',
                    cost_center : cost_center_drp.val(),
                    payroll_week : '{{ $previousPayrollWeek['payroll_week_number'].'-'.$previousPayrollWeek['year'] }}',
                },
                success: function (response) {
                    if (response.code === 200) {
                        $(".loader-text").text('Data preparing...');

                        let result = response.data;
                        /*--- Begin booking section ---*/
                        let booking_section = result.booking_section;
                        updateBookingSection("b_job_shift_worker", booking_section.job_shift_worker, booking_section.job_shift_worker_difference, booking_job_shift_worker_chart, booking_section.job_shift_worker_chart);
                        updateBookingSection("b_shifts", booking_section.shifts, booking_section.shift_difference, booking_shift_chart, booking_section.shift_chart);
                        updateBookingSection("b_jobs", booking_section.job, booking_section.job_difference, booking_job_chart, booking_section.job_chart);
                        updateBookingSection("b_site", booking_section.site, booking_section.site_difference, booking_site_chart, booking_section.site_chart);
                        updateBookingSection("b_client", booking_section.client, booking_section.client_difference, booking_client_chart, booking_section.client_chart);

                        /*--- Begin alert section ---*/
                        let alert_section = result.alert_section;
                        updateAlertSection("shift_with_space_tomorrow", alert_section.shift_with_space_tomorrow);
                        updateAlertSection("shift_with_space", alert_section.shift_with_space);
                        updateAlertSection("booking_invitation_to_chase", alert_section.booking_invitation_chase);
                        updateAlertSection("expiring_rtws", alert_section.expiring_rtws);
                        updateAlertSection("shift_workers_without_payroll", alert_section.shift_workers_without_payroll);
                        updateAlertSection("workers_worked_greater_than_12_days", alert_section.workers_worked_greater_than_12_days);

                        let weekSnapshot = result.week_snapshot_section;
                        const weekSnapshotMappings = {
                            "total_ws_shift": "shifts",
                            "total_ws_hours": "hours",
                            "total_ws_charged": "charged",
                            "total_ws_paid": "paid",
                            "total_ws_shift_difference": "timesheet_different",
                            "total_ws_hours_difference": "hours_different",
                            "total_ws_charged_difference": "charge_different",
                            "total_ws_paid_difference": "paid_different",
                            "total_ws_avg_charge": "avg_charge",
                            "total_ws_avg_pay": "avg_pay",
                            "total_ws_avg_margin": "avg_margin",
                            "avg_charge_difference": "charge_difference",
                            "avg_pay_difference": "pay_difference",
                            "avg_margin_difference": "margin_difference"
                        };

                        $.each(weekSnapshotMappings, function(elementId, dataKey) {
                            $("#" + elementId).html(weekSnapshot[dataKey]);
                        });
                        snapshot_timesheet_chart(weekSnapshot.total_timesheet_day_wise);
                        snapshot_hours_logged_chart(weekSnapshot.total_hours_logged_day_wise);
                        snapshot_charged_chart(weekSnapshot.total_charged_day_wise);
                        snapshot_paid_chart(weekSnapshot.total_pay_day_wise);

                        top_5_clients_chart(result.top_client_section);
                        shift_and_hours_trend_chart(result.shift_and_hours_trends);

                        $("#processing_section").addClass('d-none');
                        $("#error_section").addClass('d-none');
                        $(".dashboard_section").removeClass('d-none');
                    } else {
                        $("#processing_section").addClass('d-none');
                        $(".dashboard_section").addClass('d-none');
                        $("#error_section").removeClass('d-none');

                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    $("#processing_section").addClass('d-none');
                    $(".dashboard_section").addClass('d-none');
                    $("#error_section").removeClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        }

        function updateBookingSection(idPrefix, value, difference, chartFunction, chartData) {
            $(`#${idPrefix}`).text(value);
            $(`#${idPrefix}_difference`).html(difference);
            chartFunction(chartData);
        }

        function updateAlertSection(idPrefix, value) {
            $(`#${idPrefix}`).text(value);
            if (value !== 0) {
                $(`#${idPrefix}_border`).removeClass('border-gray-400').addClass('border-dark');
                $(`.${idPrefix}_class`).removeClass('text-gray-400');
                $(`.${idPrefix}`).removeClass('text-gray-400').addClass('text-danger');
            } else {
                $(`#${idPrefix}_border`).addClass('border-gray-400').removeClass('border-dark');
                $(`.${idPrefix}_class`).addClass('text-gray-400');
                $(`.${idPrefix}`).addClass('text-gray-400').removeClass('text-danger');
            }
        }
    </script>

    @yield('bookings_js')
    @yield('alert_js')
    @yield('quick_search_js')
    @yield('top_client_js')
    @yield('shifts_and_hours_trends_js')
    @yield('week_snapshot_js')
@endsection
