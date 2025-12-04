<div class="tab-pane fade show active" id="kt_table_widget_5_tab_8">
    <div id="kt_content_container" >
        <div class="d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="post flex-column-fluid" id="kt_post">
                <div id="kt_content_container">
                    @include('dashboards.partial.processing')
                    @include('dashboards.partial.error')

                    <div class="row d-none dashboard_section">
                        <div class="col-lg-6">
                            @include('clients.partials.edit_client.dashboard.alerts')
                            @include('clients.partials.edit_client.dashboard.bookings')
                        </div>
                        <div class="col-lg-6">
                            @include('clients.partials.edit_client.dashboard.snapshot')
                            @include('clients.partials.edit_client.dashboard.top_jobs')
                            @include('clients.partials.edit_client.dashboard.timesheet_and_hours_trends')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('edit_client_dashboard_js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function () {
            getDashboardData();
        });

        function getDashboardData() {
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-client-dashboard-data') }}',
                data    : {
                    _token : '{{ csrf_token() }}',
                    payroll_week : '{{ $previousPayrollWeek['payroll_week_number'].'-'.$previousPayrollWeek['year'] }}',
                    client_id : '{{ $client['id'] }}'
                },
                success: function (response) {
                    if (response.code === 200) {
                        $(".loader-text").text('Data preparing...');

                        let result = response.data;
                        /*--- Begin alert section ---*/
                        let alert_section = result.alert_section;
                        updateAlertSection("shift_with_space_tomorrow", alert_section.shift_with_space_tomorrow);
                        updateAlertSection("shift_with_space", alert_section.shift_with_space);
                        updateAlertSection("booking_invitation_to_chase", alert_section.booking_invitation_chase);

                        /*--- Begin snapshot section ---*/
                        let weekSnapshot = result.snapshot_section;
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

                        /*--- Begin booking section ---*/
                        let booking_section = result.booking_section;
                        updateBookingSection("b_job_shift_worker", booking_section.job_shift_worker, booking_section.job_shift_worker_difference);
                        updateBookingSection("b_shifts", booking_section.shifts, booking_section.shift_difference);
                        updateBookingSection("b_jobs", booking_section.job, booking_section.job_difference);
                        updateBookingSection("b_site", booking_section.site, booking_section.site_difference);
                        booking_job_shift_worker_chart(booking_section.job_shift_worker_chart);

                        top_5_job_chart(result.top_job_section);
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

        function updateAlertSection(idPrefix, value) {
            $(`#${idPrefix}`).text(value);
            if (value !== 0) {
                $(`#${idPrefix}_border`).removeClass('border-gray-400').addClass('border-dark');
                $(`.${idPrefix}_class`).removeClass('text-gray-400');
                $(`#${idPrefix}_view_btn i`).removeClass('text-gray-400').addClass('text-danger');
            } else {
                $(`#${idPrefix}_border`).addClass('border-gray-400').removeClass('border-dark');
                $(`.${idPrefix}_class`).addClass('text-gray-400');
                $(`#${idPrefix}_view_btn i`).addClass('text-gray-400').removeClass('text-danger');
            }
        }

        function updateBookingSection(idPrefix, value, difference) {
            $(`#${idPrefix}`).text(value);
            $(`#${idPrefix}_difference`).html(difference);
        }
    </script>
    @yield('alert_js')
    @yield('bookings_js')
    @yield('top_client_js')
    @yield('shifts_and_hours_trends_js')
@endsection
