<?php

namespace App\Http\Controllers\Timesheet;

use App\Http\Controllers\Controller;
use App\Models\Client\ClientJobWorker;
use App\Models\Job\JobShift;
use App\Models\Job\JobShiftWorker;
use App\Models\Timesheet\DraftTimesheet;
use App\Models\Timesheet\Timesheet;
use App\Models\Worker\RightsToWork;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DraftTimesheetController extends Controller
{
    use JsonResponse;

    public function index($job_shift_id) {
        $shift = JobShift::query()->where('id', $job_shift_id)->with(['client_job_details'])->first();

        $startTime = Carbon::parse($shift['start_time']);
        $endTime = Carbon::parse($shift['end_time']);
        $hoursWorked = $startTime->floatDiffInHours($endTime); //$endTime->floatDiffInHours($startTime);

        $clientJobWorker = ClientJobWorker::query()->where('job_id', $shift['job_id'])
            ->whereNotNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('archived_at')
            ->with('worker')
            ->get();
        return view('timesheet.dis_draft_timesheet', compact('shift', 'hoursWorked', 'clientJobWorker'));
    }

    public function getDraftTimesheetEntries(Request $request) {
        $timesheet = DraftTimesheet::query()->where('job_shift_id', $request->input('job_shift_id'))
            ->with('worker_details')
            ->get();

        $array = [];
        if ($timesheet) {
            foreach ($timesheet as $row) {
                $worker_name = $row['worker_details']['first_name'].' '.$row['worker_details']['middle_name'].' '.$row['worker_details']['last_name'];
                $start_time = ($row['in_time'] != '00:00:00') ? date('H:i', strtotime($row['in_time'])) : '';
                $date = date('d/m/Y', strtotime($row['date']));
                $hours = number_format($row['hours_worked'], 2);
                $array[] = [
                    'worker' => $worker_name,
                    'start_time' => $start_time,
                    'hours_worked' => $hours,
                    'action' => $this->timesheetAction($row['id'], $worker_name, $date, $hours, $start_time),
                ];
            }
        }
        return [
            'draw'              => 1,
            'recordsTotal'      => count($timesheet),
            'recordsFiltered'   => count($timesheet),
            'data'              => $array
        ];
    }

    private function timesheetAction($id, $worker_name, $date, $hour, $start_time) {
        return '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-2" id="delete_draft_timesheet" data-id="'.$id.'">
                <i class="fs-2 las la-trash"></i>
            </a>
            <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" id="edit_draft_timesheet" data-id="'.$id.'" data-worker="'.$worker_name.' ('.$date.')" data-hours="'.$hour.'" data-start_time="'.$start_time.'">
                <i class="fs-2 las la-edit"></i>
            </a>';
    }

    public function createDraftTimesheetEntries(Request $request) {
        try {
            $job_shift_id = $request->input('job_shift_id');

            $draftTimesheetWorkerId = DraftTimesheet::query()->where('job_shift_id', $job_shift_id)->get()->pluck('worker_id');
            $confirmedWorker = JobShiftWorker::query()->where('job_shift_id', $job_shift_id)
                ->whereNotNull('confirmed_at')
                ->whereNull('declined_at')
                ->whereNull('cancelled_at')
                ->whereNotIn('worker_id', $draftTimesheetWorkerId)
                ->with('jobShift')
                ->get();

            $insertArray = [];
            foreach ($confirmedWorker as $row) {
                $startTime = Carbon::parse($row['jobShift']['start_time']);
                $endTime = Carbon::parse($row['jobShift']['end_time']);
                $hoursWorked = $startTime->floatDiffInHours($endTime);

                $insertArray[] = [
                    'job_shift_id'  => $job_shift_id,
                    'date'          => $row['shift_date'],
                    'job_id'        => $row['jobShift']['job_id'],
                    'worker_id'     => $row['worker_id'],
                    'hours_worked'  => $hoursWorked,
                    'in_time'       => $row['jobShift']['start_time'],
                    'out_time'      => $row['jobShift']['end_time'],
                ];
            }

            if ($insertArray) {
                DraftTimesheet::query()->insert($insertArray);
                return self::responseWithSuccess('Timesheet entry successfully created.');
            } else {
                return self::responseWithSuccess('There are currently no timesheet entries available for create.');
            }
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function addDraftTimesheetAction(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'add_timesheet_worker' => 'required',
                'add_timesheet_start_time' => 'required',
                'add_hours_worked' => 'required|numeric|min:0',
            ],[
                'add_timesheet_worker.required' => 'The worker field is required.',
                'add_timesheet_start_time.required' => 'The start time field is required.',
                'add_hours_worked.required' => 'The hours worked field is required.',
                'add_hours_worked.numeric' => 'The hours worked must be a number..',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();

            $checkDuplicateEntry = DraftTimesheet::query()->where('job_shift_id', $params['job_shift_id'])
                ->where('worker_id', $params['add_timesheet_worker'])
                ->exists();
            if ($checkDuplicateEntry) {
                return self::responseWithError('Duplicate row - ignored');
            }

            $inTime = Carbon::parse($request->input('add_timesheet_start_time'));
            $hoursWorked = $request->input('add_hours_worked');
            $outTime = $inTime->copy()->addHours(floor($hoursWorked))->addMinutes(($hoursWorked - floor($hoursWorked)) * 60);

            DraftTimesheet::query()->create([
                'job_shift_id'  => $params['job_shift_id'],
                'date'          => $params['job_shift_date'],
                'job_id'        => $params['job_id'],
                'worker_id'     => $params['add_timesheet_worker'],
                'hours_worked'  => $params['add_hours_worked'],
                'in_time'       => $inTime->format('H:i'),
                'out_time'      => $outTime->format('H:i'),
            ]);

            return self::responseWithSuccess('Timesheet entry successfully created.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteDraftTimesheetEntry($timesheet_id) {
        try {
            $timesheetEntry = DraftTimesheet::query()->where('id', $timesheet_id)->first();
            if (!$timesheetEntry)
                return self::responseWithError('Timesheet entry not found, please try again later.');

            $timesheetEntry->delete();
            return self::responseWithSuccess('Timesheet entry successfully deleted.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function editDraftTimesheetAction(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'timesheet_id' => 'required',
                'hours_worked' => 'required|numeric|min:0',
                'timesheet_start_time' => 'required|date_format:H:i',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $timesheetEntry = DraftTimesheet::query()->where('id', $request->input('timesheet_id'))->first();
            if (!$timesheetEntry)
                return self::responseWithError('Timesheet entry not found, please try again later.');


            $inTime = Carbon::parse($request->input('timesheet_start_time'));
            $hoursWorked = $request->input('hours_worked');
            $outTime = $inTime->copy()->addHours(floor($hoursWorked))->addMinutes(($hoursWorked - floor($hoursWorked)) * 60);

            $timesheetEntry->update([
                'hours_worked' => $request->input('hours_worked'),
                'in_time' => $inTime->format('H:i'),
                'out_time' => $outTime->format('H:i')
            ]);
            return self::responseWithSuccess('Timesheet entry successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function checkTimesheetEntriesValidation(Request $request) {
        try {
            $timesheet = DraftTimesheet::query()->where('job_shift_id', $request->input('job_shift_id'))
                ->with(['worker_details', 'job_details'])
                ->get();

            $array = [];
            $draft_timesheet_ids = [];
            if ($timesheet) {
                foreach ($timesheet as $row) {
                    $date = date('Y-m-d', strtotime($row['date']));
                    $ready_for_create = 'Y';
                    $error = '-';

                    $isDuplicateEntry = Timesheet::query()->where('date', $date)
                        ->where('worker_id', $row['worker_details']['id'])
                        ->where('job_id', $row['job_id'])
                        ->where('in_time', $row['in_time'])
                        ->exists();

                    $rtw = RightsToWork::query()->where('worker_id', $row['worker_details']['id'])
                        ->whereDate('end_date', '>=', $date)
                        ->first();

                    if ($isDuplicateEntry) {
                        $ready_for_create = '<span class="text-danger">N</span>';
                        $error = '<span class="text-danger">duplicate row - ignored</span>';

                    } elseif ($row['job_details']['archived'] == 1) {
                        $ready_for_create = '<span class="text-danger">N</span>';
                        $error = '<span class="text-danger">Job is archived</span>';

                    } elseif ($row['job_details']['end_date'] && $date > $row['job_details']['end_date']) {
                        $ready_for_create = '<span class="text-danger">N</span>';
                        $error = '<span class="text-danger">Shift date exceeds job end date</span>';

                    } elseif (!$rtw) {
                        $ready_for_create = '<span class="text-danger">N</span>';
                        $error = '<span class="text-danger">No valid RTW found</span>';
                    }

                    if($ready_for_create == 'Y')
                        $draft_timesheet_ids[] = $row['id'];

                    $array[] = [
                        'worker' => $row['worker_details']['first_name'].' '.$row['worker_details']['middle_name'].' '.$row['worker_details']['last_name'],
                        'start_time' => ($row['in_time'] != '00:00:00') ? date('H:i', strtotime($row['in_time'])) : '',
                        'hours_worked' => number_format($row['hours_worked'], 2),
                        'ready_for_create' => $ready_for_create,
                        'error' => $error,
                    ];
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($timesheet),
                'recordsFiltered'   => count($timesheet),
                'data'              => $array,
                'draft_timesheet_ids' => $draft_timesheet_ids
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function createTimesheetUsingDraftTimesheetEntries(Request $request) {
        try {
            if (!$request->input('draft_timesheet_record_ids')) {
                return self::responseWithError('There are currently no draft timesheet entries available for create.');
            }

            $timesheet = DraftTimesheet::query()->select(['worker_id', 'job_id', 'date', 'hours_worked', 'in_time', 'out_time'])
                ->whereIn('id', $request->input('draft_timesheet_record_ids'))
                ->get()
                ->toArray();

            if ($timesheet) {
                Timesheet::query()->insert($timesheet);
                return self::responseWithSuccess('Timesheet entry successfully created.');
            }

            return self::responseWithError('There are currently no draft timesheet entries available for create.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
