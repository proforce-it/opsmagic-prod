<div class="card card-bordered card-shadow mt-6">
    <div class="card-header bg-success min-h-50px">
        <div class="card-title">
            <i class="fs-xxl-1 text-white las la-clipboard-list"></i>
            <span class="fs-2 ms-4 text-white">Bookings</span>
        </div>
        <div class="card-toolbar">
            <span class="text-white fs-5">LAST 7 DAYS</span>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table table-row-bordered align-middle fs-5 gy-3">
            <tbody class="text-gray-800">
                <tr>
                    <td class="w-20px">
                        <i class="fs-xxl-1 text-gray-800 las la-id-card"></i>
                    </td>
                    <td class="w-350px">
                        <span class="fw-bolder" id="b_job_shift_worker">0</span> bookings
                        <span id="b_job_shift_worker_difference"></span>
                    </td>
                    <td class="align-bottom pb-0">
                        <canvas id="booking_job_shift_worker_chart"></canvas>
                    </td>
                </tr>
                <tr>
                    <td class="w-20px">
                        <i class="fs-xxl-1 text-gray-800 las la-calendar-day"></i>
                    </td>
                    <td class="w-350px">
                        <span class="fw-bolder" id="b_shifts">0</span> shifts
                        <span id="b_shift_difference"></span>
                    </td>
                    <td class="align-bottom pb-0">
                        <canvas id="booking_shift_chart"></canvas>
                    </td>
                </tr>
                <tr>
                    <td class="w-20px">
                        <i class="fs-xxl-1 text-gray-800 las la-briefcase"></i>
                    </td>
                    <td class="w-350px">
                        <span class="fw-bolder" id="b_jobs">0</span> jobs
                        <span id="b_job_difference"></span>
                    </td>
                    <td class="align-bottom pb-0">
                        <canvas id="booking_job_chart"></canvas>
                    </td>
                </tr>
                <tr>
                    <td class="w-20px">
                        <i class="fs-xxl-1 text-gray-800 las la-map-marker-alt"></i>
                    </td>
                    <td class="w-350px">
                        <span class="fw-bolder" id="b_site">0</span> client sites
                        <span id="b_site_difference"></span>
                    </td>
                    <td class="align-bottom pb-0">
                        <canvas id="booking_site_chart"></canvas>
                    </td>
                </tr>
                <tr>
                    <td class="w-20px">
                        <i class="fs-xxl-1 text-gray-800 las la-industry"></i>
                    </td>
                    <td class="w-350px">
                        <span class="fw-bolder" id="b_client">0</span> clients
                        <span id="b_client_difference"></span>
                    </td>
                    <td class="align-bottom pb-0">
                        <canvas id="booking_client_chart"></canvas>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@section('bookings_js')
    <script>
        let bookingJobShiftWorkerChart = null;
        let bookingShiftChart = null;
        let bookingJobChart = null;
        let bookingSiteChart = null;
        let bookingClientChart = null;

        function booking_job_shift_worker_chart(value) {
            let booking_job_shift_worker_chart_canvas = document.getElementById('booking_job_shift_worker_chart');
            booking_job_shift_worker_chart_canvas.style.width = '148px';
            booking_job_shift_worker_chart_canvas.style.height = '43px';

            let bsc = booking_job_shift_worker_chart_canvas.getContext('2d');
            if (bookingJobShiftWorkerChart !== null) {
                bookingJobShiftWorkerChart.destroy();
            }
            bookingJobShiftWorkerChart = new Chart(bsc, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total shift',
                        data: value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: 'rgba(243, 243, 243)',
                        borderColor: 'rgba(38, 144, 250)',
                        borderWidth: (context) => {
                            const index = context.dataIndex;
                            return value[index] === 0
                                ? { top: 0, bottom: 3, left: 0, right: 0 }
                                : { top: 3, bottom: 0, left: 0, right: 0 };
                        },
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true
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
                                    return value[tooltipItem.dataIndex].toString();
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

        function booking_shift_chart(value) {
            let booking_shift_chart_canvas = document.getElementById('booking_shift_chart');
            booking_shift_chart_canvas.style.width = '148px';
            booking_shift_chart_canvas.style.height = '43px';

            let bsc = booking_shift_chart_canvas.getContext('2d');
            if (bookingShiftChart !== null) {
                bookingShiftChart.destroy();
            }
            bookingShiftChart = new Chart(bsc, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total shift',
                        data: value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: 'rgba(243, 243, 243)',
                        borderColor: 'rgba(38, 144, 250)',
                        borderWidth: (context) => {
                            const index = context.dataIndex;
                            return { top: 3, bottom: 0, left: 0, right: 0 };
                        },
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true
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
                                    return value[tooltipItem.dataIndex].toString();
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

        function booking_job_chart(value) {
            let booking_job_chart_canvas = document.getElementById('booking_job_chart');
            booking_job_chart_canvas.style.width = '148px';
            booking_job_chart_canvas.style.height = '43px';

            let bsj = booking_job_chart_canvas.getContext('2d');
            if (bookingJobChart !== null) {
                bookingJobChart.destroy();
            }
            bookingJobChart = new Chart(bsj, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total jobs',
                        data: value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: 'rgba(243, 243, 243)',
                        borderColor: 'rgba(38, 144, 250)',
                        borderWidth: (context) => {
                            const index = context.dataIndex;
                            return { top: 3, bottom: 0, left: 0, right: 0 };
                        },
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true
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
                                    return value[tooltipItem.dataIndex].toString();
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

        function booking_site_chart(value) {
            let booking_site_chart_canvas = document.getElementById('booking_site_chart');
            booking_site_chart_canvas.style.width = '148px';
            booking_site_chart_canvas.style.height = '43px';

            let bsss = booking_site_chart_canvas.getContext('2d');
            if (bookingSiteChart !== null) {
                bookingSiteChart.destroy();
            }
            bookingSiteChart = new Chart(bsss, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total site',
                        data: value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: 'rgba(243, 243, 243)',
                        borderColor: 'rgba(38, 144, 250)',
                        borderWidth: (context) => {
                            const index = context.dataIndex;
                            return { top: 3, bottom: 0, left: 0, right: 0 };
                        },
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true
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
                                    return value[tooltipItem.dataIndex].toString();
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

        function booking_client_chart(value) {
            let booking_client_canvas = document.getElementById('booking_client_chart');
            booking_client_canvas.style.width = '148px';
            booking_client_canvas.style.height = '43px';

            let bcc = booking_client_canvas.getContext('2d');
            if (bookingClientChart !== null) {
                bookingClientChart.destroy();
            }
            bookingClientChart = new Chart(bcc, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total shift',
                        data: value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: 'rgba(243, 243, 243)',
                        borderColor: 'rgba(38, 144, 250)',
                        borderWidth: (context) => {
                            const index = context.dataIndex;
                            return { top: 3, bottom: 0, left: 0, right: 0 };
                        },
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true
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
                                    return value[tooltipItem.dataIndex].toString();
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
