@extends('theme.page')

@section('title', 'Dashboard')
@section('css')
    <style>
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
            height: 300px; /* Adjust as necessary */
        }

        .top-client-chart-card-body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .top-client-chart-card-body canvas {
            margin-right: 20px; /* Spacing between the chart and legend */
        }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post"> <!--d-flex-->
                        <div id="kt_content_container" class="container-xxl"> <!--container-xxl-->

                            @include('dashboards.partial.processing')
                            @include('dashboards.partial.error')

                            <div class="row d-none" id="dashboard_section">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header border-0">
                                            <div class="card-title w-100">
                                                <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                                <select name="cost_center" id="cost_center" class="form-select form-select-lg" data-control="select2" data-placeholder="All cost center" data-allow-clear="true">
                                                    <option value="">All cost center</option>
                                                    <option value="BAR">BAR</option>
                                                    <option value="BOGW">BOGW</option>
                                                    <option value="CORN">CORN</option>
                                                    <option value="FWM">FWM</option>
                                                    <option value="HO">HO</option>
                                                    <option value="IOW">IOW</option>
                                                    <option value="KAC">KAC</option>
                                                    <option value="KFLD">KFLD</option>
                                                    <option value="KIND">KIND</option>
                                                    <option value="LINF">LINF</option>
                                                    <option value="MAKF">MAKF</option>
                                                    <option value="POUL">POUL</option>
                                                    <option value="PROD">PROD</option>
                                                    <option value="SKFD">SKFD</option>
                                                    <option value="SOT">SOT</option>
                                                    <option value="SWP">SWP</option>
                                                    <option value="THAN">THAN</option>
                                                    <option value="VAR">VAR</option>
                                                    <option value="WMID">WMID</option>
                                                    <option value="other">other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="card-body py-4">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    @include('dashboards.partial.alerts')
                                                    @include('dashboards.partial.bookings')
                                                    @include('dashboards.partial.worker_plus')
                                                </div>
                                                <div class="col-lg-6">
                                                    @include('dashboards.partial.week_snapshot')
                                                    @include('dashboards.partial.shifts_and_hours_trends')
                                                    @include('dashboards.partial.top_client')
                                                </div>
                                            </div>
                                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @yield('alert_js')
    <script>
        $(document).ready(function () {
            getDashboardData();
        });

        let cost_center_drp = $("#cost_center");
        cost_center_drp.on('change', function () {
            const cost_center = $(this).val();
            const baseUrl = "{{ url('worker-management') }}";

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
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-dashboard-data') }}',
                data    : {
                    _token : '{{ csrf_token() }}',
                    cost_center : cost_center_drp.val(),
                    payroll_week : '{{ $previousPayrollWeek['payroll_week_number'].'-'.$previousPayrollWeek['year'] }}'
                },
                success: function (response) {
                    if (response.code === 200) {
                        $(".loader-text").text('Data preparing...');

                        let result = response.data;

                        /*--- Begin alert section ---*/
                        $("#expiring_rtws").text(result.alert_section.expiring_rtws);
                        $("#shift_with_space").text(result.alert_section.shift_with_space);
                        $("#shift_workers_without_payroll").text(result.alert_section.shift_workers_without_payroll);
                        $("#workers_worked_greater_than_12_days").text(result.alert_section.workers_worked_greater_than_12_days);

                        /*--- Begin week snapshot section ---*/
                        $("#total_ws_shift").text(result.week_snapshot_section.shifts);
                        $("#total_ws_hours").text(result.week_snapshot_section.hours);
                        $("#total_ws_charged").text(result.week_snapshot_section.charged);
                        $("#total_ws_paid").text(result.week_snapshot_section.paid);

                        $("#total_ws_avg_charge").text(result.week_snapshot_section.avg_charge);
                        $("#total_ws_avg_pay").text(result.week_snapshot_section.avg_pay);
                        $("#total_ws_avg_margin").text(result.week_snapshot_section.avg_margin);

                        $("#avg_charge_difference").html(result.week_snapshot_section.avg_charge_difference);
                        $("#avg_pay_difference").html(result.week_snapshot_section.avg_pay_difference);
                        $("#avg_margin_difference").html(result.week_snapshot_section.avg_margin_difference);

                        $("#snapshot_pay_date").html(result.week_snapshot_section.pay_date);

                        /*--- Begin booking section ---*/
                        $("#b_job_shift_worker").text(result.booking_section.job_shift_worker);
                        $("#b_job_shift_worker_difference").html(result.booking_section.job_shift_worker_difference);
                        booking_job_shift_worker_chart(result.booking_section.job_shift_worker_chart);

                        $("#b_shifts").text(result.booking_section.shifts);
                        $("#b_shift_difference").html(result.booking_section.shift_difference);
                        booking_shift_chart(result.booking_section.shift_chart);

                        $("#b_jobs").text(result.booking_section.job);
                        $("#b_job_difference").html(result.booking_section.job_difference);
                        booking_job_chart(result.booking_section.job_chart);

                        $("#b_site").text(result.booking_section.site);
                        $("#b_site_difference").html(result.booking_section.site_difference);
                        booking_site_chart(result.booking_section.site_chart);

                        $("#b_client").text(result.booking_section.client);
                        $("#b_client_difference").html(result.booking_section.client_difference);
                        booking_client_chart(result.booking_section.client_chart);

                        /*--- Begin shift and hours trends ---*/
                        shift_and_hours_trend_chart(result.shift_and_hours_trends);

                        /*--- Begin worker plus section ---*/
                        $("#total_active_worker").text(result.worker_plus_section.total_active_worker);
                        $("#total_worker_added").text(result.worker_plus_section.total_worker_added);
                        $("#total_worker_leaver").text(result.worker_plus_section.total_worker_leaver);

                        /*--- Begin top 5 clients ---*/
                        top_5_clients_chart(result.top_client_section);

                        $("#processing_section").addClass('d-none');
                        $("#error_section").addClass('d-none');
                        $("#dashboard_section").removeClass('d-none');
                    } else {
                        $("#processing_section").addClass('d-none');
                        $("#dashboard_section").addClass('d-none');
                        $("#error_section").removeClass('d-none');

                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    $("#processing_section").addClass('d-none');
                    $("#dashboard_section").addClass('d-none');
                    $("#error_section").removeClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        }
    </script>

    @yield('bookings_js')
    @yield('shifts_and_hours_trends_js')
    @yield('top_client_js')
@endsection
