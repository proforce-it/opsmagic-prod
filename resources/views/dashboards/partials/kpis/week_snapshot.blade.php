<div class="card card-bordered card-shadow">
    <div class="card-header bg-gray-300 min-h-50px">
        <div class="card-title">
            <span class="fs-2">SNAPSHOT (PW {{ $previousPayrollWeek['payroll_week_number'] }})</span>
        </div>
    </div>
    <div class="card-body py-4 fs-5">
        <div class="row text-center">
            <div class="col-lg-4 p-2">
                <div class="bg-success rounded-3 text-white p-4">
                    <div class="fs-5">AVG. CHARGE</div>
                    <div class="fs-2 fw-bolder">
                        £<span id="total_ws_avg_charge">0</span>/hr
                    </div>
                    <div id="avg_charge_difference"></div>
                </div>
            </div>
            <div class="col-lg-4 p-2">
                <div class="bg-success rounded-3 text-white p-4">
                    <div class="fs-5">AVG. PAY</div>
                    <div class="fs-2 fw-bolder">
                        £<span id="total_ws_avg_pay">0</span>/hr
                    </div>
                    <div id="avg_pay_difference"></div>
                </div>
            </div>
            <div class="col-lg-4 p-2">
                <div class="bg-success rounded-3 text-white p-4">
                    <div class="fs-5">AVG. MARGIN</div>
                    <div class="fs-2 fw-bolder">
                        <span id="total_ws_avg_margin">0</span>%
                    </div>
                    <div id="avg_margin_difference"></div>
                </div>
            </div>
        </div>

        <table class="table table-row-bordered align-middle table-sm m-0 p-0">
            <tbody class="text-gray-800">
            <tr>
                <td class="w-500px">
                    TIMESHEETS LOGGED <span id="total_ws_shift_difference"></span> <br>
                    <span class="fw-bolder fs-xxl-1" id="total_ws_shift">0</span>
                </td>
                <td class="align-bottom pb-3">
                    <canvas width="185" id="total_ws_timesheet_chart"></canvas>
                </td>
            </tr>
            <tr>
                <td class="w-500px">
                    HOURS LOGGED <span id="total_ws_hours_difference"></span> <br>
                    <span class="fw-bolder fs-xxl-1" id="total_ws_hours">0</span>
                </td>
                <td class="align-bottom pb-3">
                    <canvas id="total_ws_hours_chart"></canvas>
                </td>
            </tr>
            <tr>
                <td class="w-500px">
                    TOTAL CHARGE <span id="total_ws_charged_difference"></span> <br>
                    <span class="fw-bolder fs-xxl-1" id="total_ws_charged">0</span>
                </td>
                <td class="align-bottom pb-3">
                    <canvas id="total_ws_charged_chart"></canvas>
                </td>
            </tr>
            <tr>
                <td class="w-500px">
                    TOTAL PAY <span id="total_ws_paid_difference"></span> <br>
                    <span class="fw-bolder fs-xxl-1" id="total_ws_paid">0</span>
                </td>
                <td class="align-bottom pb-3">
                    <canvas id="total_ws_paid_chart"></canvas>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

@section('week_snapshot_js')
    <script>
        let snapshotTimesheetChart = null;
        let snapshotHoursLoggedChart = null;
        let snapshotChargedChart = null;
        let snapshotPaidChart = null;

        function snapshot_timesheet_chart(value) {
            let snapshot_timesheet_chart_canvas = document.getElementById('total_ws_timesheet_chart');
            snapshot_timesheet_chart_canvas.style.width = '148px';
            snapshot_timesheet_chart_canvas.style.height = '43px';

            let bsc = snapshot_timesheet_chart_canvas.getContext('2d');
            if (snapshotTimesheetChart !== null) {
                snapshotTimesheetChart.destroy();
            }
            const allZero = value.every(v => v === 0);
            snapshotTimesheetChart = new Chart(bsc, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total shift',
                        data: allZero ? Array(7).fill(0.1) : value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: allZero ? 'rgba(0,0,0,0)' : 'rgba(243, 243, 243)',
                        borderColor: 'rgba(121, 202, 146, 1)',
                        borderWidth: { top: 3, bottom: 0, left: 0, right: 0 },
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
                            enabled: !allZero,
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

        function snapshot_hours_logged_chart(value) {
            let snapshot_hours_logged_chart_canvas = document.getElementById('total_ws_hours_chart');
            snapshot_hours_logged_chart_canvas.style.width = '148px';
            snapshot_hours_logged_chart_canvas.style.height = '43px';

            let bsc = snapshot_hours_logged_chart_canvas.getContext('2d');
            if (snapshotHoursLoggedChart !== null) {
                snapshotHoursLoggedChart.destroy();
            }
            const allZero = value.every(v => v === 0);
            snapshotHoursLoggedChart = new Chart(bsc, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total shift',
                        data: allZero ? Array(7).fill(0.1) : value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: allZero ? 'rgba(0,0,0,0)' : 'rgba(243, 243, 243)',
                        borderColor: 'rgba(121, 202, 146, 1)',
                        borderWidth: { top: 3, bottom: 0, left: 0, right: 0 },
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
                            enabled: !allZero,
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

        function snapshot_charged_chart(value) {
            let snapshot_charged_chart_canvas = document.getElementById('total_ws_charged_chart');
            snapshot_charged_chart_canvas.style.width = '148px';
            snapshot_charged_chart_canvas.style.height = '43px';

            let bsc = snapshot_charged_chart_canvas.getContext('2d');
            if (snapshotChargedChart !== null) {
                snapshotChargedChart.destroy();
            }
            const allZero = value.every(v => v === 0);
            snapshotChargedChart = new Chart(bsc, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total shift',
                        data: allZero ? Array(7).fill(0.1) : value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: allZero ? 'rgba(0,0,0,0)' : 'rgba(243, 243, 243)',
                        borderColor: 'rgba(121, 202, 146, 1)',
                        borderWidth: { top: 3, bottom: 0, left: 0, right: 0 },
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
                            enabled: !allZero,
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

        function snapshot_paid_chart(value) {
            let snapshot_paid_chart_canvas = document.getElementById('total_ws_paid_chart');
            snapshot_paid_chart_canvas.style.width = '148px';
            snapshot_paid_chart_canvas.style.height = '43px';

            let bsc = snapshot_paid_chart_canvas.getContext('2d');
            if (snapshotPaidChart !== null) {
                snapshotPaidChart.destroy();
            }
            const allZero = value.every(v => v === 0);
            snapshotPaidChart = new Chart(bsc, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total shift',
                        data: allZero ? Array(7).fill(0.1) : value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: allZero ? 'rgba(0,0,0,0)' : 'rgba(243, 243, 243)',
                        borderColor: 'rgba(121, 202, 146, 1)',
                        borderWidth: { top: 3, bottom: 0, left: 0, right: 0 },
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
                            enabled: !allZero,
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