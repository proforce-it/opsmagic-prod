@extends('theme.page')

@section('title', 'Worker uploader')

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
                                                            <label for="shift_file" class="fs-1 fw-bold mb-5">IMPORT WORKERS</label>
                                                            <input type="file" name="worker_file" id="worker_file" class="form-control">
                                                            <span class="text-danger error" id="worker_file_error"></span>
                                                        </div>
                                                        <div class="text-uppercase">
                                                            <label class="fs-6 fw-bold text-gray-400">CSV FILES ONLY <a href="javascript:;" class="text-primary" id="download_sample_file">Download sample</a></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1">
                                                        <div class="mb-10 fv-row fv-plugins-icon-container float-end">
                                                            <button type="submit" class="btn btn-primary mt-15" id="formSubmitButton">Upload</button>
                                                            <a href="{{ url('worker-uploader') }}" class="btn btn-secondary mt-15 d-none" id="reloadThisPage"> Refresh </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card mt-10">
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
                                                                <td class="p-0 ps-3">You can also <a href="{{ url('create-worker') }}">add a single worker manually</a></td>
                                                            </tr>
                                                            <tr class="fs-5 fw-bold">
                                                                <td class="p-0">2.</td>
                                                                <td class="p-0 ps-3">All imported workers will be created as ‘prospects’ and will require additional data & document uploads to be
                                                                    made active. Missing data will be indicated by red slags on the worker record.</td>
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
                                <!--end::Card body-->
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
        $("#download_sample_file").on('click', function (){
            window.location.href="{{ asset('workers/sample_file/associate_upload.csv') }}"
        });

        let data = '';
        $("#formSubmit").on('submit', function (e) {
            submitEffect('hide');

            $(".error").html('');
            e.preventDefault();

            $.ajax({
                type        : 'post',
                url         : '{{ url('worker-upload-action') }}',
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
            downloadLink.download = 'Worker-report.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
        });
    </script>
@endsection
