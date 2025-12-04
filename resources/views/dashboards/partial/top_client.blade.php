<div class="card card-bordered card-shadow mt-6">
    <div class="card-header bg-success min-h-50px">
        <div class="card-title">
            <i class="fs-xxl-1 text-white las la-medal"></i>
            <span class="fs-2 ms-4 text-white">Top 5 clients</span>
        </div>
        <div class="card-toolbar">
            <span class="text-white fs-5">LAST 5 WEEKS</span>
        </div>
    </div>
    <div class="card-body py-4 top-client-chart-card-body">
        <div style="width:100%;">
            <canvas id="top_client_chart"></canvas>
        </div>
    </div>
</div>

@section('top_client_js')
    <script>

        let topClientChart = null;
        function top_5_clients_chart(value) {
            let topClientChartData = {
                labels: value.labels,
                datasets: [{
                    backgroundColor: ['#3b76b0', '#bbde92', '#539e3f', '#ef9f9d', '#d13629', '#aecde2'],
                    hoverBackgroundColor: ['#3b76b0', '#bbde92', '#539e3f', '#ef9f9d', '#d13629', '#aecde2'],
                    data: value.data
                }]
            };

            let topClientChartOptions = {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right'
                    }
                },
                maintainAspectRatio: false
            };

            let top_5_clients_chart_canvas = document.getElementById('top_client_chart')
            top_5_clients_chart_canvas.style.width = '512px';
            top_5_clients_chart_canvas.style.height = '256px';
            let t5cc = top_5_clients_chart_canvas.getContext('2d');

            if (topClientChart !== null) {
                topClientChart.destroy();
            }
            topClientChart = new Chart(t5cc, {
                type: 'doughnut',
                data: topClientChartData,
                options: topClientChartOptions
            });
        }
    </script>
@endsection