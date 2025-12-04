<div class="tab-pane fade" id="kt_table_widget_5_tab_2">
    <div class="table-responsive">
        <form id="password_form">
            @csrf
            <input type="hidden" name="user_id"  id="user_id" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
            <div class="p-5">
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Update Password</div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Type new password</label>
                            <div class="input-group">
                                <input class="form-control" name="new_password" id="new_password" type="password" placeholder="Create new password..">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="las la-eye toggle-password" data-toggle="new_password" style="font-size: 24px;"></i></span></div>
                            </div>
                            <span class="error text-danger" id="new_password_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Retype new password</label>
                            <div class="input-group">
                                <input class="form-control" name="confirm_password" id="confirm_password" type="password" placeholder="Confirm new password..">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="las la-eye toggle-password" data-toggle="confirm_password" style="font-size: 24px;"></i></span></div>
                            </div>
                            <span class="error text-danger" id="confirm_password_error"></span>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <button type="submit" name="update_password_submit" id="update_password_submit" class="btn btn-primary btn-lg">Update password</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@section('update-user-password-tab-js')
    <script>
        $(document).ready(function () {
            $('.toggle-password').on('click', function () {
                const inputId = $(this).data('toggle');
                const input = $('#' + inputId);

                const type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);

                // Toggle icon class
                $(this).toggleClass('la-eye');
                $(this).toggleClass('la-eye-slash');
            });
        });
        $("#password_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-profile-password') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    if(response.code === 200)
                        setTimeout(function() { $('#logout-form').submit(); }, 1500);
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
