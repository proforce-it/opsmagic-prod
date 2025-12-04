<?php

namespace App\Exports;

use App\Models\Bonus\Bonus;
use App\Models\Timesheet\Timesheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BonusExport implements  FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $filterArray;

    public function __construct($filterArray) {
        $this->filterArray = $filterArray;
    }

    public function collection()
    {
        return Bonus::query()->whereIn('job_id', $this->filterArray['job_ids'])
            ->where('week_number', $this->filterArray['week_number'])
            ->where('week_year_number', $this->filterArray['week_year_number'])
            ->with(['worker_details', 'job_details'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Client name',
            'Site name',
            'Cost centre',
            'Worker id',
            'Payroll ref',
            'First name',
            'Last name',
            'Job id',
            'Job name',
            'Week number',
            'Bonus type',
            'Bonus pay amount',
            'Bonus charge amount',
        ];
    }

    public function map($bonus): array
    {
        return [
            ($bonus->job_details) ? ($bonus->job_details->client_details) ? $bonus->job_details->client_details->company_name : '' : '',
            ($bonus->job_details) ? ($bonus->job_details->site_details) ? $bonus->job_details->site_details->site_name : '' : '',
            ($bonus->worker_details) ? ($bonus->worker_details->worker_cost_center) ? $bonus->worker_details->worker_cost_center->pluck('cost_center')->join(', ') : '' : '',
            ($bonus->worker_details) ? $bonus->worker_details->worker_no : '',
            ($bonus->worker_details) ? $bonus->worker_details->payroll_reference : '',
            ($bonus->worker_details) ? $bonus->worker_details->first_name : '',
            ($bonus->worker_details) ? $bonus->worker_details->last_name : '',
            ($bonus->job_details) ? $bonus->job_details->id : '',
            ($bonus->job_details) ? $bonus->job_details->name : '',
            $bonus->week_number.'-'.$bonus->week_year_number,
            ($bonus->bonus_type) ? str_replace('_', ' ', ucfirst($bonus->bonus_type)) : '',
            number_format($bonus->bonus_pay_amount, 2),
            number_format($bonus->bonus_charge_amount, 2)
        ];
    }
}
