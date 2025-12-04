<?php

namespace App\Http\Controllers\Bonus;

use App\Exports\BonusExport;
use App\Http\Controllers\Controller;
use App\Models\Bonus\Bonus;
use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Client\ClientJobWorker;
use App\Models\Client\Site;
use App\Models\Client\SiteWeekLock;
use App\Models\Payroll\PayrollWeekDate;
use App\Models\Timesheet\Timesheet;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BonusUploaderController extends Controller
{
    use JsonResponse;
    public function index() {
        $worker = Worker::query()->select('id', 'first_name', 'last_name')->where('status', 'Active')->get();
        $payroll_week = PayrollWeekDate::query()->select('id', 'payroll_week_number', 'year')->where('year', date('Y'))->get();
        return view('bonus.bonus_uploader', compact('worker', 'payroll_week'));
    }

    public function bonusUploader(Request $request) {
        $validator = Validator::make($request->all(), [
            'bonus_file' => 'required|mimes:csv,txt'
        ]);
        if($validator->errors()->messages())
            return self::validationError($validator->errors()->messages());

        /*--- begin checkLead ---*/
        $fileExtension = $request->file('bonus_file')->getClientOriginalExtension();
        if($fileExtension !== 'csv') {
            return self::validationError('incorrect file format. Please check and try again');
        }

        $reportArray = [];
        $ReadFile    = fopen($request->file('bonus_file'), 'r');
        fgetcsv($ReadFile);

        while (($row = fgetcsv($ReadFile)) !== false) {

            $cJob = ClientJob::query()->where('id', $row[1])->with(['site_details', 'client_details'])->first();
            $cWorker = Worker::query()->where('worker_no', $row[2])->first();
            $payrollWeekData = PayrollWeekDate::query()->where('payroll_week_number', $row[0])->where('year', date('Y'))->first();

            $rowReport = [
                ($payrollWeekData) ? $row[0].'-'.$payrollWeekData['year'] : '-', //payroll_week
                $row[1], //job_id
                $row[2], //worker_id
                $row[3], //bonus_type
                $row[4], //bonus_pay
                ($cWorker) ? $cWorker['first_name'].' '.$cWorker['middle_name'].' '.$cWorker['last_name'] : '-',
                ($cJob) ? $cJob['name'] : '-',
                ($cJob) ? ($cJob['client_details']) ? $cJob['client_details']['company_name'] : '-' : '-',
                ($cJob) ? ($cJob['site_details']) ? $cJob['site_details']['site_name'] : '-' : '-',
                'Y',
                ''
            ];

            try {
                $this->processRow($row);
            } catch (\Exception $e) {
                $rowReport[9] = 'N';
                $rowReport[10] = $e->getMessage();
            }

            $reportArray[] = $rowReport;
        }
        fclose($ReadFile);

        return self::responseWithSuccess('The bonus uploading process has been completed.', [
            'reportArray'   => array_merge([['Payroll week number', 'Job ID', 'Worker ID', 'Bonus type', 'Bonus pay', 'Worker name', 'Job name', 'Client name', 'Site name', 'Created', 'Error']], $reportArray),
            'table'         => $this->tableView($reportArray)
        ]);
    }

    public function processRow($row) {
        $payroll_week = $row[0];
        $jobId = $row[1];
        $workerID = $row[2];
        $bonus_type = $row[3];
        $bonus_pay = $row[4];

        if (!in_array($bonus_type, ['attendance_bonus', 'production_bonus', 'one_off_bonus', 'loyalty_bonus', 'weekend_bonus', 'referral_bonus', 'other_bonus'])) {
            throw new \Exception('Bonus type not recognised');
        }

        if (!$payroll_week) {
            throw new \Exception('Payroll week number not define - ignored');
        }

        if (!$jobId) {
            throw new \Exception('Job id not define - ignored');
        }

        if (!$workerID) {
            throw new \Exception('Worker id not define - ignored');
        }

        if (!$bonus_type) {
            throw new \Exception('Bonus type not define - ignored');
        }

        if (!$bonus_pay) {
            throw new \Exception('Bonus amount not define - ignored');
        }

        // Step 1: Validate job_id
        $jobExists = ClientJob::query()->where('id', $jobId)->with('client_details')->first();
        if (!$jobExists) {
            throw new \Exception('Supplied job id does not exist');
        }

        if (!$jobExists['client_details']) {
            throw new \Exception('Client details not found');
        }

        // Step 2: Validate job status
        if ($jobExists['archived'] == 1) {
            throw new \Exception('Job is archived');
        }

        // Step 3: Validate worker id
        $workerExists = Worker::query()->select('id', 'worker_no')->where('worker_no', $workerID)->first();
        if (!$workerExists) {
            throw new \Exception('Worker id does not exist');
        }

        // Step 4 & 5: Validate worker status
        if (in_array($workerExists['status'], ['Leaver', 'Archived'])) {
            throw new \Exception('Worker is a '.$workerExists['status']);
        }

        // Step 6: Check for payroll week number
        //$payrollWeekData = PayrollWeekDate::query()->where('payroll_week_number', $payroll_week)->first();
        $payrollWeekData = PayrollWeekDate::query()->where('payroll_week_number', $payroll_week)->where('year', date('Y'))->first();
        if (!$payrollWeekData) {
            throw new \Exception('Payroll week data not found - ignored');
        }

        if (Carbon::createFromFormat('Y-m-d', $payrollWeekData->pay_date)->isPast()) {
            throw new \Exception('pay date has already passed - bonus can not be added');
        }

        // Step 7: Validate worker not worked in this job and shift
        $payroll_start_date_column = $jobExists['client_details']['payroll_week_starts'].'_payroll_start';
        $payroll_end_date_column = $jobExists['client_details']['payroll_week_starts'].'_payroll_end';

        $startDate = Carbon::parse($payrollWeekData[$payroll_start_date_column]);
        $endDate = Carbon::parse($payrollWeekData[$payroll_end_date_column]);

        $timesheetExists = Timesheet::query()->where('worker_id', $workerExists['id'])
            ->where('job_id', $jobId)
            ->whereBetween('date', [$startDate, $endDate])
            ->exists();

        if (!$timesheetExists) {
            throw new \Exception('No worker shift entries for week');
        }

        // Step 8: Check for duplicate entries
        $isDuplicateEntry = Bonus::query()->where('worker_id', $workerExists['id'])
            ->where('week_number', $payroll_week)
            ->where('job_id', $jobId)
            ->where('bonus_type', $bonus_type)
            ->where('week_year_number', date('Y'))
            ->exists();

        if ($isDuplicateEntry) {
            throw new \Exception('Duplicate row - ignored');
        }

        // If all checks pass, proceed with creating the bonus record
        $bonus_pay = (float)$bonus_pay;
        $bonus_charge_amount = $bonus_pay + ($bonus_pay * $jobExists['client_details']['bonus_commission_percentage']) / 100;
        Bonus::query()->create([
            'week_number' => $payroll_week,
            'week_year_number' => $payrollWeekData['year'],
            'job_id' => $jobId,
            'worker_id' => $workerExists['id'],
            'bonus_type' => $bonus_type,
            'bonus_pay_amount' => $bonus_pay,
            'bonus_charge_amount' => $bonus_charge_amount,
        ]);
    }

    private function tableView($reportArray) {
        $table = '<table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="bonus_import_datatable">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Payroll week number</th>
                            <th>Job ID</th>
                            <th>Worker ID</th>
                            <th>Bonus type</th>
                            <th>Bonus Pay</th>
                            <th>Worker name</th>
                            <th>Job name</th>
                            <th>Client name</th>
                            <th>Site name</th>
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

    public function bonusEditor() {
        $client = Client::query()->orderBy('company_name')->get();
        $payroll_week_number = PayrollWeekDate::query()->get();
        return view('bonus.bonus_editor', compact('client', 'payroll_week_number'));
    }

    public function getBonusEditorLineItemData(Request $request) {
        $params = $request->input();

        $client_and_site_name = '';
        $client_logo_url = '';
        $period_date = '';
        $week_number = '';
        $pay_date = '';
        $view_report_url_href = '';
        $payroll_created_at = '';
        $bonusData = [];
        $array = [];

        $client = Client::query()->select('id', 'company_name', 'company_logo', 'payroll_week_starts')
            ->where('id', $params['client'])
            ->first();
        if ($client) {

            $client_and_site_name = $client['company_name'];
            $client_logo_url = asset('workers/client_document/'.$client['company_logo']);

            $site = Site::query()->select(['id', 'site_name', 'client_id'])
                ->where('id', $params['site'])
                ->with('job_details')
                ->first()
                ->toArray();
            if($site) {

                $client_and_site_name = $client_and_site_name.' > '.$site['site_name'];
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

                        $bonusData = Bonus::query()->whereIn('job_id', array_column($site['job_details'], 'id'))
                            ->where('week_number', $pwData['payroll_week_number'])
                            ->where('week_year_number', $pwData['year'])
                            ->when($siteWeekLock && $params['type'] === 'lock', fn($query) => $query->whereNotNull('locked_at'))
                            ->when($siteWeekLock && $params['type'] === 'unlock', fn($query) => $query->whereNull('locked_at'))
                            ->with(['worker_details', 'job_details'])
                            ->get()
                            ->toArray();

                        if ($bonusData) {
                            foreach ($bonusData as $row) {
                                $worker_name = $row['worker_details']['first_name'].' '.$row['worker_details']['middle_name'].' '.$row['worker_details']['last_name'];
                                $array[]     = [
                                    'id'            => $row['id'],
                                    'worker_name'   => ($row['worker_details']) ? '<span>'.$worker_name.'<br><small class="text-muted">'.$row['worker_details']['worker_no'].'</small></small></span>' : '',
                                    'job'           => $row['job_details']['name'],
                                    'bonus_type'    => $row['bonus_type'],
                                    'charge'        => number_format($row['bonus_charge_amount'],2),
                                    'pay'           => number_format($row['bonus_pay_amount'],2),
                                    'edited'        => ($row['edited_at']) ? '<a href="javascript:;" title="'.date('d/m/Y H:i:s', strtotime($row['edited_at'])).'"><i class="bi bi-circle-fill text-warning"></i></a>' : '<i class="bi bi-circle text-muted"></i>',
                                    'action'        => (!$row['locked_at'] || $payroll_created_at == '') ? $this->bonusAction($row['id'], $worker_name, $row['bonus_type'], $row['bonus_pay_amount'], $payroll_created_at) : '',
                                ];
                            }
                        }
                    }
                }
            }
        }

        return [
            'draw'              => 1,
            'recordsTotal'      => ($bonusData) ? count($bonusData) : 0,
            'recordsFiltered'   => ($bonusData) ? count($bonusData) : 0,
            'data'              => $array,

            'client_and_site_name'  => $client_and_site_name,
            'client_logo_url'       => $client_logo_url,
            'period_date'           => $period_date,
            'week_number'           => $week_number,
            'pay_date'              => $pay_date,
            'total_bonus_pay'       => ($bonusData) ? number_format(collect(array_column($bonusData, 'bonus_pay_amount'))->sum(), 2) : 0.00,
            'total_bonus_charge'    => ($bonusData) ? number_format(collect(array_column($bonusData, 'bonus_charge_amount'))->sum(), 2) : 0.00,
            'payroll_created_at'    => $payroll_created_at,
            'view_report_url_href'  => $view_report_url_href,
            'array_ids'             => ($array) ? implode(',', array_column($array, 'id')) : '',
            'add_or_update_bonus_report_btn' => (\Carbon\Carbon::createFromFormat('d/m/Y', $pay_date)->isFuture())
                ? '<button type="button" class="btn btn-outline btn-outline-primary text-hover-white add-and-update-ignore-entry" data-type="timesheet">Add and update payroll report</button>'
                : '',
        ];
    }

    private function bonusAction($id, $worker_name, $bonus_type, $bonus_amount, $payroll_created_at) {
        $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-2" id="delete_bonus" data-id="'.$id.'">
            <i class="fs-2 las la-trash"></i>
        </a>';

        if (!$payroll_created_at) {
            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" id="edit_bonus" data-id="'.$id.'" data-worker="'.$worker_name.'" data-bonus_type="'.$bonus_type.'" data-bonus_amount="'.$bonus_amount.'">
               <i class="fs-2 las la-edit"></i>
            </a>';
        }

        return $action;
    }

    public function getBonusEditorWorkerSummaryData(Request $request) {
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
            $pwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', 'year', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
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
                    $bonusData = Bonus::query()->where('worker_id', $row['id'])
                        ->whereIn('job_id', $jobIds)
                        ->where('week_number', $pwData['payroll_week_number'])
                        ->where('week_year_number', $pwData['year'])
                        ->get()
                        ->toArray();

                    if ($bonusData) {
                        $editedData = array_filter($bonusData, function($bonus) {
                            return $bonus['edited_at'] !== null;
                        });

                        $array[] = [
                            'worker_id' => $row['worker_no'],
                            'worker_name' => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'],
                            'no_bonuses' => count($bonusData),
                            'charge' => number_format(collect(array_column($bonusData, 'bonus_charge_amount'))->sum(), 2),
                            'pay' => number_format(collect(array_column($bonusData, 'bonus_pay_amount'))->sum(), 2),
                            'edited' => !empty($editedData) ? '<a href="javascript:;" title="Edited"><i class="las la-circle text-warning"></i></a>' : '<i class="las la-circle"></i>',
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

    public function editBonusAction(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                'bonus_id' => 'required',
                'bonus_type' => 'required',
                'bonus_amount' => 'required|numeric',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $bonusEntry = Bonus::query()->where('id', $request->input('bonus_id'))->with('job_details')->first();
            if (!$bonusEntry)
                return self::responseWithError('Bonus entry not found, please try again later.');

            if (!$bonusEntry['job_details'])
                return self::responseWithError('Job details not found, please try again later.');

            if (!$bonusEntry['job_details']['client_details'])
                return self::responseWithError('Client details not found, please try again later.');

            $bonus_pay = $request->input('bonus_amount');
            $bonus_charge_amount = $bonus_pay + ($bonus_pay * $bonusEntry['job_details']['client_details']['bonus_commission_percentage']) / 100;
            $bonusEntry->update([
                'bonus_type' => $request->input('bonus_type'),
                'bonus_pay_amount' => $bonus_pay,
                'bonus_charge_amount' => $bonus_charge_amount,
                'edited_at' => Carbon::now(),
                'edited_by_user_id' => Auth::id(),
            ]);

            return self::responseWithSuccess('Bonus entry successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteBonusEntry($id) {
        try {

            $bonusEntry = Bonus::query()->where('id', $id)->first();
            if (!$bonusEntry)
                return self::responseWithError('Bonus entry not found, please try again later.');

            $bonusEntry->delete();
            return self::responseWithSuccess('Bonus entry successfully deleted.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function exportBonusEntry(Request $request) {
        $params = $request->input();
        $site = Site::query()->select('id','site_name', 'client_id')
            ->where('id', $params['site'])
            ->with(['client_details', 'job_details'])
            ->first()
            ->toArray();


        $pwdNode = explode('_', $params['payroll_week']);
        $params = array_merge($params, [
            'job_ids' => ($site['job_details']) ? array_column($site['job_details'], 'id') : [],
            'week_number' => $pwdNode[0],
            'week_year_number' => $pwdNode[1],
        ]);

        $file_name = strtolower('bonuses_'.str_replace(' ', '_', $site['site_name']).'_'.$params['payroll_week'].'.csv');
        return (new BonusExport($params))->download($file_name);
    }

    public function singleBonusEntryCreateAction(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'worker' => 'required',
                'job' => 'required',
                'payroll_week' => 'required',
                'bonus_type' => 'required',
                'amount' => 'required|numeric'
            ]);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $worker = Worker::query()->select('id', 'worker_no')->where('id', $request->input('worker'))->first();
            if (!$worker) {
                return self::responseWithError('Invalid worker id, please select valid worker');
            }

            $this->processRow([
                $request->input('payroll_week'),
                $request->input('job'),
                $worker['worker_no'],
                $request->input('bonus_type'),
                $request->input('amount'),
            ]);
            return self::responseWithSuccess('Bonus entry successfully created');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
