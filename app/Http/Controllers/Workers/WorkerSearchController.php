<?php

namespace App\Http\Controllers\Workers;

use App\Helper\Workers\RightToWorkHelper;
use App\Helper\Workers\WorkerHelper;
use App\Http\Controllers\Controller;
use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Client\ClientJobWorker;
use App\Models\Client\Site;
use App\Models\Group\CostCentre;
use App\Models\Group\Group;
use App\Models\Group\GroupWithWorker;
use App\Models\Group\Team;
use App\Models\Worker\Worker;
use App\Models\Worker\WorkerSearch;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WorkerSearchController extends Controller
{
    use JsonResponse;
    public function index() {
        $client = Client::query()->select('id', 'company_name')->orderBy('company_name', 'asc')->get();
        $site = Site::query()->where('archived',  '0')->orderBy('site_name', 'asc')->with('client_details')->get();
        $job = ClientJob::query()->where('archived', '0')->orderBy('name', 'asc')->with(['site_details', 'client_details'])->get();
        $searchData = WorkerSearch::query()->where('user_id', Auth::id())->orderBy('id', 'desc')->get();
        $groups = Group::query()->where('consultant_id', Auth::id())->get();
        $teams = Team::query()->get();
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('workers.dis_worker_search', compact('client', 'site', 'job', 'searchData', 'groups', 'teams', 'costCentre'));
    }

    public function workerSearchAction(Request $request) {
        $params = $request->input();

        if ($params['filter_type'] == '0') {
            $worker = $this->workerPrimaryFilterQuery($params)
                ->get()
                ->toArray();
        } else {
            $right_to_work_type = $params['right_to_work_type'] ?? null;
            $minAge = $params['age_between_min'] ?? 0;
            $maxAge = $params['age_between_max'] ?? 150;

            $worker = $this->workerPrimaryFilterQuery($params)
                ->when(request('has_already_worked_for_client') != 'Any', function ($query) {
                    $query->whereHas('clientJobWorkers.job.client_details', function ($query2) {
                        $query2->where('id', request('has_already_worked_for_client'));
                    });
                })
                ->when($minAge !== null && $maxAge !== null, function ($query) use ($minAge, $maxAge) {
                    $query->whereRaw('DATEDIFF(CURDATE(), date_of_birth) / 365.25 BETWEEN ? AND ?', [$minAge, $maxAge]);
                })
                ->when(request('has_already_worked_at_site') != 'Any', function ($query) {
                    $query->whereHas('clientJobWorkers.job.site_details', function ($query3) {
                        $query3->where('id', request('has_already_worked_at_site'));
                    });
                })
                ->when(request('gender') != 'Any', function ($query) {
                    $query->where('gender', request('gender'));
                })
                ->when(request('is_already_registered_on_job') != 'Any', function ($query) {
                    $query->whereHas('clientJobWorkers', function ($query4) {
                        $query4->where('job_id', request('is_already_registered_on_job'));
                    });
                })
                ->when(request('right_to_work_type') != null, function ($query) use ($right_to_work_type) {
                    $query->whereHas('rights_to_work_details', function ($query5) use ($right_to_work_type) {
                        $query5->where('right_to_work_type', $right_to_work_type);
                    });
                })
                ->when(request('last_shift') != 'Any', function ($query) {
                    if (request('last_shift') === '13') {
                        $dateNode = Carbon::now()->subWeeks(12);
                        $date = $dateNode->startOfWeek()->toDateString();
                        $query->whereHas('timesheet_detail', function ($query6) use ($date) {
                            $query6->where('date', '<', $date);
                        });
                    } else {
                        /*$weeks = intval(request('last_shift'));
                        $dateNode = Carbon::now()->subWeeks($weeks);
                        $date = $dateNode->startOfWeek()->toDateString();
                        $query->whereHas('timesheet_detail', function ($query6) use ($date) {
                            $query6->where('date', '<=', $date);
                        });*/
                        $date = Carbon::now()->subWeeks(intval(request('last_shift')))->startOfWeek()->toDateString();
                        $query->whereHas('timesheet_detail', function ($query6) use ($date) {
                            $query6->where('date', '>=', $date);
                        });
                    }
                })
                /*->when(request('last_shift') != 'Any', function ($query) {
                    if (request('last_shift') === '13') {
                        $dateNode = Carbon::now()->subWeeks(12);
                        $date = $dateNode->startOfWeek()->toDateString();
                        $query->whereHas('jobShiftWorker', function ($query6) use ($date) {
                            $query6->where('shift_date', '<', $date);
                        });
                    } else {
                        $weeks = intval(request('last_shift'));
                        $dateNode = Carbon::now()->subWeeks($weeks);
                        $date = $dateNode->startOfWeek()->toDateString();
                        $query->whereHas('jobShiftWorker', function ($query6) use ($date) {
                            $query6->where('shift_date', '<=', $date);
                        });
                    }
                })*/
                ->get()
                ->toArray();
        }

        $array   = [];
        $csvData[] = [
            'Worker Name',
            'Worker First Name',
            'Worker Last Name',
            'Worker_ID',
            'Payroll_ID',
            'Client_Ref',
            'Cost centres',
            'Gender',
            'Age',
            'Nationality',
            'Mobile',
            'Email',
            'NI Number',
            'Status',
            'RTW Type',
            'RTW Expires',
            'Flags',
            'Last Worked Date',
            'Last Worked Job',
            'Last Worked Site',
            'Last Worked Client',
            'Leaving date',
        ];

        if ($worker) {
            foreach ($worker as $row) {

                $rightToWorkDate = RightToWorkHelper::getLatestDate($row['rights_to_work_details']);
                $rightToWorLatestRecord = collect($row['rights_to_work_details'])->filter(function ($item) { return !is_null($item['end_date']); })->sortByDesc('end_date')->first();

                $flags = '';
                $csv_flag = [];
                /*if (strtotime(date('Y-m-d')) >= strtotime($rightToWorkDate)) {
                    $flags .= '<span class="badge badge-danger me-1 mb-1">RTW</span>';
                    $csv_flag[] = 'RTW';
                }

                if (!$row['mobile_number']) {
                    $flags .= '<span class="badge badge-warning me-1 mb-1">MOB</span>';
                    $csv_flag[] = 'MOB';
                }

                if (!$row['bank_name'] || !$row['bank_account_number'] || !$row['bank_ifsc_code']) {
                    $flags .= '<span class="badge badge-warning me-1 mb-1">BANK</span>';
                    $csv_flag[] = 'BANK';
                }

                if (!$row['national_insurance_number']) {
                    $flags .= '<span class="badge badge-warning me-1 mb-1">NI</span>';
                    $csv_flag[] = 'NI';
                }*/

                $rtw_type = $rightToWorLatestRecord ? $rightToWorLatestRecord['right_to_work_type'] : '';
                $array[] = [
                    'checkbox' => '<label class="form-check form-check-inline me-5 is-invalid"><input type="checkbox" class="form-check-input rowCheckbox" name="worker_ids[]" id="worker_ids_'.$row['id'].'" value="'.$row['id'].'"></label>',
                    'name' => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'],
                    'status' => $row['status'],
                    'rtw_type' => $rtw_type,
                    'rtw_expires' => $rightToWorkDate,
                    'mobile' => $row['mobile_number'],
                    'flags' => WorkerHelper::getFlags($row),
                    'actions' => $this->action($row['id']),
                ];

                $dob = Carbon::parse($row['date_of_birth']);
                $now = Carbon::now();

                $csvData[] = [
                    $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['worker_no'],
                    $row['payroll_reference'],
                    $row['client_reference'],
                    implode(' | ', array_column($row['worker_cost_center'], 'cost_center')),
                    $row['gender'],
                    $now->diffInYears($dob),
                    $row['nationality'],
                    $row['mobile_number'],
                    $row['email_address'],
                    $row['national_insurance_number'],
                    $row['status'],
                    $rtw_type,
                    $rightToWorkDate,
                    implode(' | ', $csv_flag),
                    ($row['last_working_log']) ? date('d-m-Y', strtotime($row['last_working_log']['date'])) : '',
                    //($row['last_working_log']) ? date('d-m-Y', strtotime($row['last_working_log']['shift_date'])) : '',
                    ($row['last_working_log']) ? ($row['last_working_log']['job_shift']) ? ($row['last_working_log']['job_shift']['client_job_details']) ? $row['last_working_log']['job_shift']['client_job_details']['name'] : '' : '' : '',
                    ($row['last_working_log']) ? ($row['last_working_log']['job_shift']) ? ($row['last_working_log']['job_shift']['client_job_details']) ? ($row['last_working_log']['job_shift']['client_job_details']['site_details']) ? $row['last_working_log']['job_shift']['client_job_details']['site_details']['site_name'] : '' : '' : '' : '',
                    ($row['last_working_log']) ? ($row['last_working_log']['job_shift']) ? ($row['last_working_log']['job_shift']['client_details']) ? $row['last_working_log']['job_shift']['client_details']['company_name'] : '' : '' : '',
                    ($row['status'] == 'Leaver') ? ($row['leaver_log']) ? date('d-m-Y', strtotime($row['leaver_log']['created_at'])) : '' : '',
                ];
            }
        }

        unset($params['_token']);
        return [
            'draw'              => 1,
            'recordsTotal'      => count($worker),
            'recordsFiltered'   => count($worker),
            'data'              => $array,
            'request_data'      => json_encode($params, true),
            'csvTableData'      => $csvData,
        ];
    }

    private function action($id) {
        return '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" data-worker-id="'.$id.'">
                    <i class="fs-2 las la-paper-plane"></i>
                </a>

                <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="archived_btn" data-worker-id="'.$id.'">
                    <i class="fs-2 las la-archive"></i>
                </a>

                <a href="'.url('view-worker-details/'.$id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_client" data-worker-id="'.$id.'">
                    <i class="fs-2 las la-arrow-right"></i>
                </a>';
    }

    private function workerPrimaryFilterQuery($params) {
        $name_email_id = $params['name_email_id'];
        $valid_right_to_work = $params['valid_right_to_work'] ?? null;

        return Worker::query()
            ->with(['rights_to_work_details', 'clientJobWorkers', 'leaverLog', 'last_working_log', 'timesheet_detail', 'worker_cost_center', 'id_documents', 'incomplete_rights_to_work_details', 'worker_documents']) /*jobShiftWorker*/
            ->where(function($query) use ($name_email_id) {
                $query->where('worker_no', $name_email_id)
                    ->orWhere(function($query) use ($name_email_id) {
                        $query->where('first_name', 'LIKE', "%{$name_email_id}%")
                            ->orWhere('middle_name', 'LIKE', "%{$name_email_id}%")
                            ->orWhere('last_name', 'LIKE', "%{$name_email_id}%")
                            ->orWhere('email_address', 'LIKE', "%{$name_email_id}%");
                    });
            })
            ->when(!empty(request('cost_center')), function ($query) {
                $query->whereHas('worker_cost_center', function($query) {
                    $query->whereIn('cost_center', request('cost_center'));
                });
            })
            ->when(request('status') != null, function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('valid_right_to_work') != null, function ($query) use ($valid_right_to_work) {
                $query->whereHas('rights_to_work_details', function ($query1) use ($valid_right_to_work) {
                    $query1->whereDate('end_date', '>=', date('Y-m-d', strtotime($valid_right_to_work)))
                        ->latest('end_date');
                });
            });
    }

    public function storeWorkerSearchRequest(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'search_title' => 'required',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            WorkerSearch::query()->create([
                'user_id' => Auth::id(),
                'name' => $request->input('search_title'),
                'request_data' => $request->input('request_data'),
            ]);

            return self::responseWithSuccess('Your filter data successfully stored.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteWorkerSearchRequestData($id) {
        try {
            $searchNode = WorkerSearch::query()->where('id', $id)->first();
            if (!$searchNode)
                return self::responseWithError('Search request not found, please try again later.');

            $searchNode->delete();
            return self::responseWithSuccess('Your search request successfully deleted.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function addWorkerToExistingGroup(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'existing_group_name' => 'required'
            ],[
                'existing_group_name.required' => 'The select group is required.'
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $workerIds = $request->input('worker_id');
            $groupId = $request->input('existing_group_name');
            if (!$workerIds) {
                throw new \Exception('Please select at lest one worker to perform this action.');
            }

            $group = Group::query()->where('id', $groupId)->with(['jobs'])->first();
            if (!$group) {
                throw new \Exception('Group details not found, please try again later.');
            }

            if ($request->input('create_type') == 0) {
                $jobNames = $group->jobs
                    ->map(fn ($job) => '- ' . $job->name)
                    ->implode('<br>');

                if ($jobNames != '') {
                    return self::responseWithSuccess('selected_group_details_fetched', [
                        'group_name' => $group['name'],
                        'job_name' => $jobNames
                    ]);
                }

                $addedCount = $this->addWorkerIntoGroup($workerIds, $groupId);
                if ($addedCount === 0) {
                    throw new \Exception('All selected workers are already part of ' . $group->name . ' group.');
                }

                DB::commit();
                return self::responseWithSuccess($addedCount . ' workers successfully added into ' . $group->name . ' group.');
            }

            if ($request->input('link_worker_to_job_using_group_type') == 'link_to_existing_job') {
                foreach ($group->jobs->pluck('id')->values() as $jobId) {

                    $job = ClientJob::query()->where('id', $jobId)->first();
                    if (!$job) {
                        throw new \Exception('Job details not found, please try again later.');
                    }

                    foreach ($workerIds as $workerId) {
                        $ClientJobWorker = ClientJobWorker::query()->where('job_id', $jobId)
                            ->where('worker_id', $workerId)
                            ->first();

                        if ($ClientJobWorker) {
                            if ($ClientJobWorker['declined_at'] || $ClientJobWorker['archived_at']) {
                                ClientJobWorker::query()->where('id', $ClientJobWorker['id'])->update([
                                    'invitation_type' => 2,
                                    'confirmed_at' => Carbon::now(),
                                    'confirmed_by_admin_user_id' => Auth::id(),
                                    'declined_at' => null,
                                    'archived_at' => null
                                ]);
                            }
                        } else {
                            ClientJobWorker::query()->create([
                                'job_id' => $jobId,
                                'worker_id' => $workerId,
                                'invitation_type' => 2,
                                'confirmed_at' => Carbon::now(),
                                'confirmed_by_admin_user_id' => Auth::id(),
                            ]);
                        }
                    }
                }
            }

            $addedCount = $this->addWorkerIntoGroup($workerIds, $groupId);
            if ($addedCount === 0) {
                throw new \Exception('All selected workers are already part of ' . $group->name . ' group.');
            }

            DB::commit();
            return self::responseWithSuccess($addedCount . ' workers successfully added into ' . $group->name . ' group.');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function addWorkerIntoGroup($workerIds, $groupId) {
        $addedCount = 0;
        foreach ($workerIds as $workerId) {
            $exists = GroupWithWorker::query()->where('group_id', $groupId)
                ->where('worker_id', $workerId)
                ->exists();

            if (!$exists) {
                GroupWithWorker::query()->create([
                    'group_id'  => $groupId,
                    'worker_id' => $workerId,
                ]);
                $addedCount++;
            }
        }
        return $addedCount;
    }

    public function addWorkerToNewCreatedGroup(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'create_group_team' => ['required', 'integer', 'exists:teams,id'],
                'create_group_name' => [
                    'required',
                    Rule::unique('groups', 'name')->where(fn($query) =>
                    $query->where('team_id', $request->input('create_group_team'))
                    ),
                ]
            ], [
                'create_group_team.required' => 'The team field is required.',
                'create_group_name.required' => 'The group name field is required.',
                'create_group_name.unique' => 'The group name must be unique within the selected team.'
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $group = Group::query()->create([
                'team_id'       => $request->input('create_group_team'),
                'consultant_id' => Auth::id(),
                'name'          => $request->input('create_group_name'),
            ]);

            $workerIds = $request->input('worker_id');
            $insertArray = collect($workerIds)->map(fn($id) => [
                'group_id'  => $group->id,
                'worker_id' => $id,
            ])->toArray();

            GroupWithWorker::query()->insert($insertArray);

            return self::responseWithSuccess(
                count($insertArray) . ' workers successfully added to the "' . e($group->name) . '" group.'
            );
        } catch (\Throwable $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
