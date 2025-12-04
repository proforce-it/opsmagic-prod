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
                <td>
                    TMSHTS <span id="total_ws_shift_difference"></span>
                </td>
                <td>
                    HOURS <span id="total_ws_hours_difference"></span>
                </td>
                <td>
                    CHARGE <span id="total_ws_charged_difference"></span>
                </td>
                <td>
                    PAY <span id="total_ws_paid_difference"></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="fw-bolder fs-xxl-1" id="total_ws_shift">0</span>
                </td>
                <td>
                    <span class="fw-bolder fs-xxl-1" id="total_ws_hours">0</span>
                </td>
                <td>
                    <span class="fw-bolder fs-xxl-1" id="total_ws_charged">0</span>
                </td>
                <td>
                    <span class="fw-bolder fs-xxl-1" id="total_ws_paid">0</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>