<?php

namespace App\Exports;

use App\Helper\Workers\RightToWorkHelper;
use App\Models\Client\ClientJobWorker;
use App\Models\Job\JobShiftWorker;
use App\Models\PickUpPoint\PickUpPoint;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingConfirmWorkerExport implements  FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $filterArray;

    public function __construct($filterArray) {
        $this->filterArray = $filterArray;
    }

    public function collection()
    {
        return JobShiftWorker::query()->select('job_shift_workers.*')
            ->where('job_shift_id', $this->filterArray['job_shift_id'])
            ->whereNotNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('cancelled_at')
            ->with(['rightsToWork', 'jobShift'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Job ID',
            'Job name',
            'Site name',
            'Client name',
            'Worker ID',
            'Worker name',
            'Worker DOB',
            'RTW expiry date',
            'Pickup point name',
            'Booking start',
            'Booking hours',
            'Actual start',
            'Actual hours',
            'Line Code'
        ];
    }

    public function map($jobShiftWorker): array
    {
        $latestRTWExpiryDate = RightToWorkHelper::getLatestDate($jobShiftWorker['rightsToWork']);
        $clientJobWorker = ClientJobWorker::query()->where('job_id', $jobShiftWorker->jobShift->job_id)
            ->where('worker_id', $jobShiftWorker->worker_id)
            ->with(['worker.preferred_pickup_point', 'rightsToWork'])
            ->first();

        $pickupDetails = '';
        if ($clientJobWorker) {
            $getPickupDetails = function ($pickupPointId) {
                $pickupPoint = PickUpPoint::query()->where('id', $pickupPointId)->first();
                if ($pickupPoint) {
                    return $pickupPoint->name . ' - ///' . $pickupPoint->what_three_words_locator;
                }
                return null;
            };

            if ($clientJobWorker['agreed_pickup_point'] == '0') {
                $pickupDetails = 'None (no pickup required)';
            } elseif ($clientJobWorker['agreed_pickup_point'] == '00') {
                $pickupDetails = 'Same as preferred';
            } elseif ($clientJobWorker['agreed_pickup_point']) {
                $pickupDetails = $getPickupDetails($clientJobWorker['agreed_pickup_point']);
            } else {
                $pickupDetails = ($clientJobWorker['worker']['preferred_pickup_point'])
                    ? $getPickupDetails($clientJobWorker['worker']['preferred_pickup_point']['id'])
                    : '';
            }
        }

        return [
            date('d-m-Y', strtotime($jobShiftWorker['shift_date'])),

            $jobShiftWorker->jobShift->job_id,

            ($jobShiftWorker->jobShift->client_job_details)
                ? $jobShiftWorker->jobShift->client_job_details->name
                : '',

            ($jobShiftWorker->jobShift->client_job_details)
                ? ($jobShiftWorker->jobShift->client_job_details->site_details)
                    ? $jobShiftWorker->jobShift->client_job_details->site_details->site_name
                    : ''
                : '',

            ($jobShiftWorker->jobShift->client_job_details)
                ? ($jobShiftWorker->jobShift->client_job_details->client_details)
                    ? $jobShiftWorker->jobShift->client_job_details->client_details->company_name
                    : ''
                : '',

            ($clientJobWorker->worker)
                ? $clientJobWorker->worker->worker_no
                : '',

            ($clientJobWorker->worker)
                ? $clientJobWorker->worker->first_name.' '.$clientJobWorker->worker->middle_name.' '.$clientJobWorker->worker->last_name
                : '',

            ($clientJobWorker->worker)
                ? date('d-m-Y', strtotime($clientJobWorker->worker->date_of_birth))
                : '',

            ($latestRTWExpiryDate)
                ? date('d-m-Y', strtotime($latestRTWExpiryDate))
                : '',

            $pickupDetails,

            date('H:i', strtotime($jobShiftWorker->jobShift->start_time)),

            $jobShiftWorker->jobShift->shift_length_hr.'.'.$jobShiftWorker->jobShift->shift_length_min,

            '',
            '',
            ''
        ];
    }
}
