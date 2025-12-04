<div class="modal fade" id="payroll_report_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Create payroll report? <span id="worker_name_and_date"></span></h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_payroll_report_modal_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                </div>
            </div>
            <form id="edit_timesheet_entry_form">
                @csrf
                <div class="modal-body scroll-y m-5">
                    <div class="fv-row row">
                        <div class="col-lg-12">
                            <div class="fv-row fv-plugins-icon-container">
                                <label class="fs-5 fw-bold mb-5"><span class="text-danger fw-boldest">Please note:</span> Creating a payroll report will lock all timesheet and bonus entries for Week <span class="payroll_month"></span> for <span class="payroll_modal_site_name">-</span>.</label>
                                <label class="fs-6 fw-bold">You will no longer be able to edit these entries and any future timesheet and bonus entries uploaded for this week for <span class="payroll_modal_site_name">-</span> for Week <span class="payroll_month"></span> will be ignored for payroll purposes.</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="fv-row row">
                        <div class="col-lg-12">
                            <button type="button" name="create_payroll_continue_btn" id="create_payroll_continue_btn" class="btn btn-primary float-end">Continue</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('create_payroll_script')
    <script>
        $(document).on('click', '#create_payroll_report_btn, .add-and-update-ignore-entry', function () {
            var weekNumber = $("#payroll_week_number").val();
            var formattedWeekNumber = weekNumber.replace('_', '-');
            $(".payroll_month").empty().append(formattedWeekNumber);
            $(".payroll_modal_site_name").text($('#site option:selected').text());
            $("#payroll_report_modal").modal('show');
        });

        $("#cls_btn_payroll_report_modal_modal").on('click', function () {
            $(".payroll_month").empty();
            $("#payroll_report_modal").modal('hide');
        });

        $("#create_payroll_continue_btn").on('click', function () {
            $("#cls_btn_payroll_report_modal_modal").click();

            $("#timesheet_editor_section").addClass('d-none');
            $("#payroll_report_process_section").removeClass('d-none');

            $("#header_sub_title").text('CREATE A NEW PAYROLL REPORT');

            $.ajax({
                type : 'post',
                url : '{{ url('create-payroll-action') }}',
                data : {
                    _token : '{{ @csrf_token() }}',
                    site_id : $("#site").val(),
                    pwd : $("#payroll_week_number").val(),
                },
                success : function (response) {
                    if(response.code === 200) {
                        toastr.success(response.message);

                        $("#payroll_report_client_name").text(response.data.client_name);
                        $("#payroll_report_site_name").text(response.data.site_name);
                        $("#payroll_report_week_number").text(response.data.payroll_week_number);
                        $("#payroll_report_pay_date").text(response.data.pay_date);
                        $("#payroll_report_client_week_start_on").text(response.data.client_week_start);
                        $("#payroll_report_date_between").text(response.data.date_between);
                        $("#payroll_report_total_charge").text(response.data.total_charge);
                        $("#payroll_report_total_pay").text(response.data.total_pay);
                        $("#view_payroll_report_btn").attr("href", response.data.view_report_url);

                        $("#payroll_report_process_section").addClass('d-none');
                        $("#payroll_report_success_section").removeClass('d-none');
                    } else {
                        toastr.error(response.message)

                        $("#timesheet_editor_section").removeClass('d-none');
                        $("#payroll_report_process_section").addClass('d-none');
                        $("#header_sub_title").text('TIMESHEET EDITOR');
                    }
                },
                error : function (response) {
                    toastr.error(response.statusText);

                    $("#timesheet_editor_section").removeClass('d-none');
                    $("#payroll_report_process_section").addClass('d-none');
                    $("#header_sub_title").text('TIMESHEET EDITOR');
                }
            });
        });

        function payroll_already_created_section_hide_show(date, view_report_url_href) {
            $("#ignored_timesheet_entry_section").addClass('d-none');
            $("#ignored_bonus_entry_section").addClass('d-none');
            if (date) {
                $('.payroll_already_created_hide_section').addClass('d-none');
                $('.payroll_already_created_show_section').removeClass('d-none');
                $(".payroll_created_at").text(date);
                $("#create_payroll_report_btn").addClass('d-none');
                $(".view_payroll_report_href").attr("href", view_report_url_href);
                ignored_entry_section();
            } else {
                $('.payroll_already_created_hide_section').removeClass('d-none');
                $('.payroll_already_created_show_section').addClass('d-none');
                $("#create_payroll_report_btn").removeClass('d-none');
            }
        }
    </script>
@endsection