<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayrollReferenceUploaderController extends Controller
{
    use JsonResponse;
    public function index() {
        return view('payroll.payroll_reference_uploader');
    }

    public function uploadPayrollReferenceNumber(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'payroll_reference_file' => 'required|mimes:csv,txt'
            ]);
            if($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            /*--- begin checkLead ---*/
            $fileExtension = $request->file('payroll_reference_file')->getClientOriginalExtension();
            if($fileExtension !== 'csv') {
                return self::validationError([
                    'payroll_reference_file' => ['Invalid file format - '.$fileExtension]
                ]);
            }

            $reportArray = [];
            $ReadFile = fopen($request->file('payroll_reference_file'), 'r');
            $headerRow = fgetcsv($ReadFile);
            $totalColumns = count($headerRow);

            if ($totalColumns == 4) {
                return self::validationError(['payroll_reference_file' => ['uploaded file does not match selected payroll reference file format. Please check and try again']]);
            }

            while (($row = fgetcsv($ReadFile)) !== false) {
                $worker = Worker::query()->select('id', 'worker_no', 'first_name', 'middle_name', 'last_name')
                    ->where('first_name', $row[1])
                    ->where('last_name', $row[2])
                    ->where('date_of_birth', Carbon::parse($row[3])->format('Y-m-d'))
                    ->first();

                if (!$worker) {

                }

                $rowReport = [
                    $worker['worker_no'],
                    $worker['first_name'],
                    $worker['last_name'],
                    Carbon::parse($worker['date_of_birth'])->format('d-m-Y'),
                    $row[4],
                    'Y',
                    ''
                ];

                try {
                    $array = [
                        $worker['id'],
                        $worker['worker_no'],
                        $row[4], // payroll reference number
                    ];

                    $this->processRow($array);
                } catch (\Exception $e) {
                    $rowReport[5] = 'N';
                    $rowReport[6] = $e->getMessage();
                }

                $reportArray[] = $rowReport;
            }
            fclose($ReadFile);

            return self::responseWithSuccess('The timesheet uploading process has been completed.', [
                'reportArray'   => array_merge([['Worker ID', 'First name', 'Last name', 'DOB', 'Payroll reference', 'Created', 'Error']], $reportArray),
                'table'         => $this->tableView($reportArray)
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function processRow($row) {

    }

    private function tableView($reportArray) {

    }
}
