<div class="tab-pane fade" id="kt_table_widget_5_tab_9">
    <!--begin::Table container-->
    <div class="table-responsive">
        <div class="p-5">
            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">ID DOCUMENT</div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table fs-7 gy-3 bg-active-dark">
                        <tbody class="text-gray-600 fw-bold">
                        @if($worker['id_documents'])
                            @foreach($worker['id_documents'] as $key => $wd_row)
                                <tr id="document_row_{{ $wd_row['id'] }}" class="bg-gray-100">
                                    <td class="text-left p-5 rounded-start w-100px">
                                        <span class="badge badge-success fs-6">{{ $wd_row['document_type'] }}</span>
                                    </td>
                                    <td class="text-left p-5 rounded-start">
                                        <span class="text-dark fw-boldest">DOCUMENT NO.</span> <span>{{ $wd_row['document_no'] }}</span>
                                        <span class="ms-3 text-dark fw-boldest">EXPIRES:</span> <span  class="{{ ($wd_row['expiry_date'] < (\Carbon\Carbon::today())) ? 'text-danger' : '' }}">{{ date('d-m-Y', strtotime($wd_row['expiry_date'])) }}</span>
                                        <br>
                                        <span class="text-dark fw-boldest">UPLOADED:</span> <span>{{ date('d-m-Y h:i:s a', strtotime($wd_row['created_at'])) }}</span>
                                        <span class="ms-3 text-dark fw-boldest">BY:</span> <span>
                                            @if($wd_row['uploaded_by_details'])
                                                {{ $wd_row['uploaded_by_details']['name'] }}
                                            @endif
                                        </span>
                                    </td>

                                    <td class="text-end p-5 rounded-end" style="width: 10%">
                                        @if($wd_row['expiry_date'] < (\Carbon\Carbon::today()))
                                            <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" id="edit_document_btn" data-document_id="{{ $wd_row['id'] }}" ><i class="fs-2 las la-redo-alt"></i></a>
                                        @endif
                                        <a href="{{ asset('workers/document/'.$wd_row['document_file']) }}" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                            <i class="las la-image" style="font-size: 24px"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr><td colspan="2"></td></tr>

                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">REGISTRATION PACK</div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table fs-7 gy-3 bg-active-dark">
                        <tbody class="text-gray-600 fw-bold">
                        @if($worker['registration_documents'])
                            @foreach($worker['registration_documents'] as $key => $wd_row)
                                <tr id="document_row_{{ $wd_row['id'] }}" class="bg-gray-100">
                                    <td class="text-left p-5 rounded-start">
                                        <span class="badge badge-success fs-6">{{ $wd_row['document_file_title'] }}</span>
                                        <span class="ms-3 text-dark fw-boldest">UPLOADED:</span> <span>{{ date('d-m-Y h:i:s a', strtotime($wd_row['created_at'])) }}</span>
                                        <span class="ms-3 text-dark fw-boldest">BY:</span> <span>
                                            @if($wd_row['uploaded_by_details'])
                                                {{ $wd_row['uploaded_by_details']['name'] }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-end p-5 rounded-end" style="width: 10%">
                                        <a href="{{ asset('workers/document/'.$wd_row['document_file']) }}" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                            <i class="las la-image" style="font-size: 24px"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr><td colspan="2"></td></tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-12 text-start text-muted fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">OTHER DOCUMENTS</div>
            </div>
            <form id="document_details_form">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <input type="file" name="document_file[]" id="document_1" class="form-control"  accept="image/jpg, image/png, image/jpeg, application/pdf"/>
                            <label class="fw-bold text-muted">PNG, JPG, JPEG or PDF (Max. 10MB)</label>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <input type="text" name="document_title[]" id="document_title_1" class="form-control border-primary" placeholder="Enter document title."/>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <input type="hidden" name="worker_id" id="worker_id" value="{{ $worker['id'] }}">
                        <input type="hidden" name="total_document_section" id="total_document_section" value="1">
                        <button type="submit" name="document_details_submit" id="document_details_submit" class="btn btn-primary float-end">
                            <i class="fs-2 las la-plus"></i>
                            Add
                        </button>
                        <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="document_details_process_btn" id="document_details_process_btn" style="display: none">
                            <span>Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-lg-12">
                    <table class="table fs-7 gy-3 bg-active-dark">
                        <tbody class="text-gray-600 fw-bold">
                        @if($worker['other_documents'])
                            @foreach($worker['other_documents'] as $key => $wd_row)
                                <tr id="document_row_{{ $wd_row['id'] }}" class="bg-gray-100">
                                    <td class="text-left p-5 rounded-start">
                                        <span class="badge badge-success fs-6">{{ $wd_row['document_file_title'] }}</span>
                                        <span class="ms-3 text-dark fw-boldest">UPLOADED:</span> <span>{{ date('d-m-Y h:i:s a', strtotime($wd_row['created_at'])) }}</span>
                                        <span class="ms-3 text-dark fw-boldest">BY:</span> <span>
                                                @if($wd_row['uploaded_by_details'])
                                                {{ $wd_row['uploaded_by_details']['name'] }}
                                            @endif
                                            </span>
                                    </td>
                                    <td class="text-end p-5 rounded-end" style="width: 10%">
                                        <a href="{{ asset('workers/document/'.$wd_row['document_file']) }}" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                            <span class="svg-icon svg-icon-1">
                                          <i class="las la-image" style="font-size: 24px"></i>
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                                <tr><td colspan="2"></td></tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Update ID Modal -->
            <div class="modal fade" id="edit_document_id_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header py-7 d-flex justify-content-between">
                            <h2>Update ID for {{$worker['first_name'].' '.$worker['last_name']}}</h2>
                            <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_edit_document_id_modal">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <form id="edit_document_id_form">
                            @csrf
                            <div class="modal-body scroll-y m-5">
                                <div class="fv-row row">
                                    <div class="col-lg-12">
                                        <div class="fv-row fv-plugins-icon-container">
                                            <label for="document_type" class="fs-6 fw-bold required">Document type</label>
                                            <select name="document_type" id="document_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                                <option value="">Select...</option>
                                                <option value="Passport">Passport</option>
                                                <option value="ID card">ID card</option>
                                            </select>
                                            <input name="document_id" id="document_id" type="hidden" value="">
                                            <span class="text-danger error" id="document_type_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="fv-row mt-10 fv-plugins-icon-container">
                                            <label for="document_number" class="fs-6 fw-bold required">Document number</label>
                                            <input class="form-control" name="document_number" id="document_number" type="text" value="" placeholder="Enter document number">
                                            <span class="text-danger error" id="document_number_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="fv-row mt-10 fv-plugins-icon-container">
                                            <label for="document_expiry_date" class="fs-6 fw-bold required">Expiry date</label>
                                            <div class="position-relative d-flex align-items-center">
                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                    <i class="fs-2 las la-calendar"></i>
                                                </span>
                                                <input class="form-control ps-12 flatpickr-input date_input" name="document_expiry_date" id="document_expiry_date" type="text" placeholder="Select expiry date" readonly="readonly" value="">
                                            </div>
                                            <span class="text-danger error" id="document_expiry_date_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="fv-row mt-10 fv-plugins-icon-container">
                                            <label for="document_scan_file" class="fs-6 fw-bold required">Document scan</label>
                                            <input type="file" name="document_scan_file" id="document_scan_file" class="form-control"  accept="image/jpg, image/png, image/jpeg, application/pdf"/>
                                            <span class="text-danger error" id="document_scan_file_error"></span>
                                            <label class="fw-bold text-muted">PNG, JPG, JPEG or PDF (Max. 10MB)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer d-flex justify-content-center">
                                <div class="fv-row">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button type="submit" name="update_edit_document_submit_btn" id="update_edit_document_submit_btn" class="btn btn-primary">Update ID</button>
                                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" name="update_edit_document_process_btn" id="update_edit_document_process_btn">
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
    <!--end::Table-->
</div>

@section('edit_worker_document_js')
    <script>
        $("#document_expiry_date").flatpickr({
            dateFormat  : "d-m-Y",
        });
    </script>
    <script>
        $("#document_details_form").on('submit', function (e) {
            $(".error").html('');
            $("#document_details_submit").hide();
            $("#document_details_process_btn").show();

            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-worker-document-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    if(response.code === 200)
                        setTimeout(function() { location.reload(); }, 1500);

                    $("#document_details_submit").show();
                    $("#document_details_process_btn").hide();
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $(document).on('click', '#delete_document', function () {
            let id = $(this).attr('data-document-id');
            sweetAlertConfirmDelete('You want to delete this document!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-document-action') }}'+'/'+id,
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                $("#document_row_"+id).hide();
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

        $(document).on('click', '#edit_document_btn', function () {
            let document_id = $(this).attr('data-document_id')
            $.ajax({
                type    : 'post',
                url     : '{{ url('/get-document-id-details') }}',
                data    : {
                    _token : '{{ @csrf_token() }}',
                    document_id : document_id,
                },
                success : function (response) {
                    $('#document_type').val(response.data.document_type).trigger('change');
                    $('#document_number').val(response.data.document_no);
                    $('#document_expiry_date').val(response.data.expiry_date);
                    $("#document_id").val(document_id);
                    $("#edit_document_id_modal").modal('show');
                },
                error : function (result) {
                    toastr.error(result.statusText);
                }
            });
        });

        $("#cls_btn_edit_document_id_modal").on('click', function (){
            $("#edit_document_id_form").trigger('reset');
            $("#document_type").val('').trigger('change');
            $("#edit_document_id_modal").modal('hide');
        });

        $("#edit_document_id_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#update_edit_document_submit_btn").addClass('d-none');
            $("#update_edit_document_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('update-document-id-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response);

                    $("#update_edit_document_submit_btn").removeClass('d-none');
                    $("#update_edit_document_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#cls_btn_edit_document_id_modal").click()
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#update_edit_document_submit_btn").removeClass('d-none');
                    $("#update_edit_document_process_btn").addClass('d-none');
                }
            });
        });

    </script>
@endsection
