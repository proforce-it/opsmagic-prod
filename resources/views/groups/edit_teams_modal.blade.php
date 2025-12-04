<div class="modal fade" id="edit_teams_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 class="modal-title">Edit team</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="edit_teams_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="edit_teams_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Team Name</label>
                                        <input type="text" name="edit_name" id="edit_name" class="form-control" value="" placeholder="Enter name">
                                        <span class="error text-danger" id="edit_name_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Cost centre</label>
                                        <select name="edit_cost_centre" id="edit_cost_centre" class="form-select form-select-lg" data-control="select2" data-placeholder="Select cost centre" data-allow-clear="true">
                                            <option value="">Select..</option>
                                            @if($costCentre)
                                                @foreach($costCentre as $costCentre_row)
                                                    <option value="{{$costCentre_row['id']}}">{{$costCentre_row['short_code']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error text-danger" id="edit_cost_centre_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Team members</label>
                                        <select name="edit_team_members[]" id="edit_team_members" class="form-select form-select-lg" data-control="select2" data-placeholder="Select team members" data-allow-clear="true" multiple>
                                        </select>
                                        <span class="error text-danger" id="edit_team_members_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="edit_teams_id" id="edit_teams_id" value="0">
                            <button type="submit" name="edit_teams_form_submit_btn" id="edit_teams_form_submit_btn" class="btn btn-primary float-end">Update</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="edit_teams_form_process_btn" id="edit_teams_form_process_btn">
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

@section('edit_teams_js')
    <script>
        $(document).ready(function() {
            $("#edit_cost_centre").select2({
                dropdownParent: $("#edit_teams_form")
            });
        });

        $(document).on('click', '#edit_teams', function () {
            $(".error").html('');
            let teamName = $(this).data('teams-name');
            $('.modal-title').text('Edit team: ' + teamName);
            $("#edit_teams_form").trigger('reset');

            $.ajax({
                type        : 'get',
                url         : '{{ url('get-single-teams') }}'+'/'+$(this).attr('data-teams-id'),
                success     : function (response) {
                    if(response.code === 200) {

                        let team = response.data.teams_details;

                        $("#edit_teams_id").val(team.id);
                        $("#edit_name").val(team.name);
                        $("#edit_cost_centre").val(team.cost_centre.id).trigger('change');
                        $("#edit_team_members").empty().append(response.data.edit_team_members_options);

                        $("#edit_teams_modal").modal('show');
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });

        $("#edit_teams_modal_close_btn").on('click', function () {
            $("#edit_teams_form").trigger('reset');
            $("#edit_cost_centre").val('').trigger('change');
            $("#edit_team_members").val('').trigger('change');
            $("#edit_teams_modal").modal('hide');
        })

        $("#edit_teams_form").on('submit', function (e) {

            $("#edit_teams_form_submit_btn").addClass('d-none');
            $("#edit_teams_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('edit-team-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#edit_teams_form_submit_btn").removeClass('d-none');
                    $("#edit_teams_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                        $("#edit_teams_modal_close_btn").click();
                    }
                },
                error   : function (response) {
                    $("#edit_teams_form_submit_btn").removeClass('d-none');
                    $("#edit_teams_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
