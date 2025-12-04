<?php

namespace App\Http\Controllers;

use App\Models\Accommodation\Accommodation;
use App\Models\Client\ClientJobWorker;
use App\Models\Client\Site;
use App\Models\Group\CostCentre;
use App\Models\PickUpPoint\PickUpPoint;
use App\Models\Worker\WorkerCostCenter;
use App\My_response\Traits\Response\JsonResponse;

class HomeController extends Controller
{
    use JsonResponse;
    public function updateSiteCostCentres() {
        $site = Site::query()->select('id', 'cost_center')->whereNotNull('cost_center')->get();
        if (!$site) {
            return self::responseWithError('Site not available.');
        }

        foreach ($site as $row) {
            $costCentre = CostCentre::query()->where('short_code', $row['cost_center'])->first();
            if ($costCentre) {
                Site::query()->where('id', $row['id'])->update([
                    'cost_center' => $costCentre['id']
                ]);
            }
        }
        return self::responseWithSuccess('Site cost centres successfully updated');
    }

    public function updateClientJobWorkerCostCentres() {
        $clientJobWorker = ClientJobWorker::query()->select('id', 'associated_cost_center')->whereNotNull('associated_cost_center')->get();
        if (!$clientJobWorker) {
            return self::responseWithError('Client job worker not available.');
        }

        foreach ($clientJobWorker as $row) {
            $explode = explode(', ', $row['associated_cost_center']);
            $update_cost_center = [];
            foreach ($explode as $cc) {
                $costCentre = CostCentre::query()->where('short_code', $cc)->first();
                if ($costCentre) {
                    $update_cost_center[] = $costCentre['id'];
                }
            }

            if ($update_cost_center) {
                ClientJobWorker::query()->where('id', $row['id'])->update([
                    'associated_cost_center' => implode(', ', $update_cost_center)
                ]);
            }
        }
        return self::responseWithSuccess('client job worker cost centres successfully updated');
    }

    public function updateWorkerCostCentres() {
        $workerCostCentres = WorkerCostCenter::query()->get();
        if (!$workerCostCentres) {
            return self::responseWithError('Worker cost centres not available.');
        }

        foreach ($workerCostCentres as $row) {
            $costCentre = CostCentre::query()->where('short_code', $row['cost_center'])->first();
            if ($costCentre) {
                WorkerCostCenter::query()->where('id', $row['id'])->update([
                    'cost_center' => $costCentre['id']
                ]);
            }
        }
        return self::responseWithSuccess('Worker cost centres successfully updated');
    }

    public function updateAccommodationCostCentres() {
        $accommodationCostCentres = Accommodation::query()->select('id', 'cost_center')->whereNotNull('cost_center')->get();
        if (!$accommodationCostCentres) {
            return self::responseWithError('accommodation not available.');
        }

        foreach ($accommodationCostCentres as $row) {
            $explode = explode(', ', $row['cost_center']);
            $update_cost_center = [];
            foreach ($explode as $cc) {
                $costCentre = CostCentre::query()->where('short_code', $cc)->first();
                if ($costCentre) {
                    $update_cost_center[] = $costCentre['id'];
                }
            }

            if ($update_cost_center) {
                Accommodation::query()->where('id', $row['id'])->update([
                    'cost_center' => implode(', ', $update_cost_center)
                ]);
            }
        }
        return self::responseWithSuccess('Accommodation cost centres successfully updated');
    }

    public function updatePickupPointCostCentres() {
        $pickupPointCostCentres = PickUpPoint::query()->select('id', 'cost_center')->whereNotNull('cost_center')->get();
        if (!$pickupPointCostCentres) {
            return self::responseWithError('Pickup point not available.');
        }

        foreach ($pickupPointCostCentres as $row) {
            $explode = explode(', ', $row['cost_center']);
            $update_cost_center = [];
            foreach ($explode as $cc) {
                $costCentre = CostCentre::query()->where('short_code', $cc)->first();
                if ($costCentre) {
                    $update_cost_center[] = $costCentre['id'];
                }
            }

            if ($update_cost_center) {
                PickUpPoint::query()->where('id', $row['id'])->update([
                    'cost_center' => implode(', ', $update_cost_center)
                ]);
            }
        }
        return self::responseWithSuccess('Pickup point cost centres successfully updated');
    }
}
