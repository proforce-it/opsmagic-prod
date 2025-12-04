<div class="tab-pane fade" id="kt_table_widget_5_tab_5">
    <!--begin::Table container-->
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="w-100">
                    <form id="document_details_form">
                        @csrf
                        <div class="fv-row" id="document_section">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <input type="file" name="document_file[]" id="document_1" class="form-control"  accept="image/jpg, image/png, image/jpeg, application/pdf"/>
                                        <label class="fw-bold text-muted">Note: Maximum file size is 10MB. PNG, JPG, JPEG and PDF files are supported.</label>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <input type="text" name="document_title[]" id="document_title_1" class="form-control border-primary" placeholder="Enter document title."/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" name="client_id" id="client_id" value="{{ $client['id'] }}">
                                <input type="hidden" name="total_document_section" id="total_document_section" value="1">
                                <!-- <input type="button" name="new_document_row" id="new_document_row" class="btn btn-primary btn-sm" value="Add" />-->
                                <button type="submit" name="document_details_submit" id="document_details_submit" class="btn btn-primary float-end">Upload</button>
                                <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="document_details_process_btn" id="document_details_process_btn" style="display: none">
                                    <span>Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="w-100 mt-5 mb-5 border-bottom border-dashed border-1"></div>
                <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="text-left">Document title</th>
                        <th class="text-center" style="width: 10%">Created at</th>
                        <th class="text-center" style="width: 10%">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                    @if($client['client_documents'])
                        @foreach($client['client_documents'] as $key => $wd_row)
                            <tr id="document_row_{{ $wd_row['id'] }}">
                                <td class="text-left">{{ $wd_row['document_file_title'] }}</td>
                                <td class="text-center"><span class="badge badge-info">{{ date('d-m-Y h:i:s a', strtotime($wd_row['created_at'])) }}</span></td>
                                <td class="text-center">
                                    <a href="{{ asset('workers/client_document/'.$wd_row['document_file']) }}" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                        <i class="fs-2 las la-file"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" data-kt-absence-table-filter="delete_row" id="delete_document" data-document-id="{{ $wd_row['id'] }}">
                                        <i class="fs-2 las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--end::Table-->
</div>

@section('edit_client_documents_detail_js')
    <script>
        $("#document_details_form").on('submit', function (e) {
            $(".error").html('');
            $("#document_details_submit").hide();
            $("#document_details_process_btn").show();

            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-client-document-details') }}',
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
                        url     : '{{ url('delete-client-document-action') }}'+'/'+id,
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
    </script>
@endsection