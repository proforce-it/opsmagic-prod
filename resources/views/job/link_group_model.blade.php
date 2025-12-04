<div class="modal fade" id="group_link_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Link groups to {{ strtolower($job['name']) }}</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="group_link_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="group_link_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Select group</label>
                                        <select name="group_name" id="group_name" class="form-select form-select-lg" data-control="select2" data-placeholder="Select group" data-allow-clear="true">
                                            <option value="">Select..</option>
                                            @if($group)
                                                @foreach($group as $group_row)
                                                    <option value="{{$group_row['id']}}">{{$group_row['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error text-danger" id="group_name_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="group_with_job_id" id="group_with_job_id" value="{{ $job['id'] }}">
                            <button type="submit" name="group_link_form_submit_btn" id="group_link_form_submit_btn" class="btn btn-primary float-end">Link group to job</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="group_link_form_process_btn" id="group_link_form_process_btn">
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
@section('add_group_link_js')
    <script>
        $(document).ready(function() {
            $("#group_name").select2({
                dropdownParent: $("#group_link_form")
            });
        });

        $(document).on('click', '#link_group_modal_btn', function () {
            $(".error").html('');
            $("#group_link_form").trigger('reset');
            $("#group_name").val('').trigger('change');
            $("#group_link_modal").modal('show');
        });

        $("#group_link_modal_close_btn").on('click', function (){
            $("#group_link_form").trigger('reset');
            $("#group_name").val('').trigger('change');
            $("#group_link_modal").modal('hide');
        })

        $("#group_link_form").on('submit', function (e) {

            $("#group_link_form_submit_btn").addClass('d-none');
            $("#group_link_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('link-group-to-job-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#group_link_form_submit_btn").removeClass('d-none');
                    $("#group_link_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                        $("#group_link_modal_close_btn").click();
                    }
                },
                error   : function (response) {
                    $("#group_link_form_submit_btn").removeClass('d-none');
                    $("#group_link_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
