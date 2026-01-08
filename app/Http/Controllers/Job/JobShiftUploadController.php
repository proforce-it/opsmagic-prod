<?php

namespace App\Http\Controllers\Job;

use App\Helper\Clients\ClientHelper;
use App\Http\Controllers\Controller;
use App\Models\Client\ClientJob;
use App\Models\Client\ClientJobWorker;
use App\Models\Job\JobLine;
use App\Models\Job\JobShift;
use App\Models\Job\JobShiftWorker;
use App\Models\Worker\RightsToWork;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobShiftUploadController extends Controller
{
    use JsonResponse;
    public function index() {
        return view('job.job_shift_uploader');
    }

    public function shiftUploader(Request $request) {
        $validator = Validator::make($request->all(), [
            'shift_file' => 'required|mimes:csv,txt'
        ],[
            'shift_file.required' => 'Please select a file to upload.',
            'shift_file.mimes' => 'Only CSV or TXT files are allowed.',
        ]
        );
        if($validator->errors()->messages())
            return self::validationError($validator->errors()->messages());

        /*--- begin checkLead ---*/
        $booking_as = $request->input('booking_as');
        $fileExtension = $request->file('shift_file')->getClientOriginalExtension();
        if($fileExtension !== 'csv') {
            return self::validationError('Invalid file format - <b>'.$fileExtension.'</b>');
        }

        $reportArray = [];
        $ReadFile    = fopen($request->file('shift_file'), 'r');
        fgetcsv($ReadFile);

        while (($row = fgetcsv($ReadFile)) !== false) {

            if ($row[0] != '' && $row[1] != '' && $row[2] != '' && $row[3] != '') {
                $cJob = ClientJob::query()->where('id', $row[0])->with(['site_details', 'client_details'])->first();
                $cWorker = Worker::query()->where('worker_no', $row[1])->first();

                $row[4] = ($row[4]) ? $row[4] : $cJob['default_shift_length_hr'].'.'.$cJob['default_shift_length_min'];
                $rowReport = [
                    $row[0],
                    $row[1],
                    date('d-m-Y', strtotime(str_replace('/','-',$row[2]))),
                    date('H:i', strtotime($row[3])),
                    $row[4],
                    ($cWorker) ? $cWorker['first_name'].' '.$cWorker['middle_name'].' '.$cWorker['last_name'] : '-',
                    ($cJob) ? $cJob['name'] : '-',
                    ($cJob) ? ($cJob['site_details']) ? $cJob['site_details']['site_name'] : '-' : '-',
                    ($cJob) ? ($cJob['client_details']) ? $cJob['client_details']['company_name'] : '-' : '-',
                    'Y',
                    '-',
                ];

                try {
                    $this->processRow($row, $booking_as);
                } catch (\Exception $e) {
                    $rowReport[9] = 'N';
                    $rowReport[10] = $e->getMessage();
                }

                $reportArray[] = $rowReport;
            }
        }
        fclose($ReadFile);

        return self::responseWithSuccess('The job shift uploading process has been completed.', [
            'reportArray'   => array_merge([['Job_id', 'Worker_id', 'Date', 'Start_time', 'Duration', 'Worker_name', 'Job', 'Client', 'Site', 'Created', 'Error']], $reportArray),
            'table'         => $this->tableView($reportArray)
        ]);
    }

    private function processRow($row, $booking_as) {
        $jobId          = $row[0];
        $workerID       = $row[1];
        $date           = date('Y-m-d', strtotime(str_replace('/','-',$row[2])));
        $start_time     = date('H:i:s', strtotime($row[3]));
        $line_code      = ($row[5]) ? $row[5] : null;

        // Check duration validation
        $duration = $row[4];
        if (!is_numeric($duration)) {
            throw new \Exception('Duration must be a valid number, not a string or character');
        }

        $duration = number_format((float)$duration, 2, '.', '');
        $durationArray = explode('.', $duration);

        if (!in_array($durationArray[1], ['00','25','50','75'])) {
            throw new \Exception('Shift duration must be in 0.25 hour increments');
        }

        $duration_hr = intval($durationArray[0]);
        $duration_min = isset($durationArray[1])
            ? intval(round($durationArray[1] * 60 / 100))
            : 0;
        $currentShiftLength = ($duration_hr * 60) + $duration_min;

        $initialTime = Carbon::parse($start_time);
        $end_time = $initialTime->copy()->addHours((int) $duration_hr)->addMinutes((int) $duration_min);

        // Check job existence and status
        $shiftDateCarbon = Carbon::parse($date);
        $todayDateCarbon = Carbon::parse(date('Y-m-d'));
        if ($todayDateCarbon->greaterThan($shiftDateCarbon))
            throw new \Exception('Shift date is in past - row ignored');

        $job = ClientJob::query()->where('id', $jobId)->with(['site_details', 'client_details'])->first();
        if (!$job) {
            throw new \Exception('Supplied job id does not exist');
        }

        if ($job->archived == 1) {
            throw new \Exception('Job is archived');
        }

        if ($job->end_date) {
            if ($date > $job->end_date) {
                throw new \Exception('Job ends on ' . date('d-m-Y', strtotime($job->end_date)));
            }
        }

        // Check worker existence and status
        $worker = Worker::query()->where('worker_no', $workerID)->first();
        if (!$worker) {
            throw new \Exception('Worker id does not exist');
        }

        if ($worker->status == 'Leaver') {
            throw new \Exception('Worker is a leaver');
        }

        if ($worker->status == 'Archived') {
            throw new \Exception('Worker is archived');
        }

        // Check if worker is linked to the job
        $jobWorker = ClientJobWorker::query()->where('job_id', $jobId)->where('worker_id', $worker->id)->first();
        if (!$jobWorker) {
            throw new \Exception('Associate not linked to this job');
        }

        if ($jobWorker->declined_at || $jobWorker->archived_at) {
            throw new \Exception('Associate no longer linked to job');
        }

        // Check if worker has a valid RTW for the date
        $rtw = RightsToWork::query()
            ->where('worker_id', $worker->id)
            ->whereDate('end_date', '>=', $date)
            ->first();
        if (!$rtw) {
            throw new \Exception('Worker has no RTW for this date');
        }

        $startRTW = RightsToWork::query()
            ->where('worker_id', $worker->id)
            ->whereNotIn('right_to_work_type', ['UK Citizen', 'Settled status', 'Pre-settled status', 'COA'])
            ->whereNotNull('start_date')
            ->whereDate('start_date', '>', $date)
            ->first();

        if ($startRTW) {
            throw new \Exception('Shift date precedes start date of RTW');
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

        // Find or create the job shift
        $jobShift = JobShift::query()
            ->where('job_id', $jobId)
            ->where('date', $date)
            //->where('start_time', $start_time)
            ->first();
        if (!$jobShift) {
            $jobShift = JobShift::query()->create([
                'job_id' => $jobId,
                'date' => $date,
                'number_workers' => 0,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'shift_length_hr' => $duration_hr,
                'shift_length_min' => $duration_min,
                'shift_length' => $currentShiftLength
            ]);
        }

        // Check for duplicates in the job_shift_workers table
        $existingShiftWorker = JobShiftWorker::query()
            ->where('job_shift_id', $jobShift->id)
            ->where('worker_id', $worker->id)
            ->first();
        if ($existingShiftWorker) {
            throw new \Exception('Duplicate row - ignored');
        }

        // Check for conflicts within the same job shift
        $shiftConflicts = JobShiftWorker::query()
            ->join('job_shifts', 'job_shift_workers.job_shift_id', '=', 'job_shifts.id')
            ->where('job_shifts.date', $date)
            ->where('job_shifts.job_id', $jobId)
            ->where('job_shift_workers.worker_id', $worker->id)
            ->where('job_shifts.start_time', '!=', $start_time)
            ->first();

        if ($shiftConflicts) {
            throw new \Exception('Worker already booked for this job');
        }

        // Check for conflicts with other jobs
        $otherJobConflicts = JobShiftWorker::query()
            ->join('job_shifts', 'job_shift_workers.job_shift_id', '=', 'job_shifts.id')
            ->where('job_shifts.date', $date)
            ->where('job_shifts.start_time', $start_time)
            ->where('job_shift_workers.worker_id', $worker->id)
            ->where('job_shifts.job_id', '!=', $jobId)
            ->first();

        if ($otherJobConflicts) {
            throw new \Exception('Worker booked on another job');
        }

        // Find existing shifts for the same job, date, and start time
        $existingShifts = JobShift::query()
            ->where('job_id', $jobId)
            ->where('date', $date)
            ->where('start_time', $start_time)
            ->get();

        // Check if there's any shift with a different length
        foreach ($existingShifts as $shift) {
            if ($shift->shift_length != $currentShiftLength) {
                throw new \Exception('Inconsistent shift start or duration.');
            }
        }

        // Check if there's any shift with an overlapping time but different start time
        /*$overlappingShifts = JobShift::query()
            ->where('job_id', $jobId)
            ->where('date', $date)
            ->where('start_time', '!=', $start_time)
            ->get();

        foreach ($overlappingShifts as $shift) {
            $shiftEndTime = Carbon::parse($shift->start_time)->addMinutes((int) $shift->shift_length);
            $newShiftEndTime = Carbon::parse($start_time)->addMinutes((int) $currentShiftLength);

            if ($newShiftEndTime > Carbon::parse($shift->start_time) && Carbon::parse($start_time) < $shiftEndTime) {
                throw new \Exception('Inconsistent shift start or duration.');
            }
        }*/

        $assign_type = ($booking_as == 'Confirmed placements')
            ? 'Direct placement'
            : 'Invitation';
        $jobShiftWorker = JobShiftWorker::query()->create([
            'job_shift_id' => $jobShift->id,
            'worker_id' => $worker->id,
            'shift_date' => $date,
            'assign_type' => $assign_type,
            'confirmed_at' => ($booking_as == 'Confirmed placements') ? Carbon::now() : null,
            'job_line_id' => ($jobLine) ? $jobLine->id : null,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'shift_length_hr' => $duration_hr,
            'shift_length_min' => $duration_min,
            'shift_length' => $currentShiftLength,
            'duration' => $duration
        ]);

        /*--- BEGIN MAIL CODE ---*/
        ClientHelper::workerAddedIntoJobShiftSendMail($worker->id, $jobShift->id, $jobShiftWorker['id'],  $assign_type);
        /*--- END MAIL CODE ---*/
    }

    private function tableView($reportArray) {
        $table = '<table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="jobs_datatable">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Job ID</th>
                            <th>Worker ID</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Worker name</th>
                            <th>Job</th>
                            <th>Site</th>
                            <th>Client</th>
                            <th>Created</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">';
        if ($reportArray) {
            foreach ($reportArray as $reportRow) {
                $textDanger = ($reportRow[9] == 'N') ? 'text-danger' : '';
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
}
