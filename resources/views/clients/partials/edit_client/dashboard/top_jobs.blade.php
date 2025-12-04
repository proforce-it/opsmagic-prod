<div class="card card-bordered card-shadow mt-6">
    <div class="card-header bg-gray-300 min-h-50px">
        <div class="card-title">
            <span class="fs-2">TOP 5 JOBS (BY HOURS - PW {{ $previousPayrollWeek['payroll_week_number'] }})</span>
        </div>
    </div>
    <div class="card-body py-4 top-client-chart-card-body">
        <div style="width:70%;">
            <canvas id="top_client_chart"></canvas>
        </div>
    </div>
</div>

@section('top_client_js')
    <script>
        let topJobChart = null;
        function top_5_job_chart(value) {
            let topClientChartData = {
                labels: value.labels,
                datasets: [{
                    backgroundColor: ['#A358A7', '#5EA89C', '#EEC578', '#DC2938', '#0D2A52'],
                    hoverBackgroundColor: ['#A358A7', '#5EA89C', '#EEC578', '#DC2938', '#0D2A52'],
                    data: value.data
                }]
            };

            let topJobChartOptions = {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right'
                    }
                },
                maintainAspectRatio: false,
                cutout: '50%',
                layout: {
                    padding: 5
                }
            };

            let top_5_job_chart_canvas = document.getElementById('top_client_chart')
            top_5_job_chart_canvas.style.width = '550px';
            top_5_job_chart_canvas.style.height = '250px';
            let t5cc = top_5_job_chart_canvas.getContext('2d');

            if (topJobChart !== null) {
                topJobChart.destroy();
            }
            topJobChart = new Chart(t5cc, {
                type: 'doughnut',
                data: topClientChartData,
                options: topJobChartOptions
            });
        }
    </script>
@endsection