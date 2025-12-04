<div class="tab-pane fade active show" id="kt_table_widget_5_tab_1">
    <div class="table-responsive">
        <form id="preferences_form">
            @csrf
            <input type="hidden" name="user_id"  id="user_id" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
            <div class="p-5">
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Dashboard</div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Dashboard initially shows</label>
                            <select name="dashboard_tab" id="dashboard_tab" class="form-select form-select-lg">
                                <option {{ ($user['dashboard_tab'] == "alerts_and_bookings_tab") ? 'selected' : '' }} value="alerts_and_bookings_tab">Alerts & Bookings tab</option>
                                <option {{ ($user['dashboard_tab'] == "kpis_tab") ? 'selected' : '' }} value="kpis_tab">KPIs tab</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <button type="submit" name="basic_details_submit" id="basic_details_submit" class="btn btn-primary btn-lg">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@section('update-dashboard-tab-js')
    <script>
        $("#preferences_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-dashboard-tab') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    if(response.code === 200)
                        setTimeout(function() { location.reload(); }, 1500);
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
    </script>

@endsection
