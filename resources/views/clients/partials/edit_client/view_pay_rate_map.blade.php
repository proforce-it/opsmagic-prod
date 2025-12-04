<div class="table-responsive">
    <div class="p-5">
        <div class="fv-row">
            <div class="w-100">
                <div class="row mb-10">
                    <div class="col-lg-10">
                        <h1>Pay maps for {{ $job['name'] }}</h1>
                    </div>
                    <div class="col-lg-2">
                        <button type="button"
                                name="make_changes_pay_rate_map_btn"
                                id="make_changes_pay_rate_map_btn"
                                class="btn btn-primary float-end make_changes_pay_rate_map_btn"
                                data-modal_title=""
                                data-type=""
                                {{ $job['upcoming_pay_rate_details'] ? 'disabled' : '' }}>
                            Update rate
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table align-middle fs-7 gy-3" id="datatable">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="ps-5">Pay Rate</th>
                                <th>Charge Rate</th>
                                <th>O/T Pay Rate</th>
                                <th>O/T Charge Rate</th>
                                <th>Overtime After</th>
                                <th>Rate Valid From</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            @if (!empty($job['pay_rate_multiple']))
                                @foreach ($job['pay_rate_multiple'] as $prm)
                                    @php
                                        $status = $prm['status'];
                                        $rowBgClass = $status === 'U' ? 'light-warning' : ($status === 'C' ? 'light-success' : '');
                                        $badgeClass = $status === 'U' ? 'warning' : 'success';
                                        $overtimeTypeLabel = $prm['overtime_type'] === 'hours_per_week' ? 'week' : 'day';
                                    @endphp

                                    <tr id="{{ $prm['id'] }}_row" class="bg-{{ $rowBgClass }}">
                                        <td class="ps-5">
                                            @if (in_array($status, ['U', 'C']))
                                                <span class="badge badge-{{ $badgeClass }} me-2">{{ $status }}</span>
                                            @endif
                                            {{ number_format($prm['base_pay_rate'], 2) }}
                                        </td>
                                        <td>{{ number_format($prm['base_charge_rate'], 2) }}</td>
                                        <td>{{ number_format($prm['default_overtime_pay_rate'], 2) }}</td>
                                        <td>{{ number_format($prm['default_overtime_charge_rate'],2) }}</td>
                                        <td>
                                            {{ $prm['default_overtime_hours_threshold'] }} Hours/{{ $overtimeTypeLabel }}
                                        </td>
                                        <td>{{ $prm['pay_rate_valid_from'] }}</td>
                                        <td class="text-end">
                                            @if ($status === 'U')
                                                <a href="javascript:;"
                                                   class="btn btn-icon btn-bg-light btn-active-color-info btn-sm"
                                                   id="delete_upcoming_pay_rate_map_btn"
                                                   data-id="{{ $prm['id'] }}">
                                                    <i class="fs-2 las la-trash"></i>
                                                </a>
                                                <a href="{{ url('pay-rate-map-step-2/'.$prm['id']) }}"
                                                   id="edit_upcoming_pay_rate_map_btn"
                                                   class="btn btn-icon btn-bg-light btn-active-color-info btn-sm make_changes_pay_rate_map_btn"
                                                   data-modal_title=""
                                                   data-type="">
                                                    <i class="fs-2 las la-edit"></i>
                                                </a>
                                            @else
                                                <a href="{{ url('prm-read-only-pay-map/'.$prm['id']) }}" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_pay_rate_map">
                                                    <span class="svg-icon svg-icon-2">
                                                        <i class="fs-2 las la-arrow-right"></i>
                                                    </span>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('view_pay_rate_map_js')
    <script>
        $(document).on('click', '#delete_upcoming_pay_rate_map_btn', function () {
            let prmid = $(this).attr('data-id')
            sweetAlertConfirmDelete('You want to delete this pay rate map entry!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-upcoming-pay-rate-map-action') }}'+'/'+prmid,
                        success : function (response) {
                            decodeResponse(response)

                            if(response.code === 200) {
                                $("#"+prmid+"_row").remove();
                                setTimeout(function () {
                                    location.reload();
                                }, 1500)
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });
    </script>
@endsection