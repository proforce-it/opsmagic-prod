@extends('theme.page')

@php
    $title = 'Worker details - '.$worker['first_name'].' '.$worker['middle_name'].' '.$worker['last_name']
@endphp
@section('title', $title)
@section('content')
    <style>
        .btn-light-primary-custom{
            background-color: #DAECF9;
        }

        .active {
            background-color: #FFF;
            color: #333;
        }
        .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered{
            color: #181c32;
        }
    </style>
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">

        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post"> <!--d-flex-->
                        <div id="kt_content_container" class=""> <!--container-xxl-->
                            <div class="card mb-5">
                                <div class="card-body py-4">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-2 col-sm-2" id="worker_profile_pic">
                                            @if($worker['profile_pic'])
                                                <img src="{{ asset('workers/profile/'.$worker['profile_pic']) }}" alt="" class="w-70px h-70px" style="object-fit: contain; object-position: center;">
                                            @else
                                                <img src="{{ asset('assets/media/avatars/worker-square.png') }}" alt="" class="w-70px h-70px" style="object-fit: contain; object-position: center;">
                                            @endif
                                        </div>
                                        <div class="col-lg-6 col-md-4 col-sm-4">
                                            <div class="fs-1 fw-bold">
                                                {{ $worker['first_name'].' '.$worker['middle_name'].' '.$worker['last_name'] }}
                                            </div>
                                            <div class="d-flex flex-wrap">
                                                @php
                                                    $flags = \App\Helper\Workers\WorkerHelper::getFlags($worker);
                                                    $hasProspectWorker = $flags && Str::contains($flags, 'prospect_worker');
                                                    $hasIdDocumentIncomplete = $flags && Str::contains($flags, 'id_document_incomplete');
                                                    $activeWorkerClass = ($hasProspectWorker) ? 'disabled' : '';

                                                    $workerFieldsForRequiredDocument = [
                                                        'proforce_to_open_bank_account' => $worker['proforce_to_open_bank_account'],
                                                        'proforce_transport' => $worker['proforce_transport'],
                                                        'accommodation_type' => $worker['accommodation_type']
                                                    ]
                                                @endphp
                                                @if($flags)
                                                    {!! $flags !!}
                                                @else
                                                    <span class="svg-icon svg-icon-xl-1 svg-icon-success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24"/>
                                                                <path d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) "/>
                                                                <path d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) "/>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-6 col-sm-6 text-end">
                                            <div class="btn-group me-3 " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                                <label class="btn btn-outline btn-light-success btn-active-success
                                                    {{ ($worker['status'] == 'Prospect') ? 'active' : '' }}
                                                    {{ ($worker['status'] == 'Active') ? 'disabled' : '' }}"
                                                       data-kt-button="true">
                                                    <input class="btn-check worker_status" type="radio" name="worker_status" {{ ($worker['status'] == 'Prospect') ? 'checked' : '' }} value="Prospect" />
                                                    Prospect
                                                </label>
                                                <label class="btn btn-outline btn-light-success btn-active-success {{ ($worker['status'] == 'Active') ? 'active' : '' }} {{ $activeWorkerClass }}" data-kt-button="true">
                                                    <input class="btn-check worker_status" type="radio" name="worker_status" {{ ($worker['status'] == 'Active') ? 'checked' : '' }} {{ $activeWorkerClass }} value="Active" id="status-active"/>
                                                    Active
                                                </label>
                                                <label class="btn btn-outline btn-light-success btn-active-success {{ ($worker['status'] == 'Leaver') ? 'active' : '' }} {{ $activeWorkerClass }}" data-kt-button="true">
                                                    <input class="btn-check worker_status" type="radio" name="worker_status" {{ ($worker['status'] == 'Active') ? 'checked' : '' }} {{ $activeWorkerClass }} value="Leaver" id="status-leaver"/>
                                                    Leaver
                                                </label>
                                                <label class="btn btn-outline btn-light-success btn-active-success {{ ($worker['status'] == 'Archived') ? 'active' : '' }}" data-kt-button="true">
                                                    <input class="btn-check worker_status" type="radio" name="worker_status" {{ ($worker['status'] == 'Active') ? 'checked' : '' }} value="Archived" />
                                                    Archived
                                                </label>
                                                @if($worker['suspend'] == 'No')
                                                    <label class="btn btn-outline btn-light-primary btn-active-primary" data-kt-button="true">
                                                        <input class="btn-check worker_status" type="radio" name="worker_status" value="Suspend" />
                                                        Suspend
                                                    </label>
                                                @else
                                                    <label class="btn btn-outline btn-light-primary btn-active-primary" data-kt-button="true">
                                                        <input class="btn-check worker_status" type="radio" name="worker_status" value="Unsuspend" />
                                                        Unsuspend
                                                    </label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('workers.partials.edit_worker.email_not_verify')
                            @include('workers.partials.edit_worker.worker_tabs')
                            <div class="card mt-n1">
                                <div class="card-body">
                                    <div class="tab-content">

                                        @include('workers.partials.edit_worker.basic_info')

                                        @include('workers.partials.edit_worker.uk_addresses')

                                        {{--@include('workers.partials.edit_worker.addresses')--}}

                                        @include('workers.partials.edit_worker.bank_details')

                                        @include('workers.partials.edit_worker.rtws')

                                        @include('workers.partials.edit_worker.dis_groups')

                                        @include('workers.partials.edit_worker.dis_job')

                                        @include('workers.partials.edit_worker.dis_shifts_booked')

                                        @include('workers.partials.edit_worker.dis_shifts_worked')

                                        @include('workers.partials.edit_worker.dis_absence')

                                        @include('workers.partials.edit_worker.dis_notes')

                                        @include('workers.partials.edit_worker.dis_document')

                                        @include('workers.partials.edit_worker.activity_logs')
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('workers.partials.edit_worker.profile_pic_modal')
    @include('workers.partials.edit_worker.leaver_worker_modal')
@endsection

@section('js')
    @yield('edit_worker_basic_details_js')
    @yield('edit_worker_uk_addresses_js')
    @yield('edit_worker_addresses_js')
    @yield('edit_worker_bank_js')
    @yield('edit_worker_rtws_js')
    @yield('edit_worker_job_js')
    @yield('edit_worker_shifts_booked_js')
    @yield('edit_worker_shifts_worked_js')
    @yield('edit_worker_absence_js')
    @yield('edit_worker_note_js')
    @yield('edit_worker_document_js')

    @yield('edit_worker_profile_pic_js')
    @yield('edit_worker_email_not_verify_js')
    @yield('add_worker_leaver_date_js')
    @yield('group_js')
    <script>
        activeMenu('/worker-management');

        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', () => {
                localStorage.setItem('viewWorkerActiveTab_'+'{{ $worker['id'] }}', tab.id);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = localStorage.getItem('viewWorkerActiveTab_'+'{{ $worker['id'] }}');
            if (activeTab) {
                document.getElementById(activeTab).click();
            }
        });
    </script>
    <script>
        national_insurance_number.addEventListener('keyup',function (e) {
            if (e.keyCode !== 8) {
                if (this.value.length === 2 || this.value.length === 5 || this.value.length === 8 || this.value.length === 11) {
                    this.value = this.value += ' ';
                }
            }
        });

        /*bank_ifsc_code.addEventListener('keyup',function (e) {
            console.log(e.keyCode);
            if (e.keyCode !== 6) {
                if (this.value.length === 2 || this.value.length === 5) {
                    this.value = this.value += '-';
                }
            }
        });*/

        let d_date    = '{{ date('m', strtotime('-16 Years')) }}' + '-' + '{{ date('d', strtotime('-16 Years')) }}' + '-' + '{{ date('y', strtotime('-16 Years')) }}';
        let d_newDate         = new Date(d_date);
        let d_currentMonth    = d_newDate.getMonth();
        let d_currentDate     = d_newDate.getDate();
        let d_currentYear     = d_newDate.getFullYear();

        $("#date_of_birth").flatpickr({
            dateFormat  : "d-m-Y",
            maxDate     : new Date(d_currentYear, d_currentMonth, d_currentDate),
        });

        $(document).on('click', '.worker_status', function () {
            let status = $('input[name="worker_status"]:checked').val();

            if (status === "Leaver") {
                $(".error").html('');
                $("#add_leaver_date_form").trigger('reset');
                $("#leaver_modal").modal('show');
            } else {
                $.ajax({
                    type        : 'post',
                    url         : '{{ url('update-worker-status') }}',
                    data        : {
                        _token      : '{{ csrf_token() }}',
                        worker_id   : '{{ $worker['id'] }}',
                        status      : $('input[name="worker_status"]:checked').val(),
                    },
                    success     : function (response) {
                        decodeResponse(response)
                        if(response.code === 200)
                            setTimeout(function() { location.reload(); }, 1500);
                    },
                    error   : function (response) {
                        toastr.error(response.statusText);
                    }
                });
            }
        })

        $(document).on('click', '.go_to_tab', function () {
            document.querySelector(`a[data-bs-toggle="tab"][href="#${this.getAttribute('data-tab_hash')}"]`).click();
        })
    </script>
    <script src="{{ asset('js/activity/datatable.js') }}"></script>
@endsection
