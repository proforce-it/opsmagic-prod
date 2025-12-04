<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Models\Job\JobLine;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobLineController extends Controller
{
    use JsonResponse;
    public function index(Request $request) {
        try {
            if ($request->input('status') == 'active') {
                $query = JobLine::query();
            } elseif ($request->input('status') == 'archived') {
                $query = JobLine::onlyTrashed();
            } else {
                $query = JobLine::withTrashed();
            }

            $jobLine = $query->where('job_id', $request->input('job_id'))
                ->get()
                ->toArray();

            $array  = [];
            if ($jobLine) {
                foreach ($jobLine as $row) {
                    $array[] = [
                        'name'  => $row['line_name'],
                        'code'  => $row['line_code'],
                        'action'=> $this->action($row),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($jobLine),
                'recordsFiltered'   => count($jobLine),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function action($row) {
        $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-2" id="edit_job_line" 
                        data-id="'.$row['id'].'"
                        data-line_name="'.$row['line_name'].'"
                        data-line_code="'.$row['line_code'].'"
                        data-color_code="'.$row['color_code'].'">
                <i class="fs-2 las la-edit"></i>
            </a>';

        if ($row['deleted_at']) {
            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-2" id="unarchived_job_line" data-id="'.$row['id'].'">
                        <i class="fs-2 las la-undo"></i>
                    </a>';
        } else {
            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-2" id="archived_job_line" data-id="'.$row['id'].'">
                        <i class="fs-2 las la-archive"></i>
                    </a>';
        }

        return $action;
    }

    public function storeJobLine(Request $request) {
        try {
            $params = $request->input();
            $store_id = $params['store_id'] ?? 0;
            $job_id = $params['job_id'];

            $validator = Validator::make($request->input(), [
                'line_name' => 'required|unique:job_lines,line_name,' . ($store_id ? $store_id : 'NULL') . ',id,job_id,' . $job_id,
                'line_code' => 'required|unique:job_lines,line_code,' . ($store_id ? $store_id : 'NULL') . ',id|max:6',
                'job_line_color' => 'required',
            ],[
                'line_name.required' => 'The name field is required.',
                'line_name.unique' => 'The line name has already been taken for this job. Please choose a different one.',
                'line_code.required' => 'The code field is required.',
                'line_code.unique' => 'The line code has already been taken. Please choose a different one.',
                'line_code.size' => 'The code must be exactly 6 characters long.',
                'job_line_color.required' => 'The color field is required.',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $array = [
                'job_id' => $params['job_id'],
                'line_name' => $params['line_name'],
                'line_code' => $params['line_code'],
                'color_code' => $params['job_line_color'],
            ];
            if ($params['store_id'] == 0) {
                JobLine::query()->create($array);
                $message = 'Job line successfully created.';
            } else {
                $jobLine = JobLine::query()->where('id', $params['store_id'])->first();

                if (!$jobLine) {
                    return self::responseWithError('Job line jot found, please try again later.');
                }

                JobLine::query()->where('id', $params['store_id'])->update($array);
                $message = 'Job line successfully updated.';
            }
            return self::responseWithSuccess($message);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function actionJobLine(Request $request) {
        try {
            $jobLine = JobLine::withTrashed()->where('id', $request->input('job_line_id'))->first();

            if (!$jobLine) {
                return self::responseWithError('Job line jot found, please try again later.');
            }

            if ($request->input('action_type') == 'archived') {
                $jobLine->delete();
                $message = 'Job line successfully archived.';
            } else {
                $jobLine->restore();
                $message = 'Job line successfully un-archived.';
            }
            return self::responseWithSuccess($message);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
