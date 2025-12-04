<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Models\Group\CostCentre;
use App\Models\Group\Team;
use App\Models\User;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class TeamsController extends Controller
{
    use JsonResponse;
    public function index() {
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        $teamMembers = User::query()->select(['id','name'])
            ->where('status','Active')
            ->where('team_id',null)
            ->orderBy('name', 'asc')
            ->get();

        return view('groups.dis_teams',compact('costCentre','teamMembers'));
    }

    public function getTeams(Request $request) {
        try {
            $teams = Team::query()->with(['costCentre','users'])->get();
            $array  = [];
            if ($teams) {
                foreach ($teams as $row) {
                    $array[] = [
                        'name'  => $row->name,
                        'cost_centre_id' => $row->costCentre->name,
                        'number_of_consultants' => $row->users->count(),
                        'action'    => $this->action($row['id'], $row['name']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($teams),
                'recordsFiltered'   => count($teams),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function action($id,$name) {
        return '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="edit_teams"
                    data-teams-id="'.$id.'"
                    data-teams-name="'.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').'">
                        <i class="fs-2 las la-edit"></i>
                   </a>';
    }

    public function storeTeamsAction(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:teams,name',
                'cost_centre' => 'required|exists:cost_centres,id',
                'team_members'  => 'required|array|min:1',
                'team_members.*'=> 'exists:users,id',
            ], [
                'name.required' => 'The team name field is required.',
                'name.unique' => 'This team name is already taken.',
                'cost_centre.unique' => 'This cost centre name is already taken.',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            $team = Team::query()->create([
                'name' => $params['name'],
                'cost_centre_id' => $params['cost_centre'],
            ]);

            if(isset($params['team_members']) && is_array($params['team_members'])) {
                User::query()->whereIn('id', $params['team_members'])
                    ->update(['team_id' => $team->id]);
            }

            return self::responseWithSuccess('Teams successfully created.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getSingleTeams($id) {
        try {
            $teams = Team::with('costCentre','users')->where('id', $id)->first();
            if (!$teams) {
                return self::responseWithError('Teams not found, please try again later.');
            }

            $userIds = $teams->users->pluck('id')->toArray();
            $teamMembers = User::query()->select(['id','name'])
                ->where('status','Active')
                ->where('team_id',null)
                ->orWhereIn('id', $userIds)
                ->orderBy('name', 'asc')
                ->get();

            $options = '';
            if($teamMembers) {
                foreach($teamMembers as $row) {
                    $selected = in_array($row->id, $userIds) ? 'selected' : '';
                    $options .= '<option value="'.$row['id'].'" ' . $selected . '>'.$row['name'].'</option>';
                }
            }

            return self::responseWithSuccess('Teams details',  [
                'teams_details' => $teams,
                'edit_team_members_options' => $options
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function editTeamAction(Request $request)
    {
        DB::beginTransaction();
        try {
            $teamId = $request->input('edit_teams_id');

            $validator = Validator::make($request->all(), [
                'edit_name' => 'required|unique:teams,name,' . $teamId,
                'edit_cost_centre' => 'required|exists:cost_centres,id',
                'edit_team_members' => 'required|array|min:1',
                'edit_team_members.*' => 'exists:users,id',
            ], [
                'edit_name.required' => 'The team name field is required.',
                'edit_name.unique' => 'This team name is already taken.',
                'edit_cost_centre.unique' => 'This cost centre name is already taken.',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            $team = Team::query()->where('id', $teamId)->first();
            if (!$team) {
                throw new \Exception('Team details not found, please try again later.');
            }

            $team->update([
                'name' => $params['edit_name'],
                'cost_centre_id' => $params['edit_cost_centre'],
            ]);

            $currentMemberIds = $team->users()->pluck('id')->toArray();
            $newMemberIds = $params['edit_team_members'];
            $membersToRemove = array_diff($currentMemberIds, $newMemberIds);

            if (!empty($membersToRemove)) {
                User::query()->whereIn('id', $membersToRemove)
                    ->update([
                        'team_id' => null
                    ]);
            }

            $membersToAdd = array_diff($newMemberIds, $currentMemberIds);
            if (!empty($membersToAdd)) {
                User::query()->whereIn('id', $membersToAdd)
                    ->update([
                        'team_id' => $teamId
                    ]);
            }

            DB::commit();
            return self::responseWithSuccess('Team successfully updated.');
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }
}
