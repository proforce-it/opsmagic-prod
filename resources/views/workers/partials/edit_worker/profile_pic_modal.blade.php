<div class="modal fade" id="edit_worker_profile_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Add / Update Photo for {{ $worker['first_name'].' '.$worker['middle_name'].' '.$worker['last_name'] }}</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_edit_worker_profile_modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                </div>
            </div>
            <div class="modal-body scroll-y m-5">
                <div class="w-100">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="fv-row fv-plugins-icon-container">
                                    <div class="image-input image-input-outline {{ ($worker['profile_pic']) ? '' : 'image-input-empty' }}" data-kt-image-input="true" style="background-image: url('{{ asset('assets/media/avatars/worker-square.png') }}');">
                                        <div class="image-input-wrapper w-lg-200px h-lg-200px"
                                             @if($worker['profile_pic'])
                                                 style="background-image: url('{{ asset('workers/profile/'.$worker['profile_pic']) }}')"
                                             @else
                                                 style="background-image: none"
                                             @endif>
                                        </div>
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <input type="file" id="worker_profile_pic_input" name="worker_profile_pic_input" accept=".png, .jpg, .jpeg" />
                                            <input type="hidden" name="avatar_remove" id="avatar_remove" />
                                        </label>
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                                <i class="bi bi-x fs-2"></i>
                                            </span>
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                                <i class="bi bi-x fs-2"></i>
                                            </span>
                                    </div>
                                    <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="fv-row">
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" name="edit_worker_profile_submit_btn" id="edit_worker_profile_submit_btn" class="btn btn-primary float-end">Upload</button>
                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="edit_worker_profile_process_btn" id="edit_worker_profile_process_btn" style="display: none">
                                    <span>Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('edit_worker_profile_pic_js')
    <script>
        $(document).ready(function() {
            $("#worker_profile_pic").on('click', function () {
                $("#edit_worker_profile_modal").modal('show');
            });

            $("#cls_btn_edit_worker_profile_modal").on('click', function () {
                $("#edit_worker_profile_modal").modal('hide');
            });

            $("#edit_worker_profile_submit_btn").on('click', function () {
                var fileInput = $('#worker_profile_pic_input')[0];
                $("#edit_worker_profile_submit_btn").hide();
                $("#edit_worker_profile_process_btn").show();

                var formData = new FormData();
                formData.append('_token', '{{ @csrf_token() }}');
                formData.append('worker_profile_pic', fileInput.files[0]);
                formData.append('worker_id', '{{ $worker['id'] }}');
                formData.append('avatar_remove', $("#avatar_remove").val());

                $.ajax({
                    type        : 'POST',
                    url         : '{{ url('upload-worker-profile-pic') }}',
                    data        : formData,
                    processData : false,
                    contentType : false,
                    success     : function (response) {
                        decodeResponse(response);

                        if(response.code === 200) {
                            $("#edit_worker_profile_modal").modal('hide');
                            setTimeout(function () {
                                location.reload();
                            }, 1500)
                        }

                        $("#edit_worker_profile_submit_btn").show();
                        $("#edit_worker_profile_process_btn").hide();
                    },
                    error: function (error) {
                        $("#edit_worker_profile_submit_btn").show();
                        $("#edit_worker_profile_process_btn").hide();

                        toastr.error(error.statusText);
                    }
                });
            });
        });
    </script>
@endsection