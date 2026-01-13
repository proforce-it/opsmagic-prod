<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NewStarterExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $workers;

    public function __construct($workers)
    {
        $this->workers = $workers; // Pass array of workers with first_working_date and worker_details
    }

    public function collection()
    {
        return collect($this->workers);
    }

    public function headings(): array
    {
        return [
            'worker_id',
            'title',
            'first_name',
            'last_name',
            'gender',
            'date_of_birth',
            'ethnicity',
            'email',
            'mobile',
            'cost_centre',
            'ni_number',
            'marital_status',
            'id_doc_number',
            'id_expiry_date',
            'rtw_type',
            'rtw_reference_number',
            'rtw_expiry_date',
            'uk_address_1',
            'uk_address_2',
            'uk_address_city',
            'uk_address_county',
            'uk_address_postcode',
            'next_of_kin_first_name',
            'next_of_kin_last_name',
            'next_of_kin_relationship',
            'next_of_kin_email',
            'next_of_kin_phone',
            'start_date'
        ];
    }

    public function map($worker): array
    {
        $details = $worker['worker_details'];

        $rtwNumber = '';
        if (isset($details['latest_end_date_rights_to_work_details'])) {
            $rtw = $details['latest_end_date_rights_to_work_details'];
            $rtwNumber = ($rtw['right_to_work_type'] == 'UK Citizen')
                ? $rtw['uk_id_document_number']
                : $rtw['reference_number'];
        }

        return [
            $details['worker_no'] ?? '',
            $details['title'] ?? '',
            $details['first_name'] ?? '',
            $details['last_name'] ?? '',
            isset($details['gender'])
                ? strtoupper(substr($details['gender'], 0, 1))
                : '',
            isset($details['date_of_birth'])
                ? date('d/m/Y', strtotime($details['date_of_birth']))
                : '',
            isset($details['nationality_details'])
                ? $details['nationality_details']['opera_ethnic_code']
                : '',
            $details['email_address'] ?? '',
            $details['mobile_number'] ?? '',
            isset($details['worker_cost_centres_with_name'])
                ? collect($details['worker_cost_centres_with_name'])->pluck('cost_center_short_code')->join(', ')
                : '',
            $details['national_insurance_number'] ?? '',
            isset($details['marital_status'])
                ? strtoupper(substr($details['marital_status'], 0, 1))
                : '',
            isset($details['id_documents'][0])
                ? $details['id_documents'][0]['document_no']
                : '',
            isset($details['id_documents'][0])
                ? Carbon::parse($details['id_documents'][0]['expiry_date'])->format('d/m/Y')
                : '',
            $details['latest_end_date_rights_to_work_details']['right_to_work_type'] ?? '',
            $rtwNumber,
            $details['latest_end_date_rights_to_work_details']['end_date'] ?? '2199-12-31',
            $details['current_address_line_one'] ?? '',
            $details['current_address_line_two'] ?? '',
            $details['current_city'] ?? '',
            $details['current_state'] ?? '',
            $details['current_post_code'] ?? '',
            $details['next_of_kin_first_name'] ?? '',
            $details['next_of_kin_last_name'] ?? '',
            $details['next_of_kin_relationship'] ?? '',
            $details['next_of_kin_email'] ?? '',
            $details['next_of_kin_mobile'] ?? '',
            isset($worker['first_working_date'])
                ? Carbon::parse($worker['first_working_date'])->format('d/m/Y')
                : ''
        ];
    }
}
