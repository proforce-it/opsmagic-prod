<div class="tab-pane fade" id="kt_table_widget_5_tab_5">
    <!--begin::Table container-->
    <div class="table-responsive">
        <div class="p-5">
            <div class="w-100">
                <div class="row">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">REQUIRED DOCUMENTS</div>
                    @if(\App\Helper\Clients\ClientHelper::clientDocumentFlag($client['client_documents']) == 1)
                        <div class="col-lg-12 mt-5">
                            <div class="alert alert-custom alert-warning" role="alert">
                                <div class="alert-text fs-4">
                                    <i class="las la-exclamation-triangle text-warning fs-xl-2"></i>
                                    All required documents must be uploaded before <strong>{{ $client['company_name'] }}</strong> can be made active
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-lg-12 mt-5">
                        <table class="table fs-7 gy-3 bg-active-dark">
                            <tbody class="text-gray-600 fw-bold">
                                @foreach(\App\Helper\Clients\ClientHelper::requiredDocuments() as $requiredDocument)
                                    @php($matchingDocuments = array_filter($client['client_documents'], function($doc) use ($requiredDocument) { return isset($doc['document_file_title']) && $doc['document_file_title'] === $requiredDocument; }))
                                    @php($matchingDocuments = array_values($matchingDocuments))
                                    @if(!empty($matchingDocuments))
                                        @php($matchingDocuments = $matchingDocuments[0])

                                        <tr id="document_row_1" class="bg-gray-100">
                                            <td class="text-left p-5 rounded-start w-100px">
                                                <table class="align-middle fs-5 gy-3" cellpadding="12">
                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                        <th><span class="badge badge-success fs-6 p-3 rounded-1">{{ $requiredDocument }}</span></th>
                                                        <th>UPLOADED AT <br> {{ date('d-m-Y - H:i:s', strtotime($matchingDocuments['uploaded_at'])) }}</th>
                                                        <th>UPLOADED BY <br> {{ $matchingDocuments['uploaded_by_details']['name'] }}</th>
                                                        <th>EXPIRY DATE <br>
                                                            @if(!empty($matchingDocuments['expiry_date']) && \Carbon\Carbon::parse($matchingDocuments['expiry_date'])->isPast())
                                                                <span class="text-danger">{{ date('d-m-Y', strtotime($matchingDocuments['expiry_date'])) }}</span>
                                                            @else
                                                                {{ date('d-m-Y', strtotime($matchingDocuments['expiry_date'])) }}
                                                            @endif
                                                        </th>
                                                    @if($matchingDocuments['document_file_title'] == 'ID')
                                                            <th>DOC TYPE <br> {{ $matchingDocuments['document_type'] }} </th>
                                                            <th>DOC NUMBER <br> {{ $matchingDocuments['document_no'] }} </th>
                                                            <th>DOC EXPIRY <br> {{ date('d-m-Y', strtotime($matchingDocuments['expiry_date'])) }} </th>
                                                        @endif
                                                    </tr>
                                                </table>
                                            </td>
                                            <td class="text-end p-5 rounded-end" style="width: 10%">
                                                @if(!empty($matchingDocuments['expiry_date']) && \Carbon\Carbon::parse($matchingDocuments['expiry_date'])->isPast())

                                                <a href="javascript:;"
                                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm upload_required_document_btn"
                                                   id="edit_required_document_btn"
                                                   data-document_title="{{ strtolower($requiredDocument) }}"
                                                   data-document_id="{{ $matchingDocuments['id'] }}">
                                                    <i class="fs-2 las la-redo-alt"></i>
                                                </a>
                                                @endif
                                                <a href="{{ asset('workers/client_document/'.$matchingDocuments['document_file']) }}"
                                                   target="_blank"
                                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                                    <i class="fs-1 las la-image"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @else
                                        <tr id="document_row_1" class="bg-gray-100">
                                            <td class="text-left p-5 rounded-start w-100px">
                                                <table class="align-middle fs-5 gy-3" cellpadding="12">
                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                        <th><span class="badge badge-danger fs-6 p-3 rounded-1">{{ $requiredDocument }}</span></th>
                                                        <th>UPLOADED AT <br> None</th>
                                                        <th>UPLOADED BY <br> None</th>
                                                        @if($requiredDocument == 'ID')
                                                            <th>DOC TYPE <br> None</th>
                                                            <th>DOC NUMBER <br> None</th>
                                                            <th>DOC EXPIRY <br> None</th>
                                                        @endif
                                                    </tr>
                                                </table>
                                            </td>
                                            <td class="text-end p-5 rounded-end" style="width: 10%">
                                                <a href="javascript:;"
                                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm upload_required_document_btn"
                                                   id="upload_required_document_btn"
                                                   data-document_title="{{ strtolower($requiredDocument) }}"
                                                   data-document_id="0" >
                                                    <i class="fs-xxl-1 las la-cloud-upload-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr><td colspan="2"></td></tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="modal fade" id="upload_required_document_modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header py-7 d-flex justify-content-between">
                                        <h2>Upload a <span id="upload_document_title"></span></h2>
                                        <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_upload_required_document_modal">
                                            <span class="svg-icon svg-icon-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <form id="upload_required_document_modal_form">
                                        @csrf
                                        <div class="modal-body scroll-y">
                                            <div class="fv-row row">
                                                <div class="col-lg-12">
                                                    <div class="fv-row mb-5 fv-plugins-icon-container">
                                                        <label for="required_document_expiry_date" class="fs-6 fw-bold">Expiry date (optional)</label>
                                                        <div class="position-relative d-flex align-items-center">
                                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                            <i class="fs-2 las la-calendar"></i>
                                                        </span>
                                                            <input class="form-control ps-12 flatpickr-input date_input" name="required_document_expiry_date" id="required_document_expiry_date" type="text" placeholder="Select expiry date" readonly="readonly" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="fv-row fv-plugins-icon-container">
                                                        <label for="required_document_file" class="fs-6 fw-bold"></label>
                                                        <input type="file" name="required_document_file" id="required_document_file" class="form-control"  accept="image/jpg, image/png, image/jpeg, application/pdf"/>
                                                        <span class="text-danger error" id="required_document_file_error"></span>
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
                                                        <input type="hidden" name="upload_required_document_client_id" id="upload_required_document_client_id" value="{{ $client['id'] }}">
                                                        <input type="hidden" name="upload_required_document_title" id="upload_required_document_title" value="">
                                                        <input type="hidden" name="upload_required_document_id" id="upload_required_document_id" value="0">
                                                        <button type="submit" name="upload_required_document_submit_btn" id="upload_required_document_submit_btn" class="btn btn-primary">Upload</button>
                                                        <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" name="upload_required_document_process_btn" id="upload_required_document_process_btn">
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
                    <div class="col-lg-6 d-flex align-items-center fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">OTHER DOCUMENTS</div>
                    <div class="col-lg-6 text-end border-bottom gs-0 border-4">
                        <div class="float-end">
                            <a href="javascript:;" id="upload_other_document_btn"><i class="las la-plus-circle text-primary fs-xxl-2qx"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-5">
                        <table class="table fs-7 gy-3 bg-active-dark">
                            <tbody class="text-gray-600 fw-bold">
                            @php($requiredDocumentTitles = \App\Helper\Clients\ClientHelper::requiredDocuments())
                            @foreach($client['client_documents'] as $document)
                                @if(!in_array($document['document_file_title'], $requiredDocumentTitles))
                                    <tr class="bg-gray-100">
                                        <td class="text-left p-5 rounded-start w-100px">
                                            <table class="align-middle fs-5 gy-3" cellpadding="12">
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                    <th><span class="badge badge-success fs-6 p-3 rounded-1">{{ $document['document_file_title'] }}</span></th>
                                                    <th>UPLOADED AT <br> {{ isset($document['uploaded_at']) ? date('d-m-Y - H:i:s', strtotime($document['uploaded_at'])) : 'N/A' }}</th>
                                                    <th>UPLOADED BY <br> {{ $document['uploaded_by_details']['name'] ?? 'Unknown' }}</th>
                                                    <th>DOC EXPIRY <br>
                                                        @if(!empty($document['expiry_date']) && \Carbon\Carbon::parse($document['expiry_date'])->isPast())
                                                            <span class="text-danger">{{ date('d-m-Y', strtotime($document['expiry_date'])) }}</span>
                                                        @else
                                                            {{ date('d-m-Y', strtotime($document['expiry_date'])) }}
                                                        @endif
                                                    </th>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text-end p-5 rounded-end" style="width: 10%">
                                            @if(!empty($document['expiry_date']) && \Carbon\Carbon::parse($document['expiry_date'])->isPast())
                                                <a href="javascript:;"
                                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm other_document_btn"
                                                   data-document_title="{{ strtolower($document['document_file_title']) }}"
                                                   data-document_id="{{ $document['id'] }}">
                                                    <i class="fs-2 las la-redo-alt"></i>
                                                </a>
                                            @endif
                                            <a href="{{ asset('workers/client_document/'.$document['document_file']) }}"
                                               target="_blank"
                                               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                                <i class="fs-1 las la-image"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr><td colspan="2"></td></tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                        <div class="modal fade" id="upload_other_document_modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header py-7 d-flex justify-content-between">
                                        <h2>Upload a new document</h2>
                                        <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_upload_other_document_modal">
                                            <span class="svg-icon svg-icon-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <form id="upload_other_document_modal_form">
                                        @csrf
                                        <div class="modal-body scroll-y">
                                            <div class="fv-row row">
                                                <div class="col-lg-12">
                                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                                        <label for="other_document_title" class="fs-6 fw-bold required">Document title</label>
                                                        <input type="text" name="other_document_title" id="other_document_title" class="form-control" placeholder="Enter document title">
                                                        <span class="text-danger error" id="other_document_title_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="fv-row mb-5 fv-plugins-icon-container">
                                                        <label for="other_document_expiry_date" class="fs-6 fw-bold">Expiry date (optional)</label>
                                                        <div class="position-relative d-flex align-items-center">
                                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                            <i class="fs-2 las la-calendar"></i>
                                                        </span>
                                                            <input class="form-control ps-12 flatpickr-input date_input" name="other_document_expiry_date" id="other_document_expiry_date" type="text" placeholder="Select expiry date" readonly="readonly" value="">
                                                        </div>
                                                        <span class="text-danger error" id="other_document_expiry_date_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="fv-row fv-plugins-icon-container">
                                                        <label for="other_document_file" class="fs-6 fw-bold"></label>
                                                        <input type="file" name="other_document_file" id="other_document_file" class="form-control"  accept="image/jpg, image/png, image/jpeg, application/pdf"/>
                                                        <span class="text-danger error" id="other_document_file_error"></span>
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
                                                        <input type="hidden" name="upload_other_document_client_id" id="upload_other_document_client_id" value="{{ $client['id'] }}">
                                                        <input type="hidden" name="upload_other_document_title" id="upload_other_document_title" value="">
                                                        <input type="hidden" name="upload_other_document_id" id="upload_other_document_id" value="0">
                                                        <button type="submit" name="upload_other_document_submit_btn" id="upload_other_document_submit_btn" class="btn btn-primary">Upload</button>
                                                        <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" name="upload_other_document_process_btn" id="upload_other_document_process_btn">
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
    </div>
    <!--end::Table-->
</div>

@section('edit_client_documents_detail_js')
    <script>
        $(".date_input").flatpickr({
            dateFormat  : "d-m-Y",
        });

        $(document).on('click', '.upload_required_document_btn', function () {
            const $btn = $(this);
            const documentId = $btn.data('document_id');
            const documentTitleRaw = $btn.data('document_title');

            let title = (documentTitleRaw === 'sla')
                ? 'service level agreement'
                : documentTitleRaw;

            if (documentId !== 0) {
                title = 'new ' + title;
            }

            $('#upload_required_document_id').val(documentId);
            $("#upload_required_document_title").val(documentTitleRaw)
            $('#upload_document_title').text(title);
            $('#upload_required_document_modal').modal('show');
        });

        $("#cls_btn_upload_required_document_modal").on('click', function () {
            $("#upload_required_document_modal_form").trigger('reset');
            $('#upload_required_document_client_id').val('{{ $client['id'] }}');
            $('#upload_required_document_id').val('0');
            $("#upload_required_document_modal").modal('hide');
        });

        $("#upload_required_document_modal_form").on('submit', function (e) {
            $(".error").html('');
            $("#upload_required_document_submit_btn").addClass('d-none');
            $("#upload_required_document_process_btn").removeClass('d-none');

            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-client-document-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    console.log(response)
                    decodeResponse(response)
                    if(response.code === 200) {
                        $("#cls_btn_upload_required_document_modal").click();
                        setTimeout(function() { location.reload(); }, 1500);
                    }

                    $("#upload_required_document_submit_btn").removeClass('d-none');
                    $("#upload_required_document_process_btn").addClass('d-none');
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#upload_required_document_submit_btn").removeClass('d-none');
                    $("#upload_required_document_process_btn").addClass('d-none');
                }
            });
        });

        $("#upload_other_document_btn").on('click', function () {
            $("#upload_other_document_modal_form").trigger('reset');
            $('#upload_other_document_modal').modal('show');
        })

        $("#cls_btn_upload_other_document_modal").on('click', function () {
            $("#upload_other_document_modal_form").trigger('reset');
            $('#upload_other_document_client_id').val('{{ $client['id'] }}');
            $("#upload_other_document_modal").modal('hide');
        });

        $("#upload_other_document_modal_form").on('submit', function (e) {
            $(".error").html('');
            $("#upload_other_document_submit_btn").addClass('d-none');
            $("#upload_other_document_process_btn").removeClass('d-none');

            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-client-other-document-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)
                    if(response.code === 200) {
                        $("#cls_btn_upload_other_document_modal").click();
                        setTimeout(function() { location.reload(); }, 1500);
                    }

                    $("#upload_other_document_submit_btn").removeClass('d-none');
                    $("#upload_other_document_process_btn").addClass('d-none');
                },
                error   : function (response) {
                    toastr.error(response.statusText);

                    $("#upload_other_document_submit_btn").removeClass('d-none');
                    $("#upload_other_document_process_btn").addClass('d-none');
                }
            });
        });

        $(document).on('click', '.other_document_btn', function () {
            const $btn = $(this);
            const documentId = $btn.data('document_id');
            const documentTitleRaw = $btn.data('document_title');
            console.log(documentTitleRaw);

            let title = (documentTitleRaw === 'sla')
                ? 'service level agreement'
                : documentTitleRaw;

            if (documentId !== 0) {
                title = 'new ' + title;
            }

            $('#upload_other_document_id').val(documentId);
            $("#upload_other_document_title").val(documentTitleRaw)
            $('#other_document_title').val(documentTitleRaw);
            $('#upload_other_document_modal').modal('show');
        });
    </script>
@endsection
