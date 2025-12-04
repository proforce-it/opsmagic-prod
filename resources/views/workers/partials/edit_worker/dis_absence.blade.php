<div class="tab-pane fade" id="kt_table_widget_5_tab_7">
    <!--begin::Table container-->
    <div class="table-responsive">
        <div class="p-5">
            <div class="row mb-7">
                <div class="w-100">
                    <form id="absence_form">
                        @csrf
                        <div class="fv-row">
                            <div><input type="hidden" name="worker_id" id="worker_id" value="{{ $worker['id'] }}"></div>
                            <div class="row">
                                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Add a new absence</div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-lg-3">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="absence_type" class="fs-6 required">Type</label>
                                        <select name="absence_type" id="absence_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Select type" data-allow-clear="true">
                                            <option value="">Select type</option>
                                            <option value="Holiday">Holiday</option>
                                            <option value="Sickness">Sickness</option>
                                            <option value="Rest">Rest</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <span class="text-danger error" id="absence_type_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="absence_type" class="fs-6 required">Start date</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <i class="fs-2 las la-calendar"></i>
                                            </span>
                                            <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select start date" name="absence_start_date" id="absence_start_date" type="text" readonly="readonly" value="">
                                        </div>
                                        <span class="text-danger error" id="absence_start_date_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="absence_type" class="fs-6 required">End date</label>
                                        <div class="position-relative d-flex align-items-center">
                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
{{--                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                                        <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="black"></path>--}}
{{--                                                        <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="black"></path>--}}
{{--                                                        <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="black"></path>--}}
{{--                                                    </svg>--}}
                                                                                                    <i class="fs-2 las la-calendar"></i>

                                                </span>
                                            <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select end date" name="absence_end_date" id="absence_end_date" type="text" readonly="readonly" value="">
                                        </div>
                                        <span class="text-danger error" id="absence_end_date_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <button type="submit" name="absence_submit_btn" id="absence_submit_btn" class="btn btn-primary mt-7">
{{--                                        <span class="svg-icon svg-icon-2">--}}
                                            <i class="fs-2 las la-plus"></i>

{{--                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="black"></rect>--}}
{{--                                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black"></rect>--}}
{{--                                            </svg>--}}
{{--                                        </span>--}}
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row mb-10 mt-10">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Absences</div>
                </div>

                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />--}}
{{--                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />--}}
{{--                                    </svg>--}}
                                    <i class="fs-2 las la-search"></i>

                                </span>
                                <input type="text" data-kt-absence-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search absence" />
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="absence_datatable">
                            <thead>
                            <tr class="text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Type</th>
                                <th>Start date</th>
                                <th>End date</th>
                                <th>Added by</th>
                                <th>Created at</th>
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
    <!--end::Table-->
</div>

@section('edit_worker_absence_js')
    <script>
        let absence_start_date = $("#absence_start_date");
        absence_start_date.flatpickr({
            dateFormat  : "d-m-Y",
        });

        absence_start_date.on('change', function () {

            let end_date_box = $( "#absence_end_date" );
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
                dateFormat  : "d-m-Y",
                minDate     : new Date(currentYear, currentMonth, currentDate)
            });
        });

        let tableNameAbsenceDatatable = $("#absence_datatable")
        let absence_datatable = tableNameAbsenceDatatable.DataTable();

        $(document).on('click', '#kt_table_widget_5_tab_7_menu', function () {
            absence_datatable.destroy();
            absence_datatable = tableNameAbsenceDatatable.DataTable({
                "serverSide"    : false,
                "processing"    : false,
                "pagination"    : true,
                "ajax"          : {
                    "url"   : '{{ url('list-of-absence') }}',
                    "type"  : 'post',
                    "data"  : function (d) {
                        d._token    = $('#_token').val();
                        d.worker_id = $('#worker_id').val();
                    },
                },
                "columns": [
                    { "data": "type"},
                    { "data": "start_date"},
                    { "data": "end_date"},
                    { "data": "added_by"},
                    { "data": "created_at"},
                    { "data": "action",  "sClass": "text-end"},
                ],
                "order": [[ 0, "desc" ]],
            });
        })

        const filterSearchAbsence = document.querySelector('[data-kt-absence-table-filter="search"]');
        filterSearchAbsence.addEventListener('keyup', function (e) {
            absence_datatable.search(e.target.value).draw();
        });

        $("#absence_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('create-absence') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    if(response.code === 200) {
                        absence_datatable.ajax.reload();
                        $("#absence_type").val('').trigger('change');
                        $("#absence_form").trigger('reset');
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(document).on('click', '#delete_absence', function () {
            sweetAlertConfirmDelete('You want to delete this absence entry!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-absence-action') }}'+'/'+$(this).attr('data-absence-id'),
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                absence_datatable.ajax.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });
    </script>
@endsection
