<?php


namespace App\Helper\Activity;


use App\Models\Activity\ActivityLog;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\State;
use Illuminate\Support\Facades\Auth;

class ActivityLogs
{
    public static function updatesLog($log_for_id, $sub_type, $request, $object, $menu_type) {

        $logs       = [];
        $different  = array_diff($request,$object);

        foreach($different as $key => $value) {
            $logs[] = [
                'user_id'       => Auth::id(),
                'log_for_id'    => $log_for_id,
                'type'          => 'Update',
                'sub_type'      => $sub_type,
                'menu_type'     => $menu_type,
                'field'         => $key,
                'old_value'     => $object[$key],
                'new_value'     => $value,
            ];
        }

        if($logs)
            ActivityLog::query()->insert($logs);
    }

    public static function createAndDeleteLog($log_for_id, $type, $sub_type, $menu_type) {
        ActivityLog::query()->create([
            'user_id'       => Auth::id(),
            'log_for_id'    => $log_for_id,
            'type'          => $type,
            'sub_type'      => $sub_type,
            'menu_type'     => $menu_type
        ]);
    }

    public static function getSubHeading($field) {
        if (in_array($field, ["first_name", "middle_name", "last_name", "date_of_birth", "email_address", "mobile_number", "marital_status", "nationality", "national_insurance_number", "name_of_partner", "id_number_of_partner", "current_address_line_one", "current_address_line_two", "current_country", "current_state", "current_city", "current_zip_code", "permanent_address_line_one", "permanent_address_line_two", "permanent_country", "permanent_state", "permanent_city", "permanent_zip_code", "next_of_kin_first_name", "next_of_kin_surname", "next_of_kin_telephone", "next_of_kin_mobile", "next_of_kin_address_line_one", "next_of_kin_address_line_two", "next_of_kin_country", "next_of_kin_state", "next_of_kin_city", "next_of_kin_zip_code", "payroll_reference"])) {
            return 'Personal details changes';
        }  else if (in_array($field, ["bank_account_number", "bank_ifsc_code", "bank_name"])) {
            return 'Bank details changes';
        } else if ($field == "right_to_work") {
            return 'Right to Work Route changes';
        } else if ($field == "medical_issue_details") {
            return 'Medical issue changes';
        } else if (in_array($field, ["visa_type", "visa_reference_number", "visa_start_date", "visa_end_date"])) {
            return 'Visa details changes';
        } else if ($field == "region") {
            return 'Region changes';
        } else if ($field == "criminal_conviction_details") {
            return 'Criminal conviction changes';
        } else if (in_array($field, ["document_one", "document_two", "document_three", "document_four", "document_five", "document_six", "document_one_title", "document_two_title", "document_three_title", "document_four_title", "document_five_title", "document_six_title"])) {
            return 'Document changes';
        } else if ($field == "status") {
            return 'Status changes';
        } else if (in_array($field, ["status", "skill", "experience", "job_title", "company_name", "work_start_date", "work_end_date", "interview_date", "interview_status", "interview_details"])) {
            return 'Experience/Skill changes';
        } else {
            return ' - ';
        }
    }

    public static function preparedDataTable($activityLog) {
        $array = [];
        if ($activityLog) {
            foreach ($activityLog as $key => $row) {

                $prepare = self::prepareValue($row['old_value'], $row['new_value'], $row['field']);

                $array[] = [
                    'no'        => $key+1,
                    'user'      => $row['user_details']['name'],
                    'sub_heading' => self::getSubHeading($row['field']),
                    'type'      => $row['type'],
                    'sub_type'  => $row['sub_type'],
                    'field'     => ($row['field']) ? ucfirst(str_replace('_', ' ', $row['field'])) : '-',
                    'old_value' => ($prepare['old_value']) ? $prepare['old_value'] : '-',
                    'new_value' => ($prepare['new_value']) ? $prepare['new_value'] : '-',
                    'created_at'=> date('d-m-Y h:i:s A', strtotime($row['created_at'])),
                ];
            }
        }

        return $array;
    }

    public static function prepareValue($old_value, $new_value, $field) {
        /*if(in_array($field, ['current_country', 'permanent_country'])) {
            $returnArray = [
                'old_value'  => Country::query()->where('id', $old_value)->pluck('name'),
                'new_value'  => Country::query()->where('id', $new_value)->pluck('name')
            ];

        } elseif(in_array($field, ['current_state', 'permanent_state'])) {
            $returnArray = [
                'old_value'  => State::query()->where('id', $old_value)->pluck('name'),
                'new_value'  => State::query()->where('id', $new_value)->pluck('name')
            ];

        } elseif(in_array($field, ['current_city', 'permanent_city'])) {
            $returnArray = [
                'old_value' => City::query()->where('id', $old_value)->pluck('name'),
                'new_value' => City::query()->where('id', $new_value)->pluck('name')
            ];

        } else*/if(strstr( $field, 'date')) {
            $returnArray = [
                'old_value'  => ($old_value) ? date('d-m-Y', strtotime($old_value)) : '-',
                'new_value'  => ($new_value) ? date('d-m-Y', strtotime($new_value)) : '-'
            ];
        } elseif(in_array($field, ['right_to_work', 'die_diligence', 'compliance', 'skill']) ) {
            $returnArray = [
                'old_value'  => implode('<br> ', explode('~~~~~', $old_value)),
                'new_value'  => implode('<br> ', explode('~~~~~', $new_value))
            ];

        } else {
            $returnArray = [
                'old_value'  => $old_value,
                'new_value'  => $new_value
            ];

        }

        return $returnArray;
    }
}
