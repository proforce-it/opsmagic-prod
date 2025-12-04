<div class="tab-pane fade" id="kt_table_widget_5_tab_9">
    <!--begin::Table container-->
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="w-100">
                    <div class="row">
                        @if(!$client['payroll_week_starts'])
                            <div class="alert alert-custom alert-warning" role="alert" id="payroll-warning">
                                <div class="alert-text fs-4">
                                    <i class="las la-exclamation-triangle text-warning fs-xl-2"></i>
                                    Payroll week starts on must be set before <strong>{{ $client['company_name'] }}</strong> can be made active.
                                </div>
                            </div>
                        @endif
                        @if($client['bonus_commission_percentage'] == 0)
                            <div class="alert alert-custom alert-warning" role="alert" id="bonus-warning">
                                <div class="alert-text fs-4">
                                    <i class="las la-exclamation-triangle text-warning fs-xl-2"></i>
                                    Bonus margin set to 0% – no markup being charged to client
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="mt-5 mb-5">
                        <div class="col-lg-12">
                            <form id="pay_details_form">
                                @csrf
                                <input type="hidden" name="pay_details_update_id" id="pay_details_update_id" value="{{ $client['id'] }}">
                                <div class="fv-row">
                                    <div class="row mb-7">
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="pay_roll_week_starts_on" class="fs-6 fw-bold required">Payroll week starts on</label>
                                                <select name="pay_roll_week_starts_on" id="pay_roll_week_starts_on" class="form-select form-select-lg {{ $client['payroll_week_starts'] ? 'bg-secondary' : '' }}" data-control="select2" data-placeholder="Select day" data-allow-clear="true" {{ $client['payroll_week_starts'] ? 'disabled' : '' }}>
                                                    <option value="">Select day...</option>
                                                    @foreach(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                                        <option value="{{$day}}" {{$client['payroll_week_starts'] === $day ? 'selected' : '' }}>{{ucfirst($day)}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger error" id="pay_roll_week_starts_on_error"></span>
                                                @if(!$client['payroll_week_starts'])
                                                    <div>
                                                        <strong class="text-danger">Note:</strong> <label class="text-gray-400">This field cannot be changed once set</label>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                                <label for="bonus_payment_margin" class="fs-6 fw-bold">Bonus payment margin</label>
                                                <div class="input-group">
                                                    <input type="text" name="bonus_payment_margin" id="bonus_payment_margin" class="form-control" placeholder="Enter bonus payment margin"  value="{{$client['bonus_commission_percentage'] ?? 0}}" />
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="las la-percent" style="font-size: 24px"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="text-danger error" id="bonus_payment_margin_error"></span>
                                                <div>
                                                    <small class="form-text text-muted">
                                                        This means the client is charged <strong id="clientCharge" class="text-black">£{{ number_format(100 + (100 * $client['bonus_commission_percentage'] / 100), 2) }}</strong> for a £100 worker bonus
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border-top border-4"></div>
                                        <div class="col-lg-12 text-center mt-10">
                                            <button type="submit" name="payroll_form_update_btn" id="payroll_form_update_btn" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Table-->
</div>

@section('edit_client_pay_detail_js')
    <script>
        $("#pay_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $.ajax({
                type        : 'post',
                url         : '{{ url('update-client-pay-details') }}',
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

        $('#bonus_payment_margin').on('input', function () {
            let margin = parseFloat($(this).val()) || 0;
            let charge = 100 + (100 * margin / 100);
            $('#clientCharge').text('£'+charge.toFixed(2));
        });
    </script>
@endsection
