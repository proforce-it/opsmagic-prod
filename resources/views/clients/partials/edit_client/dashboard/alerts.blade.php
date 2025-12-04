<div class="card card-bordered card-shadow">
    <div class="card-header bg-gray-300 min-h-50px">
        <div class="card-title">
            <span class="fs-2">ALERTS</span>
        </div>
    </div>
    <div class="card-body">
        <table class="table align-middle table-sm m-0 p-0">
            <tbody class="text-gray-800">
            <tr class="border-top-3 border-gray-400" id="shift_with_space_tomorrow_border">
                <td class="text-gray-400 shift_with_space_tomorrow_class">
                    SHIFTS WITH SPACES (TOMORROW) <br>
                    <div>
                        <span class="fw-bolder fs-xxl-1" id="shift_with_space_tomorrow">0</span>
                        <a href="javascript:;" class="shift_with_space_view" id="shift_with_space_tomorrow_view_btn" data-days="1"><i class="fs-xxl-1 las la-calendar-day float-end text-gray-400"></i></a>
                    </div>
                </td>
            </tr>
            <tr class="border-top-3 border-gray-400" id="shift_with_space_border">
                <td class="text-gray-400 shift_with_space_class">
                    SHIFTS WITH SPACES (NEXT 7 DAYS) <br>
                    <div>
                        <span class="fw-bolder fs-xxl-1" id="shift_with_space">0</span>
                        <a href="javascript:;" class="shift_with_space_view" id="shift_with_space_view_btn" data-days="7"><i class="fs-xxl-1 las la-calendar-week float-end text-gray-400"></i></a>
                    </div>
                </td>
            </tr>
            <tr class="border-top-3 border-gray-400" id="booking_invitation_to_chase_border">
                <td class="text-gray-400 booking_invitation_to_chase_class">
                    BOOKING INVITATIONS TO CHASE (<7 DAYS) <br>
                    <div>
                        <span class="fw-bolder fs-xxl-1" id="booking_invitation_to_chase">0</span>
                        <a href="javascript:;" id="booking_invitation_to_chase_view_btn"><i class="fs-xxl-1 las la-envelope-open-text float-end text-gray-400"></i></a>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="view_shift_with_space_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>SHIFTS WITH SPACES (<span id="view_shift_with_space_modal_title"></span>) </h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_shift_with_space">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <input type="text" data-kt-shift-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find client/site/job" />
                            </div>
                        </div>

                        <div class="card-toolbar"></div>
                    </div>
                    <div class="card-body py-4">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="alert_cost_center" id="alert_cost_center" value="">

                        <table class="table align-middle table-row-dashed fs-7 gy-3" id="shift_with_space_datatable">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Date</th>
                                <th>Client</th>
                                <th>Site</th>
                                <th>Job</th>
                                <th>Spaces filled</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_booking_invitation_to_chase_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Booking invitations to chase</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_view_booking_invitation_to_chase_modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y">
                <div class="card">
                    <div class="card-body py-4">
                        <table class="table align-middle table-row-dashed fs-7 gy-3" id="booking_invitations_to_chase_datatable">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Date</th>
                                <th>Job</th>
                                <th>Cost center</th>
                                <th>Worker</th>
                                <th>Additional info</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('alert_js')
    <script>
        let tableNameShiftWithSpaceDatatable = $('#shift_with_space_datatable');
        let shift_with_space_table = tableNameShiftWithSpaceDatatable.DataTable();

        $(".shift_with_space_view").on('click', function () {
            let days = $(this).attr('data-days');
            $("#view_shift_with_space_modal_title").text((days === '7') ? 'NEXT 7 DAYS' : 'TOMORROW')

            shift_with_space_table.destroy();
            shift_with_space_table = tableNameShiftWithSpaceDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-shift-with-space-in-next-7-days') }}',
                    "data": function (d) {
                        d._token = $('#_token').val();
                        d.client_id = '{{ $client['id'] }}';
                        d.days = days;
                    },
                },
                "columns": [
                    {"data": "date"},
                    {"data": "client"},
                    {"data": "site"},
                    {"data": "job"},
                    {"data": "spaces_filled", "width":"10%", "sClass":"text-end"},
                    {"data": "action", "width":"10%", "sClass":"text-end"},
                ]
            });

            $("#view_shift_with_space_modal").modal('show');
        })

        $("#cls_btn_shift_with_space").on('click', function (){
            $("#view_shift_with_space_modal").modal('hide');
        })

        const filterSearch = document.querySelector('[data-kt-shift-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            shift_with_space_table.search(e.target.value).draw();
        });
    </script>

    <script>
        let tableNameBookingInvitationToChaseDatatable = $('#booking_invitations_to_chase_datatable');
        let booking_invitation_to_chase_table = tableNameBookingInvitationToChaseDatatable.DataTable();

        $("#booking_invitation_to_chase_view_btn").on('click', function () {
            booking_invitation_to_chase_table.destroy();
            booking_invitation_to_chase_table = tableNameBookingInvitationToChaseDatatable.DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-booking-invitation-to-chase') }}',
                    "data": function (d) {
                        d._token = $('#_token').val();
                        d.client_id = '{{ $client['id'] }}';
                    },
                },
                "columns": [
                    {"data": "date"},
                    {"data": "job"},
                    {"data": "cost_center"},
                    {"data": "worker"},
                    {"data": "additional_info", "width":"15%"},
                    {"data": "action", "width":"10%", "sClass":"text-end"},
                ]
            });

            $("#view_booking_invitation_to_chase_modal").modal('show');
        });

        $("#cls_btn_view_booking_invitation_to_chase_modal").on('click', function (){
            $("#view_booking_invitation_to_chase_modal").modal('hide');
        })
    </script>
@endsection