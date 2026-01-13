<?php

namespace App\Http\Controllers\Workers;

use App\Helper\Activity\ActivityLogs;
use App\Helper\File\FileHelper;
use App\Helper\Location\CountryHelper;
use App\Helper\Location\StateHelper;
use App\Helper\Workers\RightToWorkHelper;
use App\Helper\Workers\WorkerHelper;
use App\Http\Controllers\Controller;
use App\Mail\ConfirmEmailAddress;
use App\Models\Accommodation\Accommodation;
use App\Models\Activity\ActivityLog;
use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Client\ClientJobWorker;
use App\Models\Client\SiteWeekLock;
use App\Models\Group\CostCentre;
use App\Models\Group\Group;
use App\Models\Group\GroupWithWorker;
use App\Models\Job\JobShift;
use App\Models\Job\JobShiftWorker;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Note\Note;
use App\Models\Payroll\PayrollWeekDate;
use App\Models\PickUpPoint\PickUpPoint;
use App\Models\Timesheet\Timesheet;
use App\Models\Worker\Absence;
use App\Models\Worker\Nationality;
use App\Models\Worker\RightsToWork;
use App\Models\Worker\Worker;
use App\Models\Worker\WorkerCostCenter;
use App\Models\Worker\WorkerDocument;
use App\Models\Worker\WorkerSequenceNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class WorkerController extends Controller
{
    use JsonResponse;
    public function workerManagement(Request $request) {
        $filter = $request->get('filter');
        $cost_center_val = $request->get('cost_center');
        return view('workers.dis_worker', compact(['filter', 'cost_center_val']));
    }

    public function listOfWorkers(Request $request) {
        try {
            $filter = $request->input('filter');
            $cost_center = $request->input('cost_center');

            if ($filter === 'expiring-RTWs-next-4-weeks') {

                //Expiring RTWs in next 4 weeks
                $startDate = Carbon::today()->toDateString();
                $alertEndDate = Carbon::today()->addDay(28)->toDateString();

                $workers = Worker::query()
                    ->when(request('status') != null && request('status') != 'All', function ($q) {
                        return $q->where('status', request('status'));
                    })
                    ->when($cost_center != '', function ($query) use ($cost_center) {
                        $query->whereHas('worker_cost_center', function ($subQuery) use ($cost_center) {
                            $subQuery->where('cost_center', $cost_center);
                        });
                    })
                    ->whereHas('rights_to_work_details', function ($query) use ($startDate, $alertEndDate)  {
                        $query->whereBetween('end_date', [$startDate, $alertEndDate]);
                    })
                    ->with(['rights_to_work_details', 'incomplete_rights_to_work_details', 'id_documents', 'worker_documents'])
                    ->get()
                    ->toArray();

            } else if ($filter === 'shift-workers-without-payroll-refs') {

                //Shift workers without payroll refs
                $shiftWorkersWithoutPayroll = Timesheet::query()->select('id', 'worker_id')
                    ->when($cost_center != '', function ($query) use ($cost_center) {
                        $query->whereHas('worker_details.worker_cost_center', function ($subQuery) use ($cost_center) {
                            $subQuery->where('cost_center', $cost_center);
                        });
                    })
                    ->whereHas('worker_details', function ($subQuery) {
                        $subQuery->whereNull('payroll_reference')
                            ->where('status', 'Active');
                    })
                    ->with('worker_details')
                    ->get()
                    ->pluck('worker_id')
                    ->toArray();

                $workers = Worker::query()->whereIn('id', array_unique($shiftWorkersWithoutPayroll))
                    ->when(request('status') != null && request('status') != 'All', function ($q) { return $q->where('status', request('status')); })
                    ->with(['rights_to_work_details', 'incomplete_rights_to_work_details', 'id_documents', 'worker_documents'])
                    ->get()
                    ->toArray();

            } else if ($filter === 'workers-have-worked-greater-than-12-days-in-a-row') {

                //Workers have worked >12 days in a row
                $workersWorkedGreaterThan12Days = Timesheet::query()->select('id', 'worker_id', 'date')
                    ->whereDate('date', '>=', Carbon::today()->subWeeks(4)->startOfDay())
                    ->when($cost_center != '', function ($query) use ($cost_center) {
                        $query->whereHas('worker_details.worker_cost_center', function ($subQuery) use ($cost_center) {
                            $subQuery->where('cost_center', $cost_center);
                        });
                    })
                    ->with('worker_details')
                    ->get()
                    ->groupBy('worker_id')
                    ->filter(function ($timesheets) {
                        $dates = $timesheets
                            ->sortBy('date')
                            ->map(fn ($row) => Carbon::parse($row->date)->toDateString())
                            ->unique()
                            ->values();

                        $streak = 1;
                        for ($i = 0; $i < $dates->count() - 1; $i++) {
                            if (Carbon::parse($dates[$i])->addDay()->eq(Carbon::parse($dates[$i + 1]))) {
                                $streak++;
                                if ($streak >= 12) {
                                    return true;
                                }
                            } else {
                                $streak = 1;
                            }
                        }
                        return false;
                    })
                    ->keys()
                    ->all();

                $workers = Worker::query()->whereIn('id', array_unique($workersWorkedGreaterThan12Days))
                    ->when(request('status') != null && request('status') != 'All', function ($q) { return $q->where('status', request('status')); })
                    ->with(['rights_to_work_details', 'incomplete_rights_to_work_details', 'id_documents', 'worker_documents'])
                    ->get()
                    ->toArray();

            } else if ($filter == 'workers-have-worked-greater-than-12-days-in-a-row-2') {
                $workersWorkedGreaterThan12Days_2 = Timesheet::query()->select('id', 'worker_id', 'date')
                    ->when($cost_center != '', function ($query) use ($cost_center) {
                        $query->whereHas('worker_details.worker_cost_center', function ($subQuery) use ($cost_center) {
                            $subQuery->where('cost_center', $cost_center);
                        });
                    })
                    ->whereBetween('date', [
                        Carbon::now()->subDays(12)->format('Y-m-d'),
                        Carbon::now()->subDay()->format('Y-m-d')
                    ])
                    ->with('worker_details')
                    ->get()
                    ->groupBy('worker_id')
                    ->filter(function ($timesheets) {
                        $dates = $timesheets
                            ->sortBy('date')
                            ->map(fn ($row) => Carbon::parse($row->date)->toDateString())
                            ->unique()
                            ->values();

                        $streak = 1;
                        for ($i = 0; $i < $dates->count() - 1; $i++) {
                            if (Carbon::parse($dates[$i])->addDay()->eq(Carbon::parse($dates[$i + 1]))) {
                                $streak++;
                                if ($streak >= 12) {
                                    return true;
                                }
                            } else {
                                $streak = 1;
                            }
                        }
                        return false;
                    })
                    ->keys()
                    ->all();

                    $workers = Worker::query()->whereIn('id', array_unique($workersWorkedGreaterThan12Days_2))
                        ->when(request('status') != null && request('status') != 'All', function ($q) {
                            return $q->where('status', request('status'));
                        })
                        ->with(['rights_to_work_details', 'incomplete_rights_to_work_details', 'id_documents', 'worker_documents'])
                        ->get()
                        ->toArray();
            } else {

                //List of all workers
                $workers = Worker::query()
                    ->when(request('status') != null && request('status') != 'All', function ($q) { return $q->where('status', request('status')); })
                    ->with(['rights_to_work_details', 'incomplete_rights_to_work_details', 'id_documents', 'worker_documents'])
                    ->get()
                    ->toArray();
            }

            $array = [];
            if ($workers) {
                foreach ($workers as $row) {
                    $rtw_date = RightToWorkHelper::getLatestDate($row['rights_to_work_details']);
                    $array[] = [
                        'worker_id'     => $row['worker_no'],
                        'worker_name'   => '<a href="'.url('view-worker-details/'.$row['id']).'" data-worker-id="'.$row['id'].'">'.$row['first_name'].' '.$row['middle_name'].' '.$row['last_name'].'</a>',
                        'status'        => $row['status'],
                        'right_to_work' => ($rtw_date) ? date('d-m-Y', strtotime($rtw_date)) : '-',
                        'mobile_number' => $row['mobile_number'],
                        'flags'         => WorkerHelper::getFlags($row),
                        'actions'       => $this->action($row['id'], $row['status']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($workers),
                'recordsFiltered'   => count($workers),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function action($id, $status) {
        $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" data-worker-id="'.$id.'">
                    <i class="fs-2 las la-paper-plane"></i>
                </a>';
        if (!in_array($status, ['Archived', 'Leaver'])) {
            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="delete_worker"
                data-worker-id="'.$id.'"
                data-worker-status="Archived"
                data-text="Are you sure you want to archive this associate!"
                data-btn-text="Yes, archive!"
                data-btn-color="danger">
                <i class="fs-2 las la-archive"></i>
            </a>';
        } elseif ($status == 'Archived') {
            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="delete_worker"
                data-worker-id="'.$id.'"
                data-worker-status="Active"
                data-text="Are you sure you want to active this associate!"
                data-btn-text="Yes, active!"
                data-btn-color="success">
                <i class="fs-2 las la-undo"></i>
            </a>';
        }

        $action .= '<a href="'.url('view-worker-details/'.$id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_client" data-worker-id="'.$id.'">
            <i class="fs-2 las la-arrow-right"></i>
        </a>';

        return $action;
    }

    public function createWorker() {
        $country     = CountryHelper::countryOption();
        $work_number = $this->createWorkerNumber();
        //$nationality = Nationality::query()->get();
        $nationality = Country::query()->select(['id', 'nationality'])->get();
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('workers.add_worker', compact('country', 'work_number', 'nationality', 'costCentre'));
    }

    /*public function checkWorkerValidation($section, Request $request) {
        if ($section == 0) {
            $validator = Validator::make($request->input(), [
                'title' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'date_of_birth' => 'required|date|before:-16 years',
                'gender' => 'required',
                'email_address' => 'required|email|unique:workers,email_address',
                'mobile_number' => 'required|numeric|unique:workers,mobile_number',
                'marital_status' => 'required',
                'nationality' => 'required',
                'national_insurance_number' => 'nullable|unique:workers,national_insurance_number',
                'cost_center' => 'required',
            ], [
                'date_of_birth.required' => 'The date of birth must be a date before -16 years'
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $checkWorker = Worker::query()->where('first_name', $request->input('first_name'))->where('last_name', $request->input('last_name'))->where('date_of_birth', date('Y-m-d', strtotime($request->input('date_of_birth'))))->count();
            if ($checkWorker > 0)
                return self::responseWithError('A worker with a matching name and DOB already exists on the system');

        } elseif ($section == 1) {
            $validator = Validator::make($request->all(), [
                'current_address_line_one' => 'required',
                'current_city' => 'required',
                'current_post_code' => 'required',

                'same_as_current_address' => 'nullable',
                'permanent_address_line_one' => 'required_without:same_as_current_address',
                'permanent_city' => 'required_without:same_as_current_address',
                'permanent_country' => 'required_without:same_as_current_address',

                'next_of_kin_first_name' => 'required',
                'next_of_kin_last_name' => 'required',
                'next_of_kin_mobile' => 'required_without:next_of_kin_email',
                'next_of_kin_email' => 'required_without:next_of_kin_mobile|nullable|email',
            ],[
                'current_address_line_one' => 'The address line one field is required.',
                'current_city' => 'The city field is required.',
                'current_post_code' => 'The postcode field is required.',

                'permanent_address_line_one.required_without' => 'Address Line one is required when "Same as UK address" is not checked.',
                'permanent_city.required_without' => 'City is required when "Same as UK address" is not checked.',
                'permanent_country.required_without' => 'Country is required when "Same as UK address" is not checked.',

                'next_of_kin_first_name' => 'The first name field is required.',
                'next_of_kin_last_name' => 'The surname field is required.',
                'next_of_kin_mobile.required_without' => 'The mobile field is required when email is not present.',
                'next_of_kin_email.email' => 'The email must be a valid email address.',
                'next_of_kin_email.required_without' => 'The email field is required when mobile is not present.',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

        } elseif ($section == 3) {

            $params = $request->input();
            if (empty($params['right_to_work'][0]))
                return self::validationError(['right_to_work' => ['The right to work field is required.']]);

            $rule = [];
            foreach ($params['right_to_work'] as $key => $rtwType) {
                $rule = array_merge($rule, $this->rtwValidationRule($rtwType, $key, $key+1, $params));
            }

            $validator = Validator::make($request->input(), $rule, ['required' => 'This field is required.']);
            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

        }

        return self::responseWithSuccess('Validation done.');
    }*/

    public function createWorkerNumber() {
        $year   = date('y');
        $month  = date('m');
        $project_series = WorkerSequenceNumber::query()->where('worker_number_year', $year)->where('worker_number_month', $month)->orderBy('id', 'desc')->first();

        $sequence    = ($project_series) ? $project_series['worker_number_sequence'] + 1 : '1';
        return [
            'worker_number_year'     => $year,
            'worker_number_month'    => $month,
            'worker_number_sequence' => $sequence,
            'worker_number'          => $year.$month.sprintf("%03d", $sequence),
        ];
    }

    public function createWorkerAction(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->input(), [
                'title' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'date_of_birth' => 'required|date|before:-16 years',
                'gender' => 'required',
                'email_address' => 'required|email|unique:workers,email_address',
                'mobile_number' => 'required|numeric|unique:workers,mobile_number',
                'marital_status' => 'required',
                'nationality' => 'required',
                'national_insurance_number' => 'nullable|unique:workers,national_insurance_number',
                'cost_center' => 'required',
            ], [
                'date_of_birth.required' => 'The date of birth must be a date before -16 years'
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $checkWorker = Worker::query()->where('first_name', $request->input('first_name'))->where('last_name', $request->input('last_name'))->where('date_of_birth', date('Y-m-d', strtotime($request->input('date_of_birth'))))->count();
            if ($checkWorker > 0)
                return self::responseWithError('A worker with a matching name and DOB already exists on the system');

            $params = $request->input();
            $worker = Worker::query()->create([
                'title'                     => $params['title'],
                'first_name'                => $params['first_name'],
                'middle_name'               => $params['middle_name'],
                'last_name'                 => $params['last_name'],
                'worker_no'                 => $params['worker_no'],
                'client_reference'          => $params['client_reference'],
                'national_insurance_number' => $params['national_insurance_number'],
                'payroll_reference'         => $params['payroll_reference'],
                'email_address'             => $params['email_address'],
                'mobile_number'             => $params['mobile_number'],
                'date_of_birth'             => date('Y-m-d', strtotime($params['date_of_birth'])),
                'marital_status'            => $params['marital_status'],
                'nationality'               => $params['nationality'],
/*                'name_of_partner'           => $params['name_of_partner'],
              'id_number_of_partner'      => $params['id_number_of_partner'],*/
                'gender'                    => $params['gender'],

            ]);
            WorkerSequenceNumber::query()->create($request->all(['worker_number_year', 'worker_number_month', 'worker_number_sequence']));

            $costCenter = $request->input('cost_center');

            if(!empty($costCenter) && isset($worker['id'])){
                WorkerCostCenter::query()->create([
                    'worker_id'   => $worker['id'],
                    'cost_center' => $costCenter,
                ]);
            }

            ActivityLogs::createAndDeleteLog($worker['id'], 'Create', 'Worker create', 'Worker');
            WorkerHelper::send_confirm_email($worker['id'], $worker['first_name'], $worker['last_name'], $worker['email_address']);

            DB::commit();
            return self::responseWithSuccess('Worker successfully created.', [
                'worker_id' => $worker['id'],
                'worker_name' => $worker['first_name'].' '.$worker['middle_name'].' '.$worker['last_name']
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    /*public function createWorkerAction(Request $request) {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'id_document_type' => 'required',
                'id_document_number' => 'required',
                'id_document_expiry_date' => 'required',
                'id_document_file' => 'required|file|max:10240|mimes:png,jpg,jpeg,pdf',
                'registration_file' => 'required|file|max:10240|mimes:png,jpg,jpeg,pdf',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            $rightToWork = [];

            $worker = Worker::query()->create([
                'worker_no'                 => $params['worker_no'],
                'client_reference'          => $params['client_reference'],
                'payroll_reference'         => $params['payroll_reference'],
                'title'                     => $params['title'],
                'first_name'                => $params['first_name'],
                'middle_name'               => $params['middle_name'],
                'last_name'                 => $params['last_name'],
                'date_of_birth'             => date('Y-m-d', strtotime($params['date_of_birth'])),
                'gender'                    => $params['gender'],
                'email_address'             => $params['email_address'],
                'mobile_number'             => $params['mobile_number'],
                'marital_status'            => $params['marital_status'],
                'nationality'               => $params['nationality'],
                'national_insurance_number' => $params['national_insurance_number'],
                'name_of_partner'           => $params['name_of_partner'],
                'id_number_of_partner'      => $params['id_number_of_partner'],

                'same_as_current_address' => ($request->has('same_as_current_address')) ? $params['same_as_current_address'] : null,
                'same_as_worker_home_address' => ($request->has('same_as_worker_home_address')) ? $params['same_as_worker_home_address'] : null,
                'same_as_current_address_for_next_of_kin' => ($request->has('same_as_current_address_for_next_of_kin')) ? $params['same_as_current_address_for_next_of_kin'] : null,

                'current_address_line_one'  => $params['current_address_line_one'],
                'current_address_line_two'  => $params['current_address_line_two'],
                'current_country'           => $params['current_country'],
                'current_state'             => $params['current_state'],
                'current_city'              => $params['current_city'],
                'current_post_code'          => $params['current_post_code'],

                'permanent_address_line_one'=> $params['permanent_address_line_one'],
                'permanent_address_line_two'=> $params['permanent_address_line_two'],
                'permanent_country'         => $params['permanent_country'],
                'permanent_state'           => $params['permanent_state'],
                'permanent_city'            => $params['permanent_city'],
                'permanent_post_code'        => $params['permanent_post_code'],

                'next_of_kin_first_name'      => $params['next_of_kin_first_name'],
                'next_of_kin_last_name'       => $params['next_of_kin_last_name'],
                'next_of_kin_email'           => $params['next_of_kin_email'],
                'next_of_kin_mobile'          => $params['next_of_kin_mobile'],
                'next_of_kin_address_line_one'=> $params['next_of_kin_address_line_one'],
                'next_of_kin_address_line_two'=> $params['next_of_kin_address_line_two'],
                'next_of_kin_country'         => $params['next_of_kin_country'],
                'next_of_kin_state'           => $params['next_of_kin_state'],
                'next_of_kin_city'            => $params['next_of_kin_city'],
                'next_of_kin_zip_code'        => $params['next_of_kin_zip_code'],

                'bank_name'                 => $params['bank_name'],
                'bank_account_number'       => $params['bank_account_number'],
                'bank_ifsc_code'            => $params['bank_ifsc_code'],
            ]);
            WorkerSequenceNumber::query()->create($request->all(['worker_number_year', 'worker_number_month', 'worker_number_sequence']));

            $costCenter = $request->input('cost_center');

            if(!empty($costCenter) && isset($worker['id'])){
                WorkerCostCenter::create([
                    'worker_id'   => $worker['id'],
                    'cost_center' => $costCenter,
                ]);
            }

            foreach ($params['right_to_work'] as $key => $rtw_row) {

                $uk_id_document_type    = $params['uk_id_document_type'];
                $uk_id_document_number  = $params['uk_id_document_number'];
                $start_date             = $params['start_date'];
                $end_date               = $params['expiry_date'];
                $reference_number       = $params['reference_number'];
                $worker_restrictions    = $params['worker_restrictions'];
                $document_scan          = $request->file('document_scan');

                $file_name = null;
                if(isset($document_scan[$key])) {
                    $file_upload = FileHelper::file_upload($document_scan[$key], 'workers/right_to_work');
                    $file_name   = $file_upload['file_name'];
                }

                if ($rtw_row) {

                    $endDate = ($uk_id_document_type[$key] != 'Passport') ? date($end_date[$key]) ? date('Y-m-d', strtotime($end_date[$key])) : '2199-12-31' : '2199-12-31';

                    $rightToWork[] = [
                        'worker_id'                 => $worker['id'],
                        'user_id'                   => Auth::user()['id'],
                        'right_to_work_type'        => $rtw_row,
                        'right_to_work_expiry_date' => date($end_date[$key]) ? date('Y-m-d', strtotime($end_date[$key])) : null,
                        'uk_id_document_type'       => $uk_id_document_type[$key],
                        'uk_id_document_number'     => $uk_id_document_number[$key],
                        'start_date'                => ($start_date[$key]) ? date('Y-m-d', strtotime($start_date[$key])) : null,
                        'end_date'                  => $endDate,
                        'reference_number'          => $reference_number[$key],
                        'worker_restrictions'       => $worker_restrictions[$key],
                        'document_scan'             => $file_name,
                    ];
                }
            }

            if ($rightToWork)
                RightsToWork::query()->insert($rightToWork);

            $document_file_upload = FileHelper::file_upload($request->file('id_document_file'), 'workers/document');
            WorkerDocument::query()->create([
                'worker_id' => $worker['id'],
                'type' => 'id_document',
                'document_type' => $params['id_document_type'],
                'document_no' => $params['id_document_number'],
                'expiry_date' => date('Y-m-d', strtotime($params['id_document_expiry_date'])),
                'document_file' => $document_file_upload['file_name'],
                'document_file_type' => $document_file_upload['file_type'],
                'uploaded_by' => Auth::id(),
            ]);

            $registration_document_file_upload = FileHelper::file_upload($request->file('registration_file'), 'workers/document');
            WorkerDocument::query()->create([
                'worker_id' => $worker['id'],
                'type' => 'registration',
                'document_file' => $registration_document_file_upload['file_name'],
                'document_file_type' => $registration_document_file_upload['file_type'],
                'document_file_title' => $registration_document_file_upload['file_original_name'],
                'uploaded_by' => Auth::id(),
            ]);

            ActivityLogs::createAndDeleteLog($worker['id'], 'Create', 'Worker create', 'Worker');
            WorkerHelper::send_confirm_email($worker['id'], $worker['first_name'], $worker['email_address']);

            DB::commit();
            return self::responseWithSuccess('Worker successfully created.', [
                'worker_id' => $worker['id'],
                'worker_name' => $worker['first_name'].' '.$worker['middle_name'].' '.$worker['last_name']
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }*/

    public function viewWorker($id) {
        $worker      = Worker::query()->where('id', $id)->with([
            'worker_cost_center', 'rights_to_work_details', 'incomplete_rights_to_work_details', 'worker_documents', 'id_documents', 'accommodation_details', 'groups'
        ])->first()->toArray();
        $country     = Country::query()->get();
        //$nationality = Nationality::query()->get();
        $nationality = Country::query()->select(['id', 'nationality'])->get();
        $client = Client::query()->orderBy('company_name', 'asc')->get();

        $allNotes = Note::query()->where('note_type', 'criminal')
            ->where('worker_id', $id)
            ->where('type', 'Worker')
            ->with('user_details')
            ->orderBy('id', 'desc')
            ->union(
                Note::query()->where('note_type', 'medical')
                    ->where('worker_id', $id)
                    ->where('type', 'Worker')
                    ->with('user_details')
                    ->orderBy('id', 'desc')
            )
            ->union(
                Note::query()->where('note_type', 'general')
                    ->where('worker_id', $id)
                    ->where('type', 'Worker')
                    ->with('user_details')
                    ->orderBy('id', 'desc')
            )
            ->get()
            ->groupBy('note_type');

        $worker_cost_centers = array_column($worker['worker_cost_center'], 'cost_center');
        $accommodation_site = Accommodation::query()
            ->where(function($query) use ($worker_cost_centers) {
                foreach ($worker_cost_centers as $center) {
                    $query->orWhere('cost_center', 'like', '%' . $center . '%');
                }
            })
            ->get();

        $pickup_point = PickUpPoint::query()
            ->where(function($query) use ($worker_cost_centers) {
                foreach ($worker_cost_centers as $center) {
                    $query->orWhere('cost_center', 'like', '%' . $center . '%');
                }
            })
            ->get();

        $assignedGroupIds = GroupWithWorker::where('worker_id', $id)
            ->whereNull('deleted_at') // only active assignments
            ->pluck('group_id')
            ->toArray();

        $group = Group::query()
            ->select(['id','name'])
            ->where('consultant_id', Auth::id())
            ->whereNull('deleted_at')
            ->whereNotIn('id', $assignedGroupIds)
            ->get();

        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('workers.view_worker', compact('worker', 'nationality', 'country', 'client', 'allNotes', 'accommodation_site', 'pickup_point', 'group', 'costCentre'));
    }

    /*public function addNewSectionForWorkExperience(Request $request) {
        try {
            $count = $request->input('total_work_experience_section') + 1;
            return self::responseWithSuccess('New work experience section successfully created.', [
                'count'     => $count,
                'section'   => '<div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="job_title" class="fs-6 fw-bold">Job Title / position</label>
                                <input type="text" name="job_title[]" id="job_title_'.$count.'" class="form-control" placeholder="Job Title / position" value="" />
                                <span class="error text-danger" id="job_title_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="company_name" class="fs-6 fw-bold">Company / Organization</label>
                                <input type="text" name="company_name[]" id="company_name_'.$count.'" class="form-control" placeholder="Company / Organization" value="" />
                                <span class="error text-danger" id="company_name_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="work_start_date" class="fs-6 fw-bold">Start date</label>
                                <input type="date" name="work_start_date[]" id="work_start_date_'.$count.'" class="form-control" placeholder="" value="" />
                                <span class="error text-danger" id="work_start_date_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-2 fv-row fv-plugins-icon-container">
                                <label for="work_end_date" class="fs-6 fw-bold">End date</label>
                                <input type="date" name="work_end_date[]" id="work_end_date_'.$count.'" class="form-control" placeholder="" value="" />
                                <span class="error text-danger" id="work_end_date_error"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <input type="hidden" class="form-check-input" name="current_working_here_textbox[]" id="current_working_here_textbox_'.$count.'" value="0">
                                <label for="current_working_here" class="form-check form-check-inline me-5 is-invalid">
                                    <input type="checkbox" class="form-check-input check_current_working" name="current_working_here[]" id="current_working_here_'.$count.'" value="'.$count.'">
                                    <span class="fw-bold ps-2 fs-6">Currently Working here</span>
                                </label>
                            </div>
                        </div>
                    </div><hr>
                </div>'
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }*/

    /*public function addNewSectionForInterviewRecord(Request $request) {
        try {
            $count = $request->input('total_interview_record_section') + 1;
            return self::responseWithSuccess('New interview record section successfully created.', [
                'count'     => $count,
                'section'   => '<div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="interview_date_'.$count.'" class="fs-6 fw-bold">Date</label>
                                <input type="date" name="interview_date[]" id="interview_date_'.$count.'" class="form-control" placeholder="Interview date" value="" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="interview_status_'.$count.'" class="fs-6 fw-bold">Status</label>
                                <select name="interview_status[]" id="interview_status_'.$count.'" class="form-select form-select-lg" data-control="select2" data-placeholder="Interview Status Pass / Fail" data-allow-clear="true">
                                    <option></option>
                                    <option value="Pass">Pass</option>
                                    <option value="Fail">Fail</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="interview_details_'.$count.'" class="fs-6 fw-bold">Interview Detail</label>
                                <textarea name="interview_details[]" id="interview_details_'.$count.'" rows="5" placeholder="Add details here" class="form-control"></textarea>
                            </div>
                        </div>
                    </div><hr>
                </div>'
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }*/

    /*public function addNewSectionForWorkerNote(Request $request) {
        try {
            $count = $request->input('total_worker_note_section') + 1;
            return self::responseWithSuccess('New worker note section successfully created.', [
                'count'     => $count,
                'section'   => '<div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <label for="worker_note_'.$count.'" class="fs-6 fw-bold">Notes</label>
                                <textarea name="worker_note[]" id="worker_note_'.$count.'" rows="5" placeholder="Write text here..." class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>'
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }*/

    /*public function addNewSectionForRightToWork(Request $request) {
        try {
            $count = $request->input('total_right_to_work_section') + 1;
            return self::responseWithSuccess('New worker note section successfully created.', [
                'count'     => $count,
                'section'   => '<div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <select name="right_to_work[]" id="right_to_work_'.$count.'" data-section="'.$count.'" class="form-select form-select-lg rtw_drp" data-placeholder="Select right to work" data-allow-clear="true">
                                                <option value="">Select right to work type</option>
                                                <option value="UK Citizen">UK Citizen</option>
                                                <option value="Settled Status">Settled Status</option>
                                                <option value="Pre-Settled Status">Pre-Settled Status</option>
                                                <option value="COA">COA</option>
                                                <option value="Tier 2 (skilled visa)">Tier 2 (skilled visa)</option>
                                                <option value="Tier 4 (student visa)">Tier 4 (student visa)</option>
                                                <option value="Tier 5 (Seasonal Worker Scheme)">Tier 5 (Seasonal Worker Scheme)</option>
                                                <option value="Tier 5 (Seasonal Worker Scheme - Poultry)">Tier 5 (Seasonal Worker Scheme - Poultry)</option>
                                                <option value="Timebound (BRP)">Timebound (BRP)</option>
                                                <option value="Indefinite leave to remain.">Indefinite leave to remain.</option>
                                            </select>
                                            <span class="text-danger error" id="right_to_work_'.$count.'_error"></span>
                                        </div>
                                    </div>

                                    <div class="col-lg-4" id="uk_id_document_type_section_'.$count.'" style="display:none;">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="uk_id_document_type_'.$count.'" id="label_uk_id_document_type_'.$count.'" class="fs-6 fw-bold">Document type</label>
                                            <select name="uk_id_document_type[]" id="uk_id_document_type_'.$count.'" data-section="'.$count.'" class="form-select form-select-lg dt_drp" data-placeholder="Select document type" data-allow-clear="true">
                                                <option value="">Select document type</option>
                                                <option value="Passport">Passport</option>
                                                <option value="Birth Certificate">Birth Certificate</option>
                                            </select>
                                            <span class="text-danger error" id="uk_id_document_type_'.$count.'_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4" id="uk_id_document_number_section_'.$count.'" style="display:none;">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="uk_id_document_number_'.$count.'" id="label_uk_id_document_number_'.$count.'" class="fs-6 fw-bold">Document Number</label>
                                            <input type="text" name="uk_id_document_number[]" id="uk_id_document_number_'.$count.'" placeholder="Enter document number" class="form-control">
                                            <span class="text-danger error" id="uk_id_document_number_'.$count.'_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4" id="reference_number_section_'.$count.'" style="display:none;">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="reference_number_'.$count.'" id="label_reference_number_'.$count.'" class="fs-6 fw-bold">Reference number</label>
                                            <input type="text" name="reference_number[]" id="reference_number_'.$count.'" placeholder="Enter number" class="form-control">
                                            <span class="text-danger error" id="reference_number_'.$count.'_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4" id="start_date_section_'.$count.'" style="display:none;">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="start_date_'.$count.'" id="label_start_date_'.$count.'" class="fs-6 fw-bold">Start date</label>
                                            <div class="position-relative d-flex align-items-center">
                                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="black"></path>
                                                            <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="black"></path>
                                                            <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="black"></path>
                                                        </svg>
                                                    </span>
                                                <input class="form-control ps-12 flatpickr-input r_start_date" placeholder="Select date" name="start_date[]" id="start_date_'.$count.'" type="text" readonly="readonly" value="">
                                            </div>
                                            <span class="text-danger error" id="start_date_'.$count.'_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4" id="expiry_date_section_'.$count.'" style="display:none;">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="end_date_'.$count.'" id="label_end_date_'.$count.'" class="fs-6 fw-bold">Expiry date</label>
                                            <div class="position-relative d-flex align-items-center">
                                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="black"></path>
                                                            <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="black"></path>
                                                            <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="black"></path>
                                                        </svg>
                                                    </span>
                                                <input class="form-control ps-12 flatpickr-input r_expiry_date" placeholder="Select date" name="expiry_date[]" id="expiry_date_'.$count.'" type="text" readonly="readonly" value="">
                                            </div>
                                            <span class="text-danger error" id="expiry_date_'.$count.'_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4" id="worker_restrictions_section_'.$count.'" style="display:none;">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="worker_restrictions_'.$count.'" id="label_worker_restrictions_'.$count.'" class="fs-6 fw-bold">Worker restrictions</label>
                                            <input type="text" name="worker_restrictions[]" id="worker_restrictions_'.$count.'" placeholder="Enter text" class="form-control">
                                            <span class="text-danger error" id="worker_restrictions_'.$count.'_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4" id="document_scan_section_'.$count.'" style="display:none;">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <label for="document_scan_'.$count.'" id="label_document_scan_'.$count.'" class="fs-6 fw-bold">Document Scan</label>
                                            <input type="file" name="document_scan[]" id="document_scan_'.$count.'" class="form-control" accept="image/png, image/jpeg, application/pdf">
                                            <span class="text-danger error" id="document_scan_'.$count.'_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <hr>'
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }*/

    /*public function addNewSectionForDocument(Request $request) {
        try {
            $count = $request->input('total_document_section') + 1;
            return self::responseWithSuccess('New document section successfully created.', [
                'count'     => $count,
                'section'   => '<div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <input type="file" name="document_file[]" id="document_'.$count.'" class="form-control"  accept="image/jpg, image/png, image/jpeg, application/pdf"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-10 fv-row fv-plugins-icon-container">
                                            <input type="text" name="document_title[]" id="document_title_'.$count.'" class="form-control border-primary" placeholder="Enter document title."/>
                                        </div>
                                    </div>
                                </div>'
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }*/

    public function deleteWorkerAction($id) {
        try {
            $worker = Worker::query()->where('id', $id)->first();
            if(!$worker)
                return self::responseWithError('Worker details not found, please try again.');

            ActivityLogs::updatesLog($id, 'Worker update', [ 'status' => 'Leaver'], Worker::query()->select(['status'])->where('id', $id)->first()->toArray(), 'Worker');
            //ActivityLogs::createAndDeleteLog($id, 'Delete', 'Worker delete', 'Worker');

            $worker->update(['status' => 'Leaver']);
            //$worker->delete();

            return self::responseWithSuccess('Worker successfully deleted.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateWorkerBasicDetails(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'title'             => 'required',
                'first_name'        => 'required',
                'last_name'         => 'required',
                'worker_no'         => 'required',
                'date_of_birth'     => 'required',
                'gender'            => 'required',
                'email_address'     => 'required|unique:workers,email_address,'.$request->input('update_id'),
                'mobile_number'     => 'required|unique:workers,mobile_number,'.$request->input('update_id'),
                'marital_status'    => 'required',
                'nationality'       => 'required',
                'national_insurance_number' => 'nullable|unique:workers,national_insurance_number,'.$request->input('update_id'),
                'cost_center'       => 'required',
            ],[
                'date_of_birth.required' => 'The date of birth must be a date before -16 years'
            ]);

            if($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            $costCenter = $params['cost_center'];


            $params['date_of_birth'] = date('Y-m-d', strtotime($params['date_of_birth']));

            $fields = ['title', 'first_name', 'middle_name', 'last_name', 'worker_no', 'client_reference', 'national_insurance_number', 'payroll_reference', 'mobile_number', 'email_address', 'date_of_birth', 'gender', 'marital_status', 'nationality'];
            ActivityLogs::updatesLog(
                $params['update_id'],
                'Worker update',
                $request->only($fields),
                Worker::query()->select($fields)->where('id', $params['update_id'])->first()->toArray(),
                'Worker'
            );

            Worker::query()->where('id', $params['update_id'])->update([
                'title' => $params['title'],
                'first_name' => $params['first_name'],
                'middle_name' => $params['middle_name'],
                'last_name' => $params['last_name'],
                'worker_no' => $params['worker_no'],
                'client_reference' => $params['client_reference'],
                'national_insurance_number' => $params['national_insurance_number'],
                'payroll_reference' => $params['payroll_reference'],
                'mobile_number' => $params['mobile_number'],
                'email_address' => $params['email_address'],
                'date_of_birth' => $params['date_of_birth'],
                'gender' => $params['gender'],
                'marital_status' => $params['marital_status'],
                'nationality' => $params['nationality']
                /*'name_of_partner' => $params['name_of_partner'],
                'id_number_of_partner' => $params['id_number_of_partner'],*/
                /*'bank_ifsc_code' => $params['bank_ifsc_code'],
                'bank_account_number' => $params['bank_account_number'],
                'bank_name' => $params['bank_name']*/
            ]);

            if (!empty($costCenter)) {
                WorkerCostCenter::query()->updateOrInsert(
                    ['worker_id' => $params['update_id']],
                    ['cost_center' => $costCenter]
                );
            }

            return self::responseWithSuccess('Basic details successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateWorkerAddresses(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                'same_as_current_address' => 'nullable',
                'permanent_address_line_one' => 'required_without:same_as_current_address',
                'permanent_city' => 'required_without:same_as_current_address',
                'permanent_country' => 'required_without:same_as_current_address',

                'next_of_kin_first_name' => 'required',
                //'next_of_kin_last_name' => 'required',
                'next_of_kin_mobile' => 'required_without:next_of_kin_email',
                'next_of_kin_email' => 'required_without:next_of_kin_mobile|nullable|email',
            ],[
                'permanent_address_line_one.required_without' => 'Address Line one is required when "Same as UK address" is not checked.',
                'permanent_city.required_without' => 'City is required when "Same as UK address" is not checked.',
                'permanent_country.required_without' => 'Country is required when "Same as UK address" is not checked.',

                'next_of_kin_first_name' => 'The name field is required.',
                //'next_of_kin_last_name' => 'The surname field is required.',
                'next_of_kin_mobile.required_without' => 'The mobile field is required when email is not present.',
                'next_of_kin_email.email' => 'The email must be a valid email address.',
                'next_of_kin_email.required_without' => 'The email field is required when mobile is not present.',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();

            $fields = ['permanent_address_line_one', 'permanent_address_line_two', 'permanent_country', 'permanent_state', 'permanent_city', 'permanent_post_code', 'next_of_kin_first_name', 'next_of_kin_email', 'next_of_kin_mobile',
                /*'next_of_kin_last_name', 'next_of_kin_address_line_one', 'next_of_kin_address_line_two', 'next_of_kin_country', 'next_of_kin_state', 'next_of_kin_city', 'next_of_kin_post_code' */
            ];
            ActivityLogs::updatesLog(
                $params['update_addresses_id'],
                'Worker update',
                $request->only($fields),
                Worker::query()->select($fields)->where('id', $params['update_addresses_id'])->first()->toArray(),
                'Worker'
            );

            Worker::query()->where('id', $params['update_addresses_id'])->update([
                'same_as_current_address' => ($request->has('same_as_current_address')) ? $params['same_as_current_address'] : null,
                'same_as_worker_home_address' => ($request->has('same_as_worker_home_address')) ? $params['same_as_worker_home_address'] : null,
                'same_as_current_address_for_next_of_kin' => ($request->has('same_as_current_address_for_next_of_kin')) ? $params['same_as_current_address_for_next_of_kin'] : null,

                'permanent_address_line_one' => $params['permanent_address_line_one'],
                'permanent_address_line_two' => $params['permanent_address_line_two'],
                'permanent_country' => $params['permanent_country'],
                'permanent_state' => $params['permanent_state'],
                'permanent_city' => $params['permanent_city'],
                'permanent_post_code' => $params['permanent_post_code'],

                'next_of_kin_first_name' => $params['next_of_kin_first_name'],
                'next_of_kin_email' => $params['next_of_kin_email'],
                'next_of_kin_mobile' => $params['next_of_kin_mobile'],
                'next_of_kin_relationship' => $params['next_of_kin_relationship'],

                /* 'next_of_kin_last_name' => $params['next_of_kin_last_name'],
                'next_of_kin_address_line_one' => $params['next_of_kin_address_line_one'],
                'next_of_kin_address_line_two' => $params['next_of_kin_address_line_two'],
                'next_of_kin_country' => $params['next_of_kin_country'],
                'next_of_kin_state' => $params['next_of_kin_state'],
                'next_of_kin_city' => $params['next_of_kin_city'],
                'next_of_kin_post_code' => $params['next_of_kin_post_code'], */
            ]);

            WorkerHelper::automaticWorkerActive($params['update_addresses_id']);
            return self::responseWithSuccess('Worker addresses successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateWorkerUkAddress(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'accommodation_type' => 'required',

                'current_address_line_one' => 'required_if:accommodation_type,arranged_by_worker',
                'current_city' => 'required_if:accommodation_type,arranged_by_worker',
                'current_post_code' => 'required_if:accommodation_type,arranged_by_worker',

                'accommodation_site' => 'required_if:accommodation_type,supplied_by_pro_force',

                'proforce_transport' => 'required',
                'preferred_pick_up_point_id' => 'required_if:proforce_transport,Yes',

                'same_as_current_address' => 'nullable',
                'permanent_address_line_one' => 'required_without:same_as_current_address',
                'permanent_city' => 'required_without:same_as_current_address',
                'permanent_country' => 'required_without:same_as_current_address',

                'next_of_kin_first_name' => 'required',
                'next_of_kin_mobile' => 'required_without:next_of_kin_email',
                'next_of_kin_email' => 'required_without:next_of_kin_mobile|nullable|email',
            ], [
                'accommodation_type.required' => 'Accommodation type is required.',

                'current_address_line_one.required_if' => 'Address line one is required when accommodation type is arranged by worker.',
                'current_city.required_if' => 'City is required when accommodation type is arranged by worker.',
                'current_post_code.required_if' => 'Postcode is required when accommodation type is arranged by worker.',

                'accommodation_site.required_if' => 'Accommodation site is required when accommodation type is supplied by Pro Force.',

                'proforce_transport.required' => 'Please specify if you require ProForce transport.',
                'preferred_pick_up_point_id.required_if' => 'Preferred pick up point id is required when ProForce transport is Yes.',

                'permanent_address_line_one.required_without' => 'Address Line one is required when "Same as UK address" is not checked.',
                'permanent_city.required_without' => 'City is required when "Same as UK address" is not checked.',
                'permanent_country.required_without' => 'Country is required when "Same as UK address" is not checked.',

                'next_of_kin_first_name' => 'The name field is required.',
                'next_of_kin_mobile.required_without' => 'The mobile field is required when email is not present.',
                'next_of_kin_email.email' => 'The email must be a valid email address.',
                'next_of_kin_email.required_without' => 'The email field is required when mobile is not present.',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();

            $fields = ['accommodation_type', 'accommodation_site', 'current_address_line_one', 'current_address_line_two', 'current_post_code', 'current_city', 'current_state', 'current_country', 'proforce_transport', 'preferred_pick_up_point_id', 'permanent_address_line_one', 'permanent_address_line_two', 'permanent_country', 'permanent_state', 'permanent_city', 'permanent_post_code', 'next_of_kin_first_name', 'next_of_kin_email', 'next_of_kin_mobile'];
            ActivityLogs::updatesLog(
                $params['update_uk_addresses_id'],
                'Worker update',
                $request->only($fields),
                Worker::query()->select($fields)->where('id', $params['update_uk_addresses_id'])->first()->toArray(),
                'Worker'
            );

            $update_array = [
                'accommodation_type' => $params['accommodation_type'],
                'proforce_transport' => $params['proforce_transport'],

                'same_as_current_address' => ($request->has('same_as_current_address')) ? $params['same_as_current_address'] : null,
                'permanent_address_line_one' => $params['permanent_address_line_one'],
                'permanent_address_line_two' => $params['permanent_address_line_two'],
                'permanent_country' => $params['permanent_country'],
                'permanent_state' => $params['permanent_state'],
                'permanent_city' => $params['permanent_city'],
                'permanent_post_code' => $params['permanent_post_code'],

                'next_of_kin_first_name' => $params['next_of_kin_first_name'],
                'next_of_kin_email' => $params['next_of_kin_email'],
                'next_of_kin_mobile' => $params['next_of_kin_mobile'],
                'next_of_kin_relationship' => $params['next_of_kin_relationship'],
            ];

            if ($params['accommodation_type'] === 'arranged_by_worker') {
                $update_array['current_address_line_one'] = $params['current_address_line_one'];
                $update_array['current_address_line_two'] = $params['current_address_line_two'] ?? null;
                $update_array['current_country'] = $params['current_country'] ?? null;
                $update_array['current_state'] = $params['current_state'] ?? null;
                $update_array['current_city'] = $params['current_city'];
                $update_array['current_post_code'] = $params['current_post_code'];

                $update_array['accommodation_site'] = null;
            } elseif ($params['accommodation_type'] === 'supplied_by_pro_force') {
                $update_array['accommodation_site'] = $params['accommodation_site'];

                $update_array['current_address_line_one'] = null;
                $update_array['current_address_line_two'] = null;
                $update_array['current_country'] = null;
                $update_array['current_state'] = null;
                $update_array['current_city'] = null;
                $update_array['current_post_code'] = null;
            }

            if ($params['proforce_transport'] === 'Yes') {
                $update_array['preferred_pick_up_point_id'] = $params['preferred_pick_up_point_id'];
            } else {
                $update_array['preferred_pick_up_point_id'] = null;
            }
            Worker::query()->where('id', $params['update_uk_addresses_id'])->update($update_array);
            WorkerHelper::automaticWorkerActive($params['update_uk_addresses_id']);
            return self::responseWithSuccess('Worker addresses successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateWorkerOtherDetails(Request $request) {
        try {
            $params = $request->input();
            $params = array_merge($params, [
                'right_to_work'             => ($request->has('right_to_work')) ? implode('~~~~~', $params['right_to_work']) : '',
                'die_diligence'             => ($request->has('die_diligence')) ? implode('~~~~~', $params['die_diligence']) : '',
                'compliance'                => ($request->has('compliance')) ? implode('~~~~~', $params['compliance']) : '',
            ]);

            unset($params['_token']);
            unset($params['other_details_update_id']);

            $update_id = $request->input('other_details_update_id'); /*'visa_type', 'visa_reference_number', 'visa_start_date', 'visa_end_date', 'right_to_work', 'die_diligence', 'compliance'*/
            ActivityLogs::updatesLog($update_id, 'Worker update', $params, Worker::query()->select(['bank_name', 'bank_account_number', 'bank_ifsc_code', 'medical_issue_details', 'criminal_conviction_details'])->where('id', $update_id)->first()->toArray(), 'Worker');
            Worker::query()->where('id', $update_id)->update([
                'right_to_work'             => $params['right_to_work'],
                /*'die_diligence'             => $params['die_diligence'],
                'compliance'                => $params['compliance'],*/
                //'region'                    => $params['region'],
                /*'visa_type'                 => $params['visa_type'],
                'visa_reference_number'     => $params['visa_reference_number'],
                'visa_start_date'           => $params['visa_start_date'],
                'visa_end_date'             => $params['visa_end_date'],*/
                'bank_name'                 => $params['bank_name'],
                'bank_account_number'       => $params['bank_account_number'],
                'bank_ifsc_code'            => $params['bank_ifsc_code'],
                'medical_issue_details'     => $params['medical_issue_details'],
                'criminal_conviction_details'=> $params['criminal_conviction_details'],
            ]);
            return self::responseWithSuccess('Other details successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateWorkerBankDetails(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                /*'bank_account_name' => 'required',
                'bank_name' => 'required',
                'bank_ifsc_code' => 'required',
                'bank_account_number' => 'required',*/
                'tax_statement' => 'required',
                'opt_out_48_hour_week' => 'required',
            ], [
                /*'bank_account_name.required' => 'Bank account name is required.',
                'bank_name.required' => 'Bank name is required.',
                'bank_ifsc_code.required' => 'Bank sort code is required.',
                'bank_account_number.required' => 'Bank account number is required.',*/
                'tax_statement.required' => 'Tax treatment is required.',
                'opt_out_48_hour_week.required' => 'Opt out 48 hour week is required.',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();

            if ($params['bank_account_number'] || $params['bank_ifsc_code']) {
                $checkBank = Worker::query()->where('id', '!=', $params['update_bank_details_id'])
                    ->where('bank_account_number', $params['bank_account_number'])
                    ->where('bank_ifsc_code', $params['bank_ifsc_code'])
                    ->exists();

                if ($checkBank) {
                    return self::responseWithError('This bank account number with bank sort code is already used.');
                }
            }

            $fields = ['proforce_to_open_bank_account', 'bank_account_name', 'bank_name', 'bank_ifsc_code', 'bank_account_number', 'tax_treatment', '48_hour_opt_out',
                ];
            ActivityLogs::updatesLog(
                $params['update_bank_details_id'],
                'Worker bank details updated',
                $request->only($fields),
                Worker::query()->select($fields)->where('id', $params['update_bank_details_id'])->first()->toArray(),
                'Worker'
            );

            Worker::query()->where('id', $params['update_bank_details_id'])->update([
                'proforce_to_open_bank_account' => ($request->has('requires_bank_account_setup')) ? $params['requires_bank_account_setup'] : 'No',
                'bank_account_name' => $params['bank_account_name'],
                'bank_name' => $params['bank_name'],
                'bank_ifsc_code' => $params['bank_ifsc_code'],
                'bank_account_number' => $params['bank_account_number'],
                'tax_treatment' => $params['tax_statement'],
                '48_hour_opt_out' => $params['opt_out_48_hour_week'],
            ]);

            return self::responseWithSuccess('Worker bank details successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    /*public function updateWorkerSkillDetails(Request $request) {
        try {
            $params     = $request->input();
            $update_id  = $request->input('skill_details_update_id');

            ActivityLogs::updatesLog($update_id, 'Worker update', [
                    'skill'     => ($request->has('skill')) ? implode('~~~~~', $params['skill']) : '',
                ],
                Worker::query()->select(['skill'])->where('id', $update_id)->first()->toArray(),
                'Worker'
            );

            Worker::query()->where('id', $update_id)->update([
                'skill' => ($request->has('skill')) ? implode('~~~~~', $params['skill']) : '',
            ]);
            return self::responseWithSuccess('Skill & experience details successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }*/

    public function updateWorkerDocumentDetails(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'required_document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',

                'document_type' => 'required_if:upload_required_document_title,id',
                'document_number' => 'required_if:upload_required_document_title,id',
                'document_expiry_date' => 'required_if:upload_required_document_title,id|nullable|date',
            ], [
                'required_document_file.required' => 'The document field is required.',
                'required_document_file.max' => 'Selected file size is higher than 10MB.',
                'required_document_file.mimes' => 'Selected file extension is not supported. Only jpg, png, jpeg, pdf are allowed.',

                'document_type.required_if' => 'The document type is required.',
                'document_number.required_if' => 'The document number is required.',
                'document_expiry_date.required_if' => 'The expiry date is required.',
                'document_expiry_date.date' => 'The expiry date must be a valid date.',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            $worker = Worker::query()->where('id', $params['upload_required_document_worker_id'])->first();
            if (!$worker) {
                return self::responseWithError('Worker details not found please try again.');
            }

            if ($params['upload_required_document_id'] == 0) {
                $document_file_upload = FileHelper::file_upload($request->file('required_document_file'), 'workers/document');
                WorkerDocument::query()->create([
                    'worker_id'           => $worker['id'],

                    'document_type' => ($params['upload_required_document_title'] == 'id') ? $params['document_type'] : null,
                    'document_no' => ($params['upload_required_document_title'] == 'id') ? $params['document_number'] : null,
                    'expiry_date' => ($params['upload_required_document_title'] == 'id') ? date('Y-m-d', strtotime($params['document_expiry_date'])) : null,

                    'document_file'       => $document_file_upload['file_name'],
                    'document_file_type'  => $document_file_upload['file_type'],
                    'document_file_title' => strtoupper($params['upload_required_document_title']),
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => Carbon::now()
                ]);
            } else {
                $workerDocument = WorkerDocument::query()->where('id', $params['upload_required_document_id'])->first();
                if (!$workerDocument) {
                    return self::responseWithError('Worker document not found please try again later.');
                }

                FileHelper::file_remove($workerDocument['document_file'], 'workers/document');
                $document_file_upload = FileHelper::file_upload($request->file('required_document_file'), 'workers/document');
                WorkerDocument::query()->where('id', $params['upload_required_document_id'])->update([
                    'worker_id' => $worker['id'],

                    'document_type' => ($params['upload_required_document_title'] == 'id') ? $params['document_type'] : null,
                    'document_no' => ($params['upload_required_document_title'] == 'id') ? $params['document_number'] : null,
                    'expiry_date' => ($params['upload_required_document_title'] == 'id') ? date('Y-m-d', strtotime($params['document_expiry_date'])) : null,

                    'document_file' => $document_file_upload['file_name'],
                    'document_file_type' => $document_file_upload['file_type'],
                    'document_file_title' => strtoupper($params['upload_required_document_title']),
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => Carbon::now()
                ]);
            }
            WorkerHelper::automaticWorkerActive($params['upload_required_document_worker_id']);
            return self::responseWithSuccess('Document successfully uploaded.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateWorkerIncompleteDocumentDetails(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'incomplete_document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ], [
                'incomplete_document_file.required' => 'The document field is required.',
                'incomplete_document_file.max' => 'Selected file size is higher than 10MB.',
                'incomplete_document_file.mimes' => 'Selected file extension is not supported. Only jpg, png, jpeg, pdf are allowed.',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();

            $workerDocument = WorkerDocument::query()->where('id', $params['upload_incomplete_document_id'])->first();
            if (!$workerDocument) {
                return self::responseWithError('Worker ID document not found, please try again later.');
            }

            $document_file_upload = FileHelper::file_upload($request->file('incomplete_document_file'), 'workers/document');
            WorkerDocument::query()->where('id', $params['upload_incomplete_document_id'])->update([
                'document_file' => $document_file_upload['file_name'],
                'document_file_type' => $document_file_upload['file_type'],
                'incomplete' => 0,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => Carbon::now()
            ]);

            return self::responseWithSuccess('ID document successfully uploaded.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateWorkerOtherDocumentDetails(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'other_document_title' => 'required',
                'other_document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ], [
                'other_document_title.required' => 'The document title field is required.',
                'other_document_file.required' => 'The document field is required.',
                'other_document_file.max' => 'Selected file size is higher than 10MB.',
                'other_document_file.mimes' => 'Selected file extension is not supported. Only jpg, png, jpeg, pdf are allowed.',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            $worker = Worker::query()->where('id', $params['upload_other_document_worker_id'])->first();
            if (!$worker) {
                return self::responseWithError('Worker details not found please try again.');
            }

            if($params['upload_other_document_id'] == 0){
                $document_file_upload = FileHelper::file_upload($request->file('other_document_file'), 'workers/document');
                WorkerDocument::query()->create([
                    'worker_id'           => $worker['id'],
                    'document_file'       => $document_file_upload['file_name'],
                    'document_file_type'  => $document_file_upload['file_type'],
                    'document_file_title' => $params['other_document_title'],
                    'expiry_date'         => ($params['other_document_expiry_date']) ? date('Y-m-d', strtotime($params['other_document_expiry_date'])) : null,
                    'uploaded_at' => Carbon::now(),
                    'uploaded_by' => Auth::id(),
                ]);
            }else{
                $workerDocument = WorkerDocument::query()->where('id',$params['upload_other_document_id'])->first();
                if (!$workerDocument) {
                    return self::responseWithError('Worker document not found please try again later.');
                }
                FileHelper::file_remove($workerDocument['other_document_file'], 'workers/document');
                $document_file_upload = FileHelper::file_upload($request->file('other_document_file'), 'workers/document');

                WorkerDocument::query()->where('id',$params['upload_other_document_id'])->update([
                    'worker_id'           => $worker['id'],
                    'document_file'       => $document_file_upload['file_name'],
                    'document_file_type'  => $document_file_upload['file_type'],
                    'document_file_title' => $params['other_document_title'],
                    'expiry_date'         => ($params['other_document_expiry_date']) ? date('Y-m-d', strtotime($params['other_document_expiry_date'])) : null,
                    'uploaded_at' => Carbon::now(),
                    'uploaded_by' => Auth::id(),
                ]);
            }

            return self::responseWithSuccess('Document successfully uploaded.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    /*public function getDocumentIdDetails(Request $request){
        $workerDocument = WorkerDocument::query()->where('id',$request->get('document_id'))->first();
        $workerDocument['expiry_date'] = date('d-m-Y', strtotime($workerDocument['expiry_date']));
        return self::responseWithSuccess('Document details', $workerDocument);
    }*/

    /*public function updateDocumentIdDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'document_type'       => 'required',
                'document_number'     => 'required',
                'document_expiry_date'=> 'required',
                'document_scan_file'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240'
            ]);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            if ($request->hasFile('document_scan_file')) {
                $document = WorkerDocument::query()->where('id', $params['document_id'])->first();
                if (!$document) {
                    return self::responseWithError('Document details not found, please try again later');
                }

                FileHelper::file_remove($document['document_file'], 'workers/document');
                $document_file_upload = FileHelper::file_upload($request->file('document_scan_file'), 'workers/document');

                WorkerDocument::query()->where('id', $params['document_id'])->update([
                    'document_type' => $params['document_type'],
                    'document_no' => $params['document_number'],
                    'expiry_date' => date('Y-m-d', strtotime($params['document_expiry_date'])),
                    'document_file' => $document_file_upload['file_name'],
                    'document_file_type' => $document_file_upload['file_type'],
                    'uploaded_by' => Auth::id(),
                ]);
                return self::responseWithSuccess('Document detail successfully updated');
            } else {
                return self::responseWithError('Document file field is required');
            }
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }*/

    public function updateWorkerNote(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'note_type' => 'required',
                'note_text' => 'required',
            ]);

            if($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $worker = Worker::query()->select('id')->where('id', $request->input('note_worker_id'))->first();
            if (!$worker)
                return self::responseWithError('Worker not found, please try again.');

            Note::query()->create([
                'worker_id' => $request->input('note_worker_id'),
                'user_id'   => Auth::user()['id'],
                'note_type' => $request->input('note_type'),
                'note_text' => $request->input('note_text'),
                'type'      => 'Worker',
            ]);
            return self::responseWithSuccess('Note successfully created');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    /*public function createWorkExperienceLogs($params, $update_id) {

        $work_experience = Worker::query()->select(['job_title' ,'company_name' ,'work_start_date' ,'work_end_date' ,'current_working_here'])->where('id', $update_id)->first()->toArray();
        $job_title          = explode('~~~~~', $work_experience['job_title']);
        $company_name       = explode('~~~~~', $work_experience['company_name']);
        $work_start_date    = explode('~~~~~', $work_experience['work_start_date']);
        $work_end_date      = explode('~~~~~', $work_experience['work_end_date']);
        //$current_working_here = explode('~~~~~', $work_experience['current_working_here']);

        if($job_title) {
            foreach ($params['job_title'] as $key => $jRow) {
                $oldData = [
                    'job_title'             => (isset($job_title[$key])) ? $job_title[$key] : '',
                    'company_name'          => (isset($company_name[$key])) ? $company_name[$key] : '',
                    'work_start_date'       => (isset($work_start_date[$key])) ? $work_start_date[$key] : '',
                    'work_end_date'         => (isset($work_end_date[$key])) ? $work_end_date[$key] : '',
                    //'current_working_here'  => (isset($current_working_here[$key])) ? $current_working_here[$key] : '',
                ];

                $newData = [
                    'job_title'             => $params['job_title'][$key],
                    'company_name'          => $params['company_name'][$key],
                    'work_start_date'       => $params['work_start_date'][$key],
                    'work_end_date'         => $params['work_end_date'][$key],
                    //'current_working_here'  => $params['current_working_here_textbox'][$key],
                ];

                ActivityLogs::updatesLog($update_id, 'Worker update', $newData, $oldData,'Worker');
            }

            return self::responseWithSuccess('Log successfully created.');
        } else {
            return self::responseWithError('Job title not available.');
        }
    }*/

    /*public function createInterviewRecordLogs($params, $update_id) {
        $interview_record   = Worker::query()->select(['interview_date' ,'interview_status' ,'interview_details'])->where('id', $update_id)->first()->toArray();
        $date   = explode('~~~~~', $interview_record['interview_date']);
        $status = explode('~~~~~', $interview_record['interview_status']);
        $detail = explode('~~~~~', $interview_record['interview_details']);

        if($date) {
            foreach ($params['interview_date'] as $key => $dRow) {
                $oldData = [
                    'interview_date'    => (isset($date[$key])) ? $date[$key] : '',
                    'interview_status'  => (isset($status[$key])) ? $status[$key] : '',
                    'interview_details' => (isset($detail[$key])) ? $detail[$key] : '',
                ];

                $newData = [
                    'interview_date'    => $params['interview_date'][$key],
                    'interview_status'  => $params['interview_status'][$key],
                    'interview_details' => $params['interview_details'][$key],
                ];

                ActivityLogs::updatesLog($update_id, 'Worker update', $newData, $oldData,'Worker');
            }

            return self::responseWithSuccess('Log successfully created.');
        } else {
            return self::responseWithError('Interview date not available.');
        }
    }*/

    public function updateWorkerStatus(Request $request) {
        try {
            $params = $request->input();

            $worker = Worker::query()->select(['id', 'status', 'suspend'])->where('id', $params['worker_id'])->first();
            if (!$worker)
                return self::responseWithError('Worker details not found, Please passed valid worker id');

            if (in_array($params['status'], ['Suspend', 'Unsuspend'])) {
                $worker->update([
                    'suspend' => ($params['status'] == 'Suspend') ? 'Yes' : 'No'
                ]);
            } else {
                ActivityLogs::updatesLog($params['worker_id'], 'Worker update', [ 'status' => $params['status']], Worker::query()->select(['status'])->where('id', $params['worker_id'])->first()->toArray(), 'Worker');
                $worker->update([
                    'status' => $params['status']
                ]);
            }

            return self::responseWithSuccess('Worker status successfully updated.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteRtwAction($id) {
        try {

            $rtwData = RightsToWork::query()->where('id', $id)->first();
            if (!$rtwData)
                return self::responseWithError('Rights to work details not found, Please passed valid id');

            if ($rtwData['document_scan']) {
                $url = public_path('workers/right_to_work/'.$rtwData['document_scan']);
                if(is_file($url))
                    unlink($url);
            }

            RightsToWork::query()->where('id', $id)->delete();
            return self::responseWithSuccess('Right to work successfully deleted.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function rtwValidationRule($type, $key, $field_number, $params) {
        $validationRule = [];
        $uk_id_document_type    = $params['uk_id_document_type'];
        $uk_id_document_number  = $params['uk_id_document_number'];
        $start_date             = $params['start_date'];
        $end_date               = $params['expiry_date'];
        $reference_number       = $params['reference_number'];
        $worker_restrictions    = $params['worker_restrictions'];
        //$document_scan          = $file;

        if ($type === 'UK Citizen') {
            if (!$uk_id_document_type[$key])
                $validationRule['uk_id_document_type_'.$field_number] =  'required';

            if (!$uk_id_document_number[$key])
                $validationRule['uk_id_document_number_'.$field_number] =  'required';

            if ($uk_id_document_type[$key] == 'Passport') {
                if (!$end_date[$key])
                    $validationRule['expiry_date_'.$field_number] =  'required';
            }
        }

        if (in_array($type, ['Tier 5 Seasonal Worker Visa', 'Tier 5 Poultry and HGV Worker Visa', 'Tier 4 Student Visa', 'Other (Timebound)', 'Other (Indefinite leave)'])) {
            if (!$start_date[$key])
                $validationRule['start_date_'.$field_number] =  'required';
        }

        if (in_array($type, ['EUSS (Settled)', 'EUSS (Pre-Settled)', 'EUSS (COA)', 'Tier 5 Seasonal Worker Visa', 'Tier 5 Poultry and HGV Worker Visa', 'Tier 4 Student Visa', 'Other (Timebound)'])) {
            if (!$end_date[$key])
                $validationRule['expiry_date_'.$field_number] =  'required';
        }

        if (in_array($type, ['EUSS (Settled)', 'EUSS (Pre-Settled)', 'EUSS (COA)', 'Tier 5 Seasonal Worker Visa', 'Tier 5 Poultry and HGV Worker Visa', 'Tier 4 Student Visa', 'Other (Timebound)'])) {
            if (!$reference_number[$key])
                $validationRule['reference_number_'.$field_number] =  'required';
        }

        /*'Tier 4 (student visa)', 'Indefinite leave to remain.'*/
        /*if (in_array($type, [])) {
            if (!$worker_restrictions[$key])
                $validationRule['worker_restrictions_'.$field_number] =  'required';
        }*/

        return $validationRule;
    }

    public function insertRightsToWork(Request $request) {
        try {
            $params = $request->input();

            if (empty($params['right_to_work'][0]))
                return self::validationError(['right_to_work' => ['The right to work field is required.']]);

            $rule = [];
            /*--- BEGIN VALIDATION ----*/
            foreach ($params['right_to_work'] as $key => $rtwType) {
                $rule = array_merge($rule, $this->rtwValidationRule($rtwType, $key, $key+1, $params));
            }

            $validator = Validator::make($request->input(), $rule, ['required' => 'This field is required.']);
            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());
            /*--- END VALIDATION ---*/

            $rightToWork = [];

            $worker = Worker::query()->select('id', 'national_insurance_number')->where('id', $params['worker_id'])->first();
            if (!$worker)
                return self::responseWithError('Worker not found, please try again.');

            $rtw_upload_path = 'workers/right_to_work';
            foreach ($params['right_to_work'] as $key => $rtw_row) {

                $uk_id_document_type    = $params['uk_id_document_type'];
                $uk_id_document_number  = $params['uk_id_document_number'];
                $start_date             = $params['start_date'];
                $end_date               = $params['expiry_date'];
                $reference_number       = $params['reference_number'];
                $worker_restrictions    = $params['worker_restrictions'];
                $document_scan          = $request->file('document_scan');

                $file_name = null;
                if(isset($document_scan[$key])) {
                    $file_upload = FileHelper::file_upload($document_scan[$key], $rtw_upload_path);
                    $file_name   = $file_upload['file_name'];
                }

                if ($uk_id_document_type[$key] == 'Birth Certificate' && !$worker['national_insurance_number']) {
                    return self::responseWithError('This type of RTW requires a National insurance number to be added on the worker’s basic info tab.');
                }

                if ($rtw_row) {

                    if ($uk_id_document_type[$key] == 'Passport') {
                        $endDate = '2199-12-31';
                    } elseif ($rtw_row == 'EUSS (Settled)') {
                        $endDate = '2199-12-31';
                    } else {
                        if (date($end_date[$key])) {
                            $endDate = date('Y-m-d', strtotime($end_date[$key]));
                        } else {
                            $endDate = '2199-12-31';
                        }
                    }

                    $rightToWork[] = [
                        'worker_id'                 => $worker['id'],
                        'user_id'                   => Auth::user()['id'],
                        'right_to_work_type'        => $rtw_row,
                        'right_to_work_expiry_date' => date($end_date[$key]) ? date('Y-m-d', strtotime($end_date[$key])) : null,
                        'uk_id_document_type'       => $uk_id_document_type[$key],
                        'uk_id_document_number'     => $uk_id_document_number[$key],
                        'start_date'                => ($start_date[$key]) ? date('Y-m-d', strtotime($start_date[$key])) : null,
                        'end_date'                  => $endDate,
                        'reference_number'          => $reference_number[$key],
                        'worker_restrictions'       => $worker_restrictions[$key],
                        'document_scan'             => $file_name,
                    ];
                }
            }

            //return $rightToWork;
            if ($rightToWork) {
                if ($params['update_right_to_work_id'] == 0) {
                    RightsToWork::query()->create($rightToWork[0]);
                } else {
                    $rightToWork[0]['incomplete'] = 0;
                    RightsToWork::query()->where('id', $params['update_right_to_work_id'])->update($rightToWork[0]);
                }
            }

            WorkerHelper::automaticWorkerActive($params['worker_id']);
            return self::responseWithSuccess('Right to work successfully updated');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function listOfAbsence(Request $request) {
        try {
            $array   = [];
            $absence = Absence::query()->where('worker_id', $request->input('worker_id'))->with('added_by_details')->get()->toArray();
            if ($absence) {
                foreach ($absence as $row) {
                    $array[] = [
                        'type'          => $row['absence_type'],
                        'start_date'    => date('d-m-Y', strtotime($row['start_date'])),
                        'end_date'      => date('d-m-Y', strtotime($row['end_date'])),
                        'added_by'      => $row['added_by_details']['name'],
                        'created_at'    => date('d-m-Y h:i:s a', strtotime($row['created_at'])),
                        'action'        => '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" data-kt-absence-table-filter="delete_row" id="delete_absence" data-absence-id="'.$row['id'].'">
                                                <i class="fs-2 las la-trash"></i>
                                            </a>',
                    ];
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($absence),
                'recordsFiltered'   => count($absence),
                'data'              => $array
            ];
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function createAbsence(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'absence_type'       => 'required',
                'absence_start_date' => 'required',
                'absence_end_date'   => 'required',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            Absence::query()->create([
                'worker_id'     => $params['worker_id'],
                'absence_type'  => $params['absence_type'],
                'start_date'    => date('Y-m-d', strtotime($params['absence_start_date'])),
                'end_date'      => date('Y-m-d', strtotime($params['absence_end_date'])),
                'added_by'      => Auth::id(),
            ]);

            return self::responseWithSuccess('Absence entry successfully created.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteAbsenceAction($id) {
        try {
            $absence = Absence::query()->where('id', $id)->first();
            if (!$absence)
                return self::responseWithError('Absence entry not found please try again.');

            $absence->update([
                'deleted_by' => Auth::id()
            ]);
            Absence::query()->where('id', $id)->delete();
            return self::responseWithSuccess('Absence entry successfully deleted.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteDocumentAction($id) {
        try {
            $document = WorkerDocument::query()->where('id', $id)->first();
            if (!$document)
                return self::responseWithError('Document not found please try again.');

            $path = public_path('workers/document/'.$document['document_file']);
            if (file_exists($path))
                unlink($path);

            WorkerDocument::query()->where('id', $id)->delete();
            return self::responseWithSuccess('Document successfully deleted.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getWorkerAssignedJobs(Request $request) {
        try {
            $workerId = $request->input('worker_id');
            $jobWorker = ClientJobWorker::query()->where('worker_id', $workerId)
                ->when(request('status') != null && request('status') != 'All', function ($q) {
                    return (request('status') == 0) ? $q->whereNull('archived_at') : $q->whereNotNull('archived_at');
                })
                ->with(['job','worker'])
                ->get();

            $array = [];
            if ($jobWorker) {
                foreach ($jobWorker as $row) {

                    $job_id = $row['job_id'];
                    $shiftsCount = JobShiftWorker::query()->where('worker_id', $row['worker_id'])
                        ->whereIn('job_shift_id', function ($query) use ($job_id) {
                            $query->select('id')
                                ->from('job_shifts')
                                ->where('job_id', $job_id);
                        })
                        ->count();

                    $job_name = ($row['job']) ? $row['job']['name'] : '';
                    $worker_name = $row['worker'] ? $row['worker']['first_name'].' '.$row['worker']['middle_name'].' '.$row['worker']['last_name'] : '';
                    $array[] = [
                        'name'          => $job_name,
                        'site'          => ($row['job']) ? ($row['job']['site_details']) ? $row['job']['site_details']['site_name'] : '' : '',
                        'client'        => ($row['job']) ? ($row['job']['client_details']) ? $row['job']['client_details']['company_name'] : '' : '',
                        'invited_at'    => ($row['invited_at'] && $row['invitation_type'] == 1) ? '<a href="javascript:;" title="'.date('d-m-Y H:i:s', strtotime($row['invited_at'])).'"><i class="bi bi-circle-fill text-warning"></i></a>' : '<i class="bi bi-circle"></i>',
                        'confirmed_at'  => ($row['confirmed_at']) ? '<a href="javascript:;" title="'.date('d-m-Y H:i:s', strtotime($row['confirmed_at'])).'"><i class="bi bi-circle-fill text-success"></i></a>' : '<i class="bi bi-circle"></i>',
                        'declined_at'   => ($row['declined_at']) ? '<a href="javascript:;" title="'.date('d-m-Y H:i:s', strtotime($row['declined_at'])).'"><i class="bi bi-circle-fill text-danger"></i></a>' : '<i class="bi bi-circle"></i>',
                        'no_shift'      => $shiftsCount,
                        'action'        => $this->job_action($row['id'], $row['archived_at'], $row['job_id'], $worker_name, $row['worker_id'], $job_name),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($jobWorker),
                'recordsFiltered'   => count($jobWorker),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function job_action($id, $archived, $job_id, $worker_name, $worker_id, $job_name) {
        $action = '';
        if ($archived == null) {
            $action .= '<a href="javascript:;" 
                class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 archive_action" 
                id="archive_job_worker"
                data-job_worker_id="' . $id . '"
                data-worker_id="'.$worker_id.'"
                data-worker_name="'.$worker_name.'"
                data-job_name="'.$job_name.'"
                data-job_id="'.$job_id.'">
                   <i class="fs-2 las la-unlink"></i>
                </a>';
        }

        $action .= '
                <a href="'.url('view-client-job/'.$job_id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_client_job" data-job_id="'.$id.'">
                    <i class="fs-2 las la-arrow-right"></i>
                </a>';

        return $action;
    }

    public function getWorkerShiftsBooked(Request $request) {
        try {
            $workerId = $request->input('worker_id');
            $shiftWorker = JobShiftWorker::query()->where('worker_id', $workerId)
                ->with('jobShift')
                ->get();

            $array = [];
            $shiftBookCsvTableData[] = ['Job name',  'Site',  'Client',  'Date',  'Start time',  'Exp. dur',  'Invited at',  'Confirmed at',  'Declined at'];
            if ($shiftWorker) {
                foreach ($shiftWorker as $row) {
                    $array[] = [
                        'name'          => ($row['jobShift']) ? $row['jobShift']['client_job_details']['name'] : '',
                        'site'          => ($row['jobShift']) ? ($row['jobShift']['client_job_details']['site_details']) ? $row['jobShift']['client_job_details']['site_details']['site_name'] : '' : '',
                        'client'        => ($row['jobShift']) ? ($row['jobShift']['client_details']) ? $row['jobShift']['client_details']['company_name'] : '' : '',
                        'date'          => date('d-m-Y', strtotime($row['shift_date'])),
                        'start_time'    => ($row['jobShift']) ? $row['jobShift']['start_time'] : '',
                        'exp_dur'       => ($row['jobShift']) ? $row['jobShift']['shift_length_hr'].'h '.$row['jobShift']['shift_length_min'].'m' : '',
                        'invited_at'    => ($row['invited_at'] && $row['assign_type'] == 'Invitation') ? '<a href="javascript:;" title="'.date('d-m-Y H:i:s', strtotime($row['invited_at'])).'"><i class="bi bi-circle-fill text-success"></i></a>' : '<i class="bi bi-circle text-muted"></i>',
                        'confirmed_at'  => ($row['confirmed_at']) ? '<a href="javascript:;" title="'.date('d-m-Y H:i:s', strtotime($row['confirmed_at'])).'"><i class="bi bi-circle-fill text-success"></i></a>' : '<i class="bi bi-circle text-muted"></i>',
                        'declined_at'   => ($row['declined_at']) ? '<a href="javascript:;" title="'.date('d-m-Y H:i:s', strtotime($row['declined_at'])).'"><i class="bi bi-circle-fill text-danger"></i></a>' : '<i class="bi bi-circle text-muted"></i>',
                        'action'        => $this->shift_action($row['id'], $row['archived_at'], $row['job_shift_id']),
                    ];

                    $shiftBookCsvTableData[] = [
                        ($row['jobShift']) ? $row['jobShift']['client_job_details']['name'] : '',
                        ($row['jobShift']) ? ($row['jobShift']['client_job_details']['site_details']) ? $row['jobShift']['client_job_details']['site_details']['site_name'] : '' : '',
                        ($row['jobShift']) ? ($row['jobShift']['client_details']) ? $row['jobShift']['client_details']['company_name'] : '' : '',
                        date('d-m-Y', strtotime($row['shift_date'])),
                        ($row['jobShift']) ? $row['jobShift']['start_time'] : '',
                        ($row['jobShift']) ? $row['jobShift']['shift_length_hr'].'h '.$row['jobShift']['shift_length_min'].'m' : '',
                        $row['invited_at'] ?? '',
                        $row['confirmed_at'] ?? '',
                        $row['declined_at'] ?? ''
                    ];
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($shiftWorker),
                'recordsFiltered'   => count($shiftWorker),
                'data'              => $array,
                'shiftBookCsvTableData' => $shiftBookCsvTableData
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function shift_action($id, $archived_at, $shift_id) {
        $action = '';
        if ($archived_at == null) {
            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="archive_shift_worker" data-shift_id="'.$id.'" data-text="You want to archive this worker to this shift!" data-btn_text="Yes, archive!">
                    <span class="svg-icon svg-icon-2">
                    <i class="fs-2 las la-archive"></i>
                    </span>
                </a>';
        }

        $action .= '
                <a href="'.url('view-job-shift/'.$shift_id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_client_job" data-shift_id="'.$id.'">
                    <span class="svg-icon svg-icon-2">
                        <i class="fs-2 las la-arrow-right"></i>
                    </span>
                </a>';

        return $action;
    }

    public function getWorkerShiftsWorked(Request $request) {
        try {
            $workerId = $request->input('worker_id');
            $shiftWorked = Timesheet::query()->where('worker_id', $workerId)
                ->with(['job_details', 'worker_details'])
                ->get();

            $array = [];
            $shiftWorkCsvTableData[] = ['Job name',  'Site',  'Client',  'Date',  'Start time',  'Hours'];
            if ($shiftWorked) {
                foreach ($shiftWorked as $row) {

                    $getJobShift = JobShift::query()
                        ->where('job_id', $row['job_id'])
                        ->where('date', $row['date'])
                        ->first();

                    $payrollWeekData = PayrollWeekDate::query()->select(['payroll_week_number', 'year', 'pay_date'])
                        ->where( $row['job_details']['client_details']['payroll_week_starts'].'_payroll_end', '>=', $row['date'])
                        ->first();

                    $array[] = [
                        'name'          => ($row['job_details']) ? $row['job_details']['name'] : '',
                        'site'          => ($row['job_details']) ? ($row['job_details']['site_details']) ? $row['job_details']['site_details']['site_name'] : '' : '',
                        'client'        => ($row['job_details']) ? ($row['job_details']['client_details']) ? $row['job_details']['client_details']['company_name'] : '' : '',
                        'date'          => date('d-m-Y', strtotime($row['date'])),
                        'payroll_week'  => $payrollWeekData['payroll_week_number'].'-'.$payrollWeekData['year'],
                        'start_time'    => ($row['in_time'] != '00:00:00') ? date('H:i', strtotime($row['in_time'])) : '',
                        'hours'         => number_format($row['hours_worked'], 2),
                        'edited'        => ($row['edited_at']) ? '<i class="bi bi-circle-fill text-warning"></i>' : '<i class="bi bi-circle text-muted"></i>',
                        'action'        => $this->workerTimesheetAction($getJobShift, $row, $payrollWeekData),
                    ];

                    $shiftWorkCsvTableData[] = [
                        ($row['job_details']) ? $row['job_details']['name'] : '',
                        ($row['job_details']) ? ($row['job_details']['site_details']) ? $row['job_details']['site_details']['site_name'] : '' : '',
                        ($row['job_details']) ? ($row['job_details']['client_details']) ? $row['job_details']['client_details']['company_name'] : '' : '',
                        date('d-m-Y', strtotime($row['date'])),
                        ($getJobShift) ? $getJobShift['start_time'] : '',
                        $row['hours_worked'],
                    ];
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($shiftWorked),
                'recordsFiltered'   => count($shiftWorked),
                'data'              => $array,
                'shiftWorkedCsvTableData' => $shiftWorkCsvTableData
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function workerTimesheetAction($getJobShift, $timeSheetRow, $payrollWeekData) {
        $siteWeekLock = SiteWeekLock::query()->where('site_id', $timeSheetRow['job_details']['site_id'])
            ->where('payroll_week', $payrollWeekData['payroll_week_number'].'-'.$payrollWeekData['year'])
            ->first();

        $action = ($getJobShift)
            ? '<a href="'.url('view-job-shift/'.$getJobShift['id']).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-2" id="view_job_shift"><i class="fs-2 las la-arrow-right"></i></a>'
            : '';

        $start_time = ($timeSheetRow['in_time'] != '00:00:00') ? date('H:i', strtotime($timeSheetRow['in_time'])) : '';
        $payDate = Carbon::parse($payrollWeekData['pay_date']);
        $action .= (!$siteWeekLock && $payDate->isFuture())
            ? '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" id="edit_timesheet" data-id="'.$timeSheetRow['id'].'" data-worker="'.$timeSheetRow['worker_details']['first_name'].' '.$timeSheetRow['worker_details']['middle_name'].' '.$timeSheetRow['worker_details']['last_name'].' ('.$timeSheetRow['date'].')" data-hours="'.number_format($timeSheetRow['hours_worked'], 2).'" data-start_time="'.$start_time.'"><i class="fs-2 las la-edit"></i></a>'
            : '';
        return $action;
    }

    public function workerStatusBulkAction(Request $request) {
        try {
            foreach ($request->input('worker_ids') as $worker_id) {
                $worker = Worker::query()->select(['id', 'status', 'suspend'])->where('id', $worker_id)->first();
                if ($worker) {
                    ActivityLogs::updatesLog($worker_id, 'Worker update', [ 'status' => $worker_id], Worker::query()->select(['status'])->where('id', $worker_id)->first()->toArray(), 'Worker');
                    $worker->update([
                        'status' => $request->input('status')
                    ]);
                }
            }
            return self::responseWithSuccess('Worker status successfully updated.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function uploadWorkerProfilePic(Request $request) {
        try {
            $worker = Worker::query()->select('id', 'profile_pic')->where('id', $request->input('worker_id'))->first();
            if (!$worker)
                return self::responseWithError('Worker not found, please try again later.');

            if ($request->hasFile('worker_profile_pic')) {
                if ($worker['profile_pic']) {
                    FileHelper::file_remove($worker['profile_pic'], 'workers/profile');
                }

                $upload = FileHelper::file_upload($request->file('worker_profile_pic'), 'workers/profile');
                $worker->update([
                    'profile_pic' => $upload['file_name'],
                ]);

                return self::responseWithSuccess('Profile pic successfully uploaded.');
            } else {
                if ($request->input('avatar_remove') == 1) {
                    if ($worker['profile_pic']) {
                        FileHelper::file_remove($worker['profile_pic'], 'workers/profile');
                    }

                    $worker->update([
                        'profile_pic' => null,
                    ]);

                    return self::responseWithSuccess('Profile pic successfully removed.');
                } else {
                    return self::responseWithError('Please select a profile pic.');
                }
            }
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function sendMailForWorkerEmailConformation($id) {
        try {
            $worker = Worker::query()->select('id', 'first_name', 'last_name', 'email_address')->where('id', $id)->first();

            if (!$worker)
                return self::responseWithError('Worker not found please try again.');

            WorkerHelper::send_confirm_email($worker['id'], $worker['first_name'], $worker['last_name'], $worker['email_address']);
            return self::responseWithSuccess('Conformation mail successfully send.');

        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function confirmWorkerEmail($id) {
        $worker = Worker::query()->select('id', 'first_name', 'email_address', 'email_verified_at')->where('id', $id)->first();

        if (!$worker) {
            $message = 'Worker not found please try again.';
        } elseif ($worker['email_verified_at']) {
            $message = 'Your email already confirmed.';
        } else {
            $worker->update([
                'email_verified_at' => Carbon::now(),
            ]);
            $message = '';
            WorkerHelper::automaticWorkerActive($id);
        }
        return view('workers.confirm_mail_success', compact(['worker', 'message']));
    }
    public function update_leaving_status(Request $request){
        try {
            $validator = Validator::make($request->input(), [
                'leaving_date' => 'required',
            ]);

            if($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            $worker = Worker::query()->select('id')->where('id', $params['worker_id'])->first();
            if (!$worker)
                return self::responseWithError('Worker not found, please try again.');

            ActivityLogs::updatesLog($params['worker_id'], 'Worker update', [ 'status' => $params['status']], Worker::query()->select(['status'])->where('id', $params['worker_id'])->first()->toArray(), 'Worker');
            $worker->update([
                'status' => $params['status'],
                'leaving_date'   => Carbon::parse($request->input('leaving_date'))->format('Y-m-d'),
            ]);
            return self::responseWithSuccess('Worker status successfully updated.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function update_bulk_leaving_status(Request $request){
        try {
            $validator = Validator::make($request->input(), [
                'leaving_date' => 'required',
            ]);

            if($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            foreach ($params['worker_id'] as $worker_id) {
                $worker = Worker::query()->select('id')->where('id', $worker_id)->first();
                if (!$worker) {
                    continue;
                }

                ActivityLogs::updatesLog($worker_id, 'Worker update', [ 'status' => 'Leaver'], Worker::query()->select(['status'])->where('id', $worker_id)->first()->toArray(), 'Worker');
                $worker->update([
                    'status' => 'Leaver',
                    'leaving_date'   => Carbon::parse($request->input('leaving_date'))->format('Y-m-d'),
                ]);
            }
            return self::responseWithSuccess('Worker status successfully updated.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
    public function updateRtwDocumentScan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'document_scan_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ], [
                'document_scan_file.required' => 'The document field is required.',
                'document_scan_file.max' => 'Selected file size is higher than 10MB.',
                'document_scan_file.mimes' => 'Selected file extension is not supported. Only jpg, png, jpeg, pdf are allowed.',
            ]);

            if($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            $rtw = RightsToWork::query()->where('id', $params['upload_rtws_incomplete_document_id'])->first();
            if (!$rtw) {
                return self::responseWithError('Right to work not found, please try again later.');
            }

            if ($request->hasFile('document_scan_file')) {
                $file_upload = FileHelper::file_upload($request->file('document_scan_file'), 'workers/right_to_work');
                $file_name   = $file_upload['file_name'];

                RightsToWork::query()->where('id', $params['upload_rtws_incomplete_document_id'])->update([
                    'document_scan' => $file_name,
                    'incomplete'    => 0,
                ]);

                return self::responseWithSuccess('RTW document uploaded successfully');
            } else {
                return self::responseWithError('Document scan file not found, please try again later.');
            }
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function resetWorkerPassword(Request $request) {
        $token = $request->query('token');
        $email = $request->query('email');

        $reset = DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$reset) {
            return view('workers.reset_password', [
                'token' => $token,
                'email' => $email,
                'error' => 'Invalid or expired password generating link.'
            ]);
        }

        return view('workers.reset_password', [
            'token' => $token,
            'email' => $email,
            'error' => ''
        ]);
    }

    public function resetWorkerPasswordAction(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'email' => 'required|email|exists:workers,email_address',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'email.required' => 'Email is required.',
                'email.email' => 'Email format is invalid.',
                'email.exists' => 'Email does not exist in our system.',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $email = $request->input('email');
            $token = $request->input('token');

            $reset = DB::table('password_resets')
                ->where('email', $email)
                ->where('token', $token)
                ->first();

            if (!$reset) {
                return self::responseWithError('Invalid or expired password reset link.');
            }

            Worker::query()->where('email_address', $email)->update([
                'password' => Hash::make($request->input('password'))
            ]);

            DB::table('password_resets')->where('email', $email)->delete();
            return self::responseWithSuccess('Your password has been generated successfully.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
