<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\WorkerPayrollReference;
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

            if ($totalColumns !== 5) {
                return self::validationError(['payroll_reference_file' => ['uploaded file does not match selected payroll reference file format. Please check and try again']]);
            }

            while (($row = fgetcsv($ReadFile)) !== false) {
                $worker = Worker::query()->select('id', 'worker_no', 'first_name', 'last_name', 'date_of_birth')
                    ->where('first_name', $row[1])
                    ->where('last_name', $row[2])
                    ->where('date_of_birth', Carbon::createFromFormat('d/m/Y', trim($row[3]))->format('Y-m-d'))
                    ->first();

                $added = 'Y';
                $error_message = '';

                if (!$worker) {
                    $added = 'N';
                    $error_message = 'No match found';
                }

                $rowReport = [
                    $worker?->worker_no ?? '',
                    $worker?->first_name ?? $row[1],
                    $worker?->last_name ?? $row[2],
                    $worker ? Carbon::parse($worker->date_of_birth)->format('d-m-Y') : Carbon::createFromFormat('d/m/Y', trim($row[3]))->format('d-m-Y'),
                    $row[4] ?? '',
                    $added,
                    $error_message
                ];

                if ($worker && $added === 'Y') {
                    try {
                        $this->processRow([
                            $worker->id,
                            $row[4],
                        ]);
                    } catch (\Exception $e) {
                        $rowReport[5] = 'N';
                        $rowReport[6] = $e->getMessage();
                    }
                }

                $reportArray[] = $rowReport;
            }
            fclose($ReadFile);

            return self::responseWithSuccess('The payroll reference numbers uploading process has been completed.', [
                'reportArray'   => array_merge([['Worker ID', 'First name', 'Last name', 'DOB', 'Payroll reference', 'Created', 'Error']], $reportArray),
                'table'         => $this->tableView($reportArray)
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function processRow($row) {
        $workerID = $row[0];
        $payrollReferenceNumber = $row[1];

        if (empty($payrollReferenceNumber)) {
            throw new \Exception('Payroll reference number is blank');
        }

        $hasActivePayroll = WorkerPayrollReference::query()
            ->where('worker_id', $workerID)
            ->whereNull('expires_on')
            ->exists();

        if ($hasActivePayroll) {
            throw new \Exception('Associate already has an active payroll number');
        }

        WorkerPayrollReference::query()->create([
            'worker_id'          => $workerID,
            'payroll_reference'  => $payrollReferenceNumber,
        ]);
    }

    private function tableView($reportArray) {
        $table = '<table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark" id="payroll_reference_number_datatable">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Worker ID</th>
                            <th>Worker name</th>
                            <th>DOB</th>
                            <th>Payroll REF.</th>
                            <th>Added</th>
                            <th>Error message</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">';
        if ($reportArray) {
            foreach ($reportArray as $reportRow) {
                $textDanger = ($reportRow[6]) ? 'text-danger' : '';
                $table .= '<tr>
                                <td>'.$reportRow[0].'</td>
                                <td>'.$reportRow[1].' '.$reportRow[2].'</td>
                                <td>'.$reportRow[3].'</td>
                                <td>'.$reportRow[4].'</td>
                                <td class="'.$textDanger.'">'.$reportRow[5].'</td>
                                <td class="'.$textDanger.'">'.$reportRow[6].'</td>
                          </tr>';
            }
        }
        $table .= '</tbody></table>';

        return $table;
    }
}
