<div class="tab-pane fade" id="kt_table_widget_5_tab_7">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="w-100">
                    <div class="row">
                        <div class="col-lg-10 d-flex align-items-center">
                            <div class="fs-1 fw-bolder">Add new <span id="note_title"></span> note</div>
                        </div>
                        <div class="col-lg-2">
                            <div class="float-end">
                                <a href="javascript:;" id="add_new_note">
                                    <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                </a>
                                <a href="javascript:;" class="close_note_form d-none" id="close_note_form">
                                    <i class="fs-xxl-2qx las la-times-circle text-primary"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="note_form_section" style="display: none">
                        <div class="w-100 mt-5 mb-5 border-bottom border-4"></div>
                        <div class="col-lg-12 border border-1 p-5 border-dark rounded-3">
                            <form id="note_details_form">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="fv-row fv-plugins-icon-container">
                                            <label class="fs-6 required">Note type</label>
                                            <input type="hidden" name="note_type" id="note_type" value="">
                                            <select name="c_note_type" id="c_note_type" class="form-select form-select-lg bg-secondary" data-control="select2" data-placeholder="Select note type" data-allow-clear="true" disabled>
                                                <option value="">Select note type</option>
                                                <option value="client">Client</option>
                                                <option value="job">Job</option>
                                                <option value="site">Site</option>
                                                <option value="contact">Contact</option>
                                            </select>
                                            <span class="error text-danger" id="c_note_type_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label class="text-muted fs-6 fw-bold"></label>
                                            <textarea name="note_text" id="note_text" rows="5" placeholder="Enter note text here..." class="form-control"></textarea>
                                            <span class="error text-danger" id="note_text_error"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <input type="hidden" name="action_id" id="action_id" value="">
                                            <button type="submit" name="note_form_submit" id="note_form_submit" class="btn btn-primary float-end">Add note</button>
                                            <button type="reset" name="note_form_cancel_btn" class="btn btn-dark float-end me-1 close_note_form">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="w-100 mt-5 mb-5 border-bottom border-dashed border-1"></div>
                <div class="d-flex align-items-center position-relative my-1 mb-5">
                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                        <i class="fs-2 las la-search"></i>
                    </span>
                    <input type="text" data-kt-client-note-filter-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Quick find note text" />
                </div>
                <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark table-responsive" id="note_datatable">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th>Type</th>
                        <th>Note</th>
                        <th class="text-end">Created by</th>
                        <th class="text-end">Created at</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@section('edit_client_note_js')
    <script>
        $("#notes_button").on('click', function () {
            let note_type = $(this).data('note_type');
            $("#c_note_type").val(note_type).trigger('change');
            $("#note_title").html(note_type);
            $("#note_type").val(note_type)
        });

        $("#add_new_note").on('click', function () {
            $(".error").html('');
            $("#note_text").val('');
            $("#note_form_section").slideDown(600);

            $("#add_new_note").addClass('d-none');
            $("#close_note_form").removeClass('d-none');
        });

        $(document).on('click', '.close_note_form', function (){
            $("#note_form_section").slideUp(600);
            $("#add_new_note").removeClass('d-none');
            $("#close_note_form").addClass('d-none');
        })

        let notes_table;
        notes_table = $('#note_datatable').DataTable({
            "processing": false,
            "serverSide": false,
            "ajax": {
                "type": "post",
                "url": '{{ url('get-client-notes') }}',
                "data": {
                    _token      : "{{ csrf_token() }}",
                    action_id   : $("#action_id").val(),
                    note_type   : $('#notes_button').data('note_type'),
                }
            },
            "columns": [
                {"data": "type", "width": "8%"},
                {"data": "note"},
                {"data": "created_by", "sClass": "text-end", "width": "10%"},
                {"data": "created_at", "sClass": "text-end", "width": "15%"},
            ],
            "order": [[ 0, "desc" ]],
        });

        const client_note_filterSearch = document.querySelector('[data-kt-client-note-filter-table-filter="search"]');
        client_note_filterSearch.addEventListener('keyup', function (e) {
            notes_table.search(e.target.value).draw();
        });

        $("#note_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $.ajax({
                type        : 'post',
                url         : '{{ url('store-client-note-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)
                    if(response.code === 200) {
                        $("#note_text").val('');
                        $(".close_note_form").click();

                        notes_table.ajax.reload();
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

    </script>
@endsection
