<div class="modal fade" id="view_all_pay_rate_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2>Pay rates for {{ $job['name'] }}</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="view_all_pay_rate_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <div class="card">
                <!--begin::Card body-->
                <div class="card-body py-4">
                    <table class="table align-middle fs-7 gy-3" id="datatable">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="ps-5">Pay Rate</th>
                            <th>Charge Rate</th>
                            <th>Overtime Rate</th>
                            <th>Overtime After</th>
                            <th>Rate Valid From</th>
                            <th>Actions</th>
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
                                    <td>
                                        {{ $prm['default_overtime_hours_threshold'] }} Hours/{{ $overtimeTypeLabel }}
                                    </td>
                                    <td>{{ $prm['pay_rate_valid_from'] }}</td>
                                    <td>
                                        @if ($status === 'U')
                                            <a href="javascript:;"
                                               class="btn btn-icon btn-bg-light btn-active-color-info btn-sm"
                                               id="delete_upcoming_flat_pay_rate_btn"
                                               data-id="{{ $prm['id'] }}">
                                                <i class="fs-2 las la-trash"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <!--end::Card body-->
            </div>
        </div>
    </div>
</div>
@section('view_all_flat_pay_rate_js')
    <script>
        $("#view_all_pay_rate_btn").on('click', function () {
            $("#view_all_pay_rate_modal").modal('show');
        });

        $("#view_all_pay_rate_modal_close_btn").on('click', function () {
            $("#view_all_pay_rate_modal").modal('hide');
        })

        $(document).on('click', '#delete_upcoming_flat_pay_rate_btn', function () {
            let pri = $(this).attr('data-id')
            sweetAlertConfirmDelete('You want to delete this pay rate entry!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : '{{ url('delete-flat-pay-rate-action') }}'+'/'+pri,
                        success : function (response) {
                            decodeResponse(response)

                            if(response.code === 200) {
                                $("#"+pri+"_row").remove();
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

