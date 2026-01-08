<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\Payroll\PayrollWeekDate;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CreatePayrollWeekDateEntries extends Controller
{
    use JsonResponse;
    public function index() {
        try {
            $year = '2026';
            $lastDayOfYear = Carbon::create($year, 12, 31);
            $totalWeeks = $lastDayOfYear->isoWeek() == 1 ? 52 : 53;

            $weekNumber = 1;
            $startDate = Carbon::create($year, 1, 1)->startOfWeek(Carbon::SUNDAY);

            $weeksData = [];
            for ($i = 0; $i <= $totalWeeks; $i++) {

                $tuesday_payroll_start = $startDate->copy()->subDays(12);
                $wednesday_payroll_start = $tuesday_payroll_start->copy()->addDay();
                $thursday_payroll_start = $wednesday_payroll_start->copy()->addDay();
                $friday_payroll_start = $thursday_payroll_start->copy()->addDay();
                $saturday_payroll_start = $friday_payroll_start->copy()->addDay();
                $sunday_payroll_start = $saturday_payroll_start->copy()->addDay();
                $monday_payroll_start = $sunday_payroll_start->copy()->addDay();

                $weekData = [
                    'payroll_week_number' => $weekNumber,
                    'year' => $year,
                    'pay_date' => $startDate->copy()->addDays(5)->format('Y-m-d'),

                    'tuesday_payroll_start' => $tuesday_payroll_start->format('Y-m-d'),
                    'tuesday_payroll_end' => $tuesday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                    'wednesday_payroll_start' => $wednesday_payroll_start->format('Y-m-d'),
                    'wednesday_payroll_end' => $wednesday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                    'thursday_payroll_start' => $thursday_payroll_start->format('Y-m-d'),
                    'thursday_payroll_end' => $thursday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                    'friday_payroll_start' => $friday_payroll_start->format('Y-m-d'),
                    'friday_payroll_end' => $friday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                    'saturday_payroll_start' => $saturday_payroll_start->format('Y-m-d'),
                    'saturday_payroll_end' => $saturday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                    'sunday_payroll_start' => $sunday_payroll_start->format('Y-m-d'),
                    'sunday_payroll_end' => $sunday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                    'monday_payroll_start' => $monday_payroll_start->format('Y-m-d'),
                    'monday_payroll_end' => $monday_payroll_start->copy()->addDays(6)->format('Y-m-d'),
                ];

                $weeksData[] = $weekData;

                $startDate->addWeek();
                $weekNumber++;
            }

            PayrollWeekDate::query()->insert($weeksData);
            return self::responseWithSuccess('Payroll week date entry successfully created.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
