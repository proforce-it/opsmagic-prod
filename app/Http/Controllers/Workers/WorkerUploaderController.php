<?php

namespace App\Http\Controllers\Workers;

use App\Helper\Activity\ActivityLogs;
use App\Helper\Workers\WorkerHelper;
use App\Http\Controllers\Controller;
use App\Models\Group\CostCentre;
use App\Models\Location\Country;
use App\Models\Note\Note;
use App\Models\Worker\Nationality;
use App\Models\Worker\RightsToWork;
use App\Models\Worker\Worker;
use App\Models\Worker\WorkerCostCenter;
use App\Models\Worker\WorkerDocument;
use App\Models\Worker\WorkerSequenceNumber;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WorkerUploaderController extends Controller
{
    use JsonResponse;
    public function index() {
        return view('workers.worker_uploader');
    }

    public function workerUploadAction(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'worker_file' => 'required|mimes:csv,txt'
            ],[
                    'worker_file.required' => 'Please select a file to upload.',
                    'worker_file.mimes' => 'Only CSV files are allowed.',
                ]
            );
            if($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $fileExtension = $request->file('worker_file')->getClientOriginalExtension();
            if($fileExtension !== 'csv') {
                return self::validationError('Invalid file format - <b>'.$fileExtension.'</b>');
            }

            $reportArray = [];
            $ReadFile    = fopen($request->file('worker_file'), 'r');
            fgetcsv($ReadFile);

            while (($row = fgetcsv($ReadFile)) !== false) {
                $rowReport = [
                    'title' => $row[2],
                    'first_name' => $row[3],
                    'last_name' => $row[5],
                    'dob' => $row[6],
                    'email' => $row[9],
                    'mobile_number' => $row[10],
                    'created'    => 'Y',
                    'error'      => '-',
                ];

                try {
                    $this->processRow($row);
                } catch (\Exception $e) {
                    $rowReport['created'] = 'N';
                    $rowReport['error'] = $e->getMessage();
                }

                $reportArray[] = $rowReport;
            }
            fclose($ReadFile);

            return self::responseWithSuccess('The workers uploading process has been completed.', [
                'reportArray'   => array_merge([['title', 'first_name', 'last_name', 'dob', 'email', 'mobile_number', 'created', 'error']], $reportArray),
                'table'         => $this->tableView($reportArray)
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function processRow($row) {
        $requiredFields = [
            2  => 'Title is required.', //title
            3  => 'First name is required.', //first_name
            5  => 'Last name is required.', //last_name
            6  => 'Date of birth is required.', //date_of_birth
            7  => 'Marital status is required.', //marital_status
            8  => 'Gender is required.', //gender
            9  => 'Email is required.', //email
            10 => 'Mobile number is required.', //mobile_number
            11 => 'Cost centre is required.', //cost_centre
            12 => 'Transport option is required.', //transport_required
            13 => 'Accommodation site is required.', //pro_force_accommodation
            20 => 'Same as current address is required.', //same_as_current_address
            26 => 'Nationality is required.', //nationality
            /*27 => 'Same as current address for next of kin is required.', //same_as_current_address_for_next_of_kin
            28 => 'Next of kin first name is required.', //next_of_kin_first_name
            29 => 'Next of kin last name is required.', //next_of_kin_last_name
            30 => 'Next of kin email is required.', //next_of_kin_email
            31 => 'Next of kin mobile is required.', //next_of_kin_mobile
            32 => 'next_of_kin_relationship is required.', //next_of_kin_relationship*/
            28 => 'Tax statement is required.', //tax_treatment
            29 => '48 hour opt out option is required.', //48_hour_opt_out
            30 => 'Bank account provision option is required.', //has_bank_account
        ];

        foreach ($requiredFields as $index => $errorMessage) {
            if (empty(trim($row[$index] ?? ''))) {
                throw new \Exception($errorMessage);
            }
        }

        $dob = date('Y-m-d', strtotime(str_replace('/', '-', $row[6])));
        $email = trim($row[9]);
        $mobile = trim($row[10]);

        if (!Carbon::parse($dob)->lessThanOrEqualTo(Carbon::now()->subYears(16))) {
            throw new \Exception('worker age is below 16');
        }

        if (Worker::query()->where('email_address', $email)->exists()) {
            throw new \Exception('Email is already in use.');
        }

        if (Worker::query()->where('mobile_number', $mobile)->exists()) {
            throw new \Exception('Mobile number is already in use.');
        }

        if (!empty($row[27]) && Worker::query()->where('national_insurance_number', $row[27])->exists()) {
            //27 = national_insurance_number
            throw new \Exception('National insurance number is already in use.');
        }

        if ($row[13] == 'Yes' && !$row[14]) {
            //13 = pro_force_accommodation, 14 = pro_force_accommodation_id
            throw new \Exception('Accommodation choice is required.');
        }

        if ($row[13] == 'No' && (!$row[15] || !$row[17] || !$row[18] || !$row[19])) {
            //13 = pro_force_accommodation, 15 = current_address_line_one, 17 = current_city, 18 = current_post_code, 19 = current_country
            throw new \Exception('Current address is incomplete.');
        }

        if ($row[20] == 'No' && (!$row[21] || !$row[23] || !$row[24] || !$row[25])) {
            //20 = same_as_current_address, 21 = permanent_address_line_one, 23 = permanent_city, 24 = permanent_post_code, 25 = permanent_country
            throw new \Exception('Permanent address is incomplete.');
        }

       /* if ($row[27] == 'No' && (!$row[33] || !$row[35] || !$row[36] || !$row[37])) {
            //20 = same_as_current_address_for_next_of_kin, 33 = next_of_kin_address_line_one, 35 = next_of_kin_city, 36 = next_of_kin_post_code, 37 = next_of_kin_country
            throw new \Exception('Next of kin address is incomplete.');
        }*/

        if ($row[30] == 'No' && !$row[31]) {
            //30 = has_bank_account, 31 = proforce_to_open_bank_account,
            throw new \Exception('Proforce to open bank account is required.');
        }

        /*if ($row[30] == 'Yes' && (!$row[32] || !$row[33] || !$row[34] || !$row[35])) {
            //30 = has_bank_account, 32 = bank_account_number, 33 = bank_sort_code, 34 = bank_account_name, 35 = bank_name,
            throw new \Exception('Bank account details incomplete.');
        }*/

        $workerExists = Worker::query()->where('first_name', $row[3])
            ->where('last_name', $row[5])
            ->where('date_of_birth', $dob)
            ->exists();

        if ($workerExists) {
            throw new \Exception('Worker already on system.');
        }

        $year   = date('y');
        $month  = date('m');
        $project_series = WorkerSequenceNumber::query()->where('worker_number_year', $year)->where('worker_number_month', $month)->orderBy('id', 'desc')->first();
        $sequence       = ($project_series) ? $project_series['worker_number_sequence'] + 1 : '1';
        $worker_number  = $year.$month.sprintf("%03d", $sequence);

        if ($row[13] == 'No') {
            $currentCountryExists = Country::query()->where('name', $row[19])->exists();
            if (!$currentCountryExists) {
                throw new \Exception("Please provide valid current country name.");
            }
        }

        if ($row[20] == 'No') {
            $permanentCountryExists = Country::query()->where('name', $row[25])->exists();
            if (!$permanentCountryExists) {
                throw new \Exception("Please provide valid permanent country name.");
            }
        }

        $nationalityCheck = Country::query()->where('nationality', $row[26])->first();
        if (!$nationalityCheck) {
            throw new \Exception("Please provide valid nationality.");
        }

        $checkBank = Worker::query()->where('bank_account_number', $row['32'])
            ->where('bank_ifsc_code', $row['33'])
            ->exists();

        if ($checkBank) {
            throw new \Exception("This bank account number with bank sort code is already used.");
        }

        $createdWorker = Worker::query()->create([
            'worker_no'                     => $worker_number,
            'jotform_submission_date'       => date('Y-m-d', strtotime($row[0])),
            'jotform_status'                => $row[1],
            'title'                         => $row[2],
            'first_name'                    => $row[3],
            'middle_name'                   => $row[4],
            'last_name'                     => $row[5],
            'date_of_birth'                 => date('Y-m-d', strtotime($row[6])),
            'marital_status'                => $row[7],
            'gender'                        => $row[8],
            'email_address'                 => $row[9], //email
            'mobile_number'                 => $row[10],
            'proforce_transport'            => $row[12], //transport_required
            'accommodation_type'            => ($row[13] == 'Yes') ? 'supplied_by_pro_force' : 'arranged_by_worker', //pro_force_accomm
            'accommodation_site'            => ($row[13] == 'Yes') ? $row[14] : null, //pro_force_accomm_id
            'current_address_line_one'      => ($row[13] == 'No') ? $row[15] : null,
            'current_address_line_two'      => ($row[13] == 'No') ?  $row[16] : null,
            'current_city'                  => ($row[13] == 'No') ?  $row[17] : null,
            'current_post_code'             => ($row[13] == 'No') ?  $row[18] : null,
            'current_country'               => ($row[13] == 'No') ?  $row[19] : null,
            'same_as_current_address'       => ($row[20] == 'Yes') ? 1 : 0,
            'permanent_address_line_one'    => ($row[20] == 'No') ? $row[21] : null,
            'permanent_address_line_two'    => ($row[20] == 'No') ? $row[22] : null,
            'permanent_city'                => ($row[20] == 'No') ? $row[23] : null,
            'permanent_post_code'           => ($row[20] == 'No') ? $row[24] : null,
            'permanent_country'             => ($row[20] == 'No') ? $row[25] : null,
            'nationality'                   => $nationalityCheck['id'],
            /*'same_as_current_address_for_next_of_kin' => ($row[27] == 'Yes') ? 1 : 0,
            'next_of_kin_first_name'        => $row[28],
            'next_of_kin_last_name'         => $row[29],
            'next_of_kin_email'             => $row[30],
            'next_of_kin_mobile'            => $row[31],
            'next_of_kin_relationship'      => $row[32],
            'next_of_kin_address_line_one'  => ($row[27] == 'No') ? $row[33] : null,
            'next_of_kin_address_line_two'  => ($row[27] == 'No') ? $row[34] : null,
            'next_of_kin_city'              => ($row[27] == 'No') ? $row[35] : null,
            'next_of_kin_post_code'         => ($row[27] == 'No') ? $row[36] : null,
            'next_of_kin_country'           => ($row[27] == 'No') ? $row[37] : null,*/
            'national_insurance_number'     => implode(' ', str_split(str_replace(' ', '', $row[27]), 2)),
            'tax_treatment'                 => $row[28],
            '48_hour_opt_out'               => $row[29],
            'has_bank_account'              => $row[30],
            'proforce_to_open_bank_account' => $row[31],
            'bank_account_number'           => $row[32],
            'bank_ifsc_code'                => implode('-', str_split(str_replace('-', '', $row[33]), 2)), //bank_sort_code
            'bank_account_name'             => $row[34],
            'bank_name'                     => $row[35],
            /*'criminal_conviction_details'   => $row[46],
            'medical_issue_details'         => $row[47],*/
            'created_by_id'                 => Auth::id()
        ]);

        WorkerSequenceNumber::query()->create([
            'worker_number_year'     => $year,
            'worker_number_month'    => $month,
            'worker_number_sequence' => $sequence,
        ]);

        $costCentre = CostCentre::query()->where('short_code', $row[11])->first();
        if ($costCentre) {
            WorkerCostCenter::query()->create([
                'worker_id'   => $createdWorker['id'],
                'cost_center' => $costCentre['id'],
            ]);
        }

        $endDate = ($row[41] != 'Passport')
            ? date($row[45]) ? date('Y-m-d', strtotime($row[45])) : '2199-12-31' //end_date
            : '2199-12-31';

        RightsToWork::query()->create([
            'worker_id'                 => $createdWorker['id'],
            'user_id'                   => Auth::id(),
            'right_to_work_type'        => (!$row[41] && $row[42]) ? 'UNKNOWN RTW TYPE' : $row[41], //right_to_work_type
            'right_to_work_expiry_date' => date($row[45]) ? date('Y-m-d', strtotime($row[45])) : null,
            'uk_id_document_type'       => !empty($row[39]) && in_array($row[39], ['Passport', 'Birth Certificate']) ? $row[39] : null, //uk_id_document_type
            'uk_id_document_number'     => $row[40] ?? null, //uk_id_document_number
            'start_date'                => ($row[44]) ? date('Y-m-d', strtotime($row[44])) : null, //start_date
            'end_date'                  => $endDate,
            'reference_number'          => $row[43], //reference_number
            'incomplete'                => 1, //(!$row[41] && $row[42]) ? 1 : 0,
            'rtw_share_code'            => $row[42]
        ]);

        if ($row[36] && $row[37] && $row[38]) {
            WorkerDocument::query()->create([
                'worker_id'         => $createdWorker['id'],
                'document_type'     => $row[36], //id_document_type
                'document_no'       => $row[37], //document_no
                'expiry_date'       => date('Y-m-d', strtotime($row[38])), //expiry_date
                'document_file'       => null,
                'document_file_type'  => null,
                'document_file_title' => 'ID',
                'uploaded_by'       => Auth::id(),
                'uploaded_at'       => Carbon::now(),
                'incomplete'        => 1,
            ]);
        }

        foreach ([46 => 'Criminal', 47 => 'Medical'] as $index => $noteType) {
            if ($row[$index]) {
                Note::query()->create([
                    'worker_id' => $createdWorker['id'],
                    'user_id'   => Auth::user()['id'],
                    'note_type' => $noteType,
                    'note_text' => $row[$index],
                    'type'      => 'Worker',
                ]);
            }
        }

        ActivityLogs::createAndDeleteLog($createdWorker['id'], 'Create', 'Worker create', 'Worker');
        //WorkerHelper::send_confirm_email($createdWorker['id'], $createdWorker['first_name'], $createdWorker['email_address']);
    }

    private function tableView($reportArray) {
        $table = '<table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="jobs_datatable">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Title</th>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Date of birth</th>
                            <th>Email address</th>
                            <th>Mobile number</th>
                            <th>Created</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">';
        if ($reportArray) {

            foreach ($reportArray as $reportRow) {
                $textDanger = ($reportRow['created'] === 'N') ? 'text-danger' : '';

                $table .= '<tr>
                                <td>'.$reportRow['title'].'</td>
                                <td>'.$reportRow['first_name'].'</td>
                                <td>'.$reportRow['last_name'].'</td>
                                <td>'.$reportRow['dob'].'</td>
                                <td>'.$reportRow['email'].'</td>
                                <td>'.$reportRow['mobile_number'].'</td>
                                <td class="' . $textDanger . '">' . htmlspecialchars($reportRow['created']) . '</td>
                                <td class="' . $textDanger . '">' . htmlspecialchars($reportRow['error']) . '</td>
                          </tr>';
            }
        }
        $table .= '</tbody></table>';

        return $table;
    }

}
