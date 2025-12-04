<?php

namespace App\Helper\Clients;

use App\Jobs\JobShiftInvitationMail;
use App\Jobs\JobWorkerDirectPlacementMail;
use App\Jobs\JobWorkerInvitationMail;
use App\Mail\JobDirectPlacementEmail;
use App\Mail\JobInvitationEmail;
use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Job\JobShift;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isEmpty;

class ClientHelper
{
    use JsonResponse;
    public static function workerAddedIntoJobSendMail($worker_id, $job_id, $client_job_worker_id, $invitation_type) {

        $worker = Worker::query()
            ->select('id', 'first_name', 'middle_name', 'last_name', 'email_address')
            ->where('id', $worker_id)
            ->first();

        $job = ClientJob::query()
            ->where('id', $job_id)
            ->with(['client_details', 'site_details'])
            ->first();

        $invitation = [
            'worker_name'   => $worker['first_name'],
            'client_name'   => ($job['client_details']) ? $job['client_details']['company_name'] : '',
            'site_name'     => ($job['site_details']) ? $job['site_details']['site_name'] : '',
            'site_address'  => ($job['site_details']) ? $job['site_details']['address_line_1'].', '.$job['site_details']['address_line_2'].', '.$job['site_details']['city'].', '.$job['site_details']['postcode'] : '',
            'what_three_words_address' => ($job['site_details']) ? $job['site_details']['what_three_words_address'] : '',
            'job_name'      => $job['name'],
        ];

        $attachmentPath = ($job['assignment_schedule'])
            ? public_path('workers/client_job/'.$job['assignment_schedule'])
            : '';

        if ($invitation_type == '1') {
            $invitation = array_merge($invitation, [
                'accept_link'   => url('confirm-client-job-worker/'.$client_job_worker_id.'/0/1'),
                'declined_link' => url('confirm-client-job-worker/'.$client_job_worker_id.'/0/0')
            ]);
        }

        $JobWorkerInvitationMail = new JobWorkerInvitationMail((object)$invitation, $worker['email_address'], $attachmentPath, $invitation_type);
        dispatch($JobWorkerInvitationMail);

        return self::responseWithSuccess('Mail successfully send.');
    }

    public static function workerAddedIntoJobShiftSendMail($worker_id, $shift_id, $job_shift_worker_id, $invitation_type) {

        $worker = Worker::query()
            ->select('id', 'first_name', 'middle_name', 'last_name', 'email_address')
            ->where('id', $worker_id)
            ->first();

        $jobShift = JobShift::query()
            ->where('id', $shift_id)
            ->with('client_job_details')
            ->first();

        $job = ClientJob::query()
            ->where('id', $jobShift['job_id'])
            ->with(['client_details', 'site_details'])
            ->first();

        $invitation = [
            'worker_name'   => $worker['first_name'],
            'client_name'   => ($job['client_details']) ? $job['client_details']['company_name'] : '',
            'site_name'     => ($job['site_details']) ? $job['site_details']['site_name'] : '',
            'site_address'  => ($job['site_details']) ? $job['site_details']['address_line_1'].', '.$job['site_details']['address_line_2'].', '.$job['site_details']['city'].', '.$job['site_details']['postcode'] : '',
            'what_three_words_address' => ($job['site_details']) ? $job['site_details']['what_three_words_address'] : '',
            'job_name'      => $job['name'],
            'start_time'    => $jobShift['start_time'],
            'shift_date'    => $jobShift['date'],
            'default_shift_duration' => $jobShift['shift_length_hr'].'h '.$jobShift['shift_length_min'].'m',
        ];

        if ($invitation_type == 'Invitation') {
            $invitation = array_merge($invitation, [
                'accept_link'   => url('confirm-job-shift-worker/'.$job_shift_worker_id.'/1'),
                'declined_link' => url('confirm-job-shift-worker/'.$job_shift_worker_id.'/0'),
            ]);
        }

        $JobShiftInvitationMail = new JobShiftInvitationMail((object)$invitation, $worker['email_address'], $invitation_type);
        dispatch($JobShiftInvitationMail);

        return self::responseWithSuccess('Mail successfully send.');
    }

    public static function getFlags($client) {
        $flags = '';
        if (!$client['payroll_week_starts']) {
            $flags .= '<span class="badge badge-danger me-1 mb-1 prospect_client" id="header_pay_flag">PAY</span>';
        }

        if (empty($client['client_site_details'])) {
            $flags .= '<span class="badge badge-danger me-1 mb-1 prospect_client" id="header_site_flag">SITE</span>';
        }

        if (self::clientDocumentFlag($client['client_documents']) == 1) {
            $flags .= '<span class="badge badge-danger me-1 mb-1 prospect_client">DOCUMENTS</span>';
        }

        return $flags;
    }

    public static function requiredDocuments() {
        return [
            'ETHICAL REVIEW',
            'HEALTH & SAFETY REVIEW',
            'SLA',
            'TERMS OF BUSINESS',
        ];
    }

    public static function clientDocumentFlag($client_documents) {
        if (empty($client_documents)) {
            return TRUE;
        } else {
            $requiredTitles = self::requiredDocuments();
            $documentTitles = array_map(function ($doc) {
                return strtoupper(trim($doc['document_file_title'] ?? ''));
            }, $client_documents);

            foreach ($requiredTitles as $requiredTitle) {
                if (!in_array($requiredTitle, $documentTitles)) {
                    return TRUE;
                }
            }
            return FALSE;
        }
    }

    public static function automaticClientActive($clientId) {
        $client = Client::query()->where('id', $clientId)->with(['client_documents', 'client_site_details'])
            ->first()
            ->toArray();
        if ($client && $client['status'] == 'Prospect') {
            $flags = self::getFlags($client);
            $hasProspectClient = $flags && Str::contains($flags, 'prospect_client');
            if (!$hasProspectClient) {
                Client::query()->where('id', $clientId)->update([
                    'status' => 'Active'
                ]);
            }
        }
    }
}