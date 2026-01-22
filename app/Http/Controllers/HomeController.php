<?php

namespace App\Http\Controllers;

use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;

class HomeController extends Controller
{
    use JsonResponse;
    public function updateNationalityIdInWorkerTable() {
        $worker = Worker::query()->select('id', 'nationality')->with('nationality_details')->get();
        foreach ($worker as $row) {
            if ($row['nationality_details']) {
                Worker::query()->where('id', $row['id'])->update([
                    'nationality' => $row['nationality_details']['id']
                ]);
            }
        }
        return self::responseWithSuccess('Nationality ID successfully updated');
    }
}
