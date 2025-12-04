@if(!$worker['email_verified_at'])
    <div class="alert alert-custom alert-danger" role="alert" style="background-color: #E02020;">
        <div class="alert-text fw-boldest text-white">
            <span class="svg-icon svg-icon-white svg-icon-2x">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M11.1669899,4.49941818 L2.82535718,19.5143571 C2.557144,19.9971408 2.7310878,20.6059441 3.21387153,20.8741573 C3.36242953,20.9566895 3.52957021,21 3.69951446,21 L21.2169432,21 C21.7692279,21 22.2169432,20.5522847 22.2169432,20 C22.2169432,19.8159952 22.1661743,19.6355579 22.070225,19.47855 L12.894429,4.4636111 C12.6064401,3.99235656 11.9909517,3.84379039 11.5196972,4.13177928 C11.3723594,4.22181902 11.2508468,4.34847583 11.1669899,4.49941818 Z" fill="#000000" opacity="0.3"/>
                        <rect fill="#000000" x="11" y="9" width="2" height="7" rx="1"/>
                        <rect fill="#000000" x="11" y="17" width="2" height="2" rx="1"/>
                    </g>
                </svg>
            </span>
            Worker has not validated email address via link and cannot be assigned to jobs.
            <button type="button" id="resend_link_btn" class="btn btn-light btn-sm float-end fw-boldest resend_link_btn" style="color: #E02020; margin-top: -6px">Resend link</button>
            <button type="button" id="resend_link_process_btn" class="btn btn-light btn-sm float-end disabled" style="color: #E02020; margin-top: -6px; display: none">
                <span>Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </div>
@endif

@section('edit_worker_email_not_verify_js')
    <script>
        $(document).on('click', '.resend_link_btn', function () {

            $("#resend_link_btn").hide();
            $("#resend_link_process_btn").show();

            $.ajax({
                type        : 'get',
                url         : '{{ url('send-mail-for-worker-email-conformation/'.$worker['id']) }}',
                success     : function (response) {
                    decodeResponse(response)

                    $("#resend_link_btn").show();
                    $("#resend_link_process_btn").hide();
                },
                error   : function (response) {
                    $("#resend_link_btn").show();
                    $("#resend_link_process_btn").hide();

                    toastr.error(response.statusText);
                }
            });
        });

        $("#header_additional_info").empty().append('({{ $worker['first_name'].' '.$worker['middle_name'].' '.$worker['last_name'] }})')
    </script>
@endsection
