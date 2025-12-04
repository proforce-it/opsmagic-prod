<?php

namespace App\Http\Controllers\TimesheetAndBonus;

use App\Http\Controllers\Controller;
use App\Models\Bonus\Bonus;
use App\Models\Client\Client;
use App\Models\Client\Site;
use App\Models\Payroll\PayrollWeekDate;
use App\Models\Timesheet\Timesheet;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;

class TimesheetAndBonusController extends Controller
{
    use JsonResponse;
    public function index(Request $request) {
        $selectedData = [];

        if ($request->get('filtered')) {
            $array = explode('.', $request->get('filtered'));
            $selectedData = [
                'client_id' => $array[0],
                'site_id' => $array[1],
                'payroll_week' => $array[2],
                'sites' => Site::query()->where('client_id', $array[0])->get(),
            ];
        }

        $client = Client::query()->orderBy('company_name')->get();
        $payroll_week_number = PayrollWeekDate::query()->get();
        return view('timesheet_and_bonus.dis_timesheet_and_bonus', compact('client', 'payroll_week_number', 'selectedData'));
    }

    public function deleteIgnoredEntry(Request $request) {
        try {
            $params = $request->input();

            $query = ($params['type'] == 'timesheet')
                ? Timesheet::query()
                : Bonus::query();

            $entries = $query->whereIn('id', explode(',', $params['ids']));
            if (!$entries->get())
                throw new \Exception($params['type'].' entry not found, please try again later.');

            $entries->delete();
            return self::responseWithSuccess($params['type'].' entry successfully deleted.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
