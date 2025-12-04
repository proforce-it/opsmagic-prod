<div class="card card-bordered card-shadow mt-6">
    <div class="card-header bg-success min-h-50px">
        <div class="card-title">
            <i class="fs-xxl-1 text-white las la-chart-bar"></i>
            <span class="fs-2 ms-4 text-white">Timesheet trends</span>
        </div>
        <div class="card-toolbar">
            <span class="text-white fs-5">LAST 5 WEEKS</span>
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
                            label: 'Shift',
                            backgroundColor: ['rgba(133, 200, 252)'],
                            data: result.shift_data
                        },
                        {
                            label: 'Hours',
                            backgroundColor: ['rgba(38, 144, 250)'],
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
                    }
                }
            });
        }
    </script>
@endsection