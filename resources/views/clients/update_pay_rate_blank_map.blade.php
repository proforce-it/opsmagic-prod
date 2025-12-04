@extends('theme.page')

@section('title', 'Pay rate map step - 2')
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/custom_calendar.css') }}">

    <div class="d-flex flex-column flex-column-fluid" id="kt_content"> <!--content -->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post flex-column-fluid" id="kt_post"> <!--d-flex-->
                        <div id="kt_content_container" class=""> <!--container-xxl-->
                            <div class="card mb-5">
                                <div class="card-body py-4">
                                    <div class="row mb-10">
                                        <div class="col-12 d-flex align-items-center">
                                            <div class="fs-1 fw-bold text-uppercase">
                                                {{ $job['name'] }} Pay map step 2: Add, edit and apply rates
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-10 fs-9">
                                        <div class="alert alert-custom alert-warning d-none mapped_save_alert_section" role="alert">
                                            <div class="alert-text fs-4">
                                                <i class="las la-exclamation-triangle text-warning fs-xxl-1 me-1"></i> <strong>Map has not been saved.</strong>Please click 'Continue' at the bottom of the table to save your changes.
                                            </div>
                                        </div>
                                        <div class="lozenges">
                                            <div class="lozenge">
                                                <div class="lozenge-rate-details">
                                                    <div class="lozenge-rate-name">Default Rate (DR)</div>
                                                    <div class="lozenge-rate-amounts">P: £{{ number_format($pay_rate_map['base_pay_rate'], 2) }} C: £{{ number_format($pay_rate_map['base_charge_rate'], 2) }}</div>
                                                </div>
                                                <div class="lozenge-actions text-white">
                                                    <a href="javascript:;">
                                                        <i class="las la-edit text-white" id="edit_default_pay_rate_map_btn"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            @if($extra_pay_rate_map->isNotEmpty())
                                                @foreach($extra_pay_rate_map as $eprm)
                                                    <div class="lozenge lozenge-{{ $eprm['color'] }}">
                                                        <div class="lozenge-checkbox">
                                                            <input type="checkbox" class="rate-check" name="selected_extra_pay_rate_map[]" id="selected_extra_pay_rate_map_{{ $eprm['id'] }}" value="{{ $eprm['id'] }}" data-color="{{ $eprm['color'] }}" data-short_code="{{ $eprm['pay_rate_short_code'] }}" data-extra_prm_id="{{ $eprm['id'] }}" />
                                                        </div>
                                                        <div class="lozenge-rate-details">
                                                            <div class="lozenge-rate-name">{{ $eprm['pay_rate_name'] }} ({{ $eprm['pay_rate_short_code'] }})</div>
                                                            <div class="lozenge-rate-amounts">P: £{{ number_format($eprm['base_pay_rate'], 2) }} C: £{{ number_format($eprm['base_charge_rate'], 2) }}</div>
                                                        </div>
                                                        <div class="lozenge-actions">
                                                            <a href="javascript:;">
                                                                <i class="las la-edit text-white" id="edit_extra_pay_rate_map_btn" data-id="{{ $eprm['id'] }}"></i>
                                                            </a>
                                                            <a href="javascript:;">
                                                                <i class= "las la-trash-alt text-white" id="delete_extra_pay_rate_map_btn" data-id="{{ $eprm['id'] }}" data-title="{{ $eprm['pay_rate_name'] }} ({{ $eprm['pay_rate_short_code'] }})" ></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <a href="javascript:;" class="text-primary mt-4" id="add_extra_pay_rate_map_btn">
                                                <i class="fas fa-plus-circle fs-xxl-2qx text-primary"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row mb-10">
                                        <div class="col-lg-12">
                                            <div id="week_calendar_app"></div>
<!--                                            <div id="resize-time-label" class="resize-time-label"></div>-->
                                        </div>
                                    </div>
                                    <div class="row mb-10 d-none mapped_save_alert_section">
                                        <div class="col-lg-12 text-center">
                                            <a href="javascript:;" class="btn btn-secondary" id="pay_rate_map_cancel_btn">Cancel</a>
                                            <a href="javascript:;" class="btn btn-primary" id="pay_rate_map_continue_btn">Continue</a>
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

    @include('clients.partials.edit_client.edit_default_pay_rate_map_modal')
    @include('clients.partials.edit_client.add_extra_pay_rate_map_modal')
    @include('clients.partials.edit_client.edit_extra_pay_rate_map_modal')
    @include('clients.prm_pay_map_valid_from_modal')

@endsection

@section('js')
    <script>
        activeMenu('/client-management');
        $("#last_li_of_header_title").empty().append(`
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('view-client-details/'.$job['client_id']) }}"
                   class="text-muted text-hover-primary text-uppercase">
                    {{ $job['client_details']['company_name'] }}
                </a>
            </li>
            <li class="breadcrumb-item text-gray-500">></li>
            <li class="breadcrumb-item text-muted">
            <a href="{{ url('view-client-job/'.$job['id']) }}"
                    class="text-muted text-hover-primary text-uppercase">
                    <span id="header_sub_title">JOB</span>
                    <span id="header_additional_info" class="text-uppercase ms-1">
                        : {{ $job['name'] }} (ID {{ $job['id'] }})
                    </span>
                </a>
            </li>
            <li class="breadcrumb-item text-gray-500">></li>
            <li class="breadcrumb-item text-dark">
                <span id="header_additional_info" class="text-uppercase ms-1">
                    MANAGE PAY MAP
                </span>
            </li>
        `);
    </script>
    <script>
        $('.rate-check').on('change', function () {
            if ($(this).is(':checked')) {
                $('.rate-check').not(this).prop('checked', false);
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('week_calendar_app');

            let lastClickTime = 0;
            let selectedEventId = null;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: false,
                allDaySlot: false,
                dayHeaderFormat: { weekday: 'long' },
                height: 'auto',
                editable: true,
                eventResizableFromStart: true,
                selectable: true,
                firstDay: 1,
                events: @json($events),
                dateClick: function (info) {
                    const now = new Date().getTime();
                    if (now - lastClickTime < 400) {
                        createRateBlock(info.date);
                    }
                    lastClickTime = now;

                    document.querySelectorAll('.fc-event').forEach(el => {
                        el.classList.remove('selected-event');
                    });
                },
                eventDidMount: function (info) {
                    const el = info.el;

                    const topHandle = document.createElement('div');
                    topHandle.className = 'resize-top-handle';

                    const bottomHandle = document.createElement('div');
                    bottomHandle.className = 'resize-bottom-handle';

                    el.appendChild(topHandle);
                    el.appendChild(bottomHandle);

                    if (!info.event.extendedProps.isDefault) {
                        const deleteBtn = document.createElement('span');
                        deleteBtn.innerHTML = '&times;';
                        deleteBtn.classList.add('delete-btn');

                        deleteBtn.style.position = 'absolute';
                        deleteBtn.style.top = '10px';
                        deleteBtn.style.right = '4px';
                        deleteBtn.style.cursor = 'pointer';
                        deleteBtn.style.color = '#fff';
                        deleteBtn.style.zIndex = '20';
                        deleteBtn.style.fontWeight = 'bold';
                        deleteBtn.style.fontSize = '20px';

                        el.appendChild(deleteBtn);

                        deleteBtn.addEventListener('click', function (e) {
                            e.stopPropagation();
                            e.preventDefault();
                            info.event.remove();
                            $(".mapped_save_alert_section").removeClass('d-none');
                            addDefaultDREvents(calendar);
                        });

                        el.addEventListener('click', function () {
                            document.querySelectorAll('.rate-block').forEach(e => e.classList.remove('active'));
                            el.classList.add('active');
                        });
                    }

                },
                eventAllow: function(dropInfo, draggedEvent) {
                    const originalDate = draggedEvent.start;
                    const newDate = dropInfo.start;

                    return originalDate.toDateString() === newDate.toDateString();
                },
                eventDrop: function(info) {
                    const originalDate = info.oldEvent.start;
                    const newDate = info.event.start;

                    if (originalDate.toDateString() !== newDate.toDateString()) {
                        toastr.warning('You cannot move the event to another day.');
                        info.revert();
                    }
                },
                eventClick: function(info) {
                    document.querySelectorAll('.fc-event').forEach(el => {
                        el.classList.remove('selected-event');
                    });

                    info.el.classList.add('selected-event');
                },
                eventContent: function(arg) {

                },
                eventResizeStart: function(info) {
                    document.querySelectorAll('.fc-event').forEach(el => {
                        el.classList.remove('selected-event');
                    });

                    info.el.classList.add('selected-event');

                    const label = document.getElementById('resize-time-label');
                    if (label) {
                        label.style.display = 'block';
                    }
                    $(".mapped_save_alert_section").removeClass('d-none');
                }
            });

            calendar.render();
            addDefaultDREvents(calendar);

            function createRateBlock(startDate) {
                let selectedRate = null;
                let extra_prm_id = null;
                let selectedColor = null;
                let colorCode = null;

                const rateCheck = document.querySelector('.rate-check:checked');
                if (rateCheck) {
                    selectedRate = rateCheck.getAttribute('data-short_code');
                    extra_prm_id = rateCheck.value;
                    selectedColor = rateCheck.dataset.color;
                    colorCode = $("."+selectedColor).css("background-color")
                }

                if (!selectedRate) {
                    toastr.error('Please select a pay rate option first.');
                    return;
                }

                const start = new Date(startDate);
                const end = new Date(start);
                end.setHours(start.getHours() + 1);

                calendar.addEvent({
                    title: selectedRate,
                    extra_prm_id: extra_prm_id,
                    start: start,
                    end: end,
                    classNames: ['rate-block'],
                    backgroundColor: colorCode,
                    borderColor: colorCode,
                    textColor: '#fff',
                    editable: true,
                    selectable: true,
                    eventResizableFromStart: true,
                    eventStartEditable: true,
                    eventDurationEditable: true,
                    selectMirror: true,
                    extendedProps: { isDefault: false }
                });
                $(".mapped_save_alert_section").removeClass('d-none');
            }

            function addDefaultDREvents(calendar) {
                const view = calendar.view;
                const start = view.currentStart;
                const end = view.currentEnd;
                const calendarApi = calendar;

                const startHour = 0;
                const endHour = 24;
                const stepMinutes = 60;

                const existingEvents = calendar.getEvents();

                for (let d = new Date(start); d < end; d.setDate(d.getDate() + 1)) {
                    for (let h = startHour; h < endHour; h++) {
                        const slotStart = new Date(d);
                        slotStart.setHours(h, 0, 0, 0);

                        const slotEnd = new Date(d);
                        slotEnd.setHours(h + stepMinutes / 60, 0, 0, 0);

                        const overlapping = existingEvents.some(evt =>
                            (evt.start < slotEnd && evt.end > slotStart)
                        );

                        if (!overlapping) {
                            calendarApi.addEvent({
                                title: 'DR',
                                start: slotStart.toISOString(),
                                end: slotEnd.toISOString(),
                                display: 'background',
                                classNames: ['dr-slot']
                            });
                        }
                    }
                }
            }

            // Utility Functions
            function formatTimeOnly(date) {
                return date.toTimeString().slice(0, 5); // "HH:MM"
            }

            function parseTimeString(timeStr) {
                const [hours, minutes] = timeStr.split(':').map(Number);
                return hours * 60 + minutes;
            }

            function timeRangeOverlap(startA, endA, startB, endB) {
                return Math.max(startA, startB) < Math.min(endA, endB);
            }

            function getDayName(date) {
                return date.toLocaleDateString('en-US', { weekday: 'long' });
            }

            // Main Function: Split Event into Time Chunks
            function splitEventIntoChunks(event, occupiedSlots, chunkMinutes = 30) {
                const chunks = [];
                let current = new Date(event.start);
                const end = new Date(event.end);
                const day = getDayName(current);

                while (current < end) {
                    const next = new Date(Math.min(current.getTime() + chunkMinutes * 60000, end.getTime()));
                    const startStr = formatTimeOnly(current);
                    const endStr = formatTimeOnly(next);
                    const startMin = parseTimeString(startStr);
                    const endMin = parseTimeString(endStr);
                    const isDR = event.title === 'DR';

                    const overlaps = (occupiedSlots[day] || []).some(slot =>
                        timeRangeOverlap(slot.start, slot.end, startMin, endMin)
                    );

                    if (!isDR || (isDR && !overlaps)) {
                        chunks.push({
                            title: event.title,
                            start: startStr,
                            end: endStr,
                            day,
                            id: event.id || '',
                            extra_prm_id: event.extendedProps?.extra_prm_id || ''
                        });
                    }

                    current = next;
                }

                return chunks;
            }

            // Create Day-wise Map from Events
            function buildDayWiseChunks(chunks, userEventsByDay) {
                const dayWiseMap = {};

                chunks.forEach(chunk => {
                    const { day, start, extra_prm_id } = chunk;
                    const timeKey = start.replace(':', '').padStart(4, '0');

                    if (!dayWiseMap[day]) {
                        dayWiseMap[day] = {
                            day,
                            event: userEventsByDay[day] || [],
                            default_pay_rate_id: '{{ $pay_rate_map['id'] }}'
                        };
                    }

                    dayWiseMap[day][timeKey] = parseInt(extra_prm_id || '0');
                });

                return Object.values(dayWiseMap);
            }

            // Serialize Calendar Events
            function getSerializedCalendarData(calendar) {
                const events = calendar.getEvents();
                const occupiedSlots = {};
                const userEventsByDay = {};
                const allChunks = [];
                const seenEvents = new Set();

                events.forEach(event => {
                    const start = new Date(event.start);
                    const end = new Date(event.end);
                    const day = getDayName(start);

                    const startStr = formatTimeOnly(start);
                    const endTimeStr = formatTimeOnly(end);
                    const endStr = (endTimeStr === '00:00') ? '24:00' : endTimeStr;
                    const uniqueKey = `${day}-${startStr}`;

                    if (seenEvents.has(uniqueKey)) {
                        seenEvents.delete(uniqueKey);
                        if (userEventsByDay[day]) {
                            userEventsByDay[day] = userEventsByDay[day].filter(e => e.uniqueKey !== uniqueKey);
                        }
                    }
                    seenEvents.add(uniqueKey);

                    if (event.title !== 'DR') {
                        const startMin = parseTimeString(startStr);
                        const endMin = parseTimeString(endStr);

                        if (!occupiedSlots[day]) occupiedSlots[day] = [];
                        occupiedSlots[day].push({ start: startMin, end: endMin });

                        if (!userEventsByDay[day]) userEventsByDay[day] = [];
                        userEventsByDay[day].push({
                            title: event.title,
                            start: startStr,
                            end: endStr,
                            day,
                            id: event.id || '',
                            extra_prm_id: event.extendedProps?.extra_prm_id || '',
                            bgColor: event.backgroundColor,
                            uniqueKey: uniqueKey,
                        });
                    }
                });

                seenEvents.clear();
                events.forEach(event => {
                    const start = new Date(event.start);
                    const end = new Date(event.end);
                    const day = getDayName(start);

                    const startStr = formatTimeOnly(start);
                    const endStr = formatTimeOnly(end);

                    const uniqueKey = `${event.title}-${day}-${startStr}-${endStr}`;
                    if (seenEvents.has(uniqueKey)) {
                        seenEvents.delete(uniqueKey);
                        if (userEventsByDay[day]) {
                            userEventsByDay[day] = userEventsByDay[day].filter(e => e.uniqueKey !== uniqueKey);
                        }
                    }
                    seenEvents.add(uniqueKey);

                    const chunks = splitEventIntoChunks(event, occupiedSlots);
                    allChunks.push(...chunks);
                });

                return buildDayWiseChunks(allChunks, userEventsByDay);
            }

            $("#prm_calendar_save_form").on('submit', function (e) {
                $(".error").html('');
                e.preventDefault();

                $("#prm_pay_map_valid_form_submit_btn").addClass('d-none');
                $("#prm_pay_map_valid_form_process_btn").removeClass('d-none');

                const formData = new FormData(this);
                formData.append('calendar_events', JSON.stringify(getSerializedCalendarData(calendar)));

                $.ajax({
                    type        : 'post',
                    url         : '{{ url('store-upcoming-prm-calendar-event') }}',
                    data        : formData,
                    contentType : false,
                    processData : false,
                    cache       : false,
                    success     : function (response) {
                        decodeResponse(response);

                        $("#prm_pay_map_valid_form_submit_btn").removeClass('d-none');
                        $("#prm_pay_map_valid_form_process_btn").addClass('d-none');

                        if(response.code === 200) {
                            $(".prm_pay_map_valid_from_modal_close_btn").click();
                            setTimeout(function () {
                                location.href="{{ url('view-client-job') }}"+'/'+{{ $job['id'] }};
                            }, 1500);
                        }
                    },
                    error   : function (response) {
                        toastr.error(response.statusText);

                        $("#prm_pay_map_valid_form_submit_btn").removeClass('d-none');
                        $("#prm_pay_map_valid_form_process_btn").addClass('d-none');
                    }
                });
            });
        });

        /*--- BEGIN CALENDAR VALID FROM MODEL AND CALENDAR SAVE JS ---*/
        /*let baseDate = new Date('{{ $pay_rate_map['pay_rate_valid_from'] ?? '' }}');
        baseDate.setDate(baseDate.getDate() + 1);*/
        const minDateFromController = new Date("{{ $minDate }}");
        $("#pay_map_valid_from_date").flatpickr({
            dateFormat  : "d-m-Y",
            minDate     : minDateFromController //baseDate //new Date().fp_incr(1)
        });

        $("#pay_rate_map_continue_btn").on('click',function () {
            $('#prm_pay_map_valid_from_modal').modal('show');
        });

        $("#pay_rate_map_cancel_btn").on('click',function (){
            location.reload();
        });

        $(".prm_pay_map_valid_from_modal_close_btn").on('click', function () {
            $("#prm_calendar_save_form").trigger('reset');
            $("#prm_pay_map_valid_from_modal").modal('hide');
        });
        /*--- END CALENDAR VALID FROM MODEL AND CALENDAR SAVE JS ---*/
    </script>
    @yield('edit_default_pay_rate_js')
    @yield('add_extra_pay_rate_map_modal_js')
    @yield('edit_extra_pay_rate_map_modal_js')
@endsection