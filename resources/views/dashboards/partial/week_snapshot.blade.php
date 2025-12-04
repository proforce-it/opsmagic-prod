<div class="card card-bordered card-shadow">
    <div class="card-header bg-success min-h-50px">
        <div class="card-title">
            <i class="fs-xxl-1 text-white las la-stopwatch"></i>
            <span class="fs-2 ms-4 text-white">Week {{ $previousPayrollWeek['payroll_week_number'] }} snapshot</span>
        </div>
        <div class="card-toolbar">
            <span class="text-white fs-5">PAY DATE: <span id="snapshot_pay_date"></span></span>
        </div>
    </div>
    <div class="card-body py-4 fs-5">
        <div class="row">
            <div class="col-lg-7">
                <i class="fs-xxl-1 text-gray-800 las la-users-cog"></i>
                <span class="fw-bolder ps-5" id="total_ws_shift">0</span> timesheets
            </div>
            <div class="col-lg-5">
                <i class="fs-xxl-1 text-gray-800 las la-receipt"></i>
                <span class="fw-bolder ps-5">
                    <span id="total_ws_charged">0</span>
                </span> charged
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-lg-7">
                <i class="fs-xxl-1 text-gray-800 las la-business-time"></i>
                <span class="fw-bolder ps-5" id="total_ws_hours">0</span> hours
            </div>
            <div class="col-lg-5">
                <i class="fs-xxl-1 text-gray-800 las la-wallet"></i>
                <span class="fw-bolder ps-5">
                    <span class="svg-icon svg-icon-2 svg-icon-dark me-n2">
                        <i class="fs-2 text-black las la-pound-sign"></i>
                    </span>
                    <span id="total_ws_paid">0</span>
                </span> paid
            </div>
        </div>
        <div class="row text-center mt-5 bg-gray-200 p-2 rounded-3">
            <div class="col-lg-3">
                <div class="fs-5">AVG. CHARGE</div>
                <div class="fs-2 fw-bolder">
                    £<span id="total_ws_avg_charge">0</span>/hr
                </div>
                <div id="avg_charge_difference"></div>
            </div>
            <div class="col-lg-6">
                <div class="fs-5">AVG. PAY</div>
                <div class="fs-2 fw-bolder">
                    £<span id="total_ws_avg_pay">0</span>/hr
                </div>
                <div id="avg_pay_difference"></div>
            </div>
            <div class="col-lg-3">
                <div class="fs-5">AVG. MARGIN</div>
                <div class="fs-2 fw-bolder">
                    £<span id="total_ws_avg_margin">0</span>%
                </div>
                <div id="avg_margin_difference"></div>
            </div>
        </div>

    </div>
</div>
