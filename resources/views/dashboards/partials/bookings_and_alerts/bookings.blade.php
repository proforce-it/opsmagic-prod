<div class="card card-bordered card-shadow mt-6">
    <div class="card-header bg-gray-300 min-h-50px">
        <div class="card-title">
            <span class="fs-2">CONFIRMED BOOKINGS (NEXT 7 DAYS)</span>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-row-bordered align-middle table-sm m-0 p-0">
            <tbody class="text-gray-800">
                <tr>
                    <td style="width: 75%">
                        TOTAL BOOKINGS <span id="b_job_shift_worker_difference"></span> <br>
                        <a href="javascript:;" class="total_bookings_view" data-days="7">
                            <span class="fw-bolder fs-xxl-1 text-primary" id="b_job_shift_worker">0</span>
                        </a>
                    </td>
                    <td class="align-bottom pb-3" style="width: 25%">
                        <canvas id="booking_job_shift_worker_chart"></canvas>
                    </td>
                </tr>
                <tr>
                    <td  style="width: 75%">
                        SHIFTS <span id="b_shifts_difference"></span> <br>
                        <a href="javascript:;" class="total_job_shift_view" data-days="7">
                            <span class="fw-bolder fs-xxl-1 text-primary" id="b_shifts">0</span>
                        </a>
                    </td>
                    <td class="align-bottom pb-3" style="width: 25%">
                        <canvas id="booking_shift_chart"></canvas>
                    </td>
                </tr>
                <tr>
                    <td  style="width: 75%">
                        JOBS <span id="b_jobs_difference"></span> <br>
                        <a href="javascript:;" class="total_job_view" data-days="7">
                            <span class="fw-bolder fs-xxl-1 text-primary" id="b_jobs">0</span>
                        </a>
                    </td>
                    <td class="align-bottom pb-3" style="width: 25%">
                        <canvas id="booking_job_chart"></canvas>
                    </td>
                </tr>
                <tr>
                    <td  style="width: 75%">
                        SITES <span id="b_site_difference"></span> <br>
                        <a href="javascript:;" class="total_site_view" data-days="7">
                            <span class="fw-bolder fs-xxl-1 text-primary" id="b_site">0</span>
                        </a>
                    </td>
                    <td class="align-bottom pb-3" style="width: 25%">
                        <canvas id="booking_site_chart"></canvas>
                    </td>
                </tr>
                <tr>
                    <td  style="width: 75%">
                        CLIENTS <span id="b_client_difference"></span> <br>
                        <a href="javascript:;" class="total_client_view" data-days="7">
                            <span class="fw-bolder fs-xxl-1 text-primary" id="b_client">0</span>
                        </a>
                    </td>
                    <td class="align-bottom pb-3" style="width: 25%">
                        <canvas id="booking_client_chart"></canvas>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="view_total_bookings_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 class="text-uppercase">Confirmed bookings (<span id="total_bookings_modal_header">next 7 days</span>) </h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_total_bookings_modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <input type="text" data-kt-total_bookings-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick search" />
                            </div>
                        </div>

                        <div class="card-toolbar"></div>
                    </div>
                    <div class="card-body py-4">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <table class="table align-middle table-row-dashed fs-7 gy-3" id="total_bookings_datatable">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Date</th>
                                <th>Worker</th>
                                <th>Job</th>
                                <th>Site</th>
                                <th>Client</th>
                                <th>CC</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_total_job_shift_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 class="text-uppercase">Shifts (<span id="total_job_shift_modal_header">next 7 days</span>) </h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_total_job_shift_modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <input type="text" data-kt-total_job_shift-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick search" />
                            </div>
                        </div>

                        <div class="card-toolbar"></div>
                    </div>
                    <div class="card-body py-4">
                        <table class="table align-middle table-row-dashed fs-7 gy-3" id="total_job_shift_datatable">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Date</th>
                                <th>Job</th>
                                <th>Site</th>
                                <th>Client</th>
                                <th>CC</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_total_job_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 class="text-uppercase">Jobs (<span id="total_job_modal_header">next 7 days</span>) </h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_total_job_modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <input type="text" data-kt-total_job-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick search" />
                            </div>
                        </div>

                        <div class="card-toolbar"></div>
                    </div>
                    <div class="card-body py-4">
                        <table class="table align-middle table-row-dashed fs-7 gy-3" id="total_job_datatable">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Job</th>
                                <th>Site</th>
                                <th>Client</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_total_site_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 class="text-uppercase">Sites (<span id="total_site_modal_header">next 7 days</span>) </h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_total_site_modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <input type="text" data-kt-total_site-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick search" />
                            </div>
                        </div>

                        <div class="card-toolbar"></div>
                    </div>
                    <div class="card-body py-4">
                        <table class="table align-middle table-row-dashed fs-7 gy-3" id="total_site_datatable">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Site</th>
                                <th>Client</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_total_client_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 class="text-uppercase">Clients (<span id="total_client_modal_header">next 7 days</span>) </h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_total_client_modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <input type="text" data-kt-total_client-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick search" />
                            </div>
                        </div>

                        <div class="card-toolbar"></div>
                    </div>
                    <div class="card-body py-4">
                        <table class="table align-middle table-row-dashed fs-7 gy-3" id="total_client_datatable">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Client</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
            const allZero = value.every(v => v === 0);
            bookingJobShiftWorkerChart = new Chart(bsc, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total shift',
                        data: allZero ? Array(7).fill(0.1) : value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: allZero ? 'rgba(0,0,0,0)' : 'rgba(243, 243, 243)',
                        borderColor: 'rgba(38, 144, 250, 1)',
                        borderWidth: { top: 3, bottom: 0, left: 0, right: 0 },
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: function (event, elements) {
                        if (elements.length > 0) {
                            get_total_booking_data(elements[0].index)
                        }
                    },
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

        function booking_shift_chart(value) {
            let booking_shift_chart_canvas = document.getElementById('booking_shift_chart');
            booking_shift_chart_canvas.style.width = '148px';
            booking_shift_chart_canvas.style.height = '43px';

            let bsc = booking_shift_chart_canvas.getContext('2d');
            if (bookingShiftChart !== null) {
                bookingShiftChart.destroy();
            }
            const bookingShiftChartAllZero = value.every(v => v === 0);
            bookingShiftChart = new Chart(bsc, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total shift',
                        data: bookingShiftChartAllZero ? Array(7).fill(0.1) : value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: bookingShiftChartAllZero ? 'rgba(0,0,0,0)' : 'rgba(243, 243, 243)',
                        borderColor: 'rgba(38, 144, 250, 1)',
                        borderWidth: { top: 3, bottom: 0, left: 0, right: 0 },
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: function (event, elements) {
                        if (elements.length > 0) {
                            get_total_job_shift_data(elements[0].index)
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true,
                            suggestedMin: bookingShiftChartAllZero ? 0 : undefined,
                            suggestedMax: bookingShiftChartAllZero ? 0.2 : undefined,
                            ticks: {
                                stepSize: bookingShiftChartAllZero ? 0.1 : undefined
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: !bookingShiftChartAllZero,
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
            const bookingJobChartAllZero = value.every(v => v === 0);
            bookingJobChart = new Chart(bsj, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total jobs',
                        data: bookingJobChartAllZero ? Array(7).fill(0.1) : value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: bookingJobChartAllZero ? 'rgba(0,0,0,0)' : 'rgba(243, 243, 243)',
                        borderColor: 'rgba(38, 144, 250, 1)',
                        borderWidth: { top: 3, bottom: 0, left: 0, right: 0 },
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: function (event, elements) {
                        if (elements.length > 0) {
                            get_total_job_data(elements[0].index)
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true,
                            suggestedMin: bookingJobChartAllZero ? 0 : undefined,
                            suggestedMax: bookingJobChartAllZero ? 0.2 : undefined,
                            ticks: {
                                stepSize: bookingJobChartAllZero ? 0.1 : undefined
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: !bookingJobChartAllZero,
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
            const bookingSiteChartAllZero = value.every(v => v === 0);
            bookingSiteChart = new Chart(bsss, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total site',
                        data: bookingSiteChartAllZero ? Array(7).fill(0.1) : value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: bookingSiteChartAllZero ? 'rgba(0,0,0,0)' : 'rgba(243, 243, 243)',
                        borderColor: 'rgba(38, 144, 250, 1)',
                        borderWidth: { top: 3, bottom: 0, left: 0, right: 0 },
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: function (event, elements) {
                        if (elements.length > 0) {
                            get_total_site_data(elements[0].index)
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true,
                            suggestedMin: bookingSiteChartAllZero ? 0 : undefined,
                            suggestedMax: bookingSiteChartAllZero ? 0.2 : undefined,
                            ticks: {
                                stepSize: bookingSiteChartAllZero ? 0.1 : undefined
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: !bookingSiteChartAllZero,
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
            const bookingClientChartAllZero = value.every(v => v === 0);
            bookingClientChart = new Chart(bcc, {
                type: 'bar',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'Total shift',
                        data: bookingClientChartAllZero ? Array(7).fill(0.1) : value.map(v => v === 0 ? 0.1 : v),
                        backgroundColor: bookingClientChartAllZero ? 'rgba(0,0,0,0)' : 'rgba(243, 243, 243)',
                        borderColor: 'rgba(38, 144, 250, 1)',
                        borderWidth: { top: 3, bottom: 0, left: 0, right: 0 },
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: function (event, elements) {
                        if (elements.length > 0) {
                            get_total_client_data(elements[0].index)
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true,
                            suggestedMin: bookingClientChartAllZero ? 0 : undefined,
                            suggestedMax: bookingClientChartAllZero ? 0.2 : undefined,
                            ticks: {
                                stepSize: bookingClientChartAllZero ? 0.1 : undefined
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: !bookingClientChartAllZero,
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

        // TOTAL BOOKINGS MODAL
        let tableNameTotalBookingsDatatable = $('#total_bookings_datatable');
        let total_bookings_table = tableNameTotalBookingsDatatable.DataTable();

        $(".total_bookings_view").on('click', function () {
            get_total_booking_data('All');
        })

        function get_total_booking_data(booking_bar_index) {
            $("#total_bookings_modal_header").text((booking_bar_index !== 'All') ? addDaysToCurrentDate(booking_bar_index) : 'next 7 days')
            total_bookings_table.destroy();
            total_bookings_table = tableNameTotalBookingsDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-total-bookings') }}',
                    "data": function (d) {
                        d._token = $('#_token').val();
                        d.cost_center = $("#cost_center").val();
                        d.booking_bar_index = booking_bar_index;
                    },
                },
                "columns": [
                    {"data": "date"},
                    {"data": "worker"},
                    {"data": "job"},
                    {"data": "site"},
                    {"data": "client"},
                    {"data": "cost_center"},
                    {"data": "action", "width":"10%", "sClass":"text-end"},
                ]
            });

            $("#view_total_bookings_modal").modal('show');
        }

        const filterSearchTotalBookings = document.querySelector('[data-kt-total_bookings-filter="search"]');
        filterSearchTotalBookings.addEventListener('keyup', function (e) {
            total_bookings_table.search(e.target.value).draw();
        });

        $("#cls_btn_total_bookings_modal").on('click', function (){
            $("#view_total_bookings_modal").modal('hide');
        })

        // TOTAL JOB SHIFT MODAL
        let tableNameTotalJobShiftDatatable = $('#total_job_shift_datatable');
        let total_job_shift_table = tableNameTotalJobShiftDatatable.DataTable();

        $(".total_job_shift_view").on('click', function () {
            get_total_job_shift_data('All')
        })

        function get_total_job_shift_data(job_shift_bar_index) {
            $("#total_job_shift_modal_header").text((job_shift_bar_index !== 'All') ? addDaysToCurrentDate(job_shift_bar_index) : 'next 7 days')
            total_job_shift_table.destroy();
            total_job_shift_table = tableNameTotalJobShiftDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-total-job-shift') }}',
                    "data": function (d) {
                        d._token = $('#_token').val();
                        d.cost_center = $("#cost_center").val();
                        d.job_shift_bar_index = job_shift_bar_index;
                    },
                },
                "columns": [
                    {"data": "date"},
                    {"data": "job"},
                    {"data": "site"},
                    {"data": "client"},
                    {"data": "cost_center"},
                    {"data": "action", "width":"10%", "sClass":"text-end"},
                ]
            });

            $("#view_total_job_shift_modal").modal('show');
        }

        const filterSearchTotalJobShift = document.querySelector('[data-kt-total_job_shift-filter="search"]');
        filterSearchTotalJobShift.addEventListener('keyup', function (e) {
            total_job_shift_table.search(e.target.value).draw();
        });

        $("#cls_btn_total_job_shift_modal").on('click', function (){
            $("#view_total_job_shift_modal").modal('hide');
        })

        // TOTAL JOB MODAL
        let tableNameTotalJobDatatable = $('#total_job_datatable');
        let total_job_table = tableNameTotalJobDatatable.DataTable();

        $(".total_job_view").on('click', function () {
            get_total_job_data('All')
        })

        function get_total_job_data(job_bar_index) {
            $("#total_job_modal_header").text((job_bar_index !== 'All') ? addDaysToCurrentDate(job_bar_index) : 'next 7 days')
            total_job_table.destroy();
            total_job_table = tableNameTotalJobDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-total-job') }}',
                    "data": function (d) {
                        d._token = $('#_token').val();
                        d.cost_center = $("#cost_center").val();
                        d.job_bar_index = job_bar_index;
                    },
                },
                "columns": [
                    {"data": "job"},
                    {"data": "site"},
                    {"data": "client"},
                    {"data": "action", "width":"10%", "sClass":"text-end"},
                ]
            });

            $("#view_total_job_modal").modal('show');
        }

        const filterSearchTotalJob = document.querySelector('[data-kt-total_job-filter="search"]');
        filterSearchTotalJob.addEventListener('keyup', function (e) {
            total_job_table.search(e.target.value).draw();
        });

        $("#cls_btn_total_job_modal").on('click', function (){
            $("#view_total_job_modal").modal('hide');
        })

        // TOTAL SITE MODAL
        let tableNameTotalSiteDatatable = $('#total_site_datatable');
        let total_site_table = tableNameTotalSiteDatatable.DataTable();

        $(".total_site_view").on('click', function () {
            get_total_site_data('All')
        })

        function get_total_site_data(site_bar_index) {
            $("#total_site_modal_header").text((site_bar_index !== 'All') ? addDaysToCurrentDate(site_bar_index) : 'next 7 days')
            total_site_table.destroy();
            total_site_table = tableNameTotalSiteDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-total-site') }}',
                    "data": function (d) {
                        d._token = $('#_token').val();
                        d.cost_center = $("#cost_center").val();
                        d.site_bar_index = site_bar_index;
                    },
                },
                "columns": [
                    {"data": "site"},
                    {"data": "client"},
                    {"data": "action", "width":"10%", "sClass":"text-end"},
                ]
            });

            $("#view_total_site_modal").modal('show');
        }

        const filterSearchTotalSite = document.querySelector('[data-kt-total_site-filter="search"]');
        filterSearchTotalSite.addEventListener('keyup', function (e) {
            total_site_table.search(e.target.value).draw();
        });

        $("#cls_btn_total_site_modal").on('click', function (){
            $("#view_total_site_modal").modal('hide');
        })

        // TOTAL CLIENT MODAL
        let tableNameTotalClientDatatable = $('#total_client_datatable');
        let total_client_table = tableNameTotalClientDatatable.DataTable();

        $(".total_client_view").on('click', function () {
            get_total_client_data('All')
        })

        function get_total_client_data(client_bar_index) {
            $("#total_client_modal_header").text((client_bar_index !== 'All') ? addDaysToCurrentDate(client_bar_index) : 'next 7 days')
            total_client_table.destroy();
            total_client_table = tableNameTotalClientDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-total-client') }}',
                    "data": function (d) {
                        d._token = $('#_token').val();
                        d.cost_center = $("#cost_center").val();
                        d.client_bar_index = client_bar_index;
                    },
                },
                "columns": [
                    {"data": "client"},
                    {"data": "action", "width":"10%", "sClass":"text-end"},
                ]
            });

            $("#view_total_client_modal").modal('show');
        }

        const filterSearchTotalClient = document.querySelector('[data-kt-total_client-filter="search"]');
        filterSearchTotalClient.addEventListener('keyup', function (e) {
            total_client_table.search(e.target.value).draw();
        });

        $("#cls_btn_total_client_modal").on('click', function (){
            $("#view_total_client_modal").modal('hide');
        })

        //ADD DAYS ON CURRENT DAYS
        function addDaysToCurrentDate(days) {
            const date = new Date();
            date.setDate(date.getDate() + days);

            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();

            return `${day}-${month}-${year}`;
        }
    </script>


@endsection
