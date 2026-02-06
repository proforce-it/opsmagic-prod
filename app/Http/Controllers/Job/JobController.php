<?php

namespace App\Http\Controllers\Job;

use App\Exports\BookingConfirmWorkerExport;
use App\Helper\Clients\ClientHelper;
use App\Helper\Job\JobHelper;
use App\Http\Controllers\Controller;
use App\Mail\JobShiftCancelEmail;
use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Client\ClientJobWorker;
use App\Models\Client\Site;
use App\Models\Group\CostCentre;
use App\Models\Group\Group;
use App\Models\Group\GroupWithJob;
use App\Models\Group\GroupWithWorker;
use App\Models\Job\JobLine;
use App\Models\Job\JobLineClientRequirement;
use App\Models\Job\JobShift;
use App\Models\Job\JobShiftWorker;
use App\Models\Payroll\PayrollWeekDate;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class JobController extends Controller
{
    use JsonResponse;
    public function index(Request $request) {

        $tag = $request->input('tag');

        $tagExplode = [0 => '', 1 => '', 2 => ''];
        $client     = Client::query()->orderBy('company_name')->get();
        $site       = [];
        $jobs        = [];
        $jobLineTextBox = '';
        $current_shift_id = null;
        $job = null;

        if ($tag) {
            $tagExplode = explode('.', $tag);
            $site       = Site::query()->where('client_id', $tagExplode[0])->orderBy('site_name')->get();
            $jobs        = ClientJob::query()->where('site_id', $tagExplode[1])->orderBy('name')->get();
            $jobLineTextBox = JobHelper::preparedJobLineTextBox($tagExplode[2]);
        }

        if ($request->input('view_type') == 'month') {
            $current_shift_id = JobHelper::get_shift_id($tagExplode[2]);
            $job = ClientJob::query()->where('id', $tagExplode[2])->with(['client_details', 'site_details'])->first();
        }

        return view('job.dis_job_shift', compact('client', 'site', 'jobs', 'tagExplode', 'jobLineTextBox', 'current_shift_id', 'job'));
    }

    public function getSiteUsingClient(Request $request) {
        try {
            $site = Site::query()->where('client_id', $request->input('client_id'))
                ->where('archived', 0)
                ->orderBy('site_name', 'asc')
                ->get();

            $option = '<option value=""></option>';
            if ($site) {
                foreach ($site as $row) {
                    $option .= '<option value="'.$row['id'].'">'.$row['site_name'].'</option>';
                }
            }

            return self::responseWithSuccess('Site options.', [
                'site_option' => $option
            ]);
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getJobUsingSite(Request $request) {
        try {
            $job = ClientJob::query()->where('site_id', $request->input('site_id'))
                ->where('archived', 0)
                ->orderBy('name', 'asc')
                ->get();

            $option = '<option value=""></option>';
            if ($job) {
                foreach ($job as $row) {
                    $option .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                }
            }

            return self::responseWithSuccess('job options.', [
                'job_option' => $option
            ]);
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function createJobShift(Request $request) {
        DB::beginTransaction();
        try {
            $rules = [
                'from_date'         => 'required',
                'how_many_days'     => 'required|numeric|min:1',
                'number_of_no_line'  => 'required|integer|min:1',
            ];

            $messages = [
                'number_of_no_line.required' => 'The total number of associates field is required.',
                'number_of_no_line.integer' => 'The total number of associates must be a valid number.',
                'number_of_no_line.min' => "The total number of associates must be at least 1."
            ];
            $number_workers = 0;
            if ($request->has('line_requirement_number')) {
                $rules["line_requirement_number"] = 'required|array';
                foreach ($request->input('line_requirement_number') as $id => $value) {
                    $rules["line_requirement_number.$id"] = 'required|integer|min:0';

                    $messages["line_requirement_number.$id.required"] = "Line requirement is required.";
                    $messages["line_requirement_number.$id.integer"] = "Line requirement must be a valid number.";
                    $messages["line_requirement_number.$id.min"] = "Line requirement must be at least 0.";
                }
                $number_workers = array_sum($request->input('line_requirement_number'));
                $rules['number_of_no_line'] = ($number_workers <= 0)
                    ? 'required|integer|min:1'
                    : 'nullable|integer|min:0';
            }

            $validator = Validator::make($request->input(), $rules, $messages);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            $number_workers = $number_workers + $params['number_of_no_line'];

            $jobID = $request->has('job_id') ? $params['job_id'] : $params['wa_job_id'];
            $job = ClientJob::query()->where('id', $jobID)->first();
            if (!$job)
                return self::responseWithError('Job not found, please try again.');

            $start_date = Carbon::parse($params['from_date']);
            $end_date = $start_date->copy()->addDays((int) $params['how_many_days']-1);

            $current_date = $start_date->copy();
            while ($current_date->lte($end_date)) {

                $initialTime = Carbon::parse($job['default_shift_start_time']);
                $end_time    = $initialTime->copy()->addHours((int) $job['default_shift_length_hr'])->addMinutes((int) $job['default_shift_length_min']);

                $created = JobShift::query()->create([
                    'job_id' => $jobID,
                    'date' => $current_date->toDateString(),
                    'number_workers' => $number_workers,
                    'start_time' => $job['default_shift_start_time'],
                    'end_time' => $end_time,
                    'shift_length_hr' => $job['default_shift_length_hr'],
                    'shift_length_min' => $job['default_shift_length_min'],
                    'shift_length' => $job['default_shift_length'],
                ]);

                if ($request->has('line_requirement_number') && $request->input('line_requirement_number')) {
                    foreach ($params['line_requirement_number'] as $key => $line_requirement) {
                        JobLineClientRequirement::query()->create([
                            'job_shift_id' => $created['id'],
                            'job_line_id' => $key,
                            'worker_requirement' => $line_requirement ?? 0
                        ]);
                    }
                }

                $current_date->addDay();
            }

            DB::commit();
            return self::responseWithSuccess('Job shift successfully created.');
        } catch (Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function getJobShiftData(Request $request) {
        try {
            $eventCollection = new Collection();
            if ($request->has('month')) {
                $monthYearNode = explode(' ', $request->input('month'));

                $monthName = $monthYearNode[0];
                $year = (int) $monthYearNode[1];

                $month = Carbon::parse($monthName)->month;
                $currentMonthStartDate = Carbon::createFromDate($year, $month, 1);
                $currentMonthEndDate = $currentMonthStartDate->copy()->endOfMonth();

                for ($date = $currentMonthStartDate; $date->lte($currentMonthEndDate); $date->addDay()) {
                    $shift = JobShift::query()->where('job_id', $request->input('job_id'))->whereDate('date', $date->toDateString())->get();
                    if ($shift) {
                        foreach ($shift as $row) {
                            $eventCollection->push([
                                'start' => $date->toDateString(),
                                'extendedProps' => [
                                    'assigned'  => JobShiftWorker::query()->where('job_shift_id', $row['id'])
                                        ->whereNotNull('confirmed_at')
                                        ->whereNull('declined_at')
                                        ->whereNull('cancelled_at')
                                        ->count(),
                                    'required'  => $row['number_workers'],
                                    'shift_id'  => $row['id'],
                                    'cancelled' => ($row['cancelled_at']) ? '<span class="text-danger fw-bolder">cancelled</span>' : '',
                                    'start_time' => $row['start_time'],
                                    'shift_length_hr' => $row['shift_length_hr'],
                                    'shift_length_min' => $row['shift_length_min']
                                ],
                                'className' => 'shift-required'
                            ]);
                        }
                    }
                }
            }

            return self::responseWithSuccess('Selected job shifts data', [
                'events' => $eventCollection,
                'jobLineTextBox' => JobHelper::preparedJobLineTextBox($request->input('job_id'))
            ]);
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function viewJobShift($shift_id) {
        $shift = JobShift::query()->where('id', $shift_id)->with(['client_job_details'])->first();
        $shiftDate = $shift['date'];

        $previousShift = JobShift::query()
            ->where('job_id', $shift['job_id'])
            ->where('id', '<', $shift_id)
            ->orderBy('id', 'desc')
            ->first();

        $nextShift = JobShift::query()
            ->where('job_id', $shift['job_id'])
            ->where('id', '>', $shift_id)
            ->orderBy('id', 'asc')
            ->first();

        $previous_shift_td = $previousShift?->id ?? 0;
        $next_shift_td = $nextShift?->id ?? 0;

        $job_shift_worker = JobShiftWorker::query()->where('job_shift_id', $shift['id'])->pluck('worker_id');
        $available_worker = ClientJobWorker::query()->select('client_job_workers.*')
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM job_shift_workers AS jsw
                WHERE jsw.worker_id = client_job_workers.worker_id
                AND jsw.confirmed_at IS NOT NULL
                AND jsw.shift_date BETWEEN DATE_SUB('$shiftDate', INTERVAL 14 DAY) AND '$shiftDate') AS confirmed_shifts_count"))
            ->where('job_id', $shift['job_id'])
            ->whereNotIn('worker_id', $job_shift_worker)
            /*->whereNotIn('worker_id', function($query) use ($shiftDate, $shift_id) {
                $query->select('worker_id')
                    ->from('job_shift_workers')
                    ->whereNot('job_shift_id', $shift_id)
                    ->whereDate('shift_date', $shiftDate);
            })*/
            ->with(['worker', 'rightsToWork', 'absence'])
            ->whereNotNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('archived_at')
            ->whereHas('worker', function ($query) {
                $query->where('status', 'active')
                    ->where('suspend', 'No');
            })
            ->whereHas('rightsToWork', function ($query3) use ($shiftDate) {
                $query3->where(function($query3) use ($shiftDate) {
                        $query3->whereNull('start_date')
                            ->whereDate('end_date', '>=', $shiftDate);
                })->orWhere(function($query3) use ($shiftDate) {
                    $query3->whereDate('start_date', '<=', $shiftDate)
                        ->whereDate('end_date', '>=', $shiftDate);
                })->latest('end_date');
            })
            ->whereDoesntHave('absence', function ($query4) use ($shiftDate) {
                $query4->whereDate('start_date', '<=', $shiftDate)
                    ->whereDate('end_date', '>=', $shiftDate);
            })
            ->get()->toArray();

        $archivedAndDeclinedWorkerIDs = ClientJobWorker::query()
            ->where('job_id', $shift['job_id'])
            ->where(function ($q) {
                $q->whereNotNull('archived_at')
                    ->orWhereNotNull('declined_at');
            })
            ->pluck('worker_id');

        $confirm_worker = JobShiftWorker::query()->select('job_shift_workers.*')
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM job_shift_workers AS jsw
                WHERE jsw.worker_id = job_shift_workers.worker_id
                AND jsw.confirmed_at IS NOT NULL
                AND jsw.shift_date BETWEEN DATE_SUB('$shiftDate', INTERVAL 14 DAY) AND '$shiftDate') AS confirmed_shifts_count"))
            ->where('job_shift_id', $shift['id'])
            ->whereNotNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('cancelled_at')
            ->when($archivedAndDeclinedWorkerIDs->isNotEmpty(), function ($q) use ($archivedAndDeclinedWorkerIDs) {
                $q->whereNotIn('worker_id', $archivedAndDeclinedWorkerIDs);
            })
            ->with(['worker', 'rightsToWork', 'job_line_details'])
            ->get()->toArray();

        $pastDateConfirm_worker = JobShiftWorker::query()->select('job_shift_workers.*')
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM job_shift_workers AS jsw
                WHERE jsw.worker_id = job_shift_workers.worker_id
                AND jsw.confirmed_at IS NOT NULL
                AND jsw.shift_date BETWEEN DATE_SUB('$shiftDate', INTERVAL 14 DAY) AND '$shiftDate') AS confirmed_shifts_count"))
            ->where('job_shift_id', $shift['id'])
            ->whereNotNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('cancelled_at')
            ->with(['worker', 'rightsToWork', 'job_line_details'])
            ->get()
            ->map(function($jsw){
                $jsw->client_job_worker = ClientJobWorker::query()->where('job_id', $jsw->jobShift->job_id)
                    ->where('worker_id', $jsw->worker_id)
                    ->first();
                unset($jsw->jobShift);
                return $jsw;
            })->toArray();

        $pending_worker = JobShiftWorker::query()->select('job_shift_workers.*')
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM job_shift_workers AS jsw
                WHERE jsw.worker_id = job_shift_workers.worker_id
                AND jsw.confirmed_at IS NOT NULL
                AND jsw.shift_date BETWEEN DATE_SUB('$shiftDate', INTERVAL 14 DAY) AND '$shiftDate') AS confirmed_shifts_count"))
            ->where('job_shift_id', $shift['id'])
            ->whereNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('cancelled_at')
            ->when($archivedAndDeclinedWorkerIDs->isNotEmpty(), function ($q) use ($archivedAndDeclinedWorkerIDs) {
                $q->whereNotIn('worker_id', $archivedAndDeclinedWorkerIDs);
            })
            ->with(['worker', 'rightsToWork', 'job_line_details'])
            ->get()->toArray();

        $declined_worker = JobShiftWorker::query()->where('job_shift_id', $shift['id'])
            ->whereNotNull('declined_at')
            ->when($archivedAndDeclinedWorkerIDs->isNotEmpty(), function ($q) use ($archivedAndDeclinedWorkerIDs) {
                $q->whereNotIn('worker_id', $archivedAndDeclinedWorkerIDs);
            })
            ->with(['worker', 'rightsToWork', 'job_line_details'])
            ->get()
            ->toArray();

        $cancelled_worker = JobShiftWorker::query()->where('job_shift_id', $shift['id'])
            ->whereNotNull('cancelled_at')
            ->when($archivedAndDeclinedWorkerIDs->isNotEmpty(), function ($q) use ($archivedAndDeclinedWorkerIDs) {
                $q->whereNotIn('worker_id', $archivedAndDeclinedWorkerIDs);
            })
            ->with(['worker', 'rightsToWork', 'job_line_details'])
            ->get()
            ->toArray();


        $ineligibleWorker = ClientJobWorker::query()->where('job_id', $shift['job_id'])
            ->whereNull('archived_at')
            ->whereNull('declined_at')
            ->with(['worker', 'rightsToWork', 'absence', 'worker.jobShiftWorker' => function($query) use ($shiftDate, $shift_id) {
                $query->whereDate('shift_date', $shiftDate)
                    ->whereNot('job_shift_id', $shift_id);
            }])
            ->whereNotNull('confirmed_at')
            ->where(function ($query1) use ($shiftDate, $shift_id) {
                $query1->orWhereNotNull('archived_at')
                    ->orWhereHas('worker', function ($query2) {
                        $query2->where('suspend', 'Yes');
                    })
                    ->orWhereDoesntHave('rightsToWork', function ($query3) use ($shiftDate) {
                        $query3->where(function($query3) use ($shiftDate) {
                            $query3->whereNull('start_date')
                                ->whereDate('end_date', '>=', $shiftDate);
                        })->orWhere(function($query3) use ($shiftDate) {
                            $query3->whereDate('start_date', '<=', $shiftDate)
                                ->whereDate('end_date', '>=', $shiftDate);
                        })->latest('end_date');
                    })
                    ->orWhereHas('absence', function ($query4) use ($shiftDate) {
                        $query4->whereDate('start_date', '<=', $shiftDate)
                            ->whereDate('end_date', '>=', $shiftDate);
                    })
                    ->whereNotIn('worker_id', function($query5) use ($shiftDate, $shift_id) { //orWhereIn
                        $query5->select('worker_id')
                            ->from('job_shift_workers')
                            ->whereNot('job_shift_id', $shift_id)
                            ->whereDate('shift_date', $shiftDate);
                    });
            })
            ->get()
            ->toArray();

        $jobLine = JobLine::query()
            ->where('job_id', $shift['job_id'])
            ->with(['job_line_client_requirements_details' => function ($query1) use ($shift_id) {
                $query1->where('job_shift_id', $shift_id);
            }])
            ->get()
            ->toArray();

        $clientJobIds = ClientJob::query()
            ->whereNot('id', $shift['job_id'])
            ->where('client_id', $shift['client_job_details']['client_id'])
            ->pluck('id');

        $available_workers_linked_to_client = ClientJobWorker::query()
            ->whereIn('job_id', $clientJobIds)
            ->whereNotIn('worker_id', $job_shift_worker)
            ->whereNotIn('worker_id', array_column($available_worker, 'worker_id'))
            ->whereNotIn('worker_id', function($query) use ($shiftDate, $shift_id) {
                $query->select('worker_id')
                    ->from('job_shift_workers')
                    ->whereNot('job_shift_id', $shift_id)
                    ->whereDate('shift_date', $shiftDate);
            })
            ->with(['worker', 'rightsToWork', 'absence'])
            ->whereNotNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('archived_at')
            ->whereHas('worker', function ($query) {
                $query->where('status', 'active')
                    ->where('suspend', 'No');
            })
            ->whereHas('rightsToWork', function ($query3) use ($shiftDate) {
                $query3->where(function($query3) use ($shiftDate) {
                    $query3->whereNull('start_date')
                        ->whereDate('end_date', '>=', $shiftDate);
                })->orWhere(function($query3) use ($shiftDate) {
                    $query3->whereDate('start_date', '<=', $shiftDate)
                        ->whereDate('end_date', '>=', $shiftDate);
                })->latest('end_date');
            })
            ->whereDoesntHave('absence', function ($query4) use ($shiftDate) {
                $query4->whereDate('start_date', '<=', $shiftDate)
                    ->whereDate('end_date', '>=', $shiftDate);
            })
            ->get()
            ->unique('worker_id')
            ->values()
            ->toArray();

        $startTimesDrp = [];
        $startDrpOption = strtotime('00:00');
        while ($startDrpOption <= strtotime('23:45')) {
            $startTimesDrp[] = date('H:i', $startDrpOption);
            $startDrpOption = strtotime('+15 minutes', $startDrpOption);
        }

        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();

        $availableWorkerIds = array_column($available_worker, 'worker_id');
        $confirmWorkerIds = array_column($confirm_worker, 'worker_id');
        $otherJobWorkerIds = ClientJobWorker::query()
            ->select('client_job_workers.*')
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM job_shift_workers AS jsw
                WHERE jsw.worker_id = client_job_workers.worker_id
                AND jsw.confirmed_at IS NOT NULL
                AND jsw.shift_date BETWEEN DATE_SUB('$shiftDate', INTERVAL 14 DAY) AND '$shiftDate') AS confirmed_shifts_count")
            )->where('job_id', $shift['job_id'])
            ->whereIn('worker_id', function ($query) use ($shiftDate, $shift) {
                $query->select('worker_id')
                    ->from('job_shift_workers')
                    ->where('shift_date', $shiftDate)
                    ->where('job_shift_id', '!=', $shift['id']);
            })

            ->with(['worker', 'rightsToWork', 'absence'])
            ->whereNotNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('archived_at')
            ->whereHas('worker', function ($query) {
                $query->where('status', 'active')
                    ->where('suspend', 'No');
            })
            ->whereHas('rightsToWork', function ($query3) use ($shiftDate) {
                $query3->where(function($query3) use ($shiftDate) {
                    $query3->whereNull('start_date')
                        ->whereDate('end_date', '>=', $shiftDate);
                })->orWhere(function($query3) use ($shiftDate) {
                    $query3->whereDate('start_date', '<=', $shiftDate)
                        ->whereDate('end_date', '>=', $shiftDate);
                })->latest('end_date');
            })
            ->whereDoesntHave('absence', function ($query4) use ($shiftDate) {
                $query4->whereDate('start_date', '<=', $shiftDate)
                    ->whereDate('end_date', '>=', $shiftDate);
            })
            ->get()
            ->pluck('worker_id');
        $assignedGroups = GroupWithJob::query()
            ->where('job_id', $shift['job_id'])
            ->with([
                'groups' => function ($q) use ($availableWorkerIds, $confirmWorkerIds, $otherJobWorkerIds) {
                    $q->withCount('workers');
                    $q->withCount([
                        'workers as available_workers_count' => function ($sub) use ($availableWorkerIds) {
                            $sub->whereIn('workers.id', $availableWorkerIds);
                        }
                    ]);
                    $q->withCount([
                        'workers as confirm_workers_count' => function ($sub) use ($confirmWorkerIds) {
                            $sub->whereIn('workers.id', $confirmWorkerIds);
                        }
                    ]);
                    $q->withCount([
                        'workers as other_job_workers_count' => function ($sub) use ($otherJobWorkerIds) {
                            $sub->whereIn('workers.id', $otherJobWorkerIds);
                        }
                    ]);
                    $q->with(['workers:id']);
                }
            ])
            ->get();

        // CURRENT DURATION
        $hours = $shift['shift_length_hr'];
        $minutes = round($shift['shift_length_min'] / 15) * 15;
        if ($minutes == 60) {
            $hours += 1;
            $minutes = 0;
        }
        $decimalMinutes = ($minutes / 60) * 1;
        $currentDuration = number_format($hours + $decimalMinutes, 2, '.', '');

        $assignedGroupIds = GroupWithJob::query()->where('job_id', $shift['job_id'])
            ->pluck('group_id')
            ->toArray();
        $group = Group::query()
            ->select(['id','name'])
            ->where('consultant_id', Auth::id())
            ->whereNotIn('id', $assignedGroupIds)
            ->orderBy('name', 'asc')
            ->get();

        $job = [
            'id' => $shift['client_job_details']['id'],
            'client_id' => $shift['client_job_details']['client_id'],
            'site_id' => $shift['client_job_details']['site_id'],
            'name' => $shift['client_job_details']['name'],
            'archived' => $shift['client_job_details']['archived'],
            'client_details' => [
                'company_name' => $shift['client_job_details']['client_details']['company_name'],
                'company_logo' => $shift['client_job_details']['client_details']['company_logo'],
            ],
            'site_details' => [
                'site_name' => $shift['client_job_details']['site_details']['site_name']
            ]
        ];
        $current_shift_id = JobHelper::get_shift_id($job['id']);
        return view('job.view_job_shift', compact('shift',
            'available_worker',
            'confirm_worker',
            'pending_worker',
            'declined_worker',
            'cancelled_worker',
            'ineligibleWorker',
            'jobLine',
            'available_workers_linked_to_client',
            'previous_shift_td',
            'next_shift_td',
            'startTimesDrp',
            'costCentre',
            'assignedGroups',
            'currentDuration',
            'group',
            'pastDateConfirm_worker',
            'job',
            'current_shift_id'
        ));
    }

    public function updateJobShiftBasicDetails(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'shift_start_time'  => 'required',
                'shift_duration_hr'   => 'required|integer|min:1|max:23',
                'shift_duration_min'  => 'required|integer|in:0,15,30,45',
            ],
                [
                    'shift_duration_min.in' => 'The selected shift duration min is invalid. Accept only 0, 15, 30, 45.'
                ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();

            $initialTime = Carbon::parse($params['shift_start_time']);
            $end_time    = $initialTime->copy()->addHours((int) $params['shift_duration_hr'])->addMinutes((int) $params['shift_duration_min']);

            JobShift::query()->where('id', $params['shift_id'])->update([
                'start_time'        => $params['shift_start_time'],
                'end_time'          => $end_time,
                'shift_length_hr'   => $params['shift_duration_hr'],
                'shift_length_min'  => $params['shift_duration_min'],
                'shift_length'      => ((int)$params['shift_duration_hr'] * 60) + (int)$params['shift_duration_min'],
            ]);
            return self::responseWithSuccess('Shift basic details successfully updated.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function addWorkerToJobShift(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->input(), [
                'assign_type'       => 'required',
                'available_worker'  => 'required',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();

            $jobShift = JobShift::query()->where('id', $params['shift_id'])->with('client_job_details')->first();
            if (!$jobShift)
                return self::responseWithError('Job shift not found please try again later');

            $initialTime = Carbon::parse($params['assign_selected_workers_start_time']);
            $durationArray = explode('.', $params['shift_worker_duration']);
            $hours = intval($durationArray[0]);
            $minutes = isset($durationArray[1])
                ? intval(round($durationArray[1] * 60 / 100))
                : 0;
            $end_time = $initialTime->copy()->addHours((int) $hours)->addMinutes((int) $minutes);

            $shiftDate  = $jobShift['date'];
            $startTime  = $initialTime->format('H:i:s');
            $endTime    = $end_time->format('H:i:s');

            // Compare the dates
            $shiftDateCarbon = Carbon::parse($shiftDate);
            $todayDateCarbon = Carbon::parse(date('Y-m-d'));
            if ($todayDateCarbon->greaterThan($shiftDateCarbon))
                return self::responseWithError('You cannot add a worker to a shift in the past');

            foreach ($params['available_worker'] as $workerId) {
                $overlappingShifts = JobShiftWorker::query()->with(['jobShift', 'worker'])->where('worker_id', $workerId)
                    ->where('shift_date', $shiftDate)
                    ->whereHas('jobShift', function ($query) use ($startTime, $endTime) {
                        $query->where(function ($query) use ($startTime, $endTime) {
                            $query->whereBetween('start_time', [$startTime, $endTime])
                                ->orWhereBetween('end_time', [$startTime, $endTime]);
                        })
                            ->orWhere(function ($query) use ($startTime, $endTime) {
                                $query->where('start_time', '<', $startTime)
                                    ->where('end_time', '>', $endTime);
                            });
                    })
                    ->first();

                if ($overlappingShifts) {
                    DB::rollBack();
                    return self::responseWithError($overlappingShifts['worker']['first_name'].' '.$overlappingShifts['worker']['middle_name'].' '.$overlappingShifts['worker']['last_name'].' is assigned to another shift during the specified time.');
                }
            }

            foreach ($params['available_worker'] as $a_row) {
                $jobShiftWorker = JobShiftWorker::query()->create([
                    'job_shift_id' => $params['shift_id'],
                    'worker_id'    => $a_row,
                    'shift_date'   => $jobShift['date'],
                    'assign_type'  => $params['assign_type'],
                    'confirmed_at' => ($params['assign_type'] == 'Direct placement') ? Carbon::now() : null,
                    'job_line_id'  => ($params['assign_selected_workers_job_line'] != '0') ? $params['assign_selected_workers_job_line'] : null,
                    'start_time'   => $startTime,
                    'end_time'     => $endTime,
                    'shift_length_hr' => $hours,
                    'shift_length_min' => $minutes,
                    'shift_length' => ($hours * 60) + $minutes,
                    'duration' => $params['shift_worker_duration']
                ]);

                /*--- BEGIN MAIL CODE ---*/
                ClientHelper::workerAddedIntoJobShiftSendMail($a_row, $params['shift_id'], $jobShiftWorker['id'],  $params['assign_type']);
                /*--- END MAIL CODE ---*/
            }


            DB::commit();
            return self::responseWithSuccess('Worker successfully added in job.');
        } catch (Exception $e) {

            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function confirmJobShiftWorker($id, $status) {
        try {
            $jobShiftWorker = JobShiftWorker::query()->where('id', $id)->first();
            if (!$jobShiftWorker)
                return 'Invalid ID passed please passed valid ID';

            $jobShift = JobShift::query()->where('id', $jobShiftWorker['job_shift_id'])->first();
            if (!$jobShift)
                return 'Job shift not found, please try again later';

            if ($jobShift['cancelled_at'])
                return 'This shift is cancelled, Thank you.';

            $JobShiftWorker = JobShiftWorker::query()->where('id', $id)->first();
            if ($JobShiftWorker['confirmed_at'] != null)
                return 'Invitation already accepted.';

            if ($JobShiftWorker['declined_at'] != null)
                return 'Invitation already declined.';

            if ($status == '1') {
                JobShiftWorker::query()->where('id', $id)->update([
                    'confirmed_at' => Carbon::now(),
                ]);
                //return "Thank's for accepted our invitation.";
                return view('job.shift_invitation_after_action');

            } else if ($status == '0') {
                JobShiftWorker::query()->where('id', $id)->update([
                    'declined_at' => Carbon::now(),
                    'cancelled_by' => 'worker',
                    'cancelled_by_user_id' => $jobShiftWorker['worker_id'],
                ]);
                //return "Invitation successfully declined.";
                return view('job.shift_invitation_after_action');

            } else {
                return 'Invalid status code.';
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function bookingInvitationConfirm(Request $request) {
        try {
            $params = $request->input();
            $jobShiftWorker = JobShiftWorker::query()->where('id', $params['id'])->first();
            if (!$jobShiftWorker)
                return self::responseWithError('Invalid ID passed please passed valid ID');

            $jobShift = JobShift::query()->where('id', $jobShiftWorker['job_shift_id'])->first();
            if (!$jobShift)
                return self::responseWithError('Job shift not found, please try again later');

            if ($jobShift['cancelled_at'])
                return self::responseWithError('This shift is cancelled, Thank you.');

            if ($jobShiftWorker['confirmed_at'] != null)
                return self::responseWithError('Invitation already accepted.');

            if ($jobShiftWorker['declined_at'] != null)
                return self::responseWithError('Invitation already declined.');

            if ($params['status'] == '1') {
                JobShiftWorker::query()->where('id', $params['id'])->update([
                    'confirmed_at' => Carbon::now(),
                    'last_updated_by' => Auth::id(),
                ]);
                return self::responseWithSuccess('Invitation successfully confirmed');

            } else if ($params['status'] == '0') {
                JobShiftWorker::query()->where('id', $params['id'])->update([
                    'declined_at' => Carbon::now(),
                    'cancelled_by' => 'Admin',
                    'cancelled_by_user_id' => $params['auth_id'],
                    'last_updated_by' => Auth::id(),
                ]);
                return self::responseWithSuccess('Invitation successfully declined.');

            } else {
                return self::responseWithError('Invalid status code.');
            }
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function selectedWorkerActionToJobShift(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->input(), [
                'selected_type'   => 'required',
                'selected_worker' => 'required',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            $jobShift = JobShift::query()->where('id', $params['shift_id'])->with('client_job_details')->first();
            if (!$jobShift)
                return self::responseWithError('Job shift not found please try again later');

            if ($params['selected_type'] == 'Confirm invitation') {
                foreach ($params['selected_worker'] as $s_row) {
                    JobShiftWorker::query()->where('job_shift_id', $params['shift_id'])->where('worker_id', $s_row)->update([
                        'confirmed_at' => Carbon::now(),
                        'last_updated_by' => Auth::id(),
                    ]);
                }
                $message = 'Your selected worker successfully confirm.';

            } else if ($params['selected_type'] == 'Unassigned from shift') {
                foreach ($params['selected_worker'] as $s_row) {
                    JobShiftWorker::query()->where('job_shift_id', $params['shift_id'])->where('worker_id', $s_row)->delete();
                }
                $message = 'Your selected worker successfully unassigned from shift.';

            } else if ($params['selected_type'] == 'Cancel workers and send notification') {
                foreach ($params['selected_worker'] as $s_row) {
                    JobShiftWorker::query()->where('job_shift_id', $params['shift_id'])->where('worker_id', $s_row)->update([
                        'cancelled_at' => Carbon::now(),
                        'cancelled_by' => 'admin',
                        'cancelled_by_user_id' => Auth::id(),
                        'last_updated_by' => Auth::id(),
                    ]);
                }
                $message = 'Your selected worker successfully Cancelled.';

            } else if ($params['selected_type'] == 'Cancel invitation') {
                foreach ($params['selected_worker'] as $s_row) {
                    JobShiftWorker::query()->where('job_shift_id', $params['shift_id'])->where('worker_id', $s_row)->update([
                        'cancelled_at' => Carbon::now(),
                        'cancelled_by' => 'admin',
                        'cancelled_by_user_id' => Auth::id(),
                        'last_updated_by' => Auth::id(),
                    ]);
                }
                $message = 'Your selected worker successfully Cancelled.';

            } else if ($params['selected_type'] == 'Unassign from line') {
                foreach ($params['selected_worker'] as $s_row) {
                    JobShiftWorker::query()->where('job_shift_id', $params['shift_id'])->where('worker_id', $s_row)->update([
                        'job_line_id' => null,
                        'last_updated_by' => Auth::id(),
                    ]);
                }
                $message = 'Your selected worker successfully unassign from line.';
            } else if (str_starts_with($params['selected_type'], 'assign_as_')) {
                $lineId = substr($params['selected_type'], 10);
                foreach ($params['selected_worker'] as $s_row) {
                    JobShiftWorker::query()->where('job_shift_id', $params['shift_id'])->where('worker_id', $s_row)->update([
                        'job_line_id' => $lineId,
                        'last_updated_by' => Auth::id(),
                    ]);
                }
                $message = 'Your selected worker successfully assign from line.';
            } else {
                return self::responseWithError('Invalid option, please select valid option.');
            }

            /*else if ($params['selected_type'] == 'Unassigned and remove slot') {
                foreach ($params['selected_worker'] as $s_row) {
                    JobShiftWorker::query()->where('job_shift_id', $params['shift_id'])->where('worker_id', $s_row)->delete();
                }

                $jobShift->update([
                    'number_workers' => (int)$jobShift['number_workers'] - count($params['selected_worker'])
                ]);
                $message = 'Your selected worker successfully unassigned and slot removed.';

            } else if ($params['selected_type'] == 'Cancel workers and remove slot') {
                foreach ($params['selected_worker'] as $s_row) {
                    JobShiftWorker::query()->where('job_shift_id', $params['shift_id'])->where('worker_id', $s_row)->update([
                        'cancelled_at' => Carbon::now(),
                        'cancelled_by' => 'admin',
                        'cancelled_by_user_id' => Auth::id(),
                    ]);
                }

                $jobShift->update([
                    'number_workers' => (int)$jobShift['number_workers'] - count($params['selected_worker'])
                ]);
                $message = 'Your selected worker successfully Cancelled and slot removed.';

            }*/

            DB::commit();
            return self::responseWithSuccess($message);
        } catch (Exception $e) {

            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function manageSlotAction(Request $request) {
        try {
            $rules = [
                'slot_number' => 'required|integer|min:0|not_in:0',
            ];

            $messages = [
                'slot_number.required' => 'Slot number is required.',
                'slot_number.integer' => 'Slot number must be a number.',
                'slot_number.min' => 'Slot number must be greater than or equal to 0.',
                'slot_number.not_in' => 'Slot number cannot be 0.',
            ];

            if ($request->has('line_requirement_number')) {
                $rules["line_requirement_number"] = 'required|array';
                foreach ($request->input('line_requirement_number') as $id => $value) {
                    $rules["line_requirement_number.$id"] = 'required|integer|min:0';

                    $messages["line_requirement_number.$id.required"] = "Line requirement is required.";
                    $messages["line_requirement_number.$id.integer"] = "Line requirement must be a valid number.";
                    $messages["line_requirement_number.$id.min"] = "Line requirement must be at least 0.";
                }
            }

            $validator = Validator::make($request->input(), $rules, $messages);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            if ($request->has('line_requirement_number')) {
                $totalLineRequirements = array_sum($params['line_requirement_number']);
                if ($params['slot_number'] < $totalLineRequirements) {
                    return self::validationError([
                        'slot_number' => [
                            'The worker slot number (' . $params['slot_number'] . ') cannot be less than total of line requirements (' . $totalLineRequirements . ').'
                        ]
                    ]);
                }
            }

            $jobShift = JobShift::query()->where('id', $params['manege_slot_job_shift_id'])->first();
            if (!$jobShift)
                return self::responseWithError('Job shift not found, please try again later');

            $jobShift->update([
                'number_workers' => $params['slot_number']
            ]);
            if ($request->has('line_requirement_number') && $request->input('line_requirement_number')) {
                foreach ($params['line_requirement_number'] as $key => $line_requirement) {
                    JobLineClientRequirement::query()->updateOrCreate(
                        [
                            'job_shift_id' => $params['manege_slot_job_shift_id'],
                            'job_line_id' => $key,
                        ],
                        [
                            'worker_requirement' => $line_requirement ?? 0,
                        ]
                    );
                }
            }
            return self::responseWithSuccess('Worker slot details successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteShiftAction(Request $request) {
        try {
            $params = $request->input();

            $jobShift = JobShift::query()->where('id', $params['shift_id'])->with('client_job_details')->first();
            if (!$jobShift)
                return self::responseWithError('Job shift not found, please try again later.');

            $confirm_worker = JobShiftWorker::query()->where('job_shift_id', $params['shift_id'])->whereNotNull('confirmed_at')->whereNull('declined_at')->whereNull('cancelled_at')->with(['worker', 'rightsToWork'])->get()->toArray();
            $this->job_shift_delete_action($confirm_worker, $jobShift);

            $pending_worker = JobShiftWorker::query()->where('job_shift_id', $params['shift_id'])->whereNull('confirmed_at')->whereNull('declined_at')->whereNull('cancelled_at')->with(['worker', 'rightsToWork'])->get()->toArray();
            $this->job_shift_delete_action($pending_worker, $jobShift);

            $jobShift->update([
                'cancelled_at' => Carbon::now(),
                'cancelled_by_user_id' => Auth::id(),
            ]);
            return self::responseWithSuccess('Shift successfully deleted.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function job_shift_delete_action($worker, $jobShift) {
        foreach ($worker as $row) {
            JobShiftWorker::query()->where('id', $row['id'])->update([
                'cancelled_at'          => Carbon::now(),
                'cancelled_by'          => 'admin',
                'cancelled_by_user_id'  => Auth::id(),
                'last_updated_by' => Auth::id(),
            ]);

            /*--- BEGIN MAIL CODE ---*/
            $worker = Worker::query()->select('id', 'first_name', 'middle_name', 'last_name', 'email_address')->where('id', $row['worker_id'])->first();
            $invitation = (object) [
                'worker_name'       => $worker['first_name'].' '.$worker['middle_name'].' '.$worker['last_name'],
                'job_name'          => $jobShift['client_job_details']['name'],
                'date'              => date('d-m-Y', strtotime($jobShift['date'])),
                'start_time'        => $jobShift['start_time'],
                'shift_length_hr'   => $jobShift['shift_length_hr'],
                'shift_length_min'  => $jobShift['shift_length_min'],
            ];

            Mail::to($worker['email_address'])->send(new JobShiftCancelEmail($invitation));
            /*--- END MAIL CODE ---*/
        }
    }

    public function jobManagement() {
        $client = Client::query()->orderBy('company_name')->get();
        return view('job.dis_all_jobs', compact('client'));
    }

    public function exportBookingCalendarSheetConfirmWorker($job_shift_id) {
        $shift = JobShift::query()->where('id', $job_shift_id)->with(['client_job_details'])->first();
        $file_name = str_replace([' ', '/', '\\'], ['_', '-', '-'],
            strtolower('booking_sheet-'.date('d_m_Y', strtotime($shift['date'])).'-'.$shift['client_job_details']['name'].'-'.$shift['client_job_details']['site_details']['site_name'].'-'.$shift['client_job_details']['client_details']['company_name'].'.csv')
        );

        $array = ['job_shift_id' => $job_shift_id];
        return (new BookingConfirmWorkerExport($array))->download($file_name);
    }

    public function bulkExportBookingCalendarSheetConfirmWorker(Request $request) {
        $params = $request->input();
        $job_id = $params['job_id'];

        $payroll_start_date_column = $params['payroll_week_starts'].'_payroll_start';
        $payroll_end_date_column = $params['payroll_week_starts'].'_payroll_end';

        $pwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
            ->where('payroll_week_number', $params['wa_week_number'])
            ->where('year', $params['wa_week_year'])
            ->first();

        if (!$pwData) {
            return self::responseWithError('Payroll week number data not available.');
        }

        $startDate = Carbon::parse($pwData[$payroll_start_date_column]);
        $jobIds = [];
        for ($i = 0; $i < 7; $i++) {
            $shiftDate = $startDate->copy()->addDays((int) $i)->format('Y-m-d');

            $jobShift = JobShift::query()->where('job_id', $job_id)
                ->where('date', $shiftDate)
                ->first();

            if ($jobShift) {
                $jobIds[] = $jobShift['id'];
            }
        }

        if (!$jobIds) {
            return self::responseWithError('Job shift not found for this  payroll week.');
        }

        return self::responseWithSuccess('Job shift found for export data.', $jobIds);
    }

    public function restoreDeclinedCancelledWorker(Request $request) {
        try {
            $params = $request->input();
            $jobShiftWorker = JobShiftWorker::query()->where('id', $params['job_shit_worker_id'])->first();
            if (!$jobShiftWorker) {
                return self::responseWithError('Job shift worker not found, please try again later.');
            }

            if ($params['job_shit_worker_status'] == 'declined') {
                $jobShiftWorker->update([
                    'declined_at' => null,
                    'last_updated_by' => Auth::id(),
                ]);
                return self::responseWithSuccess('Worker successfully restore.');
            } elseif ($params['job_shit_worker_status'] == 'cancelled') {
                $jobShiftWorker->update([
                    'cancelled_at' => null,
                    'cancelled_by' => null,
                    'cancelled_by_user_id' => null,
                    'last_updated_by' => Auth::id(),
                ]);
                return self::responseWithSuccess('Worker successfully restore.');
            } else {
                return self::responseWithError('Invalid status passed, please pass valid status.');
            }
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function linkedToClientWorkerAddIntoJob(Request $request) {
        try {
            $params = $request->input();
            $jobShift = JobShift::query()->where('id', $params['shift_id'])->first();
            if (!$jobShift) {
                return self::responseWithError('Job shift worker not found, please try again later.');
            }

            if (!$request->has('available_worker_add_to_job')) {
                return self::responseWithError('Please select a available workers.');
            }

            foreach ($params['available_worker_add_to_job'] as $worker_id) {
                $ClientJobWorker = ClientJobWorker::query()->create([
                    'job_id'                    => $jobShift['job_id'],
                    'worker_id'                 => $worker_id,
                    'invitation_type'           => 2,
                    'confirmed_at'              => Carbon::now(),
                    'confirmed_by_admin_user_id'=> Auth::id(),
                ]);

                /*--- BEGIN MAIL CODE ---*/
                ClientHelper::workerAddedIntoJobSendMail($worker_id, $jobShift['job_id'], $ClientJobWorker['id'], 2);
                /*--- END MAIL CODE ---*/
            }

            return self::responseWithSuccess('Worker successfully added into job.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function copyJobShift(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->input(), [
                'shift_start_date'  => 'required',
            ]);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            $jobShift = JobShift::query()->where('id',$params['copy_job_shift_id'])
                ->with(['Job_line_client_requirement_details','JobShiftWorker_details' => function ($query) {
                    $query->whereNotNull('confirmed_at');
                    $query->whereNull('cancelled_at');
                    $query->whereNull('declined_at');
                }])
                ->first();
            if (!$jobShift) {
                throw new \Exception('Job shift data not found, please try again later.');
            }

            $startDate = Carbon::parse($params['shift_start_date']);
            if (empty($params['shift_end_date'])) {
                $checkDate = $startDate->format('Y-m-d');
                if (JobShift::query()->where('job_id', $jobShift['job_id'])
                    ->where('date', $checkDate)
                    ->exists()) {
                    throw new \Exception('Job shift already exist in '.Carbon::parse($checkDate)->format('d/m/Y').'.');
                }
                $this->copyShift($jobShift, $checkDate);
            } else {
                $endDate = Carbon::parse($params['shift_end_date']);
                while ($startDate->lte($endDate)) {
                    $this->copyShift($jobShift, $startDate->format('Y-m-d'));
                    $startDate->addDay();
                }
            }

            DB::commit();
            return self::responseWithSuccess('Copy shift process has been completed.');
        } catch (\Exception $e) {

            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    private function copyShift($jobShift, $shiftDate) {
        $checkJobShift = JobShift::query()->where('job_id', $jobShift['job_id'])
            ->where('date', $shiftDate)
            ->first();
        if (!$checkJobShift) {
            $createdShift = JobShift::query()->create([
                'job_id' => $jobShift['job_id'],
                'date' => $shiftDate,
                'start_time' => $jobShift['start_time'],
                'end_time' => $jobShift['end_time'],
                'shift_length_hr' => $jobShift['shift_length_hr'],
                'shift_length_min' => $jobShift['shift_length_min'],
                'shift_length' => $jobShift['shift_length'],
                'number_workers' => $jobShift['number_workers'],
            ]);

            if($jobShift['Job_line_client_requirement_details']) {
                foreach ($jobShift['Job_line_client_requirement_details'] as $job_line_client_requirements) {
                    JobLineClientRequirement::query()->create([
                        'job_shift_id' => $createdShift->id,
                        'job_line_id' => $job_line_client_requirements['job_line_id'],
                        'worker_requirement' => $job_line_client_requirements['worker_requirement']
                    ]);
                }
            }

            foreach ($jobShift->JobShiftWorker_details as $workerDetail) {
                $existingWorkerShift = JobShiftWorker::query()->where('worker_id', $workerDetail->worker_id)
                    ->where('shift_date', $shiftDate)
                    ->exists();

                if (!$existingWorkerShift) {
                    JobShiftWorker::query()->create([
                        'job_shift_id' => $createdShift->id,
                        'worker_id' => $workerDetail->worker_id,
                        'shift_date' => $shiftDate,
                        'confirmed_at' => Carbon::now(),
                        'invited_at' => Carbon::now(),
                        'assign_type' => 'Direct placement',
                        'job_line_id' => $workerDetail->job_line_id,
                        'start_time' => $workerDetail->start_time,
                        'end_time' => $workerDetail->end_time,
                        'shift_length_hr' => $workerDetail->shift_length_hr,
                        'shift_length_min' => $workerDetail->shift_length_min,
                        'shift_length' => $workerDetail->shift_length,
                        'duration' => $workerDetail->duration
                    ]);
                }
            }
        }
    }

    public function copyJobShiftInWorkerAvailability(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->input(), [
                'date' => 'required',
            ]);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            $payroll_start_date_column = $params['payroll_week_starts'].'_payroll_start';
            $payroll_end_date_column = $params['payroll_week_starts'].'_payroll_end';

            $pwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
                ->where('payroll_week_number', $params['week_number'])
                ->where('year', $params['week_year'])
                ->first();

            if (!$pwData) {
                throw new \Exception('Payroll week data not found.');
            }

            $futurePwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
                ->where($payroll_start_date_column, Carbon::parse($params['date'])->format('Y-m-d'))
                ->first();

            if (!$futurePwData) {
                throw new \Exception('Selected payroll week data not found.');
            }

            $futureShiftWeekDate = [];
            $futureShiftWeekStartDate = Carbon::parse($futurePwData[$payroll_start_date_column]);
            $futureShiftWeekEndDate = Carbon::parse($futurePwData[$payroll_end_date_column]);
            while ($futureShiftWeekStartDate->lte($futureShiftWeekEndDate)) {
                $futureDate = $futureShiftWeekStartDate->format('Y-m-d');
                if (JobShift::query()
                    ->where('job_id', $params['job_id'])
                    ->where('date',$futureDate)
                    ->exists()) {
                    throw new \Exception('Cannot copy shifts - destination week already has at least one shift in it. You can only copy shifts into empty weeks.');
                }

                $futureShiftWeekDate[] = $futureDate;
                $futureShiftWeekStartDate->addDay();
            }

            $copyShiftWeekStartDate = Carbon::parse($pwData[$payroll_start_date_column]);
            $copyShiftWeekEndDate = Carbon::parse($pwData[$payroll_end_date_column]);
            $index = 0;
            while ($copyShiftWeekStartDate->lte($copyShiftWeekEndDate)) {

                $jobShift = JobShift::query()
                    ->where('job_id', $params['job_id'])
                    ->where('date',$copyShiftWeekStartDate->format('Y-m-d'))
                    ->with(['Job_line_client_requirement_details','JobShiftWorker_details' => function ($query) {
                        $query->whereNotNull('confirmed_at');
                        $query->whereNull('cancelled_at');
                        $query->whereNull('declined_at');
                    }])
                    ->first();

                if ($jobShift) {
                    $this->copyShift($jobShift, $futureShiftWeekDate[$index]);
                }

                $copyShiftWeekStartDate->addDay();
                $index++;
            }

            DB::commit();
            return self::responseWithSuccess('Copy shift process has been completed.');
        } catch (\Exception $e) {

            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }
}
