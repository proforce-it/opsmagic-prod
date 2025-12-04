<div class="modal fade" id="teams_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Add a team</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="teams_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="add_teams_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Team Name</label>
                                        <input type="text" name="name" id="name" class="form-control" value="" placeholder="Enter name">
                                        <span class="error text-danger" id="name_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Cost centre</label>
                                        <select name="cost_centre" id="cost_centre" class="form-select form-select-lg" data-control="select2" data-placeholder="Select cost centre" data-allow-clear="true">
                                            <option value="">Select..</option>
                                            @if($costCentre)
                                                @foreach($costCentre as $costCentre_row)
                                                    <option value="{{$costCentre_row['id']}}">{{$costCentre_row['short_code']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error text-danger" id="cost_centre_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label class="text-muted fs-6 fw-bold required">Team members</label>
                                        <select name="team_members[]" id="team_members" class="form-select form-select-lg" data-control="select2" data-placeholder="Select team members" data-allow-clear="true" multiple>
                                            @if($teamMembers)
                                                @foreach($teamMembers as $teamMembers_row)
                                                    <option value="{{$teamMembers_row['id']}}">{{$teamMembers_row['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error text-danger" id="team_members_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" name="teams_form_submit_btn" id="teams_form_submit_btn" class="btn btn-primary float-end">Add team</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" data-kt-stepper-action="submit" name="teams_form_process_btn" id="teams_form_process_btn">
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

@section('add_teams_js')
    <script>
        $(document).ready(function() {
            $("#cost_centre").select2({
                dropdownParent: $("#add_teams_form")
            });
        });

        $("#add_teams_modal_btn").on('click', function () {
            $(".error").html('');
            $("#add_teams_form").trigger('reset');
            $("#cost_centre").val('').trigger('change');
            $("#team_members").val('').trigger('change');
            $("#teams_modal").modal('show');
        });

        $("#teams_modal_close_btn").on('click', function (){
            $("#add_teams_form").trigger('reset');
            $("#cost_centre").val('').trigger('change');
            $("#team_members").val('').trigger('change');
            $("#teams_modal").modal('hide');
        })

        $("#add_teams_form").on('submit', function (e) {

            $("#teams_form_submit_btn").addClass('d-none');
            $("#teams_form_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('store-teams-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#teams_form_submit_btn").removeClass('d-none');
                    $("#teams_form_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                        $("#teams_modal_close_btn").click();
                    }
                },
                error   : function (response) {
                    $("#teams_form_submit_btn").removeClass('d-none');
                    $("#teams_form_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
