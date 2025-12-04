@extends('theme.page')

@section('title', 'Timesheet & Bonus Editor')

@section('css')
    <style>
        .btn-light-primary-custom{
            background-color: #DAECF9;
        }

        .active {
            background-color: #FFF;
            color: #333;
        }

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
@endsection

@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <div class="card-body py-4">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="client" class="text-muted fs-6 fw-bold">Client</label>
                                                        <select name="client" id="client" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                                            <option value="">Select...</option>
                                                            @if($client)
                                                                @foreach($client as $row)
                                                                    <option {{ (isset($selectedData['client_id']) && $row['id'] == $selectedData['client_id']) ? 'selected' : '' }} value="{{ $row['id'] }}">{{ $row['company_name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="site" class="text-muted fs-6 fw-bold">Site</label>
                                                        <select name="site" id="site" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                                            <option value="">Select...</option>
                                                            @if(isset($selectedData['sites']))
                                                                @foreach($selectedData['sites'] as $s_row)
                                                                    <option {{ ($s_row['id'] == $selectedData['site_id']) ? 'selected' : '' }} value="{{ $s_row['id'] }}">{{ $s_row['site_name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="site" class="text-muted fs-6 fw-bold">Payroll Week No.</label>
                                                        <select name="payroll_week_number" id="payroll_week_number" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                                            <option value="">Select...</option>
                                                            @if($payroll_week_number)
                                                                @foreach($payroll_week_number as $pwn_row)
                                                                    @php($value = $pwn_row['payroll_week_number'].'_'.$pwn_row['year'])
                                                                    <option {{ (isset($selectedData['payroll_week']) && $value == $selectedData['payroll_week']) ? 'selected' : '' }} value="{{ $pwn_row['payroll_week_number'].'_'.$pwn_row['year'] }}">{{ $pwn_row['payroll_week_number'].' - '.$pwn_row['year'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <button type="submit" name="filter_btn" id="filter_btn" class="btn btn-primary mt-7 w-100">Go</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <a href="{{ url('timesheet-and-bonus-editor') }}" id="reset_search_form"  class="btn btn-dark mt-7 w-100">Reset</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BEGIN TIMESHEET & BONUS SECTION-->
                            <div id="timesheet_editor_section">
                                <ul class="nav ms-10 pt-10">
                                    <li class="nav-item">
                                        <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm active" data-bs-toggle="tab" href="#timesheet_nav" id="timesheet_nav_menu">Timesheet</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#bonus_nav" id="bonus_nav_menu">Bonus</a>
                                    </li>
                                </ul>
                                <div class="card mt-n1">
                                    <div class="card-body">
                                        <div class="tab-content">
                                            @include('timesheet_and_bonus.partial.timesheet_editor_section')
                                            @include('timesheet_and_bonus.partial.bonus_editor_section')
                                        </div>
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <div class="card-footer text-center bg-gray-100 d-none" id="card_footer">
                                    <a href="javascript:;" class="btn btn-primary mt-10" id="create_payroll_report_btn">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fs-2 las la-lock"></i>
                                    </span>
                                        Create payroll report
                                    </a>
                                </div>
                            </div>
                            @include('payroll_report.partial.create_payroll_report_process')
                            @include('payroll_report.partial.create_payroll_report_success')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('timesheet_and_bonus.partial.edit_timesheet_entry_modal')
    @include('timesheet_and_bonus.partial.edit_bonus_entry_modal')
    @include('payroll_report.partial.create_payroll_modal')
@endsection

@section('js')
    @yield('timesheet_editor_section_js')
    @yield('edit_timesheet_entry_js')

    @yield('bonus_editor_section_js')
    @yield('edit_bonus_entry_js')

    @yield('create_payroll_script')
    <script>
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', () => {
                localStorage.setItem('viewTimesheetAndBonusActiveTab', tab.id);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = localStorage.getItem('viewTimesheetAndBonusActiveTab');
            if (activeTab) {
                document.getElementById(activeTab).click();
            }
        });
    </script>

    <script>
        $("#client").on('change', function (){
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-site-using-client') }}',
                data    : {
                    _token    : '{{ csrf_token() }}',
                    client_id : $("#client").val(),
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#site").empty().append(response.data.site_option);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(document).ready(function() {
            let payrollUrlParams = '{{ \Illuminate\Support\Facades\Request::get('filtered') }}';
            if (payrollUrlParams && payrollUrlParams.trim() !== '') {
                $("#filter_btn").click();
            }
        });

        $("#filter_btn").on('click', function() {
            let client = $("#client").val();
            let site   = $("#site").val();
            let pwn    = $("#payroll_week_number").val();

            if (client && site && pwn) {
                $("#card_footer").removeClass('d-none');

                let filterParam = "filtered=" + client + "." + site + "." + pwn;
                let newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + filterParam;
                window.history.pushState({ path: newUrl }, '', newUrl);

                if(localStorage.getItem('viewTimesheetAndBonusActiveTab') === 'timesheet_nav_menu') {
                    preparedTimesheetView(client, site, pwn);
                } else {
                    preparedBonusView(client, site, pwn);
                }
            } else {
                toastr.error('Please fill in all filter fields before submitting.');
                return;
            }
        });

        $(".delete-all-ignore-entry").on('click', function () {
            let type = $(this).attr('data-type');
            let ids  = $(this).attr('data-ids');
            sweetAlertConfirmDelete('Do you want to delete this all '+type+' entry!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('delete-ignored-entry') }}',
                        data    : {
                            _token  : '{{ @csrf_token() }}',
                            type    : type,
                            ids     : ids
                        },
                        success : function (response) {
                            decodeResponse(response);

                            if(response.code === 200) {
                                $("#filter_btn").click()
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        })
    </script>
@endsection
