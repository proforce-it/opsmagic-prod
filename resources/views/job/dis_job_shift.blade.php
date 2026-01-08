@extends('theme.page')

@section('title', 'Bookings calendar')
@section('content')
    <style>
        #calendar,
        .fc-daygrid-day,
        .fc-daygrid-day-frame,
        .fc-scrollgrid {
            overflow: hidden !important;
        }
        .fc {
            height: auto !important;
        }

        .fc .fc-daygrid-body-natural .fc-daygrid-day-events {
             margin-bottom: 0;
        }

        .shift-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            font-size: 15px;
        }
        .fc-event, .fc-event-dot {
            background-color: white !important;
            border: none !important;
        }
        .shift-info span {
            display: flex;
            align-items: center;
            margin-top: 2px;
            margin-left: 2px;
        }
        .shift-info span i {
            margin-right: 4px;
        }
        .fc-daygrid-day-number{
            font-size: 35px;
            color: #D9D9D9 !important;
        }
        .shift-available .fc-daygrid-day-number{
            color: black !important;
        }
        .shift-required{
            color: black !important;
        }
        .shift-not-available{
            font-size: 35px;
        }
        .shift-available {
            font-size: 35px;
        }
        .fc-theme-standard .fc-scrollgrid {
            border: 1px solid #999;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border: 1px solid #999 !important;
        }

        /*.fc-daygrid-event {
            border: 1px solid #999 !important;
        }*/
        .fc-day-today {
            background-color:#e6f4ea !important;
        }
        .fc-daygrid-day-frame {
            margin: 4px !important;
            box-sizing: border-box;
        }
        .fc-daygrid-day-top {
            margin: 0 !important;
            border-top-right-radius: 8px;
            border-top-left-radius: 8px;
        }
        .fc-daygrid-day {
            padding: 0 !important;
            margin: 0 !important;
        }
        .shift-info {
            margin-top: 0 !important;
        }
        .fc .fc-daygrid-day-events{
            margin: 0;
        }
        .fc-direction-ltr .fc-daygrid-event.fc-event-end{
            margin: 0;
        }
        .fc-day-today .fc-daygrid-day-number {
            color: #34A853 !important;
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
                                <div class="card-body py-4">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="client" class="fs-6 fw-bold">Client</label>
                                                        <input type="hidden" name="_token" id="_token" value="{{ @csrf_token() }}">
                                                        <select name="client" id="client" class="form-select form-select-lg" data-control="select2" data-placeholder="Select client" data-allow-clear="true">
                                                            <option value=""></option>
                                                            @if($client)
                                                                @foreach($client as $row)
                                                                    <option {{ ($tagExplode[0] == $row['id']) ? 'selected' : '' }} value="{{ $row['id'] }}">{{ $row['company_name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="site" class="fs-6 fw-bold">Site</label>
                                                        <select name="site" id="site" class="form-select form-select-lg" data-control="select2" data-placeholder="Select a client first" data-allow-clear="true">
                                                            <option value=""></option>
                                                            @if($site)
                                                                @foreach ($site as $s_row)
                                                                    <option {{ ($tagExplode[1] == $s_row['id']) ? 'selected' : '' }} value="{{ $s_row['id'] }}">{{ $s_row['site_name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="job" class="fs-6 fw-bold">Job</label>
                                                        <select name="job" id="job" class="form-select form-select-lg" data-control="select2" data-placeholder="Select a site first" data-allow-clear="true">
                                                            <option value=""></option>
                                                            @if($job)
                                                                @foreach ($job as $j_row)
                                                                    <option {{ ($tagExplode[2] == $j_row['id']) ? 'selected' : '' }} value="{{ $j_row['id'] }}">{{ $j_row['name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container float-end">
                                                        <a href="{{ url('assignment-management') }}" id="reset_search_form"  class="btn btn-dark mt-7">Reset</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-5">
                                <div class="card-body py-4">
                                    <div class="w-100">
                                        <div class="fv-row">
                                            <div id='calendar'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_job_shift_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-7 d-flex justify-content-between">
                    <h2>Add shift(s)</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_add_job_shift_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <form id="job_shift_details_form">
                    @csrf
                    <div class="modal-body scroll-y">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="from_date" class="fs-6 fw-bold required">From</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <i class="fs-2 las la-calendar"></i>
                                            </span>
                                            <input class="form-control ps-12 flatpickr-input" placeholder="Select date" name="from_date" id="from_date" type="text" readonly="readonly" value="">
                                        </div>
                                        <span class="text-danger error" id="from_date_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="how_many_days" class="fs-6 fw-bold required">For</label>
                                        <div class="input-group">
                                            <input class="form-control" name="how_many_days" id="how_many_days" type="text" value="1">
                                            <div class="input-group-prepend"><span class="input-group-text">Days</span></div>
                                        </div>
                                        <span class="text-danger error" id="how_many_days_error"></span>
                                    </div>
                                </div>
                            </div>
                            @if($jobLineTextBox)
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-7 fv-row fv-plugins-icon-container">
                                            <label class="fs-4 fw-bold">Number of associates required (by line)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="number_of_no_line" class="fs-6 fw-bold">No line</label>
                                            <input class="form-control" name="number_of_no_line" id="number_of_no_line" type="text" value="0">
                                            <span class="text-danger error" id="number_of_no_line_error"></span>
                                            <label class="fs-6 fw-bold text-gray-400">You can assign lines to these associates later</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="job_line_text_box_section">
                                    {{ $jobLineTextBox }}
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="number_of_no_line" class="fs-6 fw-bold required">Number of associates required</label>
                                            <input class="form-control" name="number_of_no_line" id="number_of_no_line" type="text" value="0">
                                            <span class="text-danger error" id="number_of_no_line_error"></span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <input class="form-control" name="job_id" id="job_id" type="hidden" value="">
                                    <button type="submit" name="client_job_worker_form_submit" id="client_job_worker_form_submit" class="btn btn-primary float-end">Add shift(s)</button>
                                    <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="client_job_worker_form_process" id="client_job_worker_form_process">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        $("#from_date").flatpickr({
            dateFormat  : "d-m-Y",
        });

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
                        $("#job").empty();

                        get_calendar_event();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#site").on('change', function (){
            $.ajax({
                type    : 'post',
                url     : '{{ url('get-job-using-site') }}',
                data    : {
                    _token  : '{{ csrf_token() }}',
                    site_id : $("#site").val(),
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#job").empty().append(response.data.job_option);

                        get_calendar_event();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        let calendar, calendarEl = document.getElementById('calendar');
        document.addEventListener('DOMContentLoaded', function() {
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
            });
            calendar.render();

            get_calendar_event();
        });

        //console.log($("#job").val());
        $("#job").on('change', function () {
            get_calendar_event();
        });

        $("#cls_btn_add_job_shift_modal").on('click', function (){
            $("#add_job_shift_modal").modal('hide');
        })

        $("#job_shift_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#client_job_worker_form_submit").addClass('d-none');
            $("#client_job_worker_form_process").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('create-job-shift') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#client_job_worker_form_submit").removeClass('d-none');
                    $("#client_job_worker_form_process").addClass('d-none');

                    if(response.code === 200) {
                        $("#job_shift_details_form").trigger('reset');
                        $("#add_job_shift_modal").modal('hide');
                        get_calendar_event();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#client_job_worker_form_submit").removeClass('d-none');
                    $("#client_job_worker_form_process").addClass('d-none');
                }
            });
        });

        function get_calendar_event() {

            const currentDate   = calendar.getDate();
            const monthName     = currentDate.toLocaleString('default', {month: 'long'});
            const monthNo       = (currentDate.getMonth() + 1).toString().padStart(2, '0');
            const year          = currentDate.getFullYear();
            const monthAndYear  = monthName+' '+year;
            const initialDate   = year+'-'+monthNo+'-01';

            calendar.destroy();
            $.ajax({
                type        : 'post',
                url         : '{{ url('get-job-shift-data') }}',
                data        : {
                    _token  : '{{ @csrf_token() }}',
                    job_id  : $("#job").val(),
                    month   : monthAndYear
                },
                success     : function (response) {
                    if(response.code === 200) {
                        $("#job_line_text_box_section").html(response.data.jobLineTextBox);
                        calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView : 'dayGridMonth',
                            contentHeight: 'auto',
                            initialDate : initialDate,
                            events      : response.data.events,
                            eventClick  : function(info) {
                                let shift_id    = info.event.extendedProps.shift_id;
                                location.href   = '{{ url('view-job-shift') }}' + '/' + shift_id;
                            },
                            dateClick   : function(info) {

                                let job_id          = $("#job").val();
                                let clickedDate     = info.dateStr;
                                let today           = moment().format("YYYY-MM-DD");
                                let eventsOnDate    = calendar.getEvents().filter(event => event.startStr === clickedDate);


                                if (eventsOnDate.length > 0) {
                                    let shift_id = eventsOnDate[0].extendedProps.shift_id;
                                    location.href = '{{ url('view-job-shift') }}'+'/'+shift_id;
                                } else {
                                    if (moment(clickedDate).isBefore(today, 'day')) {
                                        toastr.error('You cannot create a shift for a past date');
                                    } else if (job_id === '') {
                                        toastr.error('Please select a job.');
                                    } else {
                                        $("#from_date").val(moment(clickedDate, "YYYY-MM-DD").format("DD-MM-YYYY"));
                                        $("#job_id").val(job_id)
                                        $("#add_job_shift_modal").modal('show');
                                    }
                                }
                            },
                            dayCellDidMount: function(info) {
                                let dateStr = info.date.toISOString().split('T')[0];
                                let todayEvents = calendar.getEvents().filter(event => {
                                    let eventDateStr = event.start.toISOString().split('T')[0];
                                    return eventDateStr === dateStr;
                                });

                                if (todayEvents.length > 0) {
                                    const frame = info.el.querySelector('.fc-daygrid-day-frame');
                                    if (frame) {
                                        let hasOverAssigned = todayEvents.some(ev => {
                                            let assigned = parseInt(ev.extendedProps.assigned || 0, 10);
                                            let requiredRaw = ev.extendedProps.required;

                                            let isRequiredUnset =
                                                requiredRaw === null ||
                                                requiredRaw === undefined ||
                                                requiredRaw === '' ||
                                                requiredRaw === '0' ||
                                                parseInt(requiredRaw, 10) === 0;

                                            let required = parseInt(requiredRaw || 0, 10);
                                            if (!isRequiredUnset) {
                                                return assigned !== required;
                                            } else {
                                                return assigned === 0;
                                            }
                                        });

                                        frame.style.border = hasOverAssigned ? '2px solid red' : '2px solid green';
                                        frame.style.borderRadius = '8px';
                                    }

                                    info.el.classList.add('shift-available');
                                    info.el.style.backgroundColor = '#fff';

                                    const top = info.el.querySelector('.fc-daygrid-day-top');
                                    if (top) {
                                        top.style.backgroundColor = '#fff';
                                    }
                                } else {
                                    info.el.classList.add('shift-not-available');
                                }
                            },
                            eventContent: function(arg) {
                                let assigned = arg.event.extendedProps.assigned,
                                    required = arg.event.extendedProps.required,
                                    shift_id = arg.event.extendedProps.shift_id,
                                    cancelled = arg.event.extendedProps.cancelled,
                                    eventDate = new Date(arg.event.start),
                                    start_time  = arg.event.extendedProps.start_time,
                                    shift_length_hr  = arg.event.extendedProps.shift_length_hr,
                                    shift_length_min  = arg.event.extendedProps.shift_length_min,
                                    today = new Date();

                                let formattedStartTime = start_time.replace(":", "").substring(0, 4);
                                today.setHours(0, 0, 0, 0);
                                let slotsSet = required !== null && required !== undefined && required !== '' && required !== '0'
                                let color = '#000';

                                if (slotsSet && eventDate >= today && assigned !== required) {
                                    color = '#000';
                                }

                                let requiredDisplay = slotsSet ? required : '-';
                                let innerHtml = `
                                    <div class="shift-info" style="display: flex; flex-direction: column; padding-right: 5px; padding-top: 25px;">
                                        ${cancelled}
                                        <span style="color: ${color};">
                                           <i style="color: ${color};" class="las la-user-circle fs-xl-2"></i> ${assigned}/${requiredDisplay}
                                        </span>
                                        <span style="color: #000">
                                            <i style="color: #000;" class="las la-clock fs-xl-2"></i> ${formattedStartTime} (${shift_length_hr}h${shift_length_min}m)
                                        </span>
                                        <span style="color: ${color}; display: none" id="shift_id">${shift_id}</span>
                                    </div>
                                `;

                                return { html: innerHtml };
                            }
                        });

                        calendar.render();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        }

        $(document).on('click', '.fc-button-primary', function () {
            get_calendar_event();
        });
    </script>
@endsection
