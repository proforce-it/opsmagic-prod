<?php

namespace App\Http\Controllers\Workers;

use App\Http\Controllers\Controller;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;

class LeaverWorkerController extends Controller
{
    use JsonResponse;
    public function index() {
        return view('workers.leaver.dis_worker');
    }

    public function listOfLeaverWorkers() {
        try {
            $workers    = Worker::withTrashed()->where('status', 'Leaver')->get();
            $array      = [];

            if ($workers) {
                foreach ($workers as $row) {
                    $array[] = [
                        'worker_name'   => '<a href="'.url('view-leaver-worker-details/'.$row['id']).'" data-worker-id="'.$row['id'].'">'.$row['first_name'].' '.$row['middle_name'].' '.$row['last_name'].'</a>',
                        'mobile_number' => $row['mobile_number'],
                        'email_address' => $row['email_address'],
                        'right_to_work' => implode(" ",array_map(function($a) { return "<span class='badge badge-success'>".$a."</span>"; },explode('~~~~~', $row['right_to_work']))),
                        'actions'       => $this->action($row['id']),
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

    public function action($id) {
        return '<a href="'.url('view-leaver-worker-details/'.$id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_client" data-worker-id="'.$id.'">
           <i class="fs-2 las la-arrow-right"></i>
        </a>';
    }

    public function viewLeaverWorker($id) {
        $worker = Worker::withTrashed()->where('id', $id)->first();
        return view('workers.leaver.view_worker', compact('worker'));
    }
}
