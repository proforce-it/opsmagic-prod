@extends('theme.auth.auth_page')

@section('title', config('app.name').' - Forget Password')
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
                <form class="form w-100" id="forget_password_form" method="post">
                    {{ csrf_field() }}
                    <!--begin::Heading-->
                    <div class="text-center mb-10">
                        <!--begin::Title-->
                        <h2 class="text-dark mb-3">Forgot Password ?</h2>
                        <div class="text-gray-400 fw-bold fs-4">Enter your email to reset your password.</div>
                        <!--end::Title-->
                    </div>
                    <!--begin::Heading-->
                    <!--begin::Input group-->
                    <div class="fv-row mb-10">
                        <label for="email" class="form-label fs-6 fw-bolder text-dark">Email</label>
                        <input class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror" value="{{ old('email') }}" type="text" name="email" id="email" autocomplete="off" placeholder="Enter email"/>
                        <span class="error text-danger" id="email_error"></span>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Actions-->
                    <div class="text-center">
                        <!--begin::Submit button-->
                        <button type="submit" id="forget_password_submit_btn" class="btn btn-lg btn-primary w-100 mb-5">
                            <span class="indicator-label">Send password reset link</span>
                        </button>
                        <button type="button" class="btn btn-lg btn-primary w-100 mb-5 disabled d-none" data-kt-stepper-action="submit" id="forget_password_process_btn">
                            <span>Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>

                        <a href="{{ url('/') }}" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) scale(-1, 1) rotate(-90.000000) translate(-12.000000, -12.000000) " x="11" y="5" width="2" height="14" rx="1"/>
                                        <path d="M3.7071045,15.7071045 C3.3165802,16.0976288 2.68341522,16.0976288 2.29289093,15.7071045 C1.90236664,15.3165802 1.90236664,14.6834152 2.29289093,14.2928909 L8.29289093,8.29289093 C8.67146987,7.914312 9.28105631,7.90106637 9.67572234,8.26284357 L15.6757223,13.7628436 C16.0828413,14.136036 16.1103443,14.7686034 15.7371519,15.1757223 C15.3639594,15.5828413 14.7313921,15.6103443 14.3242731,15.2371519 L9.03007346,10.3841355 L3.7071045,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.000001, 11.999997) scale(-1, -1) rotate(90.000000) translate(-9.000001, -11.999997) "/>
                                    </g>
                                </svg>
                            </span>
                            Back to login
                        </a>
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


@section('forget_password_js')
    <script>
        $("#forget_password_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#forget_password_submit_btn").addClass('d-none');
            $("#forget_password_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ route('password.email') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {

                    $("#forget_password_submit_btn").removeClass('d-none');
                    $("#forget_password_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        toastr.success(response.message);
                        $("#email").val('');
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
