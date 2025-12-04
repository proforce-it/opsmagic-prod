<div class="card">
    <div class="card-body py-4">
        <div class="table-responsive">
            <div class="p-5">
                <div class="fv-row">
                    <div class="row mb-5">
                        <div class="col-lg-5">
                            <!-- BEGIN CLIENT REQUIREMENT -->
                            <div class="border border-1 border-dark rounded">
                                <div class="row ps-5 pe-5 pt-2">
                                    <div class="col-lg-8">
                                        <label class="fs-4 fw-boldest">Client requirement ({{ $shift['number_workers'] }} workers)</label>
                                    </div>
                                    <div class="col-lg-4">
                                        <a href="javascript:;" class="float-end {{ $shift['cancelled_at'] ? 'disabled' : ''  }}"
                                           id="manage_slot_btn"
                                           data-slot="{{ $shift['number_workers'] }}">
                                            <i class="fs-xxl-1 las la-pencil-alt text-primary"></i>
                                        </a>
                                        <div class="modal fade" id="manage_slot_modal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header py-7 d-flex justify-content-between">
                                                        <h2>Edit client requirements</h2>
                                                        <div class="btn btn-sm btn-icon btn-active-color-primary" id="manage_slot_cls_modal_btn">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                                            </svg>
                                                        </span>
                                                        </div>
                                                    </div>
                                                    <form id="manage_slot_form">
                                                        @csrf
                                                        <div class="modal-body scroll-y">
                                                            <div class="fv-row">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="fv-row fv-plugins-icon-container mb-10">
                                                                            <label for="slot_number" class="fs-6 fw-bold required">Worker required</label>
                                                                            <div class="input-group">
                                                                                <input class="form-control" name="slot_number" id="slot_number" type="text" value="">
                                                                                <div class="input-group-prepend"><span class="input-group-text">Slots</span></div>
                                                                            </div>
                                                                            <span class="text-danger error" id="slot_number_error"></span>
                                                                        </div>
                                                                    </div>
                                                                    @if($jobLine)
                                                                        @foreach($jobLine as $jbFRow)
                                                                            <div class="col-lg-6">
                                                                                <div class="fv-row fv-plugins-icon-container mb-10">
                                                                                    <label for="line_requirement_number" class="fs-6 fw-bold {{ $jbFRow['color_code'] }}-text">{{ $jbFRow['line_code'] }}</label>
                                                                                    <input type="text"
                                                                                           class="form-control {{ $jbFRow['color_code'] }}-border text-dark"
                                                                                           name="line_requirement_number[{{ $jbFRow['id'] }}]"
                                                                                           id="line_requirement_number_{{ $jbFRow['id'] }}"
                                                                                           value="{{ ($jbFRow['job_line_client_requirements_details']) ? $jbFRow['job_line_client_requirements_details'][0]['worker_requirement'] : 0 }}">
                                                                                    <span class="text-danger error" id="line_requirement_number_{{ $jbFRow['id'] }}_error"></span>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer" style="justify-content: center !important;">
                                                            <div class="fv-row">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <input type="hidden" name="manege_slot_job_shift_id" id="manege_slot_job_shift_id" value="{{ $shift['id'] }}">
                                                                        <button type="submit" name="manage_slot_submit_btn" id="manage_slot_submit_btn" class="btn btn-primary float-end">Update</button>
                                                                        <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" name="manage_slot_process_btn" id="manage_slot_process_btn" style="display: none">
                                                                            <span>Please wait...
                                                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ps-5 pe-5 pt-2">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <div class="d-flex align-self-center">
                                            @if($jobLine)
                                                @foreach($jobLine as $jbRow)
                                                    <span class="p-1 rounded-1 fw-bold fs-4 me-2 {{ $jbRow['color_code'] }}-border mb-2">
                                                        <span class="text-dark me-2">{{ ($jbRow['job_line_client_requirements_details']) ? $jbRow['job_line_client_requirements_details'][0]['worker_requirement'] : 0 }}</span>
                                                        {{ $jbRow['line_code'] }}
                                                    </span> &nbsp;
                                                @endforeach
                                            @endif
                                            <span class="p-1 rounded-1 fw-bold fs-4 me-2 gray-border mb-2">
                                                <span class="text-dark me-2">
                                                    {{ $shift['number_workers'] - collect($jobLine)->pluck('job_line_client_requirements_details')->flatten(1)->sum(function ($detail) { return (int) $detail['worker_requirement']; }) }}
                                                </span>
                                                NO LINE
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END CLIENT REQUIREMENT -->

                            <!-- BEGIN CONFIRMED WORKER -->
                            <div class="border border-1 border-dark rounded mt-5">
                                <div class="row ps-5 pe-5 pt-2">
                                    <div class="col-lg-8">
                                        <label class="fs-4 fw-boldest">Confirmed workers</label>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="fs-4 fw-boldest float-end {{ (count($confirm_worker) > $shift['number_workers']) ? 'text-danger' : '' }} ">
                                            {{ count($confirm_worker) }}/{{ $shift['number_workers'] }}
                                            <i class="las la-angle-right fw-boldest fs-3 text-dark cursor-pointer" id="cwar"></i>
                                            <i class="las la-angle-down fw-boldest fs-3 text-dark cursor-pointer d-none" id="cwad"></i>
                                        </label>
                                    </div>

                                    <div class="d-flex align-self-center d-none" id="cwjc">
                                        @if($jobLine)
                                            @foreach($jobLine as $jbRow)
                                                <span class="p-1 rounded-1 fw-bold fs-4 me-2 {{ $jbRow['color_code'] }}-border mb-2">
                                                    <span class="text-dark me-2">{{ collect($confirm_worker)->where('job_line_id', $jbRow['id'])->count() }}</span>
                                                    {{ $jbRow['line_code'] }}
                                                </span> &nbsp;
                                            @endforeach
                                        @endif
                                        <span class="p-1 rounded-1 fw-bold fs-4 me-2 gray-border mb-2">
                                            <span class="text-dark me-2">{{ collect($confirm_worker)->where('job_line_id', null)->count() }}</span>
                                            NO LINE
                                        </span>
                                    </div>
                                </div>

                                <div class="ps-5 pe-5 pt-2">
                                    <div class="fv-row fv-plugins-icon-container bg-gray-200 border border-gray-600 border-dashed rounded p-5">
                                        <div class="d-flex align-self-center">
                                            <div class="flex-grow-1 me-3">
                                                <select name="with_selected" id="with_selected" class="form-select" data-control="select2" data-placeholder="With selected..." data-hide-search="true">
                                                    <option value="">With selected...</option>
                                                    <option value="Unassigned from shift">Unassigned from shift</option>
                                                    <option value="Cancel workers and send notification">Cancel workers and send notification</option>
                                                    <optgroup label="LINES" class="bg-secondary">
                                                        <option value="Unassign from line">Unassign from line(s)</option>
                                                        @if($jobLine)
                                                            @foreach($jobLine as $lineRow)
                                                                <option value="assign_as_{{ $lineRow['id'] }}">Assign as {{ $lineRow['line_name'] }} - {{ $lineRow['line_code'] }}</option>
                                                            @endforeach
                                                        @endif
                                                    </optgroup>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon flex-shrink-0  {{ $shift['cancelled_at'] ? 'disabled' : ''  }}" id="with_selected_btn">Go</button>
                                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="with_selected_process_btn" id="with_selected_process_btn" style="display: none">
                                                <span>Please wait...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                        <span class="text-danger error" id="with_selected_error"></span>
                                    </div>
                                </div>

                                <div class="fv-row fv-plugins-icon-container p-5">
                                    <div class="row">
                                        @if($confirm_worker)
                                            @foreach($confirm_worker as $cow)
                                                <div class="col-lg-12 mb-3">
                                                    <div class="fv-row fv-plugins-icon-container border border-success rounded p-2">
                                                        <div class="row">
                                                            <div class="col-lg-1">
                                                                <div class="form-check form-check-sm form-check-custom">
                                                                    <input name="selected_worker" id="selected_worker_{{ $cow['worker_id'] }}" class="form-check-input widget-9-check" type="checkbox" value="{{ $cow['worker_id'] }}" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-11">
                                                                @if($cow['worker'])
                                                                    <div class="d-flex align-items-center">
                                                                        <a href="{{ url('view-worker-details/'.$cow['worker_id']) }}" class="text-dark fw-bolder text-hover-primary d-block fs-6 me-2" target="_blank">{{ $cow['worker']['first_name'].' '.$cow['worker']['middle_name'].' '.$cow['worker']['last_name'] }} </a>
                                                                        <span class="fs-7 text-muted fw-bold"><span class="fw-boldest text-muted">b.</span>{{\Illuminate\Support\Carbon::parse($cow['worker']['date_of_birth'])->format('d/m/y')}}</span>
                                                                    </div>
                                                                @endif
                                                                <span class="text-muted fw-bold d-flex fs-7 align-items-center">
                                                                    <span class="badge badge-success">C</span>

                                                                @if($cow['job_line_details'])
                                                                        <span class="p-1 rounded-1 fw-bold {{ $cow['job_line_details']['color_code'] }}-border ms-2">{{ $cow['job_line_details']['line_code'] }}</span>
                                                                    @endif
                                                                    <i class="las la-clock fs-5 me-1 ms-2"></i> {{ date('H:i', strtotime($cow['start_time'] ?? $shift['start_time'])) }}
{{--                                                                    <i class="las la-user-circle fs-1"></i>--}}
{{--                                                                    @php($cow_latestRTWExpiryDate = \App\Helper\Workers\RightToWorkHelper::getLatestDate($cow['rights_to_work']))--}}
{{--                                                                    {{ ($cow_latestRTWExpiryDate) ? date('d-m-Y', strtotime($cow_latestRTWExpiryDate)) : '-' }} &nbsp;--}}
                                                                    <i class="las la-hourglass-half fs-5 me-1 ms-2"></i>{{ $cow['duration'] ?? $shift['shift_length_hr'].'.'.$shift['shift_length_min'] }}hrs
                                                                    <i class="las la-calendar-check fs-5 me-1 ms-2"></i> {{ $cow['confirmed_shifts_count'] }}
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- END CONFIRMED WORKER -->

                            <!-- BEGIN INVITED WORKER -->
                            <div class="border border-1 border-dark rounded mt-5">
                                <div class="row ps-5 pe-5 pt-2">
                                    <div class="col-lg-8">
                                        <label class="fs-4 fw-boldest">Invited workers (unconfirmed)</label>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="fs-4 fw-boldest float-end">{{ count($pending_worker) }}</label>
                                    </div>
                                </div>

                                <div class="ps-5 pe-5 pt-2">
                                    <div class="fv-row fv-plugins-icon-container bg-gray-200 border border-gray-600 border-dashed rounded p-5">
                                        <div class="d-flex align-self-center">
                                            <div class="flex-grow-1 me-3">
                                                <select name="with_selected_invited_worker" id="with_selected_invited_worker" class="form-select" data-control="select2" data-placeholder="With selected..." data-hide-search="true">
                                                    <option value="">With selected...</option>
                                                    <option value="Confirm invitation">Confirm invitation(s)</option>
                                                    <option value="Cancel invitation">Cancel invitation(s)</option>
                                                    <optgroup label="LINES" class="bg-secondary">
                                                        <option value="Unassign from line">Unassign from line(s)</option>
                                                        @if($jobLine)
                                                            @foreach($jobLine as $lineRow)
                                                                <option value="assign_as_{{ $lineRow['id'] }}">Assign as {{ $lineRow['line_name'] }} - {{ $lineRow['line_code'] }}</option>
                                                            @endforeach
                                                        @endif
                                                    </optgroup>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon flex-shrink-0  {{ $shift['cancelled_at'] ? 'disabled' : ''  }}" id="with_selected_invited_worker_btn">Go</button>
                                            <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="with_selected_invited_worker_process_btn" id="with_selected_invited_worker_process_btn" style="display: none">
                                                <span>Please wait...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                        <span class="text-danger error" id="with_selected_error"></span>
                                    </div>
                                </div>

                                <div class="fv-row fv-plugins-icon-container p-5">
                                    <div class="row">
                                        @if($pending_worker)
                                            @foreach($pending_worker as $pw)
                                                <div class="col-lg-12 mb-3">
                                                    <div class="fv-row fv-plugins-icon-container border border-warning rounded p-2">
                                                        <div class="row">
                                                            <div class="col-lg-1">
                                                                <div class="form-check form-check-sm form-check-custom">
                                                                    <input name="selected_invited_worker" id="selected_invited_worker_{{ $pw['worker_id'] }}" class="form-check-input widget-9-check" type="checkbox" value="{{ $pw['worker_id'] }}" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-11">
                                                                @if($pw['worker'])
                                                                    <div class="d-flex align-items-center">
                                                                    <a href="{{ url('view-worker-details/'.$pw['worker_id']) }}" class="text-dark fw-bolder text-hover-primary d-block fs-6 me-2" target="_blank">{{ $pw['worker']['first_name'].' '.$pw['worker']['middle_name'].' '.$pw['worker']['last_name'] }}</a>
                                                                        <span class="fs-7 text-muted fw-bold"><span class="fw-boldest text-muted">b.</span>{{\Illuminate\Support\Carbon::parse($pw['worker']['date_of_birth'])->format('d/m/y')}}</span>
                                                                    </div>
                                                                @endif
                                                                <span class="text-muted fw-bold d-flex fs-7 align-items-center">
                                                                    <span class="badge badge-warning">I</span>
                                                                    @if($pw['job_line_details'])
                                                                        <span class="p-1 rounded-1 fw-bold {{ $pw['job_line_details']['color_code'] }}-border ms-2">{{ $pw['job_line_details']['line_code'] }}</span>
                                                                    @endif
                                                                    <i class="las la-clock fs-5 me-1 ms-2"></i> {{ date('H:i', strtotime($pw['start_time'] ?? $shift['start_time'])) }}

                                                                    {{--                                                                 <i class="las la-user-circle fs-1"></i>--}}
{{--                                                                @php($pw_latestRTWExpiryDate = \App\Helper\Workers\RightToWorkHelper::getLatestDate($pw['rights_to_work']))--}}
{{--                                                                    {{ ($pw_latestRTWExpiryDate) ? date('d-m-Y', strtotime($pw_latestRTWExpiryDate)) : '-' }}&nbsp;--}}
                                                                    <i class="las la-hourglass-half fs-5 me-1 ms-2"></i>{{ $pw['duration'] ?? $shift['shift_length_hr'].'.'.$shift['shift_length_min'] }}hrs

                                                                <i class="las la-calendar-check fs-5 me-1 ms-2"></i> {{ $pw['confirmed_shifts_count'] }}
                                                            </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- END INVITED WORKER -->

                            <!-- BEGIN DECLINED AND CANCELLED WORKER -->
                            <div class="row mt-5">
                                <div class="col-lg-12">
                                    <a href="javascript:;" class="fs-3 text-primary" id="dec_and_can_worker_modal_btn">
                                        <strong>
                                            <span class="fw-boldest">View {{ count($declined_worker) + count($cancelled_worker) }}</span>
                                            declined and cancelled
                                        </strong> workers
                                        <i class="las la-arrow-right text-primary fs-3"></i>
                                    </a>
                                    <div class="modal fade" id="dec_and_can_worker_modal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered mw-1000px modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header py-7 d-flex justify-content-between">
                                                    <h2>Declined and cancelled workers</h2>
                                                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="dec_and_can_worker_cls_modal_btn">
                                                        <i class="fs-2 las la-times"></i>
                                                    </div>
                                                </div>

                                                <div class="modal-body scroll-y">
                                                    <div class="fv-row">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="alert alert-custom alert-warning" role="alert">
                                                                    <div class="alert-text fs-4">
                                                                        <i class="las la-exclamation-triangle text-warning fs-xl-2"></i>
                                                                        <strong>Please note:</strong> undeclining or uncancelling will <strong>not</strong> send a notification to the worker. It is your responsibility to ensure they are aware of any status change.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="contact_datatable">
                                                                    <thead>
                                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                        <th style="width: 30%">Name</th>
                                                                        <th style="width: 30%">Status</th>
                                                                        <th style="width: 30%">By</th>
                                                                        <th style="width: 10%" class="text-end">Action</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody class="text-gray-600 fw-bold">
                                                                        @if($declined_worker)
                                                                            @foreach($declined_worker as $dw)
                                                                                <tr id="dw_{{ $dw['id'] }}">
                                                                                    <td>{{ $dw['worker']['first_name'].' '.$dw['worker']['middle_name'].' '.$dw['worker']['last_name'] }}</td>
                                                                                    <td>Declined at {{ date('d/m/Y - H:i', strtotime($dw['declined_at'])) }}</td>
                                                                                    <td></td>
                                                                                    <td class="text-end">
                                                                                        <a href="javascript:;"
                                                                                           class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1"
                                                                                           data-id="{{ $dw['id'] }}"
                                                                                           data-status="declined"
                                                                                           id="undo_declined_and_cancelled_worker">
                                                                                            <i class="fs-2 las la-undo"></i>
                                                                                        </a>
                                                                                        <a href="{{ url('view-worker-details/'.$dw['worker']['id']) }}"
                                                                                           class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                                                                                            <i class="fs-2 las la-arrow-right"></i>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @endif
                                                                        @if($cancelled_worker)
                                                                            @foreach($cancelled_worker as $cw)
                                                                                <tr id="cw_{{ $cw['id'] }}">
                                                                                    <td>{{ $cw['worker']['first_name'].' '.$cw['worker']['middle_name'].' '.$cw['worker']['last_name'] }}</td>
                                                                                    <td>Cancelled at {{ date('d/m/Y - H:i', strtotime($cw['created_at'])) }}</td>
                                                                                    <td>{{ \App\Helper\Job\JobHelper::getCancelledByNameToJobShiftWorker($cw['cancelled_by'], $cw['cancelled_by_user_id']) }}</td>
                                                                                    <td>
                                                                                        <a href="javascript:;"
                                                                                           class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1"
                                                                                           data-id="{{ $cw['id'] }}"
                                                                                           data-status="cancelled"
                                                                                           id="undo_declined_and_cancelled_worker">
                                                                                            <i class="fs-2 las la-undo"></i>
                                                                                        </a>
                                                                                        <a href="{{ url('view-worker-details/'.$cw['worker']['id']) }}" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                                                                                            <i class="fs-2 las la-arrow-right"></i>
                                                                                        </a>
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
                                    </div>
                                </div>
                            </div>
                            <!-- END DECLINED AND CANCELLED WORKER -->
                        </div>

                        <div class="col-lg-7">
                            <!-- BEGIN AVAILABLE WORKER -->
                            <div class="border border-1 border-dark rounded">
                                <div class="row ps-5 pe-5 pt-2">
                                    <div class="col-lg-8">
                                        <label class="fs-4 fw-boldest">Linked and available workers</label>
                                    </div>
                                    <div class="col-lg-4">
                                        <a href="javascript:;" id="add_new_client_job_worker" class="float-end me-2  {{ $shift['cancelled_at'] ? 'disabled' : ''  }}">
                                            <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                        </a>

                                        <div class="modal fade" id="add_new_client_job_worker_modal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered mw-1000px">
                                                <div class="modal-content">
                                                    <div class="modal-header py-7 d-flex justify-content-between">
                                                        <h2>Add a worker to the job worker pool</h2>
                                                        <div class="btn btn-sm btn-icon btn-active-color-primary" id="cls_btn_new_client_job_worker_modal">
                                                            <span class="svg-icon svg-icon-1">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <form id="client_job_worker_details_form">
                                                        @csrf
                                                        <div class="modal-body scroll-y m-5">
                                                            <div class="fv-row">
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <div class="fv-row fv-plugins-icon-container">
                                                                            <label for="associated_cost_center" class="fs-6 fw-bold">Cost centre (optional)</label> <!--required-->
                                                                            <select name="associated_cost_center[]" id="associated_cost_center" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Select associated cost center" data-allow-clear="true" multiple>
                                                                                @if($costCentre)
                                                                                    @foreach($costCentre as $cc_row)
                                                                                        <option value="{{ $cc_row['id'] }}">{{ $cc_row['short_code'] }}</option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                            <span class="text-danger error" id="associated_cost_center_error"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="fv-row fv-plugins-icon-container">
                                                                            <label for="job_worker_name" class="fs-6 fw-bold required">Select worker</label>
                                                                            <select name="job_worker_name[]" id="job_worker_name" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Select worker" data-allow-clear="true" multiple>
                                                                            </select>
                                                                            <span class="text-danger error" id="job_worker_name_error"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="fv-row fv-plugins-icon-container">
                                                                            <label for="invitation_type" class="fs-6 fw-bold"></label>
                                                                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-1 row-cols-xl-2 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                                                                <div class="col">
                                                                                    <label class="d-flex text-start" data-kt-button="true">
                                                                                        <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                                            <input class="form-check-input" type="radio" name="invitation_type" id="invitation_type_1" value="1" checked="checked">
                                                                                        </span>
                                                                                        <span class="ms-5">
                                                                                            <span class="fs-4 fw-bolder text-gray-800 d-block">Send invitation</span>
                                                                                        </span>
                                                                                    </label>
                                                                                </div>

                                                                                <div class="col">
                                                                                    <label class="d-flex text-start" data-kt-button="true">
                                                                                        <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                                            <input class="form-check-input" type="radio" name="invitation_type" id="invitation_type_2" value="2">
                                                                                        </span>
                                                                                        <span class="ms-5">
                                                                                            <span class="fs-4 fw-bolder text-gray-800 d-block">Add directly</span>
                                                                                        </span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-10">
                                                                    <div class="col-lg-12">
                                                                        <span class="fs-4 fw-lighter"><span class="fw-bold">Please note:</span> This worker will only appear on the shift management page when they have confirmed their acceptance of the job (or if they are placed directly).</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <div class="fv-row">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <input type="hidden" name="job_worker_id" id="job_worker_id" value="{{ $shift['job_id'] }}">
                                                                        <button type="submit" name="client_job_worker_form_submit" id="client_job_worker_form_submit" class="btn btn-primary float-end">Add worker</button>
                                                                        <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" data-kt-stepper-action="submit" name="client_job_worker_form_process" id="client_job_worker_form_process" style="display: none">
                                                                            <span>Please wait...
                                                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ps-5 pe-5 pt-2">
                                    <div class="fv-row fv-plugins-icon-container bg-gray-200 border border-dark border-dashed rounded p-5">
                                        <div class="d-flex align-items-end flex-wrap">
                                            <div class="flex-grow-1 me-3 w-100px">
                                                <label for="assign_selected_workers_via" class="fs-6 bold">With selected</label>
                                                <select name="assign_selected_workers_via" id="assign_selected_workers_via" class="form-select" data-control="select2" data-placeholder="Choose..." data-hide-search="true">
                                                    <option value="Direct placement">Add to shift</option>
                                                    <option value="Invitation">Invite to shift</option>
                                                </select>
                                            </div>
                                            @if($jobLine)
                                                <div class="flex-grow-1 me-3 w-100px">
                                                    <label for="assign_selected_workers_job_line" class="fs-6 bold">Line</label>
                                                    <select name="assign_selected_workers_job_line" id="assign_selected_workers_job_line" class="form-select" data-control="select2" data-placeholder="Choose..." data-hide-search="true">
                                                        <option value="0">No line</option>
                                                            @foreach($jobLine as $lineRow)
                                                                <option value="{{ $lineRow['id'] }}">{{ $lineRow['line_name'] }} - {{ $lineRow['line_code'] }}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <input type="hidden" name="assign_selected_workers_job_line" id="assign_selected_workers_job_line" value="0">
                                            @endif
                                            <a href="javascript:;" id="hide_and_show_job_shift_worker_start_time_and_duration_btn">
                                                <i class="las la-ellipsis-v text-primary fs-xxl-2qx"></i>
                                            </a>
                                        </div>
                                        <div class="d-none" id="hide_and_show_job_shift_worker_start_time_and_duration_section">
                                            <div class="border border-top-1 border-gray-400 mt-4 mb-3"></div>
                                            <div class="d-flex align-items-end flex-wrap">
                                                <div class="flex-grow-1 me-3 w-100px">
                                                    <label for="assign_selected_workers_start_time" class="fs-6 bold">Start time</label>
                                                    <select name="assign_selected_workers_start_time" id="assign_selected_workers_start_time" class="form-select" data-control="select2" data-placeholder="Choose..." data-allow-clear="true">
                                                        @foreach ($startTimesDrp as $s_time)
                                                            <option value="{{ $s_time }}" {{ (date('H:i', strtotime($shift['start_time'])) == $s_time) ? 'selected' : '' }}>
                                                                {{ $s_time }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex-grow-1 me-3 w-100px">
                                                    <label for="shift_worker_duration" class="fs-6 bold">Duration</label>
                                                    <select name="shift_worker_duration" id="shift_worker_duration" class="form-select" data-control="select2" data-placeholder="Choose..." data-allow-clear="true">
                                                        <option value="">Choose...</option>
                                                        @for ($hour = 1; $hour <= 12; $hour++)
                                                            @foreach (['00', '15', '30', '45'] as $minute)
                                                                @php($value = $hour . ($minute != '00' ? '.' . $minute : '.00'))
                                                                <option value="{{ $value }}" {{ $value == $currentDuration ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-self-center mt-5">
                                            <div class="form-check form-check-sm form-check-custom">
                                                <input name="select_all_available_worker" id="select_all_available_worker" class="form-check-input widget-9-check" type="checkbox" value="select_all_available_worker" />
                                                <label for="select_all_available_worker" class="fs-6 fw-bold ms-2 text-primary">Select All</label>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon flex-shrink-0 ms-auto {{ $shift['cancelled_at'] ? 'disabled' : ''  }}" id="assign_btn">Go</button>
                                            <button type="button" class="btn btn-lg btn-primary ms-auto disabled" data-kt-stepper-action="submit" name="assign_process_btn" id="assign_process_btn" style="display: none">
                                                <span>Please wait...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                        <span class="text-danger error" id="assign_selected_workers_via_error"></span>
                                    </div>
                                </div>

                                <div class="ps-5 pe-5 pt-2">
                                    <div class="fv-row border border-dark border-dashed rounded p-5">
                                        <div class="row mb-2">
                                            <div class="col-lg-10">
                                                <label class="fs-4 fw-boldest">Groups</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <a href="javascript:;" id="link_group_modal_btn" class="float-end me-2">
                                                    <i class="fs-xxl-2qx las la-plus-circle text-primary"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end flex-wrap">
                                            <div class="flex-grow-1 me-3 w-100px">
                                                <div class="fv-row fv-plugins-icon-container">
                                                    <div class="row">
                                                        @if($assignedGroups)
                                                            @foreach($assignedGroups as $avg)
                                                                <div class="col-lg-6 mb-3">
                                                                    <div class="fv-row fv-plugins-icon-container border border-1 border-dark rounded p-2">
                                                                        <div class="row">
                                                                            <div class="col-lg-1">
                                                                                <div class="form-check form-check-sm form-check-custom">
                                                                                    <input name="available_groups"
                                                                                           id="available_groups_{{ $avg['group_id'] }}"
                                                                                           class="form-check-input widget-9-check group-checkbox {{ ($avg['groups']['available_workers_count'] == 0) ? 'bg-secondary' : '' }}"
                                                                                           type="checkbox"
                                                                                           data-group-workers="{{ implode(',', $avg['groups']['workers']->pluck('id')->toArray()) }}"
                                                                                           value="{{$avg['id']}}"
`                                                                                            {{ ($avg['groups']['available_workers_count'] == 0) ? 'disabled' : '' }}>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-11">
                                                                                @if($avg['groups'])
                                                                                    <a href="javascript:;" class="text-dark fw-bolder text-dark text-hover-primary d-block fs-6">
                                                                                    <span class="fw-bolder d-flex fs-6 align-items-center">
                                                                                         <i class="fs-2 las la-users text-dark me-1"></i>
                                                                                        {{$avg['groups']['name']}}
                                                                                    </span>
                                                                                    </a>
                                                                                @endif
                                                                                <span class="text-muted fw-bold text-muted d-flex fs-7 align-items-center">
                                                                                    {{$avg['groups']['available_workers_count']}}/{{$avg['groups']['workers_count']}} Available | {{$avg['groups']['confirm_workers_count']}} Conf. | {{$avg['groups']['other_job_workers_count']}} Oth. Job
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="fv-row fv-plugins-icon-container p-5">
                                    <div class="row">
                                        @if($available_worker)
                                            @foreach($available_worker as $avw)
                                                <div class="col-lg-6 mb-3">
                                                    <div class="fv-row fv-plugins-icon-container border border-1 border-dark rounded p-2">
                                                        <div class="row">
                                                            <div class="col-lg-1">
                                                                <div class="form-check form-check-sm form-check-custom">
                                                                    <input name="available_worker" id="available_worker_{{ $avw['worker_id'] }}" class="form-check-input widget-9-check worker-checkbox" type="checkbox" value="{{ $avw['worker_id'] }}" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-11">
                                                                @if($avw['worker'])

                                                                    <div class="d-flex align-items-center">
                                                                        <a href="{{ url('view-worker-details/'.$avw['worker_id']) }}" class="text-dark fw-bolder text-hover-primary d-block fs-6 me-2" target="_blank">
                                                                            {{ $avw['worker']['first_name'].' '.$avw['worker']['middle_name'].' '.$avw['worker']['last_name'] }}
                                                                            @if(\App\Models\Job\JobShiftWorker::query()->where('shift_date', $shift['date'])->where('worker_id', $avw['worker']['id'])->whereNotNull('confirmed_at')->whereNull('declined_at')->whereNull('cancelled_at')->first())
                                                                                <i class="fs-2 las la-exclamation-triangle text-warning"></i>
                                                                            @endif
                                                                        </a>
                                                                        <span class="fs-7 text-muted fw-bold"><span class="fw-boldest text-muted">b.</span>{{\Illuminate\Support\Carbon::parse($avw['worker']['date_of_birth'])->format('d/m/y')}}</span>
                                                                    </div>
                                                                @endif
                                                                <span class="text-muted fw-bold text-muted d-flex fs-7 align-items-center">
                                                                    @php($otherJobShiftWorker = \App\Models\Job\JobShiftWorker::query()->whereNot('id', $shift['id'])->whereNot('start_time', $shift['start_time'])->where('shift_date', $shift['date'])->where('worker_id', $avw['worker']['id'])->whereNotNull('confirmed_at')->whereNull('declined_at')->whereNull('cancelled_at')->with('jobShift')->first())
                                                                    @if($otherJobShiftWorker)
                                                                        @php($tooltipText = $otherJobShiftWorker['jobShift']['client_job_details']['name'].' | '.$otherJobShiftWorker['jobShift']['client_job_details']['site_details']['site_name'].' - '.$otherJobShiftWorker['jobShift']['client_job_details']['client_details']['company_name'].' | Start '.date('H:i', strtotime($otherJobShiftWorker['jobShift']['start_time'])).' | Duration '.$otherJobShiftWorker['jobShift']['shift_length_hr'].'h'.$otherJobShiftWorker['jobShift']['shift_length_min'].'m')
                                                                        <span class="badge badge-warning me-1 cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-title="{{ htmlspecialchars($tooltipText, ENT_QUOTES) }}">Other Job</span>
                                                                    @endif
{{--                                                                     <i class="las la-user-circle fs-1"></i>--}}
{{--                                                                    @php($latestRTWExpiryDate = \App\Helper\Workers\RightToWorkHelper::getLatestDate($avw['rights_to_work']))--}}
{{--                                                                    {{ ($latestRTWExpiryDate) ? date('d-m-Y', strtotime($latestRTWExpiryDate)) : '-' }}&nbsp;--}}
                                                                    <i class="las la-calendar-check fs-5 me-1 ms-2"></i> {{ $avw['confirmed_shifts_count'] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- END AVAILABLE WORKER -->

                            <div class="row mt-5">
                                <!-- BEGIN UNAVAILABLE WORKER -->
                                <div class="col-lg-12">
                                    <a href="javascript:;" class="fs-3 text-primary" id="ineligible_worker_modal_btn">
                                        <span class="fw-boldest">View {{count($ineligibleWorker)}} unavailable</span> linked workers <i class="fs-1 las la-arrow-right text-primary"></i>
                                    </a>
                                    <div class="modal fade" id="ineligible_worker_modal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered mw-1000px modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header py-7 d-flex justify-content-between">
                                                    <h2>Ineligible workers</h2>
                                                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="ineligible_worker_cls_modal_btn">
                                                        <i class="fs-2 las la-times"></i>
                                                    </div>
                                                </div>

                                                <div class="modal-body scroll-y">
                                                    <div class="fv-row">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="alert alert-custom alert-warning" role="alert">
                                                                    <div class="alert-text fs-4">
                                                                        <i class="las la-exclamation-triangle text-warning fs-xl-2"></i>
                                                                        <strong>Please note:</strong> These workers have been successfully assigned to the current job but are ineligible to work today for the reason given below
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="contact_datatable">
                                                                    <thead>
                                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                        <th>Name</th>
                                                                        <th style="width: 25%">Reason</th>
                                                                        <th class="text-end" style="width: 10%">Action</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody class="text-gray-600 fw-bold">
                                                                        @if($ineligibleWorker)
                                                                            @foreach($ineligibleWorker as $i_row)
                                                                                <tr>
                                                                                    <td>{{ $i_row['worker']['first_name'].' '.$i_row['worker']['middle_name'].' '.$i_row['worker']['last_name'] }}</td>
                                                                                    <td>
                                                                                        @if($i_row['worker']['suspend'] == 'Yes')
                                                                                            suspended
                                                                                        @elseif($i_row['archived_at'])
                                                                                            Archived
                                                                                        @elseif( strtotime(\App\Helper\Workers\RightToWorkHelper::getLatestDate($i_row['rights_to_work'])) <= strtotime($shift['date']))
                                                                                            Expired RTW
                                                                                        @elseif( strtotime(\App\Helper\Workers\RightToWorkHelper::getLatestStartDate($i_row['rights_to_work'])) >= strtotime($shift['date']))
                                                                                            RTW does not Start
                                                                                        {{--@elseif() //count($i_row['worker']['job_shift_worker']) > 0
                                                                                            <strong>Another job shift has been assigned.</strong>--}}
                                                                                        @else
                                                                                            @foreach ($i_row['absence'] as $absence)
                                                                                                @if($shift['date'] >= $absence['start_date'] && $shift['date'] <= $absence['end_date'])
                                                                                                    Absence ({{ $absence['absence_type'] }})
                                                                                                    @break($i_row)
                                                                                                @endif
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-end">
                                                                                        <a href="{{ url('view-worker-details/'.$i_row['worker']['id']) }}"
                                                                                           class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                                                                                            <i class="fs-2 las la-arrow-right"></i>
                                                                                        </a>
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
                                    </div>
                                </div>
                                <!-- END UNAVAILABLE WORKER -->

                                <!-- BEGIN AVAILABLE WORKER LINKED TO CLIENT WORKER -->
                                <div class="col-lg-12">
                                    <a href="javascript:;" class="fs-3 text-primary" id="available_worker_linked_to_client_modal_btn">
                                        <span class="fw-boldest">View {{ count($available_workers_linked_to_client) }} available workers linked to client</span> (but not to job)
                                        <i class="las la-arrow-right text-primary fs-3"></i>
                                    </a>
                                    <div class="modal fade" id="available_workers_linked_to_client_modal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered mw-1000px modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header py-7 d-flex justify-content-between">
                                                    <h2>Available workers who have worked at {{ $shift['client_job_details']['client_details']['company_name'] }} (but not as {{ $shift['client_job_details']['name'] }})</h2>
                                                    <div class="btn btn-sm btn-icon btn-active-color-primary" id="available_worker_linked_to_client_modal_cls_btn">
                                                        <i class="fs-2 las la-times"></i>
                                                    </div>
                                                </div>
                                                <div class="modal-body scroll-y">
                                                    <div class="fv-row">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="contact_datatable">
                                                                    <thead>
                                                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                        <th class="text-center">
                                                                            <div class="form-check form-check-sm form-check-custom">
                                                                                <input name="select_all_available_worker_linked_to_client" id="select_all_available_worker_linked_to_client" class="form-check-input widget-9-check" type="checkbox" value="2">
                                                                            </div>
                                                                        </th>
                                                                        <th>ID</th>
                                                                        <th>Name</th>
                                                                        <th style="width: 25%">DOB</th>
                                                                        <th style="width: 25%">RTW EXPIRES</th>
                                                                        <th class="text-end" style="width: 10%">Action</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody class="text-gray-600 fw-bold">
                                                                        @if($available_workers_linked_to_client)
                                                                            @foreach($available_workers_linked_to_client as $awltc_row)
                                                                                <tr>
                                                                                    <td class="text-center">
                                                                                        <div class="form-check form-check-sm form-check-custom">
                                                                                            <input name="available_worker_linked_to_client"
                                                                                                   id="available_worker_linked_to_client_{{ $awltc_row['worker_id'] }}"
                                                                                                   class="form-check-input widget-9-check"
                                                                                                   type="checkbox"
                                                                                                   value="{{ $awltc_row['worker_id'] }}">
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>{{ $awltc_row['worker']['worker_no'] }}</td>
                                                                                    <td>{{ $awltc_row['worker']['first_name'].' '.$awltc_row['worker']['middle_name'].' '.$awltc_row['worker']['last_name'] }}</td>
                                                                                    <td>{{ date('d-m-Y', strtotime($awltc_row['worker']['date_of_birth'])) }}</td>
                                                                                    <td>
                                                                                        @php($latestAwltcRTWExpiryDate = \App\Helper\Workers\RightToWorkHelper::getLatestDate($awltc_row['rights_to_work']))
                                                                                        {{ ($latestAwltcRTWExpiryDate) ? date('d-m-Y', strtotime($latestAwltcRTWExpiryDate)) : '-' }}&nbsp;
                                                                                    </td>
                                                                                    <td class="text-end">
                                                                                        <a href="{{ url('view-worker-details/'.$awltc_row['worker']['id']) }}"
                                                                                           class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                                                                                            <i class="fs-2 las la-arrow-right"></i>
                                                                                        </a>
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
                                                <div class="modal-footer" style="justify-content: center !important;">
                                                    <div class="fv-row">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <button type="button" name="available_worker_linked_to_client_job_btn" id="available_worker_linked_to_client_job_btn" class="btn btn-primary float-end">Add selected worker(s) to job</button>
                                                                <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled" name="available_worker_linked_to_client_process_btn" id="available_worker_linked_to_client_process_btn" style="display: none">
                                                                    <span>Please wait...
                                                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END AVAILABLE WORKER LINKED TO CLIENT WORKER -->
                            </div>
                        </div>
                    </div>

                    @if(!$shift['cancelled_at'])
                        <div class="row border-top-dashed border-1 border-danger mt-15">
                            <div class="col-lg-12 text-center mt-5">
                                <a href="javascript:;" id="delete_shift" class="btn btn-outline btn-outline-danger text-hover-white btn-lg"><i class="fs-xxl-1 las la-trash text-danger"></i> Cancel shift</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_job_shift_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-7 d-flex justify-content-between">
                <h2 id="job_line_model_title">Edit shift details</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="job_shift_modal_close_btn">
                    <i class="fs-2 las la-times"></i>
                </div>
            </div>
            <form id="shift_basic_details_form">
                @csrf
                <div class="modal-body">
                    <div class="w-100">
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-10 fv-row fv-plugins-icon-container">
                                        <label for="job_default_shift_time" class="fs-6 fw-bold required">Shift start time</label>
                                        <div class="position-relative d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                            <i class="fs-2 las la-clock"></i>
                                        </span>
                                            <input class="form-control ps-12 flatpickr-input time_input" placeholder="Select shift start time" name="shift_start_time" id="shift_start_time" type="text" readonly="readonly" value="{{ date('H:i', strtotime($shift['start_time'])) }}">
                                        </div>
                                        <span class="text-danger error" id="shift_start_time_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label for="job_default_shift_length_hr" class="fs-6 fw-bold required">Duration</label>
                                        <div class="input-group">
                                            <input class="form-control" name="shift_duration_hr" id="shift_duration_hr" type="text" value="{{ $shift['shift_length_hr'] }}">
                                            <div class="input-group-prepend"><span class="input-group-text">hr</span></div>
                                        </div>
                                        <span class="text-danger error" id="shift_duration_hr_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="fv-row fv-plugins-icon-container">
                                        <label for="job_default_shift_length_min" class="fs-6 fw-bold"></label>
                                        <div class="input-group">
                                            <input class="form-control" name="shift_duration_min" id="shift_duration_min" type="text" value="{{ $shift['shift_length_min'] }}">
                                            <div class="input-group-prepend"><span class="input-group-text">min</span></div>
                                        </div>
                                        <span class="text-danger error" id="shift_duration_min_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center !important;">
                    <div class="fv-row">
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" name="shift_id" id="shift_id" value="{{ $shift['id'] }}" />
                                <button type="submit" name="job_shift_form_submit_btn" id="job_shift_form_submit_btn" class="btn btn-primary float-end {{ $shift['cancelled_at'] ? 'disabled' : ''  }}">Update</button>
                                <button type="button" class="btn btn-lg btn-primary me-3 float-end disabled d-none" name="job_shift_process_btn" id="job_shift_process_btn">
                                    <span>Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('job.link_group_model',['job' => $shift['client_job_details']])


@section('current_and_future_job_shift_js')
    <script>
        let page_need_to_refresh = false;
        function refreshPage() {
            if (page_need_to_refresh) {
                location.reload();
            }
        }
        $("#shift_start_time").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });

        $("#edit_booking_detail_btn").on('click', function () {
            $(".error").html('');
            $('#shift_basic_details_form').trigger('reset');
            $("#edit_job_shift_modal").modal('show');
        });

        $("#job_shift_modal_close_btn").on('click', function () {
            $(".error").html('');
            $('#shift_basic_details_form').trigger('reset');
            $("#edit_job_shift_modal").modal('hide');
        })

        $("#shift_basic_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#job_shift_form_submit_btn").addClass('d-none');
            $("#job_shift_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('update-job-shift-basic-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    $("#job_shift_form_submit_btn").removeClass('d-none');
                    $("#job_shift_process_btn").addClass('d-none');

                    decodeResponse(response);
                    if(response.code === 200) {
                        $("#job_shift_modal_close_btn").click();
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                },
                error   : function (response) {
                    $("#job_shift_form_submit_btn").removeClass('d-none');
                    $("#job_shift_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            });
        });

        $('#select_all_available_worker').change(function() {
            $('input[name="available_worker"]').prop('checked', $(this).prop('checked'));
        });

        $('input[name="available_worker"]').change(function() {
            if ($('input[name="available_worker"]:checked').length === $('input[name="available_worker"]').length) {
                $('#select_all_available_worker').prop('checked', true);
            } else {
                $('#select_all_available_worker').prop('checked', false);
            }
        });

        $("#hide_and_show_job_shift_worker_start_time_and_duration_btn").on('click', function () {
            let section = $("#hide_and_show_job_shift_worker_start_time_and_duration_section");
            if (section.is(":visible")) {
                section.slideUp(600, function () {
                    resetShiftWorkerTimeAndDuration();
                });
            } else {
                if (section.hasClass("d-none")) {
                    section.removeClass("d-none").hide();
                }
                section.slideDown(600, function () {
                    resetShiftWorkerTimeAndDuration();
                });
            }
        });

        function resetShiftWorkerTimeAndDuration() {
            let default_start_time = "{{ date('H:i', strtotime($shift['start_time'])) }}";
            let default_duration = "{{ number_format($currentDuration, 2) }}";

            $("#assign_selected_workers_start_time").val(default_start_time).trigger("change");
            $("#shift_worker_duration").val(default_duration).trigger("change");
        }

        $("#assign_btn").on('click', function () {
            let assign_type = $("#assign_selected_workers_via").val();
            let assign_selected_workers_job_line = $("#assign_selected_workers_job_line").val();

            let available_worker = [];

            $('input[name="available_worker"]:checked').each(function() {
                available_worker.push($(this).val());
            });

            if (assign_type === '') {
                toastr.error('Please select assign type.')
            } else if(available_worker.length === 0) {
                toastr.error('Please select a available workers.')
            } else {
                $("#assign_btn").hide();
                $("#assign_process_btn").show();

                $.ajax({
                    type        : 'post',
                    url         : '{{ url('add-worker-to-job-shift') }}',
                    data        : {
                        _token  : '{{ csrf_token() }}',
                        shift_id    : $("#shift_id").val(),
                        assign_type : assign_type,
                        assign_selected_workers_job_line : assign_selected_workers_job_line,
                        available_worker : available_worker,
                        assign_selected_workers_start_time : $("#assign_selected_workers_start_time").val(),
                        shift_worker_duration : $("#shift_worker_duration").val(),
                    },
                    success     : function (response) {

                        $("#assign_btn").show();
                        $("#assign_process_btn").hide();

                        if(response.code === 200) {
                            toastr.success(response.message);
                            setTimeout(function () {
                                location.reload();
                            }, 1500)
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error   : function (response) {
                        $("#assign_btn").show();
                        $("#assign_process_btn").hide();

                        toastr.error(response.statusText);
                    }
                });
            }
        });

        $("#with_selected_btn").on('click', function () {
            let selected_type    = $("#with_selected").val();
            let selected_worker = [];

            $('input[name="selected_worker"]:checked').each(function() {
                selected_worker.push($(this).val());
            });

            if (selected_type === '') {
                toastr.error('Please select with selected option.')
            } else if(selected_worker.length === 0) {
                toastr.error('Please select workers.')
            } else {
                $("#with_selected_btn").hide();
                $("#with_selected_process_btn").show();

                $.ajax({
                    type        : 'post',
                    url         : '{{ url('selected-worker-action-to-job-shift') }}',
                    data        : {
                        _token  : '{{ csrf_token() }}',
                        shift_id    : $("#shift_id").val(),
                        selected_type : selected_type,
                        selected_worker : selected_worker,
                    },
                    success     : function (response) {

                        $("#with_selected_btn").show();
                        $("#with_selected_process_btn").hide();

                        if(response.code === 200) {
                            toastr.success(response.message);
                            setTimeout(function () {
                                location.reload();
                            }, 1500)
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error   : function (response) {
                        $("#with_selected_btn").show();
                        $("#with_selected_process_btn").hide();

                        toastr.error(response.statusText);
                    }
                });
            }
        });

        $("#with_selected_invited_worker_btn").on('click', function () {
            let with_selected_type = $("#with_selected_invited_worker").val();
            let selected_invited_worker = [];

            $('input[name="selected_invited_worker"]:checked').each(function() {
                selected_invited_worker.push($(this).val());
            });

            if (with_selected_type === '') {
                toastr.error('Please select with selected option.')
            } else if(selected_invited_worker.length === 0) {
                toastr.error('Please select workers.')
            } else {
                $("#with_selected_invited_worker_btn").hide();
                $("#with_selected_invited_worker_process_btn").show();

                $.ajax({
                    type        : 'post',
                    url         : '{{ url('selected-worker-action-to-job-shift') }}',
                    data        : {
                        _token  : '{{ csrf_token() }}',
                        shift_id    : $("#shift_id").val(),
                        selected_type : with_selected_type,
                        selected_worker : selected_invited_worker,
                    },
                    success     : function (response) {

                        $("#with_selected_invited_worker_btn").show();
                        $("#with_selected_invited_worker_process_btn").hide();

                        if(response.code === 200) {
                            toastr.success(response.message);
                            setTimeout(function () {
                                location.reload();
                            }, 1500)
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error   : function (response) {
                        $("#with_selected_invited_worker_btn").show();
                        $("#with_selected_invited_worker_process_btn").hide();

                        toastr.error(response.statusText);
                    }
                });
            }
        })

        $("#delete_shift").on('click', function () {
            sweetAlertConfirmDelete('Are you sure!, This operation cannot be undone. Do you really want to cancel this shift and notify all workers').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('delete-shift-action') }}',
                        data    : {
                            _token      : "{{ @csrf_token() }}",
                            shift_id    : $("#shift_id").val(),
                        },
                        beforeSend: function() {
                            swal.fire({
                                html                : '<h5>Please wait...</h5>',
                                showConfirmButton   : false,
                                closeOnClickOutside : false,
                                allowEscapeKey      : false,
                                allowOutsideClick   : false,
                                onOpen              : function() {
                                    swal.showLoading();
                                }
                            });
                        },
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                setTimeout(function () {
                                    location.href = '{{ url('assignment-management') }}'
                                }, 1500);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $("#dec_and_can_worker_modal_btn").on('click', function () {
            $("#dec_and_can_worker_modal").modal('show');
        });

        $('#dec_and_can_worker_modal').on('hide.bs.modal', function (e) {
            refreshPage();
        });

        $("#dec_and_can_worker_cls_modal_btn").on('click', function (){
            $("#dec_and_can_worker_modal").modal('hide');
        })

        $(document).on('click', '#undo_declined_and_cancelled_worker', function () {
            let dcw_job_shit_worker_id = $(this).attr('data-id');
            let dcw_job_shit_worker_status = $(this).attr('data-status');
            sweetAlertRestore('You would like to restore this worker.').then((result) => {
                if (result.value) {
                    $.ajax({
                        type : 'post',
                        url : '{{ url('restore-declined-cancelled-worker') }}',
                        data : {
                            _token : "{{ @csrf_token() }}",
                            job_shit_worker_id : dcw_job_shit_worker_id,
                            job_shit_worker_status : dcw_job_shit_worker_status,
                        },
                        beforeSend: function() {
                            swal.fire({
                                html                : '<h5>Please wait...</h5>',
                                showConfirmButton   : false,
                                closeOnClickOutside : false,
                                allowEscapeKey      : false,
                                allowOutsideClick   : false,
                                onOpen              : function() {
                                    swal.showLoading();
                                }
                            });
                        },
                        success : function (response) {
                            swal.close();
                            if(response.code === 200) {
                                toastr.success(response.message);
                                let dcw_prefix = (dcw_job_shit_worker_status === 'declined') ? '#dw_' : '#cw_'
                                $(dcw_prefix + dcw_job_shit_worker_id).remove();
                                page_need_to_refresh = true;
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error : function (response) {
                            swal.close();
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $("#ineligible_worker_modal_btn").on('click', function () {
            $("#ineligible_worker_modal").modal('show');
        });

        $("#ineligible_worker_cls_modal_btn").on('click', function (){
            $("#ineligible_worker_modal").modal('hide');
        })
        /*--- BEGIN CLIENT JOB WORKERS ---*/
        $("#add_new_client_job_worker").on('click', function () {
            $(".error").html('');

            $("#client_job_worker_details_form").trigger('reset');
            $("#associated_cost_center").val('').trigger('change');
            $("#job_worker_name").val('').trigger('change');

            $("#client_job_worker_form_submit").show();
            $("#client_job_worker_form_process").hide();

            $("#add_new_client_job_worker_modal").modal('show');
        });

        $("#cls_btn_new_client_job_worker_modal").on('click', function (){
            $("#add_new_client_job_worker_modal").modal('hide');
        })

        $(document).ready(function() {
            $(".form-select-custom").select2({
                dropdownParent: $("#add_new_client_job_worker_modal")
            });
        });

        $(function() {
            $("#job_worker_name").select2({
                dropdownParent: $("#add_new_client_job_worker_modal"),
                ajax: {
                    url: '{{ url('search-client-job-worker') }}',
                    dataType: 'json',
                    type: "POST",
                    data: function (term) {
                        return {
                            _token: '{{ csrf_token() }}',
                            associated_cost_center : $("#associated_cost_center").val(),
                            job_id  : '{{ $shift['job_id'] }}',
                            keyword : term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                }
                            })
                        };
                    }

                }
            });
        });

        $("#client_job_worker_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#client_job_worker_form_submit").hide();
            $("#client_job_worker_form_process").show();

            $.ajax({
                type        : 'post',
                url         : '{{ url('store-client-job-worker-multiple') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#client_job_worker_form_submit").show();
                    $("#client_job_worker_form_process").hide();

                    if(response.code === 200) {
                        $("#add_new_client_job_worker_modal").modal('hide');
                        setTimeout(function (){
                            location.reload()
                        },1500);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
        /*--- END CLIENT JOB WORKERS ---*/

        /*--- BEGIN SLOT UPDATE ---*/
        $("#manage_slot_btn").on('click', function () {
            $(".error").html('');

            $("#slot_action").val('').trigger('change');
            $("#slot_number").val($(this).attr('data-slot'));

            $("#manage_slot_submit_btn").show();
            $("#manage_slot_process_btn").hide();

            $("#manage_slot_modal").modal('show');
        });

        $("#manage_slot_cls_modal_btn").on('click', function (){
            $("#manage_slot_modal").modal('hide');
        })

        $("#manage_slot_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#manage_slot_submit_btn").hide();
            $("#manage_slot_process_btn").show();

            $.ajax({
                type        : 'post',
                url         : '{{ url('manage-slot-action') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)

                    $("#manage_slot_submit_btn").show();
                    $("#manage_slot_process_btn").hide();

                    if(response.code === 200) {
                        $("#manage_slot_modal").modal('hide');
                        setTimeout(function (){
                            location.reload()
                        },1500);
                    }
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
        /*--- END SLOT UPDATE ---*/

        /*--- BEGIN AVAILABLE WORKER LINKED TO CLIENT ---*/
        $("#available_worker_linked_to_client_modal_btn").on('click', function () {
            $("#available_workers_linked_to_client_modal").modal('show');
        });

        $("#available_worker_linked_to_client_modal_cls_btn").on('click', function (){
            $("#available_workers_linked_to_client_modal").modal('hide');
        })

        $('#select_all_available_worker_linked_to_client').change(function() {
            $('input[name="available_worker_linked_to_client"]').prop('checked', $(this).prop('checked'));
        });

        $('input[name="available_worker_linked_to_client"]').change(function() {
            if ($('input[name="available_worker_linked_to_client"]:checked').length === $('input[name="available_worker_linked_to_client"]').length) {
                $('#select_all_available_worker_linked_to_client').prop('checked', true);
            } else {
                $('#select_all_available_worker_linked_to_client').prop('checked', false);
            }
        });

        $("#available_worker_linked_to_client_job_btn").on('click', function () {
            let available_worker_add_to_job = [];

            $('input[name="available_worker_linked_to_client"]:checked').each(function() {
                available_worker_add_to_job.push($(this).val());
            });

            if(available_worker_add_to_job.length === 0) {
                toastr.error('Please select a available workers.')
            } else {
                $("#available_worker_linked_to_client_job_btn").hide();
                $("#available_worker_linked_to_client_process_btn").show();

                $.ajax({
                    type        : 'post',
                    url         : '{{ url('linked-to-client-worker-add-into-job') }}',
                    data        : {
                        _token  : '{{ csrf_token() }}',
                        shift_id    : $("#shift_id").val(),
                        available_worker_add_to_job : available_worker_add_to_job,
                    },
                    success     : function (response) {

                        $("#available_worker_linked_to_client_job_btn").show();
                        $("#available_worker_linked_to_client_process_btn").hide();

                        if(response.code === 200) {
                            toastr.success(response.message);
                            $("#available_worker_linked_to_client_modal_cls_btn").click();
                            setTimeout(function () {
                                location.reload();
                            }, 1500)
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error   : function (response) {
                        $("#available_worker_linked_to_client_job_btn").show();
                        $("#available_worker_linked_to_client_process_btn").hide();

                        toastr.error(response.statusText);
                    }
                });
            }
        });
        /*--- END AVAILABLE WORKER LINKED TO CLIENT ---*/

        $("#cwar").on('click', function () {
            $("#cwar").addClass('d-none');
            $("#cwad").removeClass('d-none');
            $("#cwjc").removeClass('d-none');
        });

        $("#cwad").on('click', function () {
            $("#cwar").removeClass('d-none');
            $("#cwad").addClass('d-none');
            $("#cwjc").addClass('d-none');
        });

        $('.group-checkbox').on('change', function() {
            var workerIds = $(this).data('group-workers').toString().split(',');

            $.each(workerIds, function(index, workerId) {
                $('#available_worker_' + workerId).prop('checked', $('.group-checkbox#' + $(this).attr('id')).is(':checked'));
            }.bind(this));
        });

        $('.worker-checkbox').on('change', function() {
            $('.group-checkbox').each(function() {
                var groupWorkerIds = $(this).data('group-workers').toString().split(',');
                var allChecked = true;

                $.each(groupWorkerIds, function(i, id) {
                    if (!$('#available_worker_' + id).is(':checked')) {
                        allChecked = false;
                        return false;
                    }
                });

                if (!allChecked && $(this).is(':checked')) {
                    $(this).prop('checked', false);
                }
            });
        });
    </script>
    @yield('add_group_link_js')
@endsection
