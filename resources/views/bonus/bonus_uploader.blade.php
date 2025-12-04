@extends('theme.page')

@section('title', 'Bonus uploader')

@section('content')
    <style>
        .loader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 160px;
            background-color: #F5F8FA;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .loader {
            border: 4px solid #909FAF;
            border-top: 8px solid #019EF7;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loader-text {
            margin-top: 15px;
            font-size: 18px;
            color: #333;
        }
    </style>
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <form id="formSubmit">
                                    @csrf
                                    <div class="card-body py-4">
                                        <div class="w-100">
                                            <div class="fv-row">
                                                <div class="row">
                                                    <div class="col-lg-11">
                                                        <div class="fv-row fv-plugins-icon-container">
                                                            <label for="bonus_file" class="fs-1 fw-bold mb-5">UPLOAD BONUSES</label>
                                                            <input type="file" name="bonus_file" id="bonus_file" class="form-control">
                                                            <span class="text-danger error" id="bonus_file_error"></span>
                                                        </div>
                                                        <div class="text-uppercase">
                                                            <label class="fs-6 fw-bold text-gray-400">CSV FILES ONLY <a href="javascript:;" class="text-primary ms-2" id="download_sample_file">Download sample file</a></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container float-end">
                                                            <button type="submit" class="btn btn-primary mt-15" id="formSubmitButton">Upload</button>
                                                            <a href="{{ url('bonus-uploader') }}" class="btn btn-secondary mt-15 d-none" id="reloadThisPage"> Refresh </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="card mt-5">
                                <div class="card-body py-4">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="fv-row fv-plugins-icon-container">
                                                        <label class="fs-3 fw-bold mb-5">NOTES </label>
                                                        <table class="table p-0">
                                                            <tr class="fs-5 fw-bold">
                                                                <td class="p-0">1.</td>
                                                                <td class="p-0 ps-3">Workers can only be given bonuses for weeks in which they have worked. So, ensure you upload bonus sheets <span class="fw-boldest">after</span> timesheets for the week have been added or imported</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-5 d-none" id="import_report_section">
                                <div class="card-header border-0 pt-6">
                                    <div class="card-title">
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <span>Import report</span>
                                        </div>
                                    </div>

                                    <div class="card-toolbar">
                                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                            <a href="javascript:;" class="btn btn-primary disabled" id="download_csv">
                                                <span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                            <path d="M14.8875071,11.8306874 L12.9310336,11.8306874 L12.9310336,9.82301606 C12.9310336,9.54687369 12.707176,9.32301606 12.4310336,9.32301606 L11.4077349,9.32301606 C11.1315925,9.32301606 10.9077349,9.54687369 10.9077349,9.82301606 L10.9077349,11.8306874 L8.9512614,11.8306874 C8.67511903,11.8306874 8.4512614,12.054545 8.4512614,12.3306874 C8.4512614,12.448999 8.49321518,12.5634776 8.56966458,12.6537723 L11.5377874,16.1594334 C11.7162223,16.3701835 12.0317191,16.3963802 12.2424692,16.2179453 C12.2635563,16.2000915 12.2831273,16.1805206 12.3009811,16.1594334 L15.2691039,12.6537723 C15.4475388,12.4430222 15.4213421,12.1275254 15.210592,11.9490905 C15.1202973,11.8726411 15.0058187,11.8306874 14.8875071,11.8306874 Z" fill="#000000"/>
                                                        </g>
                                                    </svg>
												</span>
                                                Download .csv
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body py-4">
                                    <div class="loader-container" id="loader_section">
                                        <div class="loader"></div>
                                        <div class="loader-text">Processing...</div>
                                    </div>
                                    <div id="import_report_table"></div>
                                </div>

                                <div class="card-footer text-center">
                                    <a href="{{ url('timesheet-and-bonus-editor') }}" class="btn btn-primary" id="bonus_editor">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"/>
                                                    <path d="M12,22 C7.02943725,22 3,17.9705627 3,13 C3,8.02943725 7.02943725,4 12,4 C16.9705627,4 21,8.02943725 21,13 C21,17.9705627 16.9705627,22 12,22 Z" fill="#000000" opacity="0.3"/>
                                                    <path d="M11.9630156,7.5 L12.0475062,7.5 C12.3043819,7.5 12.5194647,7.69464724 12.5450248,7.95024814 L13,12.5 L16.2480695,14.3560397 C16.403857,14.4450611 16.5,14.6107328 16.5,14.7901613 L16.5,15 C16.5,15.2109164 16.3290185,15.3818979 16.1181021,15.3818979 C16.0841582,15.3818979 16.0503659,15.3773725 16.0176181,15.3684413 L11.3986612,14.1087258 C11.1672824,14.0456225 11.0132986,13.8271186 11.0316926,13.5879956 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z" fill="#000000"/>
                                                </g>
                                            </svg>
                                        </span>
                                        go to bonus editor
                                    </a>
                                </div>
                                <!--end::Card body-->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="single_bonus_entry_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Add a single bonus entry</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_single_bonus_entry_modal">
                        <i class="fs-2 las la-times"></i>
{{--                        <span class="svg-icon svg-icon-1">--}}
{{--                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />--}}
{{--                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />--}}
{{--                            </svg>--}}
{{--                        </span>--}}
                    </div>
                </div>
                <form id="single_bonus_entry_form">
                    @csrf
                    <div class="modal-body scroll-y">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="worker" class="text-muted fs-6 fw-bold required">Worker</label>
                                        <select name="worker" id="worker" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Choose a worker" data-allow-clear="true">
                                            <option value=""></option>
                                            @if($worker)
                                                @foreach($worker as $row)
                                                    <option value="{{ $row['id'] }}">{{ $row['first_name'].' '.$row['last_name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger error" id="worker_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="job" class="text-muted fs-6 fw-bold required">Job</label>
                                        <select name="job" id="job" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Choose a worker first" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                        <span class="text-danger error" id="job_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="payroll_week" class="text-muted fs-6 fw-bold required">Payroll week</label>
                                        <select name="payroll_week" id="payroll_week" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Choose a payroll week" data-allow-clear="true">
                                            <option value=""></option>
                                            @if($payroll_week)
                                                @foreach($payroll_week as $row)
                                                    <option value="{{ $row['payroll_week_number'] }}">{{ $row['payroll_week_number'].' - '.$row['year'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger error" id="payroll_week_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="bonus_type" class="text-muted fs-6 fw-bold required">Bonus type</label>
                                        <select name="bonus_type" id="bonus_type" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Choose a bonus type" data-allow-clear="true">
                                            <option value=""></option>
                                            <option value="attendance_bonus">Attendance bonus</option>
                                            <option value="production_bonus">Production bonus</option>
                                            <option value="one_off_bonus">One off bonus</option>
                                            <option value="loyalty_bonus">Loyalty bonus</option>
                                            <option value="weekend_bonus">Weekend bonus</option>
                                            <option value="referral_bonus">Referral bonus</option>
                                            <option value="other_bonus">Other bonus</option>
                                        </select>
                                        <span class="text-danger error" id="bonus_type_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="amount" class="text-muted fs-6 fw-bold required">Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                        <i class="las la-pound-sign" style="font-size: 24px"></i>
                                                </span>
                                            </div>
                                            <input class="form-control" name="amount" id="amount" type="text" value="" placeholder="0.00">
                                        </div>
                                        <span class="text-danger error" id="amount_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" name="single_bonus_entry_submit_btn" id="single_bonus_entry_submit_btn" class="btn btn-primary">Add bonus</button>
                                    <button type="button" class="btn btn-lg btn-primary disabled d-none" data-kt-stepper-action="submit" name="single_bonus_entry_process_btn" id="single_bonus_entry_process_btn">
                                        <span>Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $("#download_sample_file").on('click', function (){
            window.location.href="{{ asset('workers/sample_file/bonus.csv') }}"
        });

        let data = '';
        $("#formSubmit").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            submitEffect('hide');

            $.ajax({
                type        : 'post',
                url         : '{{ url('upload-bonus') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {

                    let i;
                    if(response.code === 200) {
                        data = response.data.reportArray;

                        toastr.success(response.message);
                        submitEffect('success');
                        $("#import_report_table").empty().append(response.data.table);
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                        submitEffect('show');
                    } else {
                        for (i = 0; i < Object.keys(response.data).length; i++) {
                            if (i === 0) {
                                $("#"+Object.keys(response.data)[0]).focus();
                            }
                            $("#" + Object.keys(response.data)[i] + "_error").empty().append(response.data[Object.keys(response.data)[i]][0]);

                        }
                        submitEffect('show');
                    }
                },
                error   : function (response) {
                    submitEffect('show');
                    toastr.error(response.statusText);
                }
            });
        });

        function submitEffect(value) {
            if(value === 'hide') {
                $("#import_report_section").removeClass('d-none')
                $("#formSubmitButton").addClass('d-none')
                $("#reloadThisPage").removeClass('d-none')
            } else if(value === 'show') {
                $("#import_report_section").addClass('d-none')
                $("#formSubmitButton").removeClass('d-none')
                $("#reloadThisPage").addClass('d-none')
                $("#download_csv").addClass('disabled')
            } else {
                $("#download_csv").removeClass('disabled')
                $("#loader_section").addClass('d-none')
            }
        }

        $('#download_csv').on('click', function() {
            let csv = data.map(row => row.join(',')).join('\n');
            let csvFile;
            let downloadLink;

            csvFile = new Blob([csv], { type: 'text/csv' });
            downloadLink = document.createElement('a');
            downloadLink.download = 'bonus-import-report.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
        });

        $(document).ready(function() {
            $(".form-select-custom").select2({
                dropdownParent: $("#single_bonus_entry_form")
            });
        });

        $("#single_bonus_entry_modal_btn").on('click', function () {
            $(".error").html('');
            $(".select2_drp").val('').trigger('change');
            $("#single_bonus_entry_form").trigger('reset');
            $("#single_bonus_entry_modal").modal('show');
        });

        $("#cls_btn_single_bonus_entry_modal").on('click', function (){
            $(".select2_drp").val('').trigger('change');
            $("#single_bonus_entry_form").trigger('reset');
            $("#single_bonus_entry_modal").modal('hide');
        });

        $("#worker").on('change', function () {
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-client-job-using-worker') }}',
                data    : {
                    _token    : '{{ csrf_token() }}',
                    worker_id : $("#worker").val(),
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#job").empty().append(response.data.job_option);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#single_bonus_entry_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#single_bonus_entry_submit_btn").addClass('d-none')
            $("#single_bonus_entry_process_btn").removeClass('d-none')

            $.ajax({
                type        : 'post',
                url         : '{{ url('single-bonus-entry-create-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    $("#single_bonus_entry_submit_btn").removeClass('d-none')
                    $("#single_bonus_entry_process_btn").addClass('d-none')

                    if(response.code === 200) {
                        toastr.success(response.message);
                        $("#cls_btn_single_bonus_entry_modal").click();
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                    } else {
                        for (let i = 0; i < Object.keys(response.data).length; i++) {
                            if (i === 0) {
                                $("#"+Object.keys(response.data)[0]).focus();
                            }
                            $("#" + Object.keys(response.data)[i] + "_error").empty().append(response.data[Object.keys(response.data)[i]][0]);

                        }
                    }
                },
                error   : function (response) {
                    submitEffect('show');
                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
