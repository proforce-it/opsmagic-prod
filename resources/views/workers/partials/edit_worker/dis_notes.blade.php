<div class="tab-pane fade" id="kt_table_widget_5_tab_8">
    <div class="table-responsive">
        <form id="worker_note_form">
            @csrf
            <div class="p-5">
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Notes</div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Note type</label>
                            <select name="note_type" id="note_type" class="form-select form-select-lg" data-control="select2" data-placeholder="Choose a note type" data-allow-clear="true">
                                <option value="">Choose a note type</option>
                                <option value="Criminal">Criminal</option>
                                <option value="Medical">Medical</option>
                                <option value="General">General</option>
                            </select>
                            <span class="error text-danger" id="note_type_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="text-muted fs-6 fw-bold"></label>
                            <input type="hidden" name="note_worker_id" id="note_worker_id" value="{{ $worker['id'] }}">
                            <textarea name="note_text" id="note_text" rows="5" placeholder="Enter note text here..." class="form-control"></textarea>
                            <span class="error text-danger" id="note_text_error"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" name="worker_note_update" id="worker_note_update" class="btn btn-primary btn-lg">
                                    <i class="fs-2 las la-plus"></i>
                                Add note
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="p-5">
            <div class="row mb-5">
                <div class="col-lg-12 text-start text-muted fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark table-responsive">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="text-left">Type</th>
                            <th class="text-left">Note</th>
                            <th class="text-center">Created by</th>
                            <th class="text-center">Created at</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                        @if($allNotes)
                            @foreach($allNotes as $noteObject)
                                @foreach($noteObject as $wn_row)
                                    <tr>
                                        <td class="text-justify">
                                            @if($wn_row['note_type'] == 'Medical' || $wn_row['note_type'] == 'Criminal')
                                                <span class="badge badge-danger">{{ $wn_row['note_type'] }}</span>
                                            @else
                                                <span class="badge badge-success">{{ $wn_row['note_type'] }}</span>
                                            @endif
                                        </td>
                                        <td class="text-justify">{!! $wn_row['note_text'] !!}</td>
                                        <td class="text-center"><span class="badge bg-gray-600">{{ $wn_row['user_details']['name'] }}</span></td>
                                        <td class="text-center"><span class="badge bg-gray-600">{{ date('d-m-Y h:i:s a', strtotime($wn_row['created_at'])) }}</span></td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('edit_worker_note_js')
    <script>
        $("#worker_note_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-worker-note') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    if(response.code === 200)
                        setTimeout(function() { location.reload(); }, 1500);
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
