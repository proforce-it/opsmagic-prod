@extends('theme.auth.auth_page')

@section('title', config('app.name').' - Reset Password')
@section('form_content')
    <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-color: #4E618F;">
        <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
            <a href="javascript:;" class="mb-12">
                <img alt="Logo" src="{{ asset('assets/media/logos/new_logo.png') }}" class="h-100px" />
            </a>
            <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                @if($error)
                    <div class="alert alert-custom alert-danger" role="alert">
                        <div class="alert-text fs-4">
                            <i class="las la-exclamation-triangle text-danger fs-xl-2"></i>
                            {{ $error }}
                        </div>
                    </div>
                @else
                    <div class="alert alert-custom alert-success d-none" id="success_alert" role="alert">
                        <div class="alert-text fs-4">
                            <i class="las la-check-circle text-success fs-xl-2"></i>
                            <span id="success_message"></span>
                        </div>
                    </div>
                    <form class="form w-100" id="create_worker_password_form" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="text-center mb-10">
                            <h2 class="text-dark mb-3">Setup New Password</h2>
                        </div>
                        <div class="mb-10 fv-row fv-plugins-icon-container" data-kt-password-meter="true">
                            <div class="mb-1">
                                <label class="form-label fw-bolder text-dark fs-6">Password</label>
                                <div class="position-relative mb-3">
                                    <input class="form-control form-control-lg form-control-solid" type="password" placeholder="Enter password" name="password" id="password" autocomplete="off">
                                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                    <i class="bi bi-eye-slash fs-2"></i>
                                    <i class="bi bi-eye fs-2 d-none"></i>
                                </span>
                                    <span class="error text-danger" id="password_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="fv-row mb-10 fv-plugins-icon-container">
                            <label class="form-label fw-bolder text-dark fs-6">Confirm Password</label>
                            <input class="form-control form-control-lg form-control-solid" type="password" placeholder="Enter confirm password" name="password_confirmation" id="password_confirmation" autocomplete="off">
                            <span class="error text-danger" id="password_confirmation_error"></span>
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                        </div>
                        <div class="text-center">
                            <button type="submit" id="create_worker_password_submit_btn" class="btn btn-lg btn-primary w-100 mb-5">
                                <span class="indicator-label">Create Password</span>
                            </button>
                            <button type="button" class="btn btn-lg btn-primary w-100 mb-5 disabled d-none" data-kt-stepper-action="submit" id="create_worker_password_process_btn">
                            <span>Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
        @include('theme.auth.partials.auth_footer')
    </div>
@endsection

@section('reset_worker_password_js')
    <script>
        $("#create_worker_password_form").on('submit', function (e) {

            $("#create_worker_password_submit_btn").addClass('d-none');
            $("#create_worker_password_process_btn").removeClass('d-none');

            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('reset-worker-password-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {

                    $("#create_worker_password_submit_btn").removeClass('d-none');
                    $("#create_worker_password_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#create_worker_password_form").addClass('d-none');
                        $("#success_message").text(response.message)
                        $("#success_alert").removeClass('d-none');
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                    } else {
                        for (let i = 0; i < Object.keys(response.data).length; i++) {
                            if (i === 0) {
                                $("#"+Object.keys(response.data)[0]).focus();
                            }
                            $("#" + Object.keys(response.data)[i] + "_error").empty().append(response.data[Object.keys(response.data)[i]][0]);
                        }
                    }
                },
                error   : function (response) {
                    $("#create_worker_password_submit_btn").removeClass('d-none');
                    $("#create_worker_password_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
