@extends('theme.auth.auth_page')

@section('title', config('app.name').' - Reset Password')
@section('form_content')
    <!--begin::Authentication - Sign-in -->
    <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-color: #4E618F;">
        <!--begin::Content-->
        <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
            <!--begin::Logo-->
            <a href="javascript:;" class="mb-12">
                <img alt="Logo" src="{{ asset('assets/media/logos/new_logo.png') }}" class="h-100px" />
            </a>
            <!--end::Logo-->
            <!--begin::Wrapper-->
            <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                <!--begin::Form-->
                <form class="form w-100" id="password_reset_form" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <!--begin::Heading-->
                    <div class="text-center mb-10">
                        <!--begin::Title-->
                        <h2 class="text-dark mb-3">Setup New Password</h2>
                        <div class="text-gray-400 fw-bold fs-4">Already have reset your password ? <a href="{{ url('/') }}" class="link-primary fw-bolder">Sign in here</a></div>

                        <!--end::Title-->
                    </div>
                    <!--begin::Heading-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container" data-kt-password-meter="true">
                        <!--begin::Wrapper-->
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Password</label>
                            <div class="position-relative mb-3">
                                <input class="form-control form-control-lg form-control-solid" type="password" placeholder="Enter new password" name="password" id="password" autocomplete="off">
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
                    <!--end::Input group-->

                    <!--begin::Actions-->
                    <div class="text-center">
                        <!--begin::Submit button-->
                        <button type="submit" id="reset_password_submit_btn" class="btn btn-lg btn-primary w-100 mb-5">
                            <span class="indicator-label">Reset Password</span>
                        </button>
                        <button type="button" class="btn btn-lg btn-primary w-100 mb-5 disabled d-none" data-kt-stepper-action="submit" id="reset_password_process_btn">
                            <span>Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <!--end::Submit button-->
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Content-->
        <!--begin::Footer-->
        @include('theme.auth.partials.auth_footer')
        <!--end::Footer-->
    </div>
    <!--end::Authentication - Sign-in-->
@endsection

@section('reset_password_js')
    <script>
        $("#password_reset_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#reset_password_submit_btn").addClass('d-none');
            $("#reset_password_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ route('password.update') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {

                    $("#reset_password_submit_btn").removeClass('d-none');
                    $("#reset_password_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        toastr.success(response.message);
                        setTimeout(function () {
                            location.href="{{ url('/') }}"
                        }, 1500);
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
                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
