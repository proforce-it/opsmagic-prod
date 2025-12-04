<div class="card card-bordered card-shadow mt-6">
    <div class="card-header bg-gray-300 min-h-50px">
        <div class="card-title">
            <span class="fs-2">TIMESHEETS & HOURS (5WK TREND)</span>
        </div>
    </div>
    <div class="card-body py-4">
        <canvas id="shift_and_hours_trends_chart" style="height: 180px; max-height: 180px;"></canvas>
    </div>
</div>

@section('shifts_and_hours_trends_js')
    <script>
        let shiftsAndHoursTrendsChart = null;
        function shift_and_hours_trend_chart(result) {
            let sht = document.getElementById('shift_and_hours_trends_chart').getContext('2d');
            if (shiftsAndHoursTrendsChart !== null) {
                shiftsAndHoursTrendsChart.destroy();
            }
            shiftsAndHoursTrendsChart = new Chart(sht, {
                type: 'bar',
                data: {
                    labels: result.label,
                    datasets: [
                        {
                            label: 'Timesheets',
                            backgroundColor: ['rgba(152, 166, 194, 0.8)'],
                            data: result.shift_data
                        },
                        {
                            label: 'Hours',
                            backgroundColor: ['rgba(57, 75, 95, 0.8)'],
                            data: result.hours_data
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                title: function() {
                                    return '';
                                },
                                label: function(context) {
                                    return context.raw.toString();
                                }
                            }
                        }
                    }
                },
            });
        }
    </script>
@endsection
