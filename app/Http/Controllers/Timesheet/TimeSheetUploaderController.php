<?php

namespace App\Http\Controllers\Timesheet;

use App\Exports\TimesheetExport;
use App\Http\Controllers\Controller;
use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Client\ClientJobWorker;
use App\Models\Client\Site;
use App\Models\Client\SiteWeekLock;
use App\Models\Job\JobLine;
use App\Models\Job\JobShift;
use App\Models\Job\JobShiftWorker;
use App\Models\Payroll\PayrollWeekDate;
use App\Models\Timesheet\Timesheet;
use App\Models\Worker\RightsToWork;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TimeSheetUploaderController extends Controller
{
    use JsonResponse;
    public function index() {
        $worker = Worker::query()->select('id', 'first_name', 'last_name')->where('status', 'Active')->get();
        return view('timesheet.timesheet_uploader', compact('worker'));
    }

    public function timesheetUploader(Request $request) {
        $validator = Validator::make($request->all(), ['timesheet_file' => 'required|mimes:csv,txt']);
        if($validator->errors()->messages())
            return self::validationError($validator->errors()->messages());

        /*--- begin checkLead ---*/
        $fileExtension = $request->file('timesheet_file')->getClientOriginalExtension();
        if($fileExtension !== 'csv') {
            return self::validationError(['timesheet_file' => ['Invalid file format - '.$fileExtension]]);
        }

        $reportArray = [];
        $ReadFile    = fopen($request->file('timesheet_file'), 'r');
        $headerRow = fgetcsv($ReadFile);
        $totalColumns = count($headerRow);

        $fileType = $request->input('timesheet_file_type');

        if (($totalColumns == 5 && $fileType == 'completed_booking_sheet') || ($totalColumns == 14 && $fileType == 'simple_timesheet')) {
            return self::validationError(['timesheet_file' => ['uploaded file does not match selected timesheet format. Please check and try again']]);
        }

        $mapping = [
            'completed_booking_sheet' => [0, 1, 5, 12, 13],
            'default'                 => [0, 1, 2, 3, 4],
        ];
        $indices = $mapping[$fileType] ?? $mapping['default'];

        while (($row = fgetcsv($ReadFile)) !== false) {

            if (array_intersect_key($row, array_flip($indices)) !== array_filter(array_intersect_key($row, array_flip($indices)))) {
                continue;
            }

            $array = array_intersect_key($row, array_flip($indices));
            $array[] = ($fileType == 'completed_booking_sheet') ? $row[14] : $row[5];
            $array = array_values($array);

            $cJob = ClientJob::query()->where('id', $array[1])->with(['site_details', 'client_details'])->first();
            $cWorker = Worker::query()->where('worker_no', $array[2])->first();

            $workedMinutes  = $array[4] * 60;
            $inTimestamp    = strtotime($array[3]);
            $outTimestamp   = $inTimestamp + ($workedMinutes * 60);

            $array[3] = date('H:i:s', $inTimestamp);
            $array[5] = date('H:i:s', $outTimestamp);
            $array[6] = $row[5];

            $rowReport = [
                date('d-m-Y', strtotime(str_replace('/','-',$array[0]))),
                $array[1],
                $array[2],
                date('H:i', $inTimestamp),
                $array[4],
                ($cWorker) ? $cWorker['first_name'].' '.$cWorker['middle_name'].' '.$cWorker['last_name'] : '-',
                ($cJob) ? $cJob['name'] : '-',
                ($cJob) ? ($cJob['client_details']) ? $cJob['client_details']['company_name'] : '-' : '-',
                ($cJob) ? ($cJob['site_details']) ? $cJob['site_details']['site_name'] : '-' : '-',
                'Y',
                ''
            ];

            try {
                $this->processRow($array);
            } catch (\Exception $e) {
                $rowReport[9] = 'N';
                $rowReport[10] = $e->getMessage();
            }

            $reportArray[] = $rowReport;
        }
        fclose($ReadFile);

        return self::responseWithSuccess('The timesheet uploading process has been completed.', [
            'reportArray'   => array_merge([['Date', 'Job ID', 'Worker ID', 'Start Time', 'Hours', 'Worker name', 'Job', 'Client', 'Site', 'Created', 'Error']], $reportArray),
            'table'         => $this->tableView($reportArray)
        ]);
    }

    private function processRow($row) {
        $date           = date('Y-m-d', strtotime(str_replace('/','-',$row[0])));
        $jobId          = $row[1];
        $workerID       = $row[2];
        $hrs_worked     = $row[4];
        $inTime         = $row[3];
        $outTime        = $row[5];
        $line_code      = ($row[6]) ? $row[6] : null;

        if (!$row[0]) {
            throw new \Exception('Date not define - ignored');
        }

        // Check job line
        if ($line_code) {
            $jobLine = JobLine::query()->where('line_code', $line_code)
                ->where('job_id', $jobId)
                ->first();
            if (!$jobLine) {
                throw new \Exception('No such line code for this job');
            }
        } else {
            $jobLine = null;
        }

        if (!$hrs_worked) {
            throw new \Exception('Worked hours not define - ignored');
        }

        if (!$hrs_worked) {
            throw new \Exception('Worked hours not define - ignored');
        }

        if (strtotime($date) > time()) {
            throw new \Exception('Date in future - row ignored');
        }

        if (($inTime && !$outTime) || (!$inTime && $outTime)) {
            throw new \Exception("Both in time and out time are required if one is filled.");
        }

        // Step 3: Validate worker_id
        $workerExists = Worker::query()->select('id', 'worker_no')->where('worker_no', $workerID)->first();
        if (!$workerExists) {
            throw new \Exception('Worker id does not exist');
        }

        // Step 1: Check for duplicate entries
        $isDuplicateEntry = Timesheet::query()->where('date', $date)
            ->where('worker_id', $workerExists['id'])
            ->where('job_id', $jobId)
            ->where('in_time', $inTime)
            //->where('out_time', $outTime)
            ->exists();

        if ($isDuplicateEntry) {
            throw new \Exception('duplicate row - ignored');
        }

        // Step 2: Validate job_id
        $jobExists = ClientJob::query()->where('id', $jobId)->first();
        if (!$jobExists) {
            throw new \Exception('Job id does not exist');
        }

        // Step 4: Check if worker is linked to the job
        $isValidJobWorker = ClientJobWorker::query()->where('worker_id', $workerExists['id'])
            ->where('job_id', $jobId)
            ->exists();
        if (!$isValidJobWorker) {
            throw new \Exception('Worker not linked to job');
        }

        // Step 5: Check job status
        if ($jobExists['archived'] == 1) {
            throw new \Exception('Job is archived');
        }

        // Step 6: Check date against job end_date
        if ($jobExists['end_date'] && $date > $jobExists['end_date']) {
            throw new \Exception("Shift date exceeds job end date");
        }

        $rtw = RightsToWork::query()->where('worker_id', $workerExists['id'])
            ->whereDate('end_date', '>=', $date)
            ->first();
        if (!$rtw) {
            throw new \Exception("No valid RTW found");
        }

        // If all checks pass, proceed with creating the timesheet record
        Timesheet::query()->create([
            'date'          => $date,
            'job_id'        => $jobId,
            'worker_id'     => $workerExists['id'],
            'hours_worked'  => $hrs_worked,
            'in_time'       => $inTime,
            'out_time'      => $outTime,
            'job_line_id'   => ($jobLine) ? $jobLine->id : null
        ]);
    }

    private function tableView($reportArray) {
        $table = '<table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="jobs_datatable">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Date</th>
                            <th>Job ID</th>
                            <th>Worker ID</th>
                            <th>Start Time</th>
                            <th>Hours</th>
                            <th>Worker name</th>
                            <th>Job</th>
                            <th>Client</th>
                            <th>Site</th>
                            <th>Created</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">';
        if ($reportArray) {
            foreach ($reportArray as $reportRow) {
                $textDanger = ($reportRow[10]) ? 'text-danger' : '';
                $table .= '<tr>
                                <td>'.$reportRow[0].'</td>
                                <td>'.$reportRow[1].'</td>
                                <td>'.$reportRow[2].'</td>
                                <td>'.$reportRow[3].'</td>
                                <td>'.$reportRow[4].'</td>
                                <td>'.$reportRow[5].'</td>
                                <td>'.$reportRow[6].'</td>
                                <td>'.$reportRow[7].'</td>
                                <td>'.$reportRow[8].'</td>
                                <td class="'.$textDanger.'">'.$reportRow[9].'</td>
                                <td class="'.$textDanger.'">'.$reportRow[10].'</td>
                          </tr>';
            }
        }
        $table .= '</tbody></table>';

        return $table;
    }

    public function timesheetEditor() {
        $client = Client::query()->orderBy('company_name')->get();
        $payroll_week_number = PayrollWeekDate::query()->get();
        return view('timesheet.timesheet_editor', compact('client', 'payroll_week_number'));
    }

    public function getTimesheetEditorShiftLineItemData(Request $request) {
        try {
            $params = $request->input();

            $client_and_site_name = '';
            $client_logo_url = '';
            $period_date = '';
            $week_number = '';
            $pay_date = '';
            $payroll_created_at = '';
            $view_report_url_href = '';
            $timesheetData = [];
            $array = [];

            $client = Client::query()->select('id', 'company_name', 'company_logo', 'payroll_week_starts')
                ->where('id', $params['client'])
                ->first();
            if ($client) {
                $client_and_site_name = $client['company_name'];

                $site = Site::query()->select(['id', 'site_name', 'client_id'])
                    ->where('id', $params['site'])
                    ->with('job_details')
                    ->first()
                    ->toArray();
                if($site) {
                    $client_and_site_name = $client_and_site_name.' > '.$site['site_name'];
                    $client_logo_url = asset('workers/client_document/'.$client['company_logo']);

                    $payroll_start_date_column = $client['payroll_week_starts'].'_payroll_start';
                    $payroll_end_date_column = $client['payroll_week_starts'].'_payroll_end';

                    $pwdNode = explode('_', $params['pwn']);
                    $pwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', 'year', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
                        ->where('payroll_week_number', $pwdNode[0])
                        ->where('year', $pwdNode[1])
                        ->first();
                    if($pwData) {
                        $period_date = date('d/m/Y', strtotime($pwData[$payroll_start_date_column])).' - '.date('d/m/Y', strtotime($pwData[$payroll_end_date_column]));
                        $week_number = '(Week '.$pwData['payroll_week_number'].')';
                        $pay_date = date('d/m/Y', strtotime($pwData['pay_date']));
                        $view_report_url_href = url('view-payroll-report?payroll='.$site['client_id'].'.'.$site['id'].'.'.$params['pwn']);

                        if ($site['job_details']) {
                            $siteWeekLock = SiteWeekLock::query()->where('site_id', $params['site'])->where('payroll_week', str_replace('_', '-', $params['pwn']))->first();
                            $payroll_created_at = ($siteWeekLock) ? date('d/m/Y H:i', strtotime($siteWeekLock['created_at'])) : '';

                            $timesheetData = Timesheet::query()->whereIn('job_id', array_column($site['job_details'], 'id'))
                                ->whereBetween('date', [$pwData[$payroll_start_date_column], $pwData[$payroll_end_date_column]])
                                ->with(['worker_details', 'job_details', 'shift_details'])
                                ->when($siteWeekLock && $params['type'] === 'lock', fn($query) => $query->whereNotNull('locked_at'))
                                ->when($siteWeekLock && $params['type'] === 'unlock', fn($query) => $query->whereNull('locked_at'))
                                ->get()
                                ->toArray();

                            $array = [];
                            if ($timesheetData) {
                                //$demoDate = ['30/07/2025','01/08/2025','02/08/2025','03/08/2025','30/09/2025','01/10/2025'];
                                //$demoDate = ['2025/07/30','2025/08/01','2025/08/02','2025/08/03','2025/09/30','2025/10/01'];
                                foreach ($timesheetData as $row) {
                                    //$randomDate = $demoDate[array_rand($demoDate)];
                                    $shiftDetail = collect($row['shift_details'])->firstWhere('date', $row['date']);
                                    $worker_name = $row['worker_details']['first_name'].' '.$row['worker_details']['middle_name'].' '.$row['worker_details']['last_name'];
                                    $date = date('d/m/Y', strtotime($row['date']));
                                    $hours = number_format($row['hours_worked'], 2);

                                    $start_time = ($row['in_time'] != '00:00:00') ? date('H:i', strtotime($row['in_time'])) : '';
                                    $array[]     = [
                                        'id'            => $row['id'],
                                        'worker_name'   => ($row['worker_details']) ? '<span>'.$worker_name.'<br><small class="text-muted">'.$row['worker_details']['worker_no'].'</small></small></span>' : '',
                                        'date'          => $date,
                                        'job'           => $row['job_details']['name'],
                                        'start_time'    => $start_time,
                                        'hours'         => $hours,
                                        'edited'        => ($row['edited_at']) ? '<a href="javascript:;" title="'.date('d/m/Y H:i:s', strtotime($row['edited_at'])).'"><i class="bi bi-circle-fill text-warning"></i></a>' : '<i class="bi bi-circle text-muted"></i>',
                                        'action'        => (!$row['locked_at'] || $payroll_created_at == '') ? $this->timesheetAction($row['id'], $worker_name, $date, $hours, $payroll_created_at, $start_time) : '',
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($timesheetData),
                'recordsFiltered'   => count($timesheetData),
                'data'              => $array,

                'client_and_site_name'  => $client_and_site_name,
                'client_logo_url'       => $client_logo_url,
                'period_date'           => $period_date,
                'week_number'           => $week_number,
                'pay_date'              => $pay_date,
                'total_hrs'             => ($timesheetData) ? number_format(collect(array_column($timesheetData, 'hours_worked'))->sum(), 2) : 0,
                'payroll_created_at'    => $payroll_created_at,
                'view_report_url_href'  => $view_report_url_href,
                'array_ids'             => ($array) ? implode(',', array_column($array, 'id')) : '',
                'add_or_update_timesheet_report_btn' => (Carbon::createFromFormat('d/m/Y', $pay_date)->isFuture())
                    ? '<button type="button" class="btn btn-outline btn-outline-primary text-hover-white add-and-update-ignore-entry" data-type="timesheet">Add and update payroll report</button>'
                    : '',
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getTimesheetEditorTotalHourPerWorkerData(Request $request) {
        try {

            $params = $request->input();

            $client = Client::query()->select('id', 'company_name', 'company_logo', 'payroll_week_starts')->where('id', $params['client'])->first();
            if (!$client)
                throw new \Exception('Client not found, please try again later.');;

            $site = Site::query()->select(['id', 'site_name'])->where('id', $params['site'])->with('job_details')->first()->toArray();
            if(!$site)
                throw new \Exception('Site not found, please try again later.');

            $payroll_start_date_column = $client['payroll_week_starts'].'_payroll_start';
            $payroll_end_date_column = $client['payroll_week_starts'].'_payroll_end';

            $pwdNode = explode('_', $params['pwn']);
            $pwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
                ->where('payroll_week_number', $pwdNode[0])
                ->where('year', $pwdNode[1])
                ->first();
            if(!$pwData)
                throw new \Exception('Payment week number data not found, please try again later.');

            if (!$site['job_details'])
                throw new \Exception('Job details not found, please try again later.');

            $jobIds = array_column($site['job_details'], 'id');
            $clientJobWorkerId = ClientJobWorker::query()->whereIn('job_id', $jobIds)->whereNotNull('confirmed_at')->pluck('worker_id');
            $worker = Worker::query()->select('id', 'first_name', 'middle_name', 'last_name', 'worker_no')->whereIn('id', $clientJobWorkerId)->get();

            $array = [];
            if ($worker) {
                foreach ($worker as $row) {
                    $timesheetData = Timesheet::query()->where('worker_id', $row['id'])
                        ->whereIn('job_id', $jobIds)
                        ->whereBetween('date', [$pwData[$payroll_start_date_column], $pwData[$payroll_end_date_column]])
                        ->get()
                        ->toArray();

                    if ($timesheetData) {
                        $timesheet_job_ids  = array_column($timesheetData, 'job_id');
                        $job_count          = ClientJob::query()->whereIn('id', $timesheet_job_ids)->count();

                        $array[] = [
                            'worker_name'   => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'],
                            'worker_id'     => $row['worker_no'],
                            'no_jobs'       => $job_count,
                            'no_shifts'     => count($timesheetData),
                            'total_hrs'     => number_format(collect(array_column($timesheetData, 'hours_worked'))->sum(), 2),
                        ];
                    }
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($worker),
                'recordsFiltered'   => count($worker),
                'data'              => $array,
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function timesheetAction($id, $worker_name, $date, $hour, $payroll_created_at, $start_time) {
        $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-2" id="delete_timesheet" data-id="'.$id.'">
                <i class="fs-2 las la-trash"></i>
            </a>';

        if (!$payroll_created_at) {
            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" id="edit_timesheet" data-id="'.$id.'" data-worker="'.$worker_name.' ('.$date.')" data-hours="'.$hour.'" data-start_time="'.$start_time.'">
                <i class="fs-2 las la-edit"></i>
            </a>';
        }

        return $action;
    }

    public function editTimesheetAction(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                'timesheet_id' => 'required',
                'hours_worked' => 'required|numeric',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $timesheetEntry = Timesheet::query()->where('id', $request->input('timesheet_id'))->first();
            if (!$timesheetEntry)
                return self::responseWithError('Timesheet entry not found, please try again later.');

            $updateArray = [
                'hours_worked' => $request->input('hours_worked'),
                'edited_at' => Carbon::now(),
                'edited_by_user_id' => Auth::id(),
            ];

            if (($request->has('timesheet_start_time'))) {
                $updateArray['in_time'] = ($request->input('timesheet_start_time') == null) ? '00:00:00' : $request->input('timesheet_start_time');
            }

            $timesheetEntry->update($updateArray);

            return self::responseWithSuccess('Worker worked hours successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteTimesheetEntry($id) {
        try {

            $timesheetEntry = Timesheet::query()->where('id', $id)->first();
            if (!$timesheetEntry)
                return self::responseWithError('Timesheet entry not found, please try again later.');

            $timesheetEntry->delete();
            return self::responseWithSuccess('Timesheet entry successfully deleted.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function exportTimesheetEntry(Request $request) {
        $params = $request->input();
        $site = Site::query()->select('id','site_name', 'client_id')
            ->where('id', $params['site'])
            ->with(['client_details', 'job_details'])
            ->first()
            ->toArray();

        $payroll_start_date_column = $site['client_details']['payroll_week_starts'].'_payroll_start';
        $payroll_end_date_column = $site['client_details']['payroll_week_starts'].'_payroll_end';
        $pwdNode = explode('_', $params['payroll_week']);

        $pwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
            ->where('payroll_week_number', $pwdNode[0])
            ->where('year', $pwdNode[1])
            ->first();

        $params = array_merge($params, [
            'job_ids' => ($site['job_details']) ? array_column($site['job_details'], 'id') : [],
            'payroll_week_start' => date('Y-m-d', strtotime($pwData[$payroll_start_date_column])),
            'payroll_week_end' => date('Y-m-d', strtotime($pwData[$payroll_end_date_column])),
        ]);

        $file_name = strtolower('timesheet_'.str_replace(' ', '_', $site['site_name']).'_'.$params['payroll_week'].'.csv');
        return (new TimesheetExport($params))->download($file_name);
    }

    public function getClientJobUsingWorker(Request $request) {
        $clientJobWorker = ClientJobWorker::query()
            ->where('worker_id', $request->input('worker_id'))
            ->whereNotNull('confirmed_at')
            ->whereNull('archived_at')
            ->with('job_details')
            ->get()->toArray();

        $option = '<option value=""></option>';
        if ($clientJobWorker) {
            foreach ($clientJobWorker as $row) {
                $option .= '<option value="'.$row['job_details']['id'].'">'.$row['job_details']['name'].'</option>';
            }
        }

        return self::responseWithSuccess('Site options.', [
            'job_option' => $option
        ]);
    }

    public function singleTimesheetEntryCreateAction(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'worker' => 'required',
                'job' => 'required',
                'date' => 'required',
                'time_worked' => 'required|numeric',
                'in_time'      => 'required_with:out_time',
            ]);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $worker = Worker::query()->select('id', 'worker_no')->where('id', $request->input('worker'))->first();
            if (!$worker) {
                return self::responseWithError('Invalid worker id, please select valid worker');
            }

            $time_worked = $request->input('time_worked');
            $in_time = $request->input('in_time');

            $workedMinutes  = $time_worked * 60;
            $inTimestamp    = strtotime($in_time);
            $outTimestamp   = $inTimestamp + ($workedMinutes * 60);

            $inTime         = date('H:i:s', $inTimestamp);
            $outTime        = date('H:i:s', $outTimestamp);

            $this->processRow([
                $request->input('date'),
                $request->input('job'),
                $worker['worker_no'],
                $inTime,
                $time_worked,
                $outTime,
            ]);
            return self::responseWithSuccess('Timesheet entry successfully created');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
