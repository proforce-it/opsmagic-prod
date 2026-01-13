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
                                <form id="report_form">
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
                                                                <option value="Any">Any...</option>
                                                                @if($costCentre)
                                                                    @foreach($costCentre as $cc_row)
                                                                        <option value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
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
                                                                <input class="form-control ps-12 flatpickr-input date_input " placeholder="Select start date" name="start_date" id="start_date" type="text" value=""> <!--readonly="readonly"-->
                                                            </div>
                                                            <span class="text-danger error" id="start_date_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                                            <label for="end_date" class="text-muted fs-6 fw-bold required">End date</label>
                                                            <div class="position-relative d-flex align-items-center">
                                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                    <i class="fs-2 las la-calendar"></i>
                                                                </span>
                                                                <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select end date" name="end_date" id="end_date" type="text" readonly="readonly" value="">
                                                            </div>
                                                            <span class="text-danger error" id="end_date_error"></span>
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
        let start_date = $("#start_date");
        start_date.flatpickr({
            dateFormat  : "d-m-Y",
            allowInput: true
        });

        start_date.on('change', function () {
            let end_date_box = $("#end_date");
            end_date_box.val('');

            let value   = $(this).val();
            let dateAr  = value.split('-');
            let date    = dateAr[1] + '-' + dateAr[0] + '-' + dateAr[2];

            let newDate         = new Date(date);
            let currentMonth    = newDate.getMonth();
            let currentDate     = newDate.getDate();
            let currentYear     = newDate.getFullYear();

            end_date_box.prop('disabled', false)
            end_date_box.flatpickr({
                minDate: new Date(currentYear, currentMonth, currentDate),
                dateFormat  : "d-m-Y",
                allowInput: true
            });
        });

        $("#report_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#report_form_submit_btn").addClass('d-none');
            $("#report_form_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('new-starters-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#report_form_submit_btn").removeClass('d-none');
                    $("#report_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        const blob = new Blob([response.data.csv], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement("a");
                        const url = URL.createObjectURL(blob);
                        link.setAttribute("href", url);
                        link.setAttribute("download", response.data.fileName);
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#report_form_submit_btn").removeClass('d-none');
                    $("#report_form_process_btn").addClass('d-none');
                }
            });
        });
    </script>
@endsection
