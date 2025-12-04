<?php

namespace App\Exports;

use App\Models\Timesheet\Timesheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TimesheetExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $filterArray;

    public function __construct($filterArray) {
        $this->filterArray = $filterArray;
    }

    public function collection()
    {
        return Timesheet::query()->whereIn('job_id', $this->filterArray['job_ids'])
            ->whereBetween('date', [$this->filterArray['payroll_week_start'], $this->filterArray['payroll_week_end']])
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
            'Date',
            'Hours',
        ];
    }

    public function map($timesheet): array
    {
        return [
            ($timesheet->job_details) ? ($timesheet->job_details->client_details) ? $timesheet->job_details->client_details->company_name : '' : '',
            ($timesheet->job_details) ? ($timesheet->job_details->site_details) ? $timesheet->job_details->site_details->site_name : '' : '',
            ($timesheet->worker_details) ? ($timesheet->worker_details->worker_cost_center) ? $timesheet->worker_details->worker_cost_center->pluck('cost_center')->join(', ') : '' : '',
            ($timesheet->worker_details) ? $timesheet->worker_details->worker_no : '',
            ($timesheet->worker_details) ? $timesheet->worker_details->payroll_reference : '',
            ($timesheet->worker_details) ? $timesheet->worker_details->first_name : '',
            ($timesheet->worker_details) ? $timesheet->worker_details->last_name : '',
            ($timesheet->job_details) ? $timesheet->job_details->id : '',
            ($timesheet->job_details) ? $timesheet->job_details->name : '',
            ($timesheet->date) ? date('d-m-Y', strtotime($timesheet->date)) : '',
            number_format($timesheet->hours_worked, 2)
        ];
    }
}
