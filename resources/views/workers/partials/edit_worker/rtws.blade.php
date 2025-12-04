<div class="tab-pane fade" id="kt_table_widget_5_tab_3">
    <!--begin::Table container-->
    <div class="table-responsive border-1 rounded-3">
        <div class="p-5">
            @if(!$worker['rights_to_work_details'])
                <div class="alert alert-custom alert-warning mb-10" role="alert">
                    <div class="alert-text fs-4">
                        <i class="las la-exclamation-triangle text-warning fs-xl-2"></i> This tab has missing data that must be completed before the worker can be made active
                    </div>
                </div>
            @elseif(in_array(1, array_column($worker['rights_to_work_details'], 'incomplete'))) {{--in_array('UNKNOWN RTW TYPE', array_column($worker['rights_to_work_details'], 'right_to_work_type')) || --}}
                <div class="alert alert-custom alert-warning mb-10" role="alert">
                    <div class="alert-text fs-4">
                        <i class="las la-exclamation-triangle text-warning fs-xl-2"></i> Incomplete right to work data. Please click the penicl icon to edit
                    </div>
                </div>
            @endif
            <form id="rights_to_work_form">
                @csrf
                <div class="row mb-5">
                    <div class="col-lg-6 d-flex align-items-center fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">RIGHT TO WORK</div>
                    <div class="col-lg-6 text-end border-bottom gs-0 border-4">
                        <div class="float-end">
                          <a href="javascript:;" id="add_right_to_work"><i class="las la-plus-circle text-primary fs-xxl-2qx"></i></a>
                            <a href="javascript:;" id="close_right_to_work" class="close_right_to_work d-none"><i class="las la-times-circle text-primary fs-xxl-2qx"></i></a>
                        </div>
                    </div>

                </div>
                <div id="right_to_work_section" style="display: none">
                    <div class="p-5 border border-1 rounded-3">
                        <div><input type="hidden" name="worker_id" id="worker_id" value="{{ $worker['id'] }}"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <select name="right_to_work[]" id="right_to_work_1" data-section="1" class="form-select form-select-lg rtw_drp" data-placeholder="Select right to work" data-allow-clear="true">
                                        <option value="">Select right to work type</option>
                                        <option value="UK Citizen">UK Citizen</option>
                                        <option value="EUSS (Settled)"> EUSS (Settled)</option>
                                        <option value="EUSS (Pre-Settled)">EUSS (Pre-Settled)</option>
                                        <option value="EUSS (COA)">EUSS (COA)</option>
                                        <option value="Tier 4 Student Visa">Tier 4 Student Visa</option>
                                        <option value="Tier 5 Seasonal Worker Visa">Tier 5 Seasonal Worker Visa</option>
                                        <option value="Tier 5 Poultry and HGV Worker Visa">Tier 5 Poultry and HGV Worker Visa</option>
                                        <option value="Other (Timebound)">Other (Timebound)</option>
                                        <option value="Other (Indefinite leave)">Other (Indefinite leave)</option>
                                    </select>
                                    <span class="text-danger error" id="right_to_work_error"></span>
                                </div>
                            </div>

                            <div class="col-lg-4" id="uk_id_document_type_section_1" style="display:none;">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="uk_id_document_type_1" id="label_uk_id_document_type_1" class="fs-6 fw-bold">Document type</label>
                                    <select name="uk_id_document_type[]" id="uk_id_document_type_1" data-section="1" class="form-select form-select-lg dt_drp" data-placeholder="Select document type" data-allow-clear="true">
                                        <option value="">Select document type</option>
                                        <option value="Passport">Passport</option>
                                        <option value="Birth Certificate">Birth Certificate</option>
                                    </select>
                                    <span class="text-danger error" id="uk_id_document_type_1_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-4" id="uk_id_document_number_section_1" style="display:none;">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="uk_id_document_number_1" id="label_uk_id_document_number_1" class="fs-6 fw-bold">Document Number</label>
                                    <input type="text" name="uk_id_document_number[]" id="uk_id_document_number_1" placeholder="Enter document number" class="form-control">
                                    <span class="text-danger error" id="uk_id_document_number_1_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-4" id="reference_number_section_1" style="display:none;">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="reference_number_1" id="label_reference_number_1" class="fs-6 fw-bold">Reference number</label>
                                    <input type="text" name="reference_number[]" id="reference_number_1" placeholder="Enter number" class="form-control">
                                    <span class="text-danger error" id="reference_number_1_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-4" id="start_date_section_1" style="display:none;">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="start_date_1" id="label_start_date_1" class="fs-6 fw-bold">Start date</label>
                                    <div class="position-relative d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                            <i class="fs-2 las la-calendar"></i>
                                        </span>
                                        <input class="form-control ps-12 flatpickr-input r_start_date" placeholder="Select date" name="start_date[]" id="start_date_1" type="text" readonly="readonly" value="">
                                    </div>
                                    <span class="text-danger error" id="start_date_1_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-4" id="expiry_date_section_1" style="display:none;">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="end_date_1" id="label_end_date_1" class="fs-6 fw-bold">Expiry date</label>
                                    <div class="position-relative d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                            <i class="fs-2 las la-calendar"></i>
                                        </span>
                                        <input class="form-control ps-12 flatpickr-input r_expiry_date" placeholder="Select date" name="expiry_date[]" id="expiry_date_1" type="text" readonly="readonly" value="">
                                    </div>
                                    <span class="text-danger error" id="expiry_date_1_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-4" id="worker_restrictions_section_1" style="display:none;">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="worker_restrictions_1" id="label_worker_restrictions_1" class="fs-6 fw-bold">Worker restrictions</label>
                                    <input type="text" name="worker_restrictions[]" id="worker_restrictions_1" placeholder="Enter text" class="form-control">
                                    <span class="text-danger error" id="worker_restrictions_1_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-4" id="document_scan_section_1" style="display:none;">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="document_scan_1" id="label_document_scan_1" class="fs-6 fw-bold">Upload document scan (.pdf, .jpeg, or .png)</label>
                                    <input type="file" name="document_scan[]" id="document_scan_1" class="form-control" accept="image/png, image/jpeg, application/pdf">
                                    <span class="text-danger error" id="document_scan_1_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" name="total_right_to_work_section" id="total_right_to_work_section" value="1">
                                <input type="hidden" name="update_right_to_work_id" id="update_right_to_work_id" value="1">
                                <button type="submit" name="new_right_to_work_update_submit_btn" id="new_right_to_work_update_submit_btn" class="btn btn-primary float-end">
                                    <i class="fs-2 las la-plus"></i>
                                    Add RTWs
                                </button>
                                <button type="button" class="btn btn-primary float-end disabled d-none" name="new_right_to_work_update_process_btn" id="new_right_to_work_update_process_btn">
                                    <span>Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                                <button type="reset" class="btn btn-dark float-end me-1 close_right_to_work">Cancel</button>

                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-lg-12">
                    <table class="table fs-7 gy-3 bg-active-dark">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="text-left"></th>
                            <th class="text-center" style="width: 10%"></th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                        @if($worker['rights_to_work_details'])
                            @foreach($worker['rights_to_work_details'] as $rtw_row)
                                <tr id="row_{{ $rtw_row['id'] }}" class="bg-gray-100">
                                    <td class="p-5 rounded-start {{ ($rtw_row['incomplete'] == 1) ? 'border-start border-top border-bottom border-warning' : '' }}">
                                        <span class="badge
                                            {{ ($rtw_row['right_to_work_type'] == 'UNKNOWN RTW TYPE' || $rtw_row['incomplete'] == 1) ? 'badge-warning' : 'badge-success' }}  fs-6 p-3 rounded-1">
                                            {{ $rtw_row['right_to_work_type'] }}
                                        </span>
                                        <br>
                                        <table class="align-middle fs-5 gy-3" cellpadding="12">
                                            @if($rtw_row['right_to_work_type'] == 'UK Citizen')
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    @if($rtw_row['rtw_share_code'])
                                                        <th>RTW SHARE CODE <br> {{ $rtw_row['rtw_share_code'] }}</th>
                                                    @endif
                                                    <th>Document type <br> {{ $rtw_row['uk_id_document_type'] }}</th>
                                                    <th>Document Number <br> {{ $rtw_row['uk_id_document_number'] }}</th>
                                                    @if($rtw_row['uk_id_document_type'] == 'Passport')
                                                        <th>Expiry date <br> {{ ($rtw_row['end_date']) ? date('d-m-Y', strtotime($rtw_row['end_date'])) : '-' }}</th>
                                                    @endif
                                                </tr>
                                            @elseif($rtw_row['right_to_work_type'] == 'EUSS (Settled)')
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    @if($rtw_row['rtw_share_code'])
                                                        <th>RTW SHARE CODE <br> {{ $rtw_row['rtw_share_code'] }}</th>
                                                    @endif
                                                    <th>SS permit number <br> {{ $rtw_row['reference_number'] }}</th>
                                                    <th>Expiry date <br> {{ ($rtw_row['end_date']) ? date('d-m-Y', strtotime($rtw_row['end_date'])) : '-' }}</th>
                                                </tr>
                                            @elseif($rtw_row['right_to_work_type'] == 'EUSS (Pre-Settled)')
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    @if($rtw_row['rtw_share_code'])
                                                        <th>RTW SHARE CODE <br> {{ $rtw_row['rtw_share_code'] }}</th>
                                                    @endif
                                                    <th>PS permit number <br> {{ $rtw_row['reference_number'] }}</th>
                                                    <th>Expiry date <br> {{ ($rtw_row['end_date']) ? date('d-m-Y', strtotime($rtw_row['end_date'])) : '-' }}</th>
                                                </tr>
                                            @elseif($rtw_row['right_to_work_type'] == 'EUSS (COA)')
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    @if($rtw_row['rtw_share_code'])
                                                        <th>RTW SHARE CODE <br> {{ $rtw_row['rtw_share_code'] }}</th>
                                                    @endif
                                                    <th>COA ref.number <br> {{ $rtw_row['reference_number'] }}</th>
                                                    <th>Expiry date <br> {{ ($rtw_row['end_date']) ? date('d-m-Y', strtotime($rtw_row['end_date'])) : '-' }}</th>
                                                </tr>
                                            @elseif($rtw_row['right_to_work_type'] == 'Tier 5 Seasonal Worker Visa' || $rtw_row['right_to_work_type'] == 'Tier 5 Poultry and HGV Worker Visa')
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    @if($rtw_row['rtw_share_code'])
                                                        <th>RTW SHARE CODE <br> {{ $rtw_row['rtw_share_code'] }}</th>
                                                    @endif
                                                    <th>Visa ref. number <br> {{ $rtw_row['reference_number'] }}</th>
                                                    <th>Visa start date <br> {{ ($rtw_row['start_date']) ? date('d-m-Y', strtotime($rtw_row['start_date'])) : '-' }}</th>
                                                    <th>Expiry date <br> {{ ($rtw_row['end_date']) ? date('d-m-Y', strtotime($rtw_row['end_date'])) : '-' }}</th>
                                                </tr>
                                            @elseif($rtw_row['right_to_work_type'] == 'Tier 4 Student Visa')
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    @if($rtw_row['rtw_share_code'])
                                                        <th>RTW SHARE CODE <br> {{ $rtw_row['rtw_share_code'] }}</th>
                                                    @endif
                                                    <th>Visa ref. number <br> {{ $rtw_row['reference_number'] }}</th>
                                                    <th>Visa start date <br> {{ ($rtw_row['start_date']) ? date('d-m-Y', strtotime($rtw_row['start_date'])) : '-' }}</th>
                                                    <th>Expiry date <br> {{ ($rtw_row['end_date']) ? date('d-m-Y', strtotime($rtw_row['end_date'])) : '-' }}</th>
                                                    <th>Student working hours restrictions <br> {{ $rtw_row['worker_restrictions'] }}</th>
                                                </tr>
                                            @elseif($rtw_row['right_to_work_type'] == 'Other (Timebound)')
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    @if($rtw_row['rtw_share_code'])
                                                        <th>RTW SHARE CODE <br> {{ $rtw_row['rtw_share_code'] }}</th>
                                                    @endif
                                                    <th>BRP ref. number <br> {{ $rtw_row['reference_number'] }}</th>
                                                    <th>Start date <br> {{ ($rtw_row['start_date']) ? date('d-m-Y', strtotime($rtw_row['start_date'])) : '-' }}</th>
                                                    <th>Expiry date <br> {{ ($rtw_row['end_date']) ? date('d-m-Y', strtotime($rtw_row['end_date'])) : '-' }}</th>
                                                </tr>
                                            @elseif($rtw_row['right_to_work_type'] == 'Other (Indefinite leave)')
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    @if($rtw_row['rtw_share_code'])
                                                        <th>RTW SHARE CODE <br> {{ $rtw_row['rtw_share_code'] }}</th>
                                                    @endif
                                                    <th>Start date <br> {{ ($rtw_row['start_date']) ? date('d-m-Y', strtotime($rtw_row['start_date'])) : '-' }}</th>
                                                    <th>Working restrictions <br> {{ $rtw_row['worker_restrictions'] }}</th>
                                                </tr>
                                            @elseif($rtw_row['right_to_work_type'] == 'UNKNOWN RTW TYPE')
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    <th>RTW SHARE CODE <br> {{ $rtw_row['rtw_share_code'] }}</th>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                    <td class="text-center p-8 rounded-end {{ ($rtw_row['incomplete'] == 1) ? 'border-top border-bottom border-end border-warning' : '' }}">
                                        @if($rtw_row['document_scan'])
                                            <a href="{{ asset('workers/right_to_work/'.$rtw_row['document_scan']) }}" class="btn btn-icon btn-bg-light btn-active-color-primary" target="_blank" title="View document">
                                                <i class="fs-xxl-2x las la-file-image"></i>
                                            </a>
                                        @endif

                                        @if($rtw_row['right_to_work_type'] == 'UNKNOWN RTW TYPE')
                                            <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary" id="edit_rtw" data-rtw-id="{{ $rtw_row['id'] }}">
                                                <i class="fs-xxl-2x las la-edit"></i>
                                            </a>
                                        @elseif($rtw_row['incomplete'] == 1 && !$rtw_row['document_scan'])
                                            <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary" id="edit_incomplete_rtw" data-rtw-id="{{ $rtw_row['id'] }}">
                                                <i class="fs-xxl-2x las la-edit"></i>
                                            </a>
                                        @endif
                                        <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary" id="delete_rtw" data-rtw-id="{{ $rtw_row['id'] }}">
                                            <i class="fs-xxl-2x las la-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr><td colspan="2"></td></tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="modal fade" id="upload_document_scan_modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header py-7 d-flex justify-content-between">
                                    <h2>Upload a RTWs document</h2>
                                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_upload_document_scan_modal">
                                        <span class="svg-icon svg-icon-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <form id="upload_document_scan_form">
                                    @csrf
                                    <div class="modal-body scroll-y">
                                        <div class="fv-row row">
                                            <div class="col-lg-12">
                                                <div class="fv-row fv-plugins-icon-container">
                                                    <input type="file" name="document_scan_file" id="document_scan_file" class="form-control"  accept="image/jpg, image/png, image/jpeg, application/pdf"/>
                                                    <span class="text-danger error" id="document_scan_file_error"></span>
                                                    <div>
                                                        <label class="fw-bold text-muted">PNG, JPG, JPEG or PDF (Max. 10MB)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-center">
                                        <div class="fv-row">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <input type="hidden" name="upload_rtws_incomplete_document_id" id="upload_rtws_incomplete_document_id" value="">
                                                    <button type="submit" name="upload_document_scan_submit_btn" id="upload_document_scan_submit_btn" class="btn btn-primary">Upload</button>
                                                    <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" name="upload_document_scan_process_btn" id="upload_document_scan_process_btn">
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
                </div>
            </div>
        </div>
    </div>
    <!--end::Table-->
</div>


@section('edit_worker_rtws_js')
    <script>
        $(document).on('click', '#delete_rtw', function () {
            let id = $(this).attr('data-rtw-id');
            sweetAlertConfirmDelete('You want to delete this rights to work section!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-rtw-action') }}'+'/' +id,
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                $("#row_"+id).hide();
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

        $(document).ready(function (){
            $('#add_right_to_work').on('click',function (){
                $('#new_right_to_work_update_submit_btn')
                    .text('Add RTWs')
                    .prepend('<i class="fs-2 las la-plus"></i>');

                $("#update_right_to_work_id").val('0');

                let section = $('#right_to_work_section');
                section.slideDown(600);
                $('#add_right_to_work').addClass('d-none');
                $('#close_right_to_work').removeClass('d-none');
            });

            $("#edit_rtw").on('click', function () {
                $('#new_right_to_work_update_submit_btn')
                    .text('Update RTWs')
                    .prepend('<i class="fs-2 las la-edit"></i>');

                $("#update_right_to_work_id").val($(this).attr('data-rtw-id'));

                let section = $('#right_to_work_section');
                section.slideDown(600);
                $('#add_right_to_work').addClass('d-none');
                $('#close_right_to_work').removeClass('d-none');
            })

            $('.close_right_to_work').on('click',function (){
                $('#right_to_work_section').slideUp(600);
                $('#add_right_to_work').removeClass('d-none');
                $('#close_right_to_work').addClass('d-none');
            });
        });

        $("#rights_to_work_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#new_right_to_work_update_submit_btn").addClass('d-none');
            $("#new_right_to_work_update_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('insert-rights-to-work') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {

                    $("#new_right_to_work_update_submit_btn").removeClass('d-none');
                    $("#new_right_to_work_update_process_btn").addClass('d-none');

                    decodeResponse(response)

                    if(response.code === 200) {
                        $('.close_right_to_work').click();
                        setTimeout(function() { location.reload(); }, 1500);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#new_right_to_work_update_submit_btn").removeClass('d-none');
                    $("#new_right_to_work_update_process_btn").addClass('d-none');
                }
            });
        });

        $(document).ready(function (){
            prepared_right_to_work_datepicker();
        })

        function prepared_right_to_work_datepicker() {
            $(".r_start_date").flatpickr({
                dateFormat  : "d-m-Y",
            });

            $(".r_expiry_date").flatpickr({
                dateFormat  : "d-m-Y",
            });
        }

        $(document).on('change', '.rtw_drp', function (){
            $(".error").html('');

            let value = $(this).val();
            let section = $(this).attr('data-section');

            let label_uk_id_document_type = $("#label_uk_id_document_type_"+section)
            let label_uk_id_document_number = $("#label_uk_id_document_number_"+section)
            let label_reference_number = $("#label_reference_number_"+section)
            let label_start_date = $("#label_start_date_"+section)
            let label_expiry_date = $("#label_expiry_date_"+section)
            let label_worker_restrictions = $("#label_worker_restrictions_"+section)
            let label_document_scan = $("#label_document_scan_"+section)

            let uk_id_document_type_section = $("#uk_id_document_type_section_"+section)
            let uk_id_document_number_section = $("#uk_id_document_number_section_"+section)
            let reference_number_section = $("#reference_number_section_"+section)
            let start_date_section = $("#start_date_section_"+section)
            let expiry_date_section = $("#expiry_date_section_"+section)
            let worker_restrictions_section = $("#worker_restrictions_section_"+section)
            let document_scan_section = $("#document_scan_section_"+section)

            uk_id_document_type_section.hide();
            uk_id_document_number_section.hide();
            reference_number_section.hide();
            start_date_section.hide();
            expiry_date_section.hide();
            worker_restrictions_section.hide();
            document_scan_section.hide();

            if (value === 'UK Citizen') {
                uk_id_document_type_section.show()
                uk_id_document_number_section.show();
                document_scan_section.show();
            } else if (value === 'EUSS (Settled)') {
                label_reference_number.empty().append('SS permit number');

                reference_number_section.show();
                expiry_date_section.show();
                document_scan_section.show();
            } else if (value === 'EUSS (Pre-Settled)') {
                label_reference_number.empty().append('PS permit number')

                reference_number_section.show();
                expiry_date_section.show();
                document_scan_section.show();
            } else if (value === 'EUSS (COA)') {
                label_reference_number.empty().append('COA ref.number')

                reference_number_section.show();
                expiry_date_section.show();
                document_scan_section.show();
            } else if (value === 'Tier 5 Seasonal Worker Visa' || value === 'Tier 5 Poultry and HGV Worker Visa') {
                label_reference_number.empty().append('Visa ref. number')
                label_start_date.empty().append('Visa start date')
                label_expiry_date.empty().append('Visa expiry date')

                reference_number_section.show();
                start_date_section.show()
                expiry_date_section.show();
                document_scan_section.show();
            } else if (value === 'Tier 4 Student Visa') {
                label_reference_number.empty().append('Visa ref. number')
                label_start_date.empty().append('Visa start date')
                label_expiry_date.empty().append('Visa expiry date')
                label_worker_restrictions.empty().append('Student working hours restrictions')

                reference_number_section.show();
                start_date_section.show()
                expiry_date_section.show();
                worker_restrictions_section.show();
                document_scan_section.show();
            } else if (value === 'Other (Timebound)') {
                label_reference_number.empty().append('BRP ref. number')
                label_start_date.empty().append('Start date')
                label_expiry_date.empty().append('End date')

                reference_number_section.show();
                start_date_section.show()
                expiry_date_section.show();
                document_scan_section.show();
            } else if (value === 'Other (Indefinite leave)') {
                label_reference_number.empty().append('Start date')
                label_worker_restrictions.empty().append('Working restrictions')

                start_date_section.show()
                worker_restrictions_section.show();
                document_scan_section.show();
            }
        });

        $(document).on('change', '.dt_drp', function (){
            let value = $(this).val();
            let section = $(this).attr('data-section');

            let expiry_date_section = $("#expiry_date_section_"+section)

            expiry_date_section.hide();

            if (value === 'Passport') {
                expiry_date_section.show();
            }
        });
    </script>
    <script>
        $("#edit_incomplete_rtw").on('click', function () {
            $("#upload_document_scan_form").trigger('reset');
            $("#upload_rtws_incomplete_document_id").val($(this).attr("data-rtw-id"));
            $('#upload_document_scan_modal').modal('show');
        })

        $("#cls_btn_upload_document_scan_modal").on('click', function () {
            $("#upload_document_scan_form").trigger('reset');
            $("#upload_document_scan_modal").modal('hide');
        });

        $("#upload_document_scan_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#upload_document_scan_submit_btn").addClass('d-none');
            $("#upload_document_scan_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('update-worker-document-scan') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    $("#upload_document_scan_submit_btn").removeClass('d-none');
                    $("#upload_document_scan_process_btn").addClass('d-none');

                    decodeResponse(response)
                    if(response.code === 200) {
                        $("#cls_btn_upload_document_scan_modal").click();
                        setTimeout(function() { location.reload(); }, 1500);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#upload_document_scan_submit_btn").removeClass('d-none');
                    $("#upload_document_scan_process_btn").addClass('d-none');
                }
            });
        });
    </script>
@endsection
