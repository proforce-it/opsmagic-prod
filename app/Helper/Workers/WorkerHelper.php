<?php

namespace App\Helper\Workers;

use App\Mail\ConfirmEmailAddress;
use App\Models\Worker\Worker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WorkerHelper
{
    public static function getFlags($worker) {
        $flags = '';
        if (!$worker['email_verified_at']) {
            $flags .= '<span class="badge border border-danger text-danger bg-transparent me-1 mb-1 cursor-pointer resend_link_btn prospect_worker">EMAIL</span>';
        }

        if (!$worker['accommodation_type'] || !$worker['proforce_transport']) {
            $flags .= '<span class="badge border border-danger text-danger bg-transparent me-1 mb-1 cursor-pointer go_to_tab prospect_worker" data-tab_hash="kt_table_widget_5_tab_11">UK ADDR</span>';
        }

        if ((!$worker['same_as_current_address'] && (!$worker['permanent_address_line_one'] || !$worker['permanent_city'] || !$worker['permanent_country']))) { /* || (!$worker['next_of_kin_first_name'] || !$worker['next_of_kin_last_name'] || !$worker['next_of_kin_mobile'] || !$worker['next_of_kin_email'])*/
            $flags .= '<span class="badge border border-danger text-danger bg-transparent me-1 mb-1 cursor-pointer go_to_tab prospect_worker" data-tab_hash="kt_table_widget_5_tab_2">OTHER ADDR</span>';
        }

        if (
            strtotime(date('Y-m-d')) >= strtotime(RightToWorkHelper::getLatestDate($worker['rights_to_work_details']))
            || ($worker['incomplete_rights_to_work_details'])
        ) {
            $flags .= '<span class="badge border border-danger text-danger bg-transparent me-1 mb-1 cursor-pointer go_to_tab prospect_worker" data-tab_hash="kt_table_widget_5_tab_3">RTW</span>';
        }

        if($worker['suspend'] == 'Yes') {
            $flags .= '<span class="badge border border-danger text-danger bg-transparent me-1 mb-1 prospect_worker">Suspended</span>';
        }

        if (self::workerDocumentFlag($worker['worker_documents'], [
                'proforce_to_open_bank_account' => $worker['proforce_to_open_bank_account'],
                'proforce_transport' => $worker['proforce_transport'],
                'accommodation_type' => $worker['accommodation_type']
            ]) == 1) {
            $flags .= '<span class="badge border border-danger text-danger bg-transparent me-1 mb-1 cursor-pointer go_to_tab prospect_worker" data-tab_hash="kt_table_widget_5_tab_9">DOCS</span>';
        }

        if($worker['tax_treatment'] && $worker['48_hour_opt_out'] && (!$worker['bank_account_number'] || !$worker['bank_account_name'] || !$worker['bank_name'] || !$worker['bank_ifsc_code'])) {
            $flags .= '<span class="badge border border-warning text-warning bg-transparent me-1 mb-1 cursor-pointer go_to_tab" data-tab_hash="kt_table_widget_5_tab_12">BANK</span>';
        } else if(!$worker['bank_account_number'] || !$worker['bank_account_name'] || !$worker['bank_name'] || !$worker['bank_ifsc_code']) {
            $flags .= '<span class="badge border border-danger text-danger bg-transparent me-1 mb-1 cursor-pointer go_to_tab prospect_worker" data-tab_hash="kt_table_widget_5_tab_12">BANK</span>';
        }

        if ($worker['id_documents'] && strtotime(date('Y-m-d')) >= strtotime(\App\Helper\Workers\RightToWorkHelper::getIdDocumentLatestDate($worker['id_documents']))) {
            $flags .= '<span class="badge border border-warning text-warning bg-transparent me-1 mb-1 cursor-pointer go_to_tab" data-tab_hash="kt_table_widget_5_tab_9">ID DOC. EXPIRED</span>';
        }

        if ($worker['id_documents'] && $worker['id_documents'][0]['incomplete'] == 1 && !$worker['id_documents'][0]['document_file']) {
            $flags .= '<span class="badge border border-danger text-danger bg-transparent me-1 mb-1 cursor-pointer go_to_tab prospect_worker id_document_incomplete" data-tab_hash="kt_table_widget_5_tab_9">ID</span>';
        }

        if (!$worker['mobile_number']) {
            $flags .= '<span class="badge border border-warning text-danger bg-transparent me-1 mb-1">MOBILE</span>';
        }

        if (!$worker['national_insurance_number']) {
            $flags .= '<span class="badge border border-warning text-warning bg-transparent me-1 mb-1 cursor-pointer go_to_tab" data-tab_hash="kt_table_widget_5_tab_1">NI</span>';
        }

        if (!$worker['worker_payroll_references']) {
            $flags .= '<span class="badge border border-warning text-warning bg-transparent me-1 mb-1">PAYROLL</span>';
        }

        if ($worker['leaving_date'] && $worker['status'] == 'Leaver') {
            $flags .= '<span class="badge border border-warning text-warning bg-transparent me-1 mb-1">LEAVER ('.date('d-m-Y', strtotime($worker['leaving_date'])).')</span>';
        }
        return $flags;
    }

    public static function send_confirm_email($id, $first_name, $last_name, $email) {
        $confirmData = (object) [
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'confirm_link'  => url('confirm-worker-email/'.$id),
        ];
        Mail::to($email)->send(new ConfirmEmailAddress($confirmData));
    }

    public static function preparedBookingDifference($nextValue, $preValue) {
        $difference = $nextValue - $preValue;
        if ($difference > 0) {
            return '<span class="text-gray-500"><i class="fs-3 las la-arrow-alt-circle-up"></i> '.number_format($difference).'</span>';
        } else if($difference < 0) {
            return '<span class="text-gray-500"><i class="fs-3 las la-arrow-alt-circle-down"></i> '.number_format(str_replace('-', '', $difference)).'</span>';
        } else {
            return '<span class="text-gray-500"><i class="fs-3 las la-minus-circle"></i> '.number_format(str_replace('-', '', $difference)).'</span>';
        }
    }

    public static function preparedSnapShotDifference($value) {
        if ($value > 0) {
            $arrow = '<i class="text-white las la-arrow-alt-circle-up"></i>';
        } else if($value < 0) {
            $arrow = '<i class="text-white las la-arrow-alt-circle-down"></i>';
        } else {
            $arrow = '<i class="text-white las la-minus-circle"></i>';
        }

        return '<span class="text-white rounded-3">'.$arrow.' £'.number_format($value, 2).'</span>';
    }

    public static function workerDocumentFlag($worker_documents, $workerFieldsForRequiredDocuments) {
        if (empty($worker_documents)) {
            return TRUE;
        } else {
            $requiredTitles = self::requiredDocuments($workerFieldsForRequiredDocuments);
            $documentTitles = array_map(function ($doc) {
                return strtoupper(trim($doc['document_file_title'] ?? ''));
            }, $worker_documents);

            foreach ($requiredTitles as $requiredTitle) {
                if (!in_array($requiredTitle, $documentTitles)) {
                    return TRUE;
                }
            }
            return FALSE;
        }
    }

    public static function requiredDocuments($workerFieldsForRequiredDocuments) {
        $docArray = [
            'ID',
            'REGISTRATION FORM',
            'CONTRACT',
            'ONBOARDING DOCS',
            /*'BANK ACCOUNT APPLICATION',
            'TRANSPORT FORM',
            'ACCOMMODATION FORM',*/
        ];

        if ($workerFieldsForRequiredDocuments['proforce_to_open_bank_account'] == 'Yes') {
            $docArray[] = 'BANK ACCOUNT APPLICATION';
        }

        if ($workerFieldsForRequiredDocuments['proforce_transport'] == 'Yes') {
            $docArray[] = 'TRANSPORT FORM';
        }

        if ($workerFieldsForRequiredDocuments['accommodation_type'] == 'supplied_by_pro_force') {
            $docArray[] = 'ACCOMMODATION FORM';
        }

        return $docArray;
    }

    public static function automaticWorkerActive($workerId) {
        $worker = Worker::query()->where('id', $workerId)->with(['worker_cost_center', 'rights_to_work_details', 'incomplete_rights_to_work_details', 'worker_documents', 'id_documents', 'accommodation_details', 'worker_payroll_references'])
            ->first()
            ->toArray();
        if ($worker && $worker['status'] == 'Prospect') {
            $flags = self::getFlags($worker);
            $hasProspectWorker = $flags && Str::contains($flags, 'prospect_worker');
            if (!$hasProspectWorker) {
                Worker::query()->where('id', $workerId)->update([
                    'status' => 'Active'
                ]);
            }
        }
    }
}
