<?php

namespace App\Helper\Job;

use App\Helper\Workers\RightToWorkHelper;
use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Client\ClientJobWorker;
use App\Models\Job\JobLine;
use App\Models\Job\JobShift;
use App\Models\Job\JobShiftWorker;
use App\Models\Job\PayrollLineItem;
use App\Models\Payroll\PayrollWeekDate;
use App\Models\User;
use App\Models\Worker\Worker;
use Carbon\Carbon;

class JobHelper
{
    public static function getCancelledByNameToJobShiftWorker($cancelled_by, $cancelled_by_user_id)
    {
        $cancelled_by_name = '';
        if ($cancelled_by == 'worker') {
            $cancelled_by_details = Worker::query()->select(['id', 'first_name', 'middle_name', 'last_name'])
                ->where('id', $cancelled_by_user_id)
                ->first();
            if ($cancelled_by_details) {
                $cancelled_by_name = $cancelled_by_details['first_name'].' '.$cancelled_by_details['middle_name'].' '.$cancelled_by_details['last_name'];
            }
        } else if ($cancelled_by == 'client') {
            $cancelled_by_details = Client::query()->select(['id', 'company_name'])
                ->where('id', $cancelled_by_user_id)
                ->first();
            if ($cancelled_by_details) {
                $cancelled_by_name = $cancelled_by_details['company_name'];
            }
        }  else if ($cancelled_by == 'admin') {
            $cancelled_by_details = User::query()->select(['id', 'name'])
                ->where('id', $cancelled_by_user_id)
                ->first();
            if ($cancelled_by_details) {
                $cancelled_by_name = $cancelled_by_details['name'];
            }
        }
        return $cancelled_by_name;
    }

    public static function preparedJobLineTextBox($job_id) {
        $jobLine = JobLine::query()->where('job_id', $job_id)->get();
        $textBoxes = '';
        if ($jobLine) {
            foreach ($jobLine as $row) {
                $textBoxes .= <<<HTML
                            <div class="col-lg-12">
                                <div class="mb-10 fv-row fv-plugins-icon-container">
                                    <label for="line_requirement_number_{$row['id']}" class="fs-6 fw-bold">{$row['line_name']} ({$row['line_code']})</label>
                                    <input class="form-control" name="line_requirement_number[{$row['id']}]" id="line_requirement_number_{$row['id']}" type="text" value="0">
                                    <span class="text-danger error" id="line_requirement_number_{$row['id']}_error"></span>
                                </div>
                            </div>
                            HTML;
            }
        }
        return $textBoxes;
    }

    public static function getWorkerAvailabilityBox($array, &$table_th) {
        $shift = self::getJobShift($array);
        if ($shift) {
            $isPast = Carbon::parse($array['shift_date'])->isPast();
            $dayData = JobShiftWorker::query()->where('worker_id', $array['worker_id'])
                ->where('shift_date', $array['shift_date'])
                ->with('jobShift')
                ->orderBy('id', 'desc')
                ->first();
            if ($dayData) {
                if ($array['job_id'] != $dayData['jobShift']['job_id'] && $dayData['jobShift']['start_time'] == $shift['start_time']) {
                    return <<<HTML
                    <div class="bg-gray-500 border border-2 border-danger position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                        <div class="position-relative d-flex p-3">
                            <div class="form-check form-check-sm form-check-custom">
                                <label class="fs-4 fw-bold ms-2 text-white">Other Job</label>
                            </div>
                        </div>
                    </div>
                HTML;
                } elseif ($array['job_id'] != $dayData['jobShift']['job_id'] && $dayData['jobShift']['start_time'] != $shift['start_time']) {
                    $tooltipText = $dayData['jobShift']['client_job_details']['name'].' | '.$dayData['jobShift']['client_job_details']['site_details']['site_name'].' - '.$dayData['jobShift']['client_job_details']['client_details']['company_name'].' | Start '.date('H:i', strtotime($dayData['jobShift']['start_time'])).' | Duration '.$dayData['jobShift']['shift_length_hr'].'h'.$dayData['jobShift']['shift_length_min'].'m';
                    $tooltipText = htmlspecialchars($tooltipText, ENT_QUOTES);
                    if ($isPast) {
                        return <<<HTML
                            <div class="bg-o_job position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                <div class="position-relative d-flex p-3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-title="{$tooltipText}">
                                    <div class="form-check form-check-sm form-check-custom">
                                        <label class="ms-7 fs-4 fw-bold ms-2 o_job-text">O/Job</label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                    } else {
                        return <<<HTML
                            <div class="bg-o_job position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                <div class="position-relative d-flex p-3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-title="{$tooltipText}">
                                    <div class="form-check form-check-sm form-check-custom">
                                        <input name="worker_availability_checkbox" id="worker_checkbox_{$shift['id']}_{$dayData['worker_id']}" class="form-check-input widget-9-check" type="checkbox" value="{$shift['id']}_{$dayData['worker_id']}_o/job" />
                                        <label for="worker_checkbox_{$shift['id']}_{$dayData['worker_id']}" class="fs-4 fw-bold ms-2 o_job-text">O/Job</label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                    }
                } elseif ($dayData['confirmed_at'] && $dayData['declined_at'] == null && $dayData['cancelled_at'] == null) {
                    $table_th['confirm'] = $table_th['confirm'] + 1;
                    if ($isPast) {
                        return <<<HTML
                            <div class="bg-confirm position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                <div class="position-relative d-flex p-3">
                                    <div class="form-check form-check-sm form-check-custom">
                                        <label class="ms-7 fs-4 fw-bold ms-2 confirm-text">Conf.</label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                    } else {
                        return <<<HTML
                            <div class="bg-confirm position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                <div class="position-relative d-flex p-3">
                                    <div class="form-check form-check-sm form-check-custom">
                                        <input name="worker_availability_checkbox" id="worker_checkbox_{$dayData['job_shift_id']}_{$dayData['worker_id']}" class="form-check-input widget-9-check" type="checkbox" value="{$dayData['job_shift_id']}_{$dayData['worker_id']}_confirmed" />
                                        <label for="worker_checkbox_{$dayData['job_shift_id']}_{$dayData['worker_id']}" class="fs-4 fw-bold ms-2 confirm-text">Conf.</label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                    }
                } elseif ($dayData['invited_at'] && $dayData['confirmed_at'] == null && $dayData['declined_at'] == null && $dayData['cancelled_at'] == null) {
                    $table_th['invited'] = $table_th['invited'] + 1;
                    if ($isPast) {
                        return <<<HTML
                            <div class="bg-invited position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                <div class="position-relative d-flex p-3">
                                    <div class="form-check form-check-sm form-check-custom">
                                        <label class="ms-7 fs-4 fw-bold ms-2 invited-text">Invited</label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                    } else {
                        return <<<HTML
                            <div class="bg-invited position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                <div class="position-relative d-flex p-3">
                                    <div class="form-check form-check-sm form-check-custom">
                                        <input name="worker_availability_checkbox" id="worker_checkbox_{$dayData['job_shift_id']}_{$dayData['worker_id']}" class="form-check-input widget-9-check" type="checkbox" value="{$dayData['job_shift_id']}_{$dayData['worker_id']}_invited" />
                                        <label for="worker_checkbox_{$dayData['job_shift_id']}_{$dayData['worker_id']}" class="fs-4 fw-bold ms-2 invited-text">Invited</label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                    }
                } elseif ($dayData['cancelled_at']) {
                    return <<<HTML
                    <div class="bg-gray-500 position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                        <div class="position-relative d-flex p-3">
                            <div class="form-check form-check-sm form-check-custom">
                                <label class="fs-4 fw-bold ms-2 text-white">Cancelled</label>
                            </div>
                        </div>
                    </div>
                HTML;
                } elseif ($dayData['declined_at']) {
                    return <<<HTML
                    <div class="bg-gray-500 position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                        <div class="position-relative d-flex p-3">
                            <div class="form-check form-check-sm form-check-custom">
                                <label class="fs-4 fw-bold ms-2 text-white">Declined</label>
                            </div>
                        </div>
                    </div>
                HTML;
                }
            } else {
                $available_worker = self::getAvailableWorker($array);
                if ($available_worker) {
                    $table_th['available'] = $table_th['available'] + 1;
                    if ($isPast) {
                        return <<<HTML
                            <div class="bg-available position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                <div class="position-relative d-flex p-3">
                                    <div class="form-check form-check-sm form-check-custom">
                                        <label class="ms-7 fs-4 fw-bold ms-2 available-text">Avail.</label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                    } else {
                        return <<<HTML
                            <div class="bg-available position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                <div class="position-relative d-flex p-3">
                                    <div class="form-check form-check-sm form-check-custom">
                                        <input name="worker_availability_checkbox" id="worker_checkbox_{$shift['id']}_{$available_worker['worker_id']}" class="form-check-input widget-9-check" type="checkbox" value="{$shift['id']}_{$available_worker['worker_id']}_available" />
                                        <label for="worker_checkbox_{$shift['id']}_{$available_worker['worker_id']}" class="fs-4 fw-bold ms-2 available-text">Avail.</label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                    }
                } else {
                    $ineligibleWorker = self::getIneligibleWorker($array, $shift['id']);
                    if ($ineligibleWorker) {
                        if (strtotime(RightToWorkHelper::getLatestDate($ineligibleWorker['rightsToWork'])) <= strtotime($array['shift_date']) || strtotime(RightToWorkHelper::getLatestStartDate($ineligibleWorker['rightsToWork'])) >= strtotime($array['shift_date'])) {
                            return <<<HTML
                                <div class="bg-danger position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                    <div class="position-relative d-flex p-3">
                                        <div class="form-check form-check-sm form-check-custom">
                                            <label class="fs-4 fw-bold ms-2 text-white">No RTW</label>
                                        </div>
                                    </div>
                                </div>
                            HTML;
                        } else {
                            foreach ($ineligibleWorker['absence'] as $absence) {
                                if ($shift['date'] >= $absence['start_date'] && $shift['date'] <= $absence['end_date']) {
                                    if ($absence['absence_type'] == 'Holiday') {
                                        return <<<HTML
                                            <div class="bg-gray-500 position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                                <div class="position-relative d-flex p-3">
                                                    <div class="form-check form-check-sm form-check-custom">
                                                        <label class="fs-4 fw-bold ms-2 text-white">Hols.</label>
                                                    </div>
                                                </div>
                                            </div>
                                        HTML;
                                    } elseif ($absence['absence_type'] == 'Other') {
                                        return <<<HTML
                                            <div class="bg-gray-500 position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                                <div class="position-relative d-flex p-3">
                                                    <div class="form-check form-check-sm form-check-custom">
                                                        <label class="fs-4 fw-bold ms-2 text-white">Oth. Abs.</label>
                                                    </div>
                                                </div>
                                            </div>
                                        HTML;
                                    } elseif ($absence['absence_type'] == 'Sickness') {
                                        return <<<HTML
                                            <div class="bg-gray-500 position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                                <div class="position-relative d-flex p-3">
                                                    <div class="form-check form-check-sm form-check-custom">
                                                        <label class="fs-4 fw-bold ms-2 text-white">Sick</label>
                                                    </div>
                                                </div>
                                            </div>
                                        HTML;
                                    } else {
                                        if ($isPast) {
                                            return <<<HTML
                                                <div class="bg-gray-500 position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                                    <div class="position-relative d-flex p-3">
                                                        <div class="form-check form-check-sm form-check-custom">
                                                            <label class="ms-7 fs-4 fw-bold ms-2 text-white">Rest</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            HTML;
                                        } else {
                                            return <<<HTML
                                                <div class="bg-gray-500 position-absolute rounded-2" style="top: 4px; left: 4px; right: 4px; bottom: 4px;"></div>
                                                    <div class="position-relative d-flex p-3">
                                                        <div class="form-check form-check-sm form-check-custom">
                                                            <input name="worker_availability_checkbox" id="worker_checkbox_{$shift['id']}_{$absence['worker_id']}" class="form-check-input widget-9-check" type="checkbox" value="{$shift['id']}_{$absence['worker_id']}_rest" />
                                                            <label for="worker_checkbox_{$shift['id']}_{$absence['worker_id']}" class="fs-4 fw-bold ms-2 text-white">Rest</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            HTML;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            return <<<HTML
                <div class="bg-secondary position-absolute" style="top: 0px; left: 0px; right: 0px; bottom: 0px;"></div>
            HTML;
        }
    }

    public static function getJobShift($array) {
        return JobShift::query()->where('job_id', $array['job_id'])
            ->where('date', $array['shift_date'])
            ->with('JobShiftWorker_details')
            ->first();
    }

    public static function getAvailableWorker($array) {
        return ClientJobWorker::query()->select('client_job_workers.*')
            ->where('job_id', $array['job_id'])
            ->where('worker_id', $array['worker_id'])
            ->with(['worker', 'rightsToWork', 'absence'])
            ->whereNotNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('archived_at')
            ->whereHas('worker', function ($query) {
                $query->where('status', 'active')
                    ->where('suspend', 'No');
            })
            ->whereHas('rightsToWork', function ($query3) use ($array) {
                $query3->where(function($query3) use ($array) {
                    $query3->whereNull('start_date')
                        ->whereDate('end_date', '>=', $array['shift_date']);
                })->orWhere(function($query3) use ($array) {
                    $query3->whereDate('start_date', '<=', $array['shift_date'])
                        ->whereDate('end_date', '>=', $array['shift_date']);
                })->latest('end_date');
            })
            ->whereDoesntHave('absence', function ($query4) use ($array) {
                $query4->whereDate('start_date', '<=', $array['shift_date'])
                    ->whereDate('end_date', '>=', $array['shift_date']);
            })
            ->first();
    }

    public static function getIneligibleWorker($array, $shift_id) {
        return ClientJobWorker::query()->where('job_id', $array['job_id'])
            ->with(['worker', 'rightsToWork', 'absence'])
            ->where('worker_id', $array['worker_id'])
            ->whereNotNull('confirmed_at')
            ->where(function ($query1) use ($array, $shift_id) {
                $query1->orWhereNotNull('archived_at')
                    ->orWhereHas('worker', function ($query2) {
                        $query2->where('suspend', 'Yes');
                    })
                    ->orWhereDoesntHave('rightsToWork', function ($query3) use ($array) {
                        $query3->where(function($query3) use ($array) {
                            $query3->whereNull('start_date')
                                ->whereDate('end_date', '>=', $array['shift_date']);
                        })->orWhere(function($query3) use ($array) {
                            $query3->whereDate('start_date', '<=', $array['shift_date'])
                                ->whereDate('end_date', '>=', $array['shift_date']);
                        })->latest('end_date');
                    })
                    ->orWhereHas('absence', function ($query4) use ($array) {
                        $query4->whereDate('start_date', '<=', $array['shift_date'])
                            ->whereDate('end_date', '>=', $array['shift_date']);
                    });
            })
            ->first();
    }

    public static function validFromMinDate($job_id) {
        $payrollLineItem = PayrollLineItem::query()
            ->where('job_id', $job_id)
            ->latest('id')
            ->first();

        if (! $payrollLineItem) {
            return now()->startOfYear()->format('Y-m-d');
        }

        $pwdNode = explode('-', $payrollLineItem->payroll_week);
        $weekNumber = $pwdNode[0] ?? null;
        $year = $pwdNode[1] ?? null;

        $job = ClientJob::query()->where('id', $job_id)->with(['client_details'])->first();
        $prefix = $job['client_details']['payroll_week_starts'];
        $startColumn = "{$prefix}_payroll_start";
        $endColumn = "{$prefix}_payroll_end";

        $pwData = PayrollWeekDate::query()
            ->select(['id', 'payroll_week_number', 'year', $startColumn, $endColumn, 'pay_date'])
            ->where('payroll_week_number', $weekNumber)
            ->where('year', $year)
            ->first();

        return optional($pwData)
            ? Carbon::parse($pwData[$endColumn])->addDay()->format('Y-m-d')
            : now()->startOfYear()->format('Y-m-d');
    }
}