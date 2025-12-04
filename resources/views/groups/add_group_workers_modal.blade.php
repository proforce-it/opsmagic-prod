<div class="modal fade" id="group_worker_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Link associates(s) to group: {{ $group['name'] }}</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="group_worker_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="add_group_worker_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="associated_cost_center" class="text-muted fs-6 fw-bold">Cost centre (optional)</label>
                                        <select name="associated_cost_center[]" id="associated_cost_center" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Select associated cost center" data-allow-clear="true" multiple>
                                            @if($cost_centres)
                                                @foreach($cost_centres as $c_row)
                                                    <option value="{{ $c_row['id'] }}">{{ $c_row['short_code'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error text-danger" id="associated_cost_center_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label for="group_worker_name" class="text-muted fs-6 fw-bold required">Select associate(s)</label>
                                        <select name="group_worker_name[]" id="group_worker_name" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Start typing to display matching results" data-allow-clear="true" multiple>
                                        </select>
                                        <span class="text-danger error" id="group_worker_name_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="group_id" id="group_id" value="{{ $group['id'] }}">
                            <button type="submit" name="group_worker_form_submit_btn" id="group_worker_form_submit_btn" class="btn btn-primary float-end">Create group</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="group_worker_form_process_btn" id="group_worker_form_process_btn">
                                <span>Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('add_group_worker_js')
    <script>
        $(document).ready(function() {
            $("#team").select2({
                dropdownParent: $("#add_group_worker_form")
            });
        });

        $("#add_group_worker_modal_btn").on('click', function () {
            $(".error").html('');
            $("#associated_cost_center").val('').trigger('change');
            $("#group_worker_name").val('').trigger('change');
            $("#group_worker_modal").modal('show');
        });

        $("#group_worker_modal_close_btn").on('click', function (){
            $("#associated_cost_center").val('').trigger('change');
            $("#group_worker_name").val('').trigger('change');
            $("#group_worker_modal").modal('hide');
        })

        $(function() {
            $("#group_worker_name").select2({
                $dropdownParent: $("#add_group_worker_form"),
                ajax: {
                    url: '{{ url('search-worker-to-add-group') }}',
                    dataType: 'json',
                    type: "POST",
                    data: function (term) {
                        return {
                            _token: '{{ csrf_token() }}',
                            associated_cost_center : $("#associated_cost_center").val(),
                            group_id  : '{{ $group['id'] }}',
                            keyword : term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.data, function (item) {
                                console.log(item)
                                return {
                                    text: item.name,
                                    id: item.id,
                                }
                            })
                        };
                    }

                }
            });
        });

        $("#add_group_worker_form").on('submit', function (e) {

            $("#group_worker_form_submit_btn").addClass('d-none');
            $("#group_worker_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-group-worker-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#group_worker_form_submit_btn").removeClass('d-none');
                    $("#group_worker_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        group_worker_datatable.ajax.reload();
                        $("#group_worker_modal_close_btn").click();
                    }
                },
                error   : function (response) {
                    $("#group_worker_form_submit_btn").removeClass('d-none');
                    $("#group_worker_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
