<div class="table-responsive">
    <div class="p-5">
        <div class="fv-row">
            <div class="w-100">
                <div class="row mb-10">
                    <div class="col-lg-10">
                        <h1>Type : Flat rate</h1>
                        {{--@if($job['upcoming_pay_rate_details'])
                            <div class="alert alert-custom alert-warning mt-5" role="alert">
                                <div class="alert-text">
                                    This job has an <strong>upcoming</strong> rate change. <a href="javascript:;" id="view_all_pay_rate_btn">Click to view all rates </a>for this job
                                </div>
                            </div>
                        @endif--}}
                    </div>
                    <div class="col-lg-2">
                        <button type="button"
                                name="make_changes_flat_rate_btn"
                                id="make_changes_flat_rate_btn"
                                class="btn btn-primary float-end make_changes_flat_rate_btn"
                                data-modal_title="Update pay rate for {{ $job['name'] }}"
                                data-type="UpdateOrCreateUpcoming"
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
                                        <td>
                                            @if ($status === 'U')
                                                <a href="javascript:;"
                                                   class="btn btn-icon btn-bg-light btn-active-color-info btn-sm"
                                                   id="delete_upcoming_flat_pay_rate_btn"
                                                   data-id="{{ $prm['id'] }}">
                                                    <i class="fs-2 las la-trash"></i>
                                                </a>
                                                <a href="javascript:;"
                                                    id="edit_upcoming_flat_rate_btn"
                                                    class="btn btn-icon btn-bg-light btn-active-color-info btn-sm make_changes_flat_rate_btn"
                                                    data-modal_title="Edit upcoming pay rate for {{ $job['name'] }}"
                                                    data-type="UpdateUpcomingPayRate">
                                                    <i class="fs-2 las la-edit"></i>
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
<!--                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="view_base_pay_rate_per_hour" class="fs-6 fw-bold required">Base pay rate per hour</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="las la-pound-sign" style="font-size: 24px"></i>
                                    </span>
                                </div>
                                <input type="text" name="view_base_pay_rate_per_hour" id="view_base_pay_rate_per_hour" class="form-control bg-secondary" placeholder="Enter base pay rate per hour" value="{{ $job['pay_rate_details']['base_pay_rate'] }}" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="view_base_charge_rate_per_hour" class="fs-6 fw-bold required">Base charge rate per hour</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="las la-pound-sign" style="font-size: 24px"></i>
                                    </span>
                                </div>
                                <input type="text" name="view_base_charge_rate_per_hour" id="view_base_charge_rate_per_hour" class="form-control bg-secondary" placeholder="Enter base charge rate per hour" value="{{ $job['pay_rate_details']['base_charge_rate'] }}" readonly />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="view_overtime_pay_rate_per_hour" class="fs-6 fw-bold">Overtime pay rate per hour (optional)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="las la-pound-sign" style="font-size: 24px"></i>
                                    </span>
                                </div>
                                <input type="text" name="view_overtime_pay_rate_per_hour" id="view_overtime_pay_rate_per_hour" class="form-control bg-secondary" placeholder="Enter overtime pay rate per hour (optional)" value="{{ $job['pay_rate_details']['default_overtime_pay_rate'] }}" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="view_overtime_charge_rate_per_hour" class="fs-6 fw-bold">Overtime charge rate per hour (optional)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="las la-pound-sign" style="font-size: 24px"></i>
                                    </span>
                                </div>
                                <input type="text" name="view_overtime_charge_rate_per_hour" id="view_overtime_charge_rate_per_hour" class="form-control bg-secondary" placeholder="Enter overtime charge rate per hour" value="{{ $job['pay_rate_details']['default_overtime_charge_rate'] }}" readonly />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="view_overtime_paid_after" class="fs-6 fw-bold">Overtime paid after (optional)</label>
                            <div class="input-group">
                                <input type="text" name="view_overtime_paid_after" id="view_overtime_paid_after" class="form-control bg-secondary" placeholder="Overtime paid after (optional)" value="{{ $job['pay_rate_details']['default_overtime_hours_threshold'] }}" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="view_overtime_type" class="fs-6 fw-bold"></label>
                            <select name="view_overtime_type" id="view_overtime_type" class="form-select bg-secondary" disabled>
                                <option value="hours_per_week" {{ ($job['pay_rate_details']['overtime_type'] == 'hours_per_week') ? 'selected' : '' }}>Hours per week</option>
                                <option value="hours_per_day" {{ ($job['pay_rate_details']['overtime_type'] == 'hours_per_day') ? 'selected' : '' }}>Hours per day</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label for="view_pay_rate_valid_from" class="fs-6 fw-bold">pay rate valid from</label>
                            <div class="position-relative d-flex align-items-center">
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <i class="fs-2 las la-calendar"></i>
                                </span>
                                <input class="form-control ps-12 flatpickr-input date_input bg-secondary pay_rate_date" placeholder="Select pay rate date" name="view_pay_rate_valid_from" id="view_pay_rate_valid_from" type="text" value="{{ date('d-m-Y', strtotime($job['pay_rate_details']['pay_rate_valid_from'])) }}" disabled />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <hr>
                    <div class="col-lg-12 text-center">
                        <button type="button"
                                name="make_changes_flat_rate_btn"
                                id="make_changes_flat_rate_btn"
                                class="btn btn-primary"
                                {{ $job['upcoming_pay_rate_details'] ? 'disabled' : '' }}>
                            Make changes <i class="fs-2 las la-arrow-right"></i>
                        </button>
                    </div>
                </div>-->
            </div>
        </div>
    </div>
</div>
