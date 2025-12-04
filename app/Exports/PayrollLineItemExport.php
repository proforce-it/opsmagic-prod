<?php

namespace App\Exports;

use App\Models\Job\PayrollLineItem;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PayrollLineItemExport implements  FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $filterArray;

    public function __construct($filterArray) {
        $this->filterArray = $filterArray;
    }

    public function collection()
    {
        return PayrollLineItem::query()->where('site_id', $this->filterArray['site'])
            ->where('payroll_week', str_replace('_', '-', $this->filterArray['payroll_week']))
            ->with(['job_details', 'worker_details', 'worker_cost_center'])
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
            'Week start',
            'Week end',
            'Pay rate name',
            'Bonus type',
            'Charge rate',
            'Pay rate',
            'Day 1 hours',
            'Day 2 hours',
            'Day 3 hours',
            'Day 4 hours',
            'Day 5 hours',
            'Day 6 hours',
            'Day 7 hours',
            'Total hours',
            'Total charge',
            'Total pay'
        ];
    }

    public function map($payroll): array
    {
        return [
            ($payroll->job_details) ? ($payroll->job_details->client_details) ? $payroll->job_details->client_details->company_name : '' : '',
            ($payroll->job_details) ? ($payroll->job_details->site_details) ? $payroll->job_details->site_details->site_name : '' : '',
            ($payroll->worker_cost_center) ? implode(', ', $payroll->worker_cost_center->pluck('cost_center')->toArray()) : '',
            ($payroll->worker_details) ? $payroll->worker_details->worker_no : '',
            $payroll->worker_payroll_ref ?? '',
            ($payroll->worker_details) ? $payroll->worker_details->first_name : '',
            ($payroll->worker_details) ? $payroll->worker_details->last_name : '',
            ($payroll->job_details) ? $payroll->job_details->id : '',
            ($payroll->job_details) ? $payroll->job_details->name : '',
            $payroll->payroll_week ?? '',
            $this->filterArray['payroll_week_start'],
            $this->filterArray['payroll_week_end'],
            ($payroll->pay_rate_name != 'Basic pay') ? ($payroll->pay_rate_name != 'Overtime') ? 'BONUS' : 'WEEKLY_OT' : 'BASE_RATE',
            $payroll->bonus_type ?? '',
            ($payroll->charge_rate) ? number_format($payroll->charge_rate, 2) : '',
            ($payroll->pay_rate) ? number_format($payroll->pay_rate, 2) : '',
            ($payroll->day_1_hours) ? number_format($payroll->day_1_hours, 2) : '',
            ($payroll->day_2_hours) ? number_format($payroll->day_2_hours, 2) : '',
            ($payroll->day_3_hours) ? number_format($payroll->day_3_hours, 2) : '',
            ($payroll->day_4_hours) ? number_format($payroll->day_4_hours, 2) : '',
            ($payroll->day_5_hours) ? number_format($payroll->day_5_hours, 2) : '',
            ($payroll->day_6_hours) ? number_format($payroll->day_6_hours, 2) : '',
            ($payroll->day_7_hours) ? number_format($payroll->day_7_hours, 2) : '',
            ($payroll->total_hours) ? number_format($payroll->total_hours, 2) : '',
            ($payroll->total_charge) ? number_format($payroll->total_charge, 2) : '',
            ($payroll->total_pay) ? number_format($payroll->total_pay, 2) : '',
        ];
    }
}
