<?php

namespace App\Http\Controllers\Group;

use App\Helper\Workers\RightToWorkHelper;
use App\Http\Controllers\Controller;
use App\Models\Client\ClientJob;
use App\Models\Client\ClientJobWorker;
use App\Models\Group\CostCentre;
use App\Models\Group\Group;
use App\Models\Group\GroupWithJob;
use App\Models\Group\GroupWithWorker;
use App\Models\Group\Team;
use App\Models\User;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    use JsonResponse;
    public function index() {
        $teamId = User::query()->where('id', Auth::id())->first()->team_id;
        return view('groups.dis_groups', compact('teamId'));
    }

    public function getGroup(Request $request) {
        try {
            if ($request->input('status') == 'Archived') {
                $query = Group::onlyTrashed();
            } else if($request->input('status') == 'All') {
                $query = Group::withTrashed();
            } else {
                $query = Group::query();
            }
            $groups = $query->where('consultant_id', Auth::id())
                ->withCount([
                    // Active only if has valid RTW
                    'workers as active_members_count' => function ($q) {
                        $q->where('status', 'Active')
                            ->whereHas('latest_end_date_rights_to_work_details', function ($sub) {
                                $sub->whereDate('end_date', '>=', Carbon::today());
                            });
                    },

                    // Leaver with valid RTW
                    'workers as leavers_count' => function ($q) {
                        $q->where('status', 'Leaver')
                            ->whereHas('latest_end_date_rights_to_work_details', function ($sub) {
                                $sub->whereDate('end_date', '>=', Carbon::today());
                            });
                    },

                    // Archived with valid RTW
                    'workers as archived_count' => function ($q) {
                        $q->where('status', 'Archived')
                            ->whereHas('latest_end_date_rights_to_work_details', function ($sub) {
                                $sub->whereDate('end_date', '>=', Carbon::today());
                            });
                    },

                    // Workers without valid RTW
                    'workers as no_rtw_count' => function ($q) {
                        $q->where(function ($sub) {
                            $sub->whereDoesntHave('latest_end_date_rights_to_work_details')
                                ->orWhereHas('latest_end_date_rights_to_work_details', function ($inner) {
                                    $inner->whereDate('end_date', '<', Carbon::today());
                                });
                        });
                    },
                ])
                ->with(['team', 'workers.latest_end_date_rights_to_work_details'])
                ->get();

            $array  = [];
            if ($groups) {
                foreach ($groups as $row) {
                    $array[] = [
                        'name'  => $row['name'],
                        'team_name' => $row['team']['name'],
                        'active_members' => $row['active_members_count'],
                        'no_rtw' => $row['no_rtw_count'],
                        'leavers' => $row['leavers_count'],
                        'archived' => $row['archived_count'],
                        'action' => $this->action($row['id'], $row['deleted_at'], $row['name']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($groups),
                'recordsFiltered'   => count($groups),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function action($id, $deleted_at, $name) {
        if (is_null($deleted_at)) {
            $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 archive_un_archived_group" data-group_id="'.$id.'" data-status="archived">
                    <i class="fs-2 las la-archive"></i>
                </a>

                <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="edit_group" data-group_id="'.$id.'"  data-group_name="'.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').'">
                    <i class="fs-2 las la-edit"></i>
                </a>

                <a href="'.url('associate-groups-details/'.$id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_group" data-group_id="'.$id.'"  data-group_name="'.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').'">
                    <i class="fs-2 las la-arrow-right"></i>
                </a>';
        } else {
            $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 archive_un_archived_group" data-group_id="'.$id.'" data-status="unarchived">
                <i class="fs-2 las la-undo"></i>
            </a>';
        }

        return $action;
    }

    public function storeGroupAction(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'team' => ['required'],
                'group_name' => [
                    'required',
                    Rule::unique('groups', 'name')->where(function ($query) use ($request) {
                        return $query->where('team_id', $request->input('team'));
                    }),
                ],
            ], [
                'group_name.unique' => 'The group name must be unique within the selected team.',
            ]);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            Group::query()->create([
                'team_id' => $params['team'],
                'consultant_id' => Auth::id(),
                'name' => $params['group_name'],
            ]);
            return self::responseWithSuccess('Group successfully created.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getSingleGroup($id) {
        try {
            $group = Group::query()->where('id', $id)->first();
            if (!$group) {
                return self::responseWithError('Group details not found, please try again later.');
            }

            return self::responseWithSuccess('Group details',  [
                'group_details' => $group
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function editGroupAction(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'edit_team' => ['required'],
                'edit_group_name' => [
                    'required',
                    Rule::unique('groups', 'name')
                        ->where(function ($query) use ($request) {
                            return $query->where('team_id', $request->input('edit_team'));
                        })
                        ->ignore($request->input('edit_group_id')),
                ],
            ], [
                'edit_group_name.unique' => 'The group name must be unique within the selected team.',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            Group::query()->where('id', $params['edit_group_id'])->update([
                'team_id' => $params['edit_team'],
                'name' => $params['edit_group_name'],
            ]);

            return self::responseWithSuccess('Group successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function archivedUnArchivedGroupAction(Request $request) {
        try {
            $params = $request->input();
            $group = Group::withTrashed()->where('id', $params['id'])->first();
            if(!$group) {
                return self::responseWithError('Group details not found, please try again.');
            }

            if ($params['status'] == 'archived') {
                Group::query()->where('id', $params['id'])->delete();
                $message = 'Group successfully archived.';
            } else {
                Group::onlyTrashed()->where('id', $params['id'])->update([
                    'deleted_at' => null
                ]);
                $message = 'Group successfully un-archived.';
            }
            return self::responseWithSuccess($message);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function storeGroupWithWorkerAction(Request $request){
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
               'group_name' => 'required',
            ],[
                'group_name.required' => 'The group field is required.',
            ]);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            $group = Group::query()->where('id', $params['group_name'])
                ->with(['jobs'])
                ->first();

            if (!$group) {
                throw new \Exception('Group details not found, please try again later.');
            }

            if ($params['create_type'] == 0) {
                $jobNames = $group->jobs
                    ->map(fn ($job) => '- ' . $job->name)
                    ->implode('<br>');

                if ($jobNames != '') {
                    return self::responseWithSuccess('selected_group_details_fetched', [
                        'group_name' => $group['name'],
                        'job_name' => $jobNames
                    ]);
                }

                GroupWithWorker::query()->create([
                    'group_id' => $params['group_name'],
                    'worker_id' => $params['worker_id']
                ]);

                DB::commit();
                return self::responseWithSuccess('Worker successfully added into group.');
            }

            if ($params['link_worker_to_job_using_group_type'] == 'link_to_existing_job') {
                foreach ($group->jobs->pluck('id')->values() as $jobId) {

                    $job = ClientJob::query()->where('id', $jobId)->first();
                    if (!$job) {
                        throw new \Exception('Job details not found, please try again later.');
                    }

                    $ClientJobWorker = ClientJobWorker::query()->where('job_id', $jobId)
                        ->where('worker_id', $params['worker_id'])
                        ->first();

                    if ($ClientJobWorker) {
                        if ($ClientJobWorker['declined_at'] || $ClientJobWorker['archived_at']) {
                            ClientJobWorker::query()->where('id', $ClientJobWorker['id'])->update([
                                'invitation_type'           => 2,
                                'confirmed_at'              => Carbon::now(),
                                'confirmed_by_admin_user_id'=> Auth::id(),
                                'declined_at'               => null,
                                'archived_at'               => null
                            ]);
                        }
                    } else {
                        ClientJobWorker::query()->create([
                            'job_id'                    => $jobId,
                            'worker_id'                 => $params['worker_id'],
                            'invitation_type'           => 2,
                            'confirmed_at'              => Carbon::now(),
                            'confirmed_by_admin_user_id'=> Auth::id(),
                        ]);
                    }
                }
            }

            GroupWithWorker::query()->create([
                'group_id' => $params['group_name'],
                'worker_id' => $params['worker_id']
            ]);

            DB::commit();
            return self::responseWithSuccess('Worker successfully added into group.');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function getGroupWithWorker(Request $request) {
        try {
            $groups = GroupWithWorker::with(['group' => function($q) {
                $q->withCount('workers');
            }])
                ->where('worker_id', $request->input('worker_id'))
                ->get();

            $array = [];
            foreach ($groups as $row) {
                $array[] = [
                    'group_name'        => $row['group']['name'],
                    'number_of_members' => $row['group']['workers_count'],
                    'action'            => $this->groupWithWorkerAction($row['id']),
                ];
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($array),
                'recordsFiltered'   => count($array),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function groupWithWorkerAction($id) {
        return ' <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm me-1 unlink_group" data-group-with-worker_id="'.$id.'">
            <i class="fs-2 las la-unlink"></i>
        </a>

        <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
            <i class="fs-2 las la-arrow-right"></i>
        </a>';
    }

    public function unlinkGroupWithWorkerAction($id) {
        try {
            $groupWithWorker = GroupWithWorker::query()->where('id', $id)->first();
            if (!$groupWithWorker) {
                return self::responseWithError('Worker group details not found, Please try again.');
            }

            GroupWithWorker::query()->where('id', $id)->delete();
            return self::responseWithSuccess('Worker successfully removed from group.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function associateGroupsDetails($id) {
        $group = Group::query()->where('id', $id)->first();
        $cost_centres = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('groups.group_details', compact(['group', 'cost_centres']));
    }

    public function getGroupWorkers(Request $request) {
        try {
            $query = GroupWithWorker::query()
                ->where('group_id', $request->input('group_id'));

            if (request('status') !== null && request('status') !== 'All') {
                $query->withWhereHas('worker', function ($q) {
                    $q->where('status', request('status'))
                        ->with('rights_to_work_details');
                });
            } else {
                $query->with('worker.rights_to_work_details');
            }

            $groupWorker = $query->get();

            $array  = [];
            if ($groupWorker) {
                foreach ($groupWorker as $row) {
                    $worker = $row['worker'];
                    $rtw_date = RightToWorkHelper::getLatestDate($worker['rights_to_work_details']);
                    if ($rtw_date) {
                        $formattedDate = date('d-m-Y', strtotime($rtw_date));
                        $isExpired = strtotime($rtw_date) < strtotime(date('Y-m-d'));

                        $rtwExpires = $isExpired
                            ? '<span class="text-danger">'.$formattedDate.'</span>'
                            : $formattedDate;
                    } else {
                        $rtwExpires = '-';
                    }

                    $array[] = [
                        'worker_name' => $worker['first_name'].' '.$worker['middle_name'].' '.$worker['last_name'],
                        'worker_status' => ($worker['leaving_date'] && $worker['status'] == 'Leaver')
                                            ? 'Leaver ('.date('d-m-Y', strtotime($worker['leaving_date'])).')'
                                            : $worker['status'],
                        'rtw_expires' => $rtwExpires,
                        'actions' => $this->groupWorkersAction($row['id'], $row['worker_id']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($groupWorker),
                'recordsFiltered'   => count($groupWorker),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage(), $e->getLine());
        }
    }

    private function groupWorkersAction($id, $worker_id) {
        return ' <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm me-1 unlink_group" data-group-with-worker_id="'.$id.'">
            <i class="fs-2 las la-unlink"></i>
        </a>

        <a href="'.url('view-worker-details/'.$worker_id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
            <i class="fs-2 las la-arrow-right"></i>
        </a>';
    }

    public function searchWorkerToAddGroup(Request $request) {
        try {
            $params  = $request->input();

            if (!$params['keyword']['term']) {
                return self::responseWithError('Please enter keyword to search worker');
            }

            $groupWorker = GroupWithWorker::query()->where('group_id', $params['group_id'])->pluck('worker_id');

            $searchTerms = explode(' ', $params['keyword']['term']);
            $worker = Worker::query()
                ->select(['id', 'first_name', 'middle_name', 'last_name', 'date_of_birth'])
                ->with('worker_cost_center')
                ->whereNotNull('email_verified_at')
                ->where('status', 'Active')
                ->where(function ($query) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $query->where(function ($query) use ($term) {
                            $query->where('first_name', 'LIKE', '%'.$term.'%')
                                ->orWhere('middle_name', 'LIKE', '%'.$term.'%')
                                ->orWhere('last_name', 'LIKE', '%'.$term.'%');
                        });
                    }
                })
                ->when(request('associated_cost_center') != null, function ($query) use ($params) {
                    $query->whereHas('worker_cost_center', function ($subQuery) use ($params) {
                        $subQuery->whereIn('cost_center', $params['associated_cost_center']);
                    });
                })
                ->whereNotIn('id', $groupWorker)
                ->get();

            $array = [];
            if ($worker) {
                foreach ($worker as $row) {
                    $array[] = [
                        'id'    => $row['id'],
                        'name'  => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'].' - '.date('d/m/Y', strtotime($row['date_of_birth'])),
                    ];
                }
            }

            return self::responseWithSuccess('Worker details', $array);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function storeGroupWorkerAction(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'group_worker_name' => 'required',
            ], [
                'group_worker_name.required' => 'The associate field is required.',
            ]);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();

            $group = Group::query()->where('id', $params['group_id'])->with(['jobs'])->first();
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

                $insertArray = $this->addWorkerIntoGroup($params['group_worker_name'], $params['group_id']);
                if (!$insertArray) {
                    throw new \Exception('Worker not available to added into group.');
                }

                GroupWithWorker::query()->insert($insertArray);
                DB::commit();
                return self::responseWithSuccess('Worker successfully added into group.');
            }

            if ($request->input('link_worker_to_job_using_group_type') == 'link_to_existing_job') {
                foreach ($group->jobs->pluck('id')->values() as $jobId) {

                    $job = ClientJob::query()->where('id', $jobId)->first();
                    if (!$job) {
                        throw new \Exception('Job details not found, please try again later.');
                    }

                    foreach ($params['group_worker_name'] as $workerId) {
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

            $insertArray = $this->addWorkerIntoGroup($params['group_worker_name'], $params['group_id']);
            if (!$insertArray) {
                throw new \Exception('Worker not available to added into group.');
            }

            GroupWithWorker::query()->insert($insertArray);
            DB::commit();
            return self::responseWithSuccess('Worker successfully added into group.');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function addWorkerIntoGroup($workerIds, $groupId) {
        $insertArray = [];
        if (!$workerIds) {
            return self::responseWithError('Please select a associate.');
        }

        foreach ($workerIds as $worker_id) {
            $insertArray[] = [
                'group_id' => $groupId,
                'worker_id' => $worker_id,
            ];
        }

        return $insertArray;
    }

    public function linkGroupToJobAction(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'group_name'        => 'required',
                'group_with_job_id' => 'required'
            ], [
                'group_name.required' => 'The group field is required.',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $jobId   = $request->input('group_with_job_id');
            $groupId = $request->input('group_name');

            $job = ClientJob::query()->where('id', $jobId)->with(['client_job_worker_details' => function ($q) {
                $q->whereNull('archived_at');
            }])->first();
            if (!$job) {
                throw new \Exception('Job details not found, please try again later.');
            }

            $group = Group::query()->where('id', $groupId)->with('workers:id')->first();
            if (!$group) {
                throw new \Exception('Group details not found, please try again later.');
            }

            $filteredWorkers = $group->workers->whereNotIn('id', $job->client_job_worker_details->pluck('worker_id'))->values();

            $alreadyLinked = GroupWithJob::query()->where([
                'group_id' => $groupId,
                'job_id'   => $jobId,
            ])->exists();

            if ($alreadyLinked) {
                throw new \Exception('This group is already assigned to this job.');
            }

            GroupWithJob::query()->create([
                'group_id' => $groupId,
                'job_id'   => $jobId,
            ]);

            if ($filteredWorkers) {
                foreach ($filteredWorkers as $row) {

                    $ClientJobWorker = ClientJobWorker::query()->where('job_id', $jobId)
                        ->where('worker_id', $row['id'])
                        ->first();

                    if ($ClientJobWorker) {
                        ClientJobWorker::query()->where('id', $ClientJobWorker['id'])->update([
                            'invitation_type'           => 2,
                            'confirmed_at'              => Carbon::now(),
                            'confirmed_by_admin_user_id'=> Auth::id(),
                            'declined_at'               => null,
                            'archived_at'               => null
                        ]);
                    } else {
                        ClientJobWorker::query()->create([
                            'job_id'                    => $jobId,
                            'worker_id'                 => $row['id'],
                            'invitation_type'           => 2,
                            'confirmed_at'              => Carbon::now(),
                            'confirmed_by_admin_user_id'=> Auth::id(),
                        ]);
                    }
                }
            }

            DB::commit();
            return self::responseWithSuccess($group['name'].' group successfully linked to '.strtolower($job['name']).' job');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function unlinkGroupToJobAction(Request $request) {
        DB::beginTransaction();
        try {
            $params = $request->input();
            $groupWithJob = GroupWithJob::query()->where('id', $params['record_id'])->first();
            if (!$groupWithJob) {
                throw new \Exception('Group job details not found, please try again later');
            }
            GroupWithJob::query()->where('id', $params['record_id'])->delete();

            if ($params['unlink_groups'] == 2) {
                $workerIds = GroupWithWorker::query()
                    ->where('group_id', $params['group_id'])
                    ->pluck('worker_id');

                if ($workerIds) {
                    ClientJobWorker::query()
                        ->whereIn('worker_id', $workerIds)
                        ->where('job_id', $params['job_id'])
                        ->update([
                            'archived_at' => Carbon::now(),
                            'archived_reason' => 'Unlink group'
                        ]);
                }
            }

            DB::commit();
            return self::responseWithSuccess('Group unlinked from this job successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }
}
