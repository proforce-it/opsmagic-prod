@extends('theme.page')

@section('title', 'Pay rate map read only')
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
                                                {{ $job['name'] }} Pay map (read only)
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-custom alert-warning mt-5" role="alert">
                                        <div class="alert-text">
                                            Showing pay map <strong>valid from {{ \Illuminate\Support\Carbon::parse($pay_rate_map['pay_rate_valid_from'])->format('Y-m-d') }} to {{ \Illuminate\Support\Carbon::parse($pay_rate_map['pay_rate_valid_from'])->subDay()->format('Y-m-d') }}</strong>
                                        </div>
                                    </div>
                                    <div class="row mb-10">
                                        <div class="lozenges">
                                            <div class="lozenge">
                                                <div class="lozenge-rate-details">
                                                    <div class="lozenge-rate-name">Default Rate (DR)</div>
                                                    <div class="lozenge-rate-amounts">P: £{{ number_format($pay_rate_map['base_pay_rate'], 2) }} C: £{{ number_format($pay_rate_map['base_charge_rate'], 2) }}</div>
                                                </div>
                                            </div>
                                            @if($extra_pay_rate_map->isNotEmpty())
                                                @foreach($extra_pay_rate_map as $eprm)
                                                    <div class="lozenge lozenge-{{ $eprm['color'] }}">
                                                        <div class="lozenge-rate-details">
                                                            <div class="lozenge-rate-name">{{ $eprm['pay_rate_name'] }} ({{ $eprm['pay_rate_short_code'] }})</div>
                                                            <div class="lozenge-rate-amounts">P: £{{ number_format($eprm['base_pay_rate'], 2) }} C: £{{ number_format($eprm['base_charge_rate'], 2) }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-10">
                                        <div class="col-lg-12">
                                            <div id="week_calendar_app"></div>
                                            <div id="resize-time-label" class="resize-time-label"></div>
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

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: false,
                allDaySlot: false,
                dayHeaderFormat: { weekday: 'long' },
                height: 'auto',
                editable: false,
                eventResizableFromStart: false,
                selectable: false,
                firstDay: 1,
                events: @json($events),
            });

            calendar.render();
            addDefaultDREvents(calendar);

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
        });
    </script>
@endsection

