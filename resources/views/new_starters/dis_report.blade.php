@extends('theme.page')

@section('title', 'New starter report')

@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <form id="report_form" method="post" action="{{ url('new-starters-action') }}">
                                    @csrf
                                    <div class="card-body py-4 collapsible_content mt-5">
                                        <div class="w-100">
                                            <div class="fv-row">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="report_type" class="text-muted fs-6 fw-bold">Select a report</label>
                                                            <select name="report_type" id="report_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Select a report type" data-allow-clear="true">
                                                                <option value="new_starters">New starters</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-5">
                                                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase text-muted gs-0 border-bottom border-4">FILTERS</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="cost_center" class="text-muted fs-6 fw-bold">Cost center</label>
                                                            <select name="cost_center" id="cost_center" class="form-select form-select-lg" data-control="select2" data-placeholder="Any..." data-allow-clear="true">
                                                                <option {{ old('cost_center', 'Any') == 'Any' ? 'selected' : '' }} value="Any">Any...</option>
                                                                @if($costCentre)
                                                                    @foreach($costCentre as $cc_row)
                                                                        <option {{ old('cost_center') == $cc_row['id'] ? 'selected' : '' }} value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="time_period" class="text-muted fs-6 fw-bold">Time period</label>
                                                            <select name="time_period" id="time_period" class="form-select form-select-lg bg-secondary" data-control="select2" data-placeholder="Select time period..." data-allow-clear="true" disabled>
                                                                <option value="between_two_dates">Between two dates</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="start_date" class="text-muted fs-6 fw-bold required">Start date</label>
                                                            <div class="position-relative d-flex align-items-center">
                                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                    <i class="fs-2 las la-calendar"></i>
                                                                </span>
                                                                <input class="form-control ps-12 flatpickr-input date_input " placeholder="Select start date" name="start_date" id="start_date" type="text" value="{{ old('start_date') }}">
                                                            </div>
                                                            @if ($errors->has('start_date'))
                                                                <span class="text-danger">{{ $errors->first('start_date') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="end_date" class="text-muted fs-6 fw-bold required">End date</label>
                                                            <div class="position-relative d-flex align-items-center">
                                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                    <i class="fs-2 las la-calendar"></i>
                                                                </span>
                                                                <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select end date" name="end_date" id="end_date" type="text" readonly="readonly" value="{{ old('end_date') }}">
                                                            </div>
                                                            @if ($errors->has('end_date'))
                                                                <span class="text-danger">{{ $errors->first('end_date') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-center">
                                        <button type="submit" name="report_form_submit_btn" id="report_form_submit_btn" class="btn btn-primary"> <i class="fs-1 las la-file-download text-white"></i> Download CSV </button>
                                        <button type="button" class="btn btn-lg btn-primary disabled d-none" name="report_form_process_btn" id="report_form_process_btn">
                                            <span>Please wait...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('#cost_center').val("{{ old('cost_center') }}").trigger('change');
        });

        let startDateInput = document.getElementById('start_date');
        let endDateInput   = document.getElementById('end_date');
        let endPicker = null;

        let startPicker = flatpickr(startDateInput, {
            dateFormat: "d-m-Y",
            allowInput: true,
            onChange: function (selectedDates, dateStr) {
                enableEndDate(dateStr, true);
            }
        });

        function enableEndDate(startDateStr, clearEnd = false) {
            if (!startDateStr) return;

            let parts = startDateStr.split('-');
            let minDate = new Date(parts[2], parts[1] - 1, parts[0]);

            endDateInput.disabled = false;

            if (endPicker) {
                endPicker.destroy();
            }

            endPicker = flatpickr(endDateInput, {
                dateFormat: "d-m-Y",
                allowInput: true,
                minDate: minDate
            });

            if (clearEnd) {
                endPicker.clear();
            }

            @if (old('end_date'))
                endPicker.setDate("{{ old('end_date') }}", true);
            @endif
        }

        if (startDateInput.value) {
            enableEndDate(startDateInput.value);
        }
    </script>
@endsection
