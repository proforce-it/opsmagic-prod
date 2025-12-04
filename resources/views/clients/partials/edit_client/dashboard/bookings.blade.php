<div class="card card-bordered card-shadow mt-6">
    <div class="card-header bg-gray-300 min-h-50px">
        <div class="card-title">
            <span class="fs-2">BOOKINGS (NEXT 7 DAYS)</span>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-row-bordered align-middle table-sm m-0 p-0">
            <tbody class="text-gray-800">
            <tr>
                <td>
                    BOOKINGS <span id="b_job_shift_worker_difference"></span>
                </td>
                <td>
                    SHIFTS <span id="b_shift_difference"></span>
                </td>
                <td>
                    JOBS <span id="b_job_difference"></span>
                </td>
                <td>
                    SITES <span id="b_site_difference"></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="fw-bolder fs-xxl-1" id="b_job_shift_worker">0</span>
                </td>
                <td>
                    <span class="fw-bolder fs-xxl-1" id="b_shifts">0</span>
                </td>
                <td>
                    <span class="fw-bolder fs-xxl-1" id="b_jobs">0</span>
                </td>
                <td>
                    <span class="fw-bolder fs-xxl-1" id="b_site">0</span>
                </td>
            </tr>
            <tr class="border-top-2 border-gray-400">
                <td colspan="4">
                    <canvas id="booking_job_shift_worker_chart" style="height: 180px; max-height: 180px;"></canvas>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

@section('bookings_js')
    <script>
        let bookingJobShiftWorkerChart = null;

        function booking_job_shift_worker_chart(value) {
            let booking_job_shift_worker_chart_canvas = document.getElementById('booking_job_shift_worker_chart');
            booking_job_shift_worker_chart_canvas.style.width = '148px';
            booking_job_shift_worker_chart_canvas.style.height = '43px';

            let bsc = booking_job_shift_worker_chart_canvas.getContext('2d');
            if (bookingJobShiftWorkerChart !== null) {
                bookingJobShiftWorkerChart.destroy();
            }
            const allZero = value.data.every(v => v === 0);
            bookingJobShiftWorkerChart = new Chart(bsc, {
                type: 'bar',
                data: {
                    labels: value.labels,
                    datasets: [{
                        label: 'Total shift',
                        data: allZero ? Array(7).fill(0.1) : value.data.map(v => v === 0 ? 0.02 : v),
                        backgroundColor: allZero ? 'rgba(0,0,0,0)' : 'rgba(152, 166, 194)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: true
                        },
                        y: {
                            display: true,
                            beginAtZero: true,
                            suggestedMin: allZero ? 0 : undefined,
                            suggestedMax: allZero ? 0.2 : undefined,
                            ticks: {
                                stepSize: allZero ? 0.1 : undefined
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            position: 'nearest',
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#fff',
                            borderWidth: 1,
                            caretSize: 5,
                            padding: 6,
                            boxWidth: 10,
                            callbacks: {
                                title: function(tooltipItem) {
                                    return '';
                                },
                                label: function(tooltipItem) {
                                    return value.data[tooltipItem.dataIndex].toString();
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        intersect: false,
                    },
                    hover: {
                        mode: 'nearest',
                        animationDuration: 0,
                        intersect: false
                    }
                }
            });
        }
    </script>
@endsection
