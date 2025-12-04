<div class="card mb-5">
<!--    <div class="card-body py-4">
        <div class="table-responsive">
            <div class="p-5">
                <div class="fv-row">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="fv-row fv-plugins-icon-container">
                                <label for="job_default_shift_time" class="fs-6 fw-bold required">Shift start time</label>
                                <div class="position-relative d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                            <i class="fs-2 las la-clock"></i>
                                        </span>
                                    <input class="form-control ps-12 flatpickr-input time_input bg-secondary text-gray-500" placeholder="Select shift start time" name="shift_start_time" id="shift_start_time" type="text" readonly="readonly" value="{{ $shift['start_time'] }}">
                                </div>
                                <span class="text-danger error" id="shift_start_time_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="fv-row fv-plugins-icon-container">
                                <label for="job_default_shift_length_hr" class="fs-6 fw-bold required">Shift length</label>
                                <div class="input-group">
                                    <input class="form-control bg-secondary text-gray-500" name="shift_length_hr" id="shift_length_hr" type="text" value="{{ $shift['shift_length_hr'] }}" readonly="readonly">
                                    <div class="input-group-prepend"><span class="input-group-text bg-secondary text-gray-500">hr</span></div>
                                </div>
                                <span class="text-danger error" id="shift_length_hr_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="fv-row fv-plugins-icon-container">
                                <label for="job_default_shift_length_min" class="fs-6 fw-bold"></label>
                                <div class="input-group">
                                    <input class="form-control bg-secondary text-gray-500" name="shift_length_min" id="shift_length_min" type="text" value="{{ $shift['shift_length_min'] }}" readonly="readonly">
                                    <div class="input-group-prepend"><span class="input-group-text bg-secondary text-gray-500">min</span></div>
                                </div>
                                <span class="text-danger error" id="shift_length_min_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>-->
</div>

<div class="card mb-5">
    <div class="card-body py-4">
        <div class="table-responsive">
            <div class="p-5">
                <div class="fv-row">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert alert-custom alert-warning" role="alert">
                                <div class="alert-text fs-4">
                                    <i class="las la-exclamation-triangle text-warning fs-xl-2"></i>
                                    This shift is for a past date. Worker assignments can not be changed
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-lg-6">
                            <label class="fs-4 fw-boldest {{ (count($confirm_worker) > $shift['number_workers']) ? 'text-danger' : '' }} ">Assigned workers ({{ $shift['number_workers'] }})</label>
                            <div class="fv-row fv-plugins-icon-container border border-gray-600 border-dashed rounded p-5 mb-5">
                                <div class="row">
                                    @if($confirm_worker)
                                        @foreach($confirm_worker as $cow)
                                            <div class="col-lg-12 mb-3">
                                                <div class="fv-row fv-plugins-icon-container border border-gray-300 rounded p-2">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            @if($cow['worker'])
                                                                <a href="{{ url('view-worker-details/'.$cow['worker_id']) }}" class="text-dark fw-bolder text-hover-primary d-block fs-6" target="_blank">{{ $cow['worker']['first_name'].' '.$cow['worker']['middle_name'].' '.$cow['worker']['last_name'] }}</a>
                                                            @endif
                                                            <span class="text-muted fw-bold text-muted d-flex fs-7 align-items-center">
                                                                <span class="badge badge-success">C</span>
                                                                 @if($cow['job_line_details'])
                                                                    <span class="p-1 rounded-1 fw-bold {{ $cow['job_line_details']['color_code'] }}-border ms-2">{{ $cow['job_line_details']['line_code'] }}</span>
                                                                @endif
{{--                                                                <i class="las la-calendar-check fs-1"></i> {{ $cow['confirmed_shifts_count'] }} &nbsp;--}}
                                                                    <i class="las la-clock fs-5 me-1 ms-2"></i> {{ date('H:i', strtotime($cow['start_time'] ?? $shift['start_time'])) }}
                                                                    <i class="las la-hourglass-half fs-5 me-1 ms-2"></i>{{ $cow['duration'] ?? $shift['shift_length_hr'].'.'.$shift['shift_length_min'] }}hrs

{{--                                                                <i class="las la-user-circle fs-1"></i>--}}
{{--                                                                @php($cow_latestRTWExpiryDate = \App\Helper\Workers\RightToWorkHelper::getLatestDate($cow['rights_to_work']))--}}
{{--                                                                {{ ($cow_latestRTWExpiryDate) ? date('d-m-Y', strtotime($cow_latestRTWExpiryDate)) : '-' }}--}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="fs-4 fw-boldest">Pending, Declined and cancelled</label>
                            <div class="fv-row fv-plugins-icon-container border border-gray-600 border-dashed rounded p-5">
                                <div class="row">
                                    @if($pending_worker)
                                        @foreach($pending_worker as $pw)
                                            <div class="col-lg-12 mb-3">
                                                <div class="fv-row fv-plugins-icon-container border border-gray-300 rounded p-2">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            @if($pw['worker'])
                                                                <a href="{{ url('view-worker-details/'.$pw['worker_id']) }}" class="text-dark fw-bolder text-hover-primary d-block fs-6" target="_blank">{{ $pw['worker']['first_name'].' '.$pw['worker']['middle_name'].' '.$pw['worker']['last_name'] }}</a>
                                                            @endif
                                                            <span class="text-muted fw-bold text-muted d-flex fs-7 align-items-center">
                                                                <span class="badge badge-warning">I</span> &nbsp;
                                                                 @if($pw['job_line_details'])
                                                                    <span class="p-1 rounded-1 fw-bold {{ $pw['job_line_details']['color_code'] }}-border ms-2">{{ $pw['job_line_details']['line_code'] }}</span>
                                                                @endif
                                                                    <i class="las la-clock fs-5 me-1 ms-2"></i> {{ date('H:i', strtotime($pw['start_time'] ?? $shift['start_time'])) }}
                                                                    <i class="las la-hourglass-half fs-5 me-1 ms-2"></i>{{ $pw['duration'] ?? $shift['shift_length_hr'].'.'.$shift['shift_length_min'] }}hrs

{{--                                                                <i class="las la-calendar-check fs-1"></i> {{ $pw['confirmed_shifts_count'] }} &nbsp;--}}
{{--                                                                <i class="las la-user-circle fs-1"></i>--}}
{{--                                                                @php($pw_latestRTWExpiryDate = \App\Helper\Workers\RightToWorkHelper::getLatestDate($pw['rights_to_work']))--}}
{{--                                                                {{ ($pw_latestRTWExpiryDate) ? date('d-m-Y', strtotime($pw_latestRTWExpiryDate)) : '-' }}--}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if($declined_worker)
                                        @foreach($declined_worker as $dw)
                                            <div class="col-lg-12 mb-3">
                                                <div class="fv-row fv-plugins-icon-container border border-gray-300 rounded p-2">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            @if($dw['worker'])
                                                                <a href="{{ url('view-worker-details/'.$dw['worker_id']) }}" class="text-dark fw-bolder text-hover-primary d-block fs-6" target="_blank"> {{ $dw['worker']['first_name'].' '.$dw['worker']['middle_name'].' '.$dw['worker']['last_name'] }}</a>
                                                            @endif
                                                            <span class="text-muted fw-bold text-muted d-flex fs-7 align-items-center">
                                                                <span class="badge badge-danger text-uppercase">Declined</span>
                                                                 @if($dw['job_line_details'])
                                                                    <span class="p-1 rounded-1 fw-bold {{ $dw['job_line_details']['color_code'] }}-border ms-2">{{ $dw['job_line_details']['line_code'] }}</span>
                                                                @endif
                                                                <i class="las la-thumbs-down fs-5 me-1 ms-2 u-flip"></i> <span class="me-1">{{\Illuminate\Support\Carbon::parse($dw['declined_at'])->format('d-m-y')}}</span><span>{{ date('H:i', strtotime($dw['start_time'] ?? $shift['start_time'])) }}</span>
{{--                                                                @php($dw_latestRTWExpiryDate = \App\Helper\Workers\RightToWorkHelper::getLatestDate($dw['rights_to_work']))--}}
{{--                                                                {{ ($dw_latestRTWExpiryDate) ? date('d-m-Y', strtotime($dw_latestRTWExpiryDate)) : '-' }}--}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if($cancelled_worker)
                                        @foreach($cancelled_worker as $cw)
                                            <div class="col-lg-12 mb-3">
                                                <div class="fv-row fv-plugins-icon-container border border-gray-300 rounded p-2">
                                                    <div class="row">
                                                        <div class="col-lg-112">
                                                            @if($cw['worker'])
                                                                <a href="{{ url('view-worker-details/'.$cw['worker_id']) }}" class="text-dark fw-bolder text-hover-primary d-block fs-6" target="_blank">{{ $cw['worker']['first_name'].' '.$cw['worker']['middle_name'].' '.$cw['worker']['last_name'] }}</a>
                                                            @endif
                                                            <span class="text-muted fw-bold text-muted d-flex fs-7 align-items-center">
                                                                <span class="badge badge-danger text-uppercase">Cancelled</span>
                                                                 @if($cw['job_line_details'])
                                                                    <span class="p-1 rounded-1 fw-bold {{ $cw['job_line_details']['color_code'] }}-border ms-2">{{ $cw['job_line_details']['line_code'] }}</span>
                                                                @endif
                                                                <i class="las la-thumbs-down fs-5 me-1 ms-2 u-flip"></i> <span class="me-1">{{\Illuminate\Support\Carbon::parse($cw['cancelled_at'])->format('d-m-y')}}</span><span>{{ date('H:i', strtotime($cw['start_time'] ?? $shift['start_time'])) }}</span>

{{--                                                                <i class="las la-user-circle fs-1"></i>--}}
{{--                                                                @php($cw_latestRTWExpiryDate = \App\Helper\Workers\RightToWorkHelper::getLatestDate($cw['rights_to_work']))--}}
{{--                                                                {{ ($cw_latestRTWExpiryDate) ? date('d-m-Y', strtotime($cw_latestRTWExpiryDate)) : '-' }}--}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!$shift['cancelled_at'])
                        <div class="row border-top-dashed border-1 mt-15">
                            <div class="col-lg-12 text-center mt-5">
                                <a href="javascript:;" id="create_timesheet_entries_btn" class="btn btn-outline btn-outline-primary text-hover-white btn-lg">
                                    <i class="fs-xxl-1 las la-clock text-primary"></i> create timesheet entries
                                </a>
                                <button type="button" class="btn btn-lg btn-primary btn-lg disabled d-none" data-kt-stepper-action="submit" id="create_timesheet_entries_process_btn">
                                <span>Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('past_job_shift_js')
    <script>
        $("#create_timesheet_entries_btn").on('click', function () {
            $("#create_timesheet_entries_btn").addClass('d-none');
            $("#create_timesheet_entries_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('create-draft-timesheet-entries') }}',
                data        : {
                    _token : '{{ @csrf_token() }}',
                    job_shift_id : '{{ $shift['id'] }}',
                },
                success     : function (response) {
                    $("#create_timesheet_entries_btn").removeClass('d-none');
                    $("#create_timesheet_entries_process_btn").addClass('d-none');

                    decodeResponse(response)
                    if(response.code === 200) {
                        setTimeout(function () {
                            window.location.href = `{{ url('view-draft-timesheet-entries/'.$shift['id']) }}`;
                        }, 1500);
                    }
                },
                error : function (response) {
                    $("#create_timesheet_entries_btn").removeClass('d-none');
                    $("#create_timesheet_entries_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        })
    </script>
@endsection
