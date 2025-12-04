<?php

namespace App\Http\Controllers\Clients;

use App\Helper\Job\JobHelper;
use App\Helper\PayRate\PayRateMapHelper;
use App\Http\Controllers\Controller;
use App\Models\Client\ClientJob;
use App\Models\Job\ClientJobPayRate;
use App\Models\Job\ExtraPayRateDay;
use App\Models\Job\ExtraPayRateMap;
use App\Models\Job\TemporaryExtraPayRateDay;
use App\Models\Job\TemporaryExtraPayRateMaps;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PayRateController extends Controller
{
    use JsonResponse;

    public function createFlatPayRateDetails(Request $request) {
        try {
            $params = $request->input();
            $rules = [
                'base_pay_rate_per_hour'    => 'required|numeric|min:0',
                'base_charge_rate_per_hour' => 'required|numeric|min:0',

                'overtime_pay_rate_per_hour'    => 'nullable|numeric|min:0|required_with:overtime_charge_rate_per_hour,overtime_paid_after',
                'overtime_charge_rate_per_hour' => 'nullable|numeric|min:0|required_with:overtime_pay_rate_per_hour,overtime_paid_after',
                //'overtime_paid_after'           => 'nullable|integer|min:0|required_with:overtime_pay_rate_per_hour,overtime_charge_rate_per_hour',
                'overtime_paid_after' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'required_with:overtime_pay_rate_per_hour,overtime_charge_rate_per_hour',
                    function ($attribute, $value, $fail) {
                        if (!is_numeric($value)) {
                            $fail('The overtime paid after must be a numeric value.');
                            return;
                        }

                        if (fmod($value * 10, 5) !== 0.0) {
                            $fail('The overtime paid after must be in 0.5 increments only (e.g., 0, 0.5, 1, 1.5, 2, ...).');
                        }
                    },
                ],

                'pay_rate_valid_from' => 'required|date',
            ];

            $validator = Validator::make($params, $rules);
            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $clientJob = ClientJob::query()->where('id', $params['pay_rate_job_id'])->with('pay_rate_multiple')->first();
            if (!$clientJob) {
                return self::responseWithError('Client job not found, please try again later.');
            }

            if ($clientJob->pay_rate_multiple->isEmpty()) {
                ClientJob::query()
                    ->where('id', $params['pay_rate_job_id'])
                    ->update(['pay_rate_type' => 'flat_rate']);
            }

            ClientJobPayRate::query()->create([
                'job_id' => $params['pay_rate_job_id'],

                'base_pay_rate' => $params['base_pay_rate_per_hour'],
                'base_charge_rate' => $params['base_charge_rate_per_hour'],

                'default_overtime_pay_rate' => $params['overtime_pay_rate_per_hour'],
                'default_overtime_charge_rate' => $params['overtime_charge_rate_per_hour'],

                'default_overtime_hours_threshold' => $params['overtime_paid_after'],
                'overtime_type' => $params['overtime_type'],

                'pay_rate_valid_from' => Carbon::parse($params['pay_rate_valid_from'])->format('Y-m-d'),
            ]);

            return self::responseWithSuccess('Pay rate successfully created.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function editFlatPayRateDetails(Request $request) {
        try {
            $params = $request->input();
            $rules = [
                'edit_base_pay_rate_per_hour'    => 'required|numeric|min:0',
                'edit_base_charge_rate_per_hour' => 'required|numeric|min:0',

                'edit_overtime_pay_rate_per_hour'    => 'nullable|numeric|min:0|required_with:edit_overtime_charge_rate_per_hour,edit_overtime_paid_after',
                'edit_overtime_charge_rate_per_hour' => 'nullable|numeric|min:0|required_with:edit_overtime_pay_rate_per_hour,edit_overtime_paid_after',
                //'edit_overtime_paid_after'           => 'nullable|integer|min:0|required_with:edit_overtime_pay_rate_per_hour,edit_overtime_charge_rate_per_hour',
                'edit_overtime_paid_after' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'required_with:edit_overtime_pay_rate_per_hour,edit_overtime_charge_rate_per_hour',
                    function ($attribute, $value, $fail) {
                        if (!is_numeric($value)) {
                            $fail('The overtime paid after must be a numeric value.');
                            return;
                        }

                        if (fmod($value * 10, 5) !== 0.0) {
                            $fail('The overtime paid after must be in 0.5 increments only (e.g., 0, 0.5, 1, 1.5, 2, ...).');
                        }
                    },
                ],
            ];

            $validator = Validator::make($params, $rules);
            if ($validator->errors()->messages()) {
                return self::validationError(collect($validator->errors()->messages())->map(function ($messageArray) {
                    return array_map(function ($message) {
                        return str_replace('edit', '', $message);
                    }, $messageArray);
                })->toArray());
            }

            if ($params['action_type'] == 'UpdateUpcomingPayRate') {
                ClientJobPayRate::query()->where('id', $params['flat_pay_rate_update_id'])->update([
                    'base_pay_rate' => $params['edit_base_pay_rate_per_hour'],
                    'base_charge_rate' => $params['edit_base_charge_rate_per_hour'],
                    'default_overtime_pay_rate' => $params['edit_overtime_pay_rate_per_hour'],
                    'default_overtime_charge_rate' => $params['edit_overtime_charge_rate_per_hour'],
                    'default_overtime_hours_threshold' => $params['edit_overtime_paid_after'],
                    'overtime_type' => $params['edit_overtime_type'],
                    'pay_rate_valid_from' => Carbon::parse($params['updated_pay_rate_valid_from'])->format('Y-m-d'),
                ]);
            } else {
                $validFrom = isset($params['updated_pay_rate_valid_from']) && !empty($params['updated_pay_rate_valid_from'])
                    ? Carbon::parse($params['updated_pay_rate_valid_from'])->startOfDay()
                    : null;

                if ($validFrom) {
                    /*--- Change status -> current to past when past date is selected ---*/
                    $ClientJobPayRate = ClientJobPayRate::query()->where('id', $params['flat_pay_rate_update_id'])->first();
                    $status = 'U';
                    if ($ClientJobPayRate && $validFrom->isPast()) {
                        $payRateValidFrom = Carbon::parse($ClientJobPayRate->pay_rate_valid_from);
                        if ($payRateValidFrom->isBefore($validFrom)) {
                            $status = 'C';

                            $pay_rate_valid_to = $validFrom->copy()->subDay()->format('Y-m-d');
                            ClientJobPayRate::query()->where('id', $params['flat_pay_rate_update_id'])
                                ->update([
                                    'status' => 'P',
                                    'pay_rate_valid_to' => $pay_rate_valid_to,
                                ]);
                        }
                    }

                    ClientJobPayRate::query()->create([
                        'job_id' => $params['edit_pay_rate_job_id'],
                        'base_pay_rate' => $params['edit_base_pay_rate_per_hour'],
                        'base_charge_rate' => $params['edit_base_charge_rate_per_hour'],
                        'default_overtime_pay_rate' => $params['edit_overtime_pay_rate_per_hour'],
                        'default_overtime_charge_rate' => $params['edit_overtime_charge_rate_per_hour'],
                        'default_overtime_hours_threshold' => $params['edit_overtime_paid_after'],
                        'overtime_type' => $params['edit_overtime_type'],
                        'pay_rate_valid_from' => $validFrom->format('Y-m-d'),
                        'status' => $status,
                    ]);
                } else {
                    ClientJobPayRate::query()->where('id', $params['flat_pay_rate_update_id'])
                        ->update([
                            'base_pay_rate'                 => $params['edit_base_pay_rate_per_hour'],
                            'base_charge_rate'             => $params['edit_base_charge_rate_per_hour'],
                            'default_overtime_pay_rate'    => $params['edit_overtime_pay_rate_per_hour'],
                            'default_overtime_charge_rate' => $params['edit_overtime_charge_rate_per_hour'],
                            'default_overtime_hours_threshold' => $params['edit_overtime_paid_after'],
                            'overtime_type'                => $params['edit_overtime_type'],
                        ]);
                }
            }

            return self::responseWithSuccess('Pay rate successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteFlatPayRateAction($id) {
        try {
            $ClientJobPayRate = ClientJobPayRate::query()->where('id', $id)->first();
            if (!$ClientJobPayRate) {
                return self::responseWithError('Client job pay rate not found please try again later.');
            }

            $ClientJobPayRate->delete();
            return self::responseWithSuccess('Upcoming pay rate successfully removed.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function createPayRateMapDetails(Request $request) {
        try {
            $params = $request->input();
            $rules = [
                'default_pay_rate_map_per_hour' => 'required|numeric|min:0',
                'default_charge_rate_map_per_hour' => 'required|numeric|min:0',

                'default_overtime_pay_rate_map_per_hour' => 'nullable|numeric|min:0|required_with:default_overtime_charge_rate_map_per_hour,map_overtime_paid_after',
                'default_overtime_charge_rate_map_per_hour' => 'nullable|numeric|min:0|required_with:default_overtime_pay_rate_map_per_hour,map_overtime_paid_after',
                //'map_overtime_paid_after' => 'nullable|integer|min:0|required_with:default_overtime_pay_rate_map_per_hour,default_overtime_charge_rate_map_per_hour'
                'map_overtime_paid_after' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'required_with:default_overtime_pay_rate_map_per_hour,default_overtime_charge_rate_map_per_hour',
                    function ($attribute, $value, $fail) {
                        if (!is_numeric($value)) {
                            $fail('The overtime paid after must be a numeric value.');
                            return;
                        }

                        if (fmod($value * 10, 5) !== 0.0) {
                            $fail('The overtime paid after must be in 0.5 increments only (e.g., 0, 0.5, 1, 1.5, 2, ...).');
                        }
                    },
                ],
            ];

            $validator = Validator::make($params, $rules);
            if ($validator->errors()->messages()) {
                return self::validationError(collect($validator->errors()->messages())->map(function ($messageArray) {
                    return array_map(function ($message) {
                        return str_replace('map', '', $message);
                    }, $messageArray);
                })->toArray());
            }

            $clientJob = ClientJob::query()->where('id', $params['pay_rate_map_job_id'])->with('pay_rate_multiple')->first();
            if (!$clientJob) {
                return self::responseWithError('Client job not found, please try again later.');
            }

            if ($clientJob->pay_rate_multiple->isEmpty()) {
                ClientJob::query()
                    ->where('id', $params['pay_rate_map_job_id'])
                    ->update(['pay_rate_type' => 'pay_rate_map']);
            }

            $ClientJobPayRate = ClientJobPayRate::query()->create([
                'job_id' => $params['pay_rate_map_job_id'],

                'base_pay_rate' => $params['default_pay_rate_map_per_hour'],
                'base_charge_rate' => $params['default_charge_rate_map_per_hour'],

                'default_overtime_pay_rate' => $params['default_overtime_pay_rate_map_per_hour'],
                'default_overtime_charge_rate' => $params['default_overtime_charge_rate_map_per_hour'],

                'default_overtime_hours_threshold' => $params['map_overtime_paid_after'],
                'overtime_type' => $params['map_overtime_type'],

                'pay_rate_valid_from' => JobHelper::validFromMinDate($params['pay_rate_map_job_id']) //Carbon::today()->format('Y-m-d'),
            ]);

            return self::responseWithSuccess('Pay rate map successfully created.', [
                'id' => $ClientJobPayRate['id']
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function payRateMapStepTwo($id) {
        $pay_rate_map = ClientJobPayRate::query()->where('id', $id)->first();
        $extra_pay_rate_map = ExtraPayRateMap::query()->where('default_pay_rate_id', $id)->get();

        $job = ClientJob::query()->where('id', $pay_rate_map['job_id'])
            ->with(['client_details', 'site_details'])
            ->first();

        $events = PayRateMapHelper::getCalendarEventForPRM($id, 'current');
        $type = 'current';
        $minDate = JobHelper::validFromMinDate($pay_rate_map['job_id']);
        return view('clients.view_pay_rate_map', compact('pay_rate_map', 'extra_pay_rate_map', 'job', 'events', 'type', 'minDate'));
    }

    public function storePrmCalendarEvent(Request $request) {
        DB::beginTransaction();
        try {
            $params = $request->input();
            $validator = Validator::make($params, [
                'pay_map_valid_from_date' => 'required',
            ]);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $insertArray = PayRateMapHelper::generatePayRateMapInsertArray($params['calendar_events']);
            ExtraPayRateDay::query()->where('default_pay_rate_id', $params['default_pay_rate_id'])->delete();
            ExtraPayRateDay::query()->insert($insertArray);

            ClientJobPayRate::query()->where('id', $params['default_pay_rate_id'])->update([
                'pay_rate_valid_from' => Carbon::parse($params['pay_map_valid_from_date'])->format('Y-m-d')
            ]);

            DB::commit();
            return self::responseWithSuccess('Calendar event successfully saved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function editDefaultPayRateMapAction(Request $request) {
        try {
            $params = $request->input();
            $rules = [
                'edit_default_pay_rate_map_per_hour' => 'required|numeric|min:0',
                'edit_default_charge_rate_map_per_hour' => 'required|numeric|min:0',

                'edit_default_overtime_pay_rate_map_per_hour' => 'nullable|numeric|min:0|required_with:edit_default_overtime_charge_rate_map_per_hour',
                'edit_default_overtime_charge_rate_map_per_hour' => 'nullable|numeric|min:0|required_with:edit_default_overtime_pay_rate_map_per_hour',
            ];

            $validator = Validator::make($params, $rules);
            if ($validator->errors()->messages()) {
                return self::validationError(collect($validator->errors()->messages())->map(function ($messageArray) {
                    return array_map(function ($message) {
                        return str_replace(['map', 'edit'], '', $message);
                    }, $messageArray);
                })->toArray());
            }

            $updateArray = [
                'base_pay_rate' => $params['edit_default_pay_rate_map_per_hour'],
                'base_charge_rate' => $params['edit_default_charge_rate_map_per_hour'],
                'default_overtime_pay_rate' => $params['edit_default_overtime_pay_rate_map_per_hour'],
                'default_overtime_charge_rate' => $params['edit_default_overtime_charge_rate_map_per_hour']
            ];

            ($params['edit_prm_type'] == 'current')
                ? ClientJobPayRate::query()->where('id', $params['edit_default_pay_rate_map_id'])->update($updateArray)
                : TemporaryExtraPayRateMaps::query()->where('id', $params['edit_default_pay_rate_map_id'])->update($updateArray);

            return self::responseWithSuccess('Default pay rate map successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function addExtraPayRateMapAction(Request $request) {
        try {
            $params = $request->input();
            $rules = [
                'add_extra_pay_rate_map_name' => 'required',
                'add_extra_pay_rate_map_short_code' => 'required|max:4',
                'add_extra_pay_rate_map_bg_color' => 'required',

                'add_extra_pay_rate_map_per_hour' => 'required|numeric|min:0',
                'add_extra_charge_rate_map_per_hour' => 'required|numeric|min:0',

                'add_extra_overtime_pay_rate_map_per_hour' => 'nullable|numeric|min:0|required_with:add_extra_overtime_charge_rate_map_per_hour',
                'add_extra_overtime_charge_rate_map_per_hour' => 'nullable|numeric|min:0|required_with:add_extra_overtime_pay_rate_map_per_hour',
            ];

            $validator = Validator::make($params, $rules);
            if ($validator->errors()->messages()) {
                return self::validationError(collect($validator->errors()->messages())->map(function ($messageArray) {
                    return array_map(function ($message) {
                        return str_replace(['add', 'extra'], '', $message);
                    }, $messageArray);
                })->toArray());
            }

            $array = [
                'job_id' => $params['add_extra_pay_rate_map_job_id'],
                'default_pay_rate_id' => $params['add_extra_pay_rate_map_default_pay_rate_id'],
                'pay_rate_name' => $params['add_extra_pay_rate_map_name'],
                'pay_rate_short_code' => $params['add_extra_pay_rate_map_short_code'],
                'color' => $params['add_extra_pay_rate_map_bg_color'],
                'base_pay_rate' => $params['add_extra_pay_rate_map_per_hour'],
                'base_charge_rate' => $params['add_extra_charge_rate_map_per_hour'],
                'default_overtime_pay_rate' => $params['add_extra_overtime_pay_rate_map_per_hour'],
                'default_overtime_charge_rate' => $params['add_extra_overtime_charge_rate_map_per_hour'],
            ];

            if($params['add_extra_pay_rate_map_type'] == 'current') {
                $created = ExtraPayRateMap::query()->create($array);
                $extra_pay_rate_details = ExtraPayRateMap::query()->where('id', $created['id'])->first();
            } else {
                $array['parent_id'] = $array['default_pay_rate_id'];
                unset($array['default_pay_rate_id']);
                $created = TemporaryExtraPayRateMaps::query()->create($array);
                $extra_pay_rate_details = TemporaryExtraPayRateMaps::query()->where('id', $created['id'])->first();
            }

            return self::responseWithSuccess('Extra pay rate map successfully created.', [
                'extra_pay_rate_details' => $extra_pay_rate_details
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getExtraPayRateMapDetails(Request $request) {
        try {
            $extraPayMap = ($request->input('type') == 'current')
                ? ExtraPayRateMap::query()->where('id', $request->input('id'))->first()
                : TemporaryExtraPayRateMaps::query()->where('id', $request->input('id'))->first();

            if (!$extraPayMap)
                self::responseWithError('Extra pay rate map details not found, please try again later.');

            return self::responseWithSuccess('Extra pay rate map details.', $extraPayMap);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function editExtraPayRateMapAction(Request $request) {
        try {
            $params = $request->input();
            $rules = [
                'edit_extra_pay_rate_map_name' => 'required',
                'edit_extra_pay_rate_map_short_code' => 'required|max:4',
                'edit_extra_pay_rate_map_bg_color' => 'required',

                'edit_extra_pay_rate_map_per_hour' => 'required|numeric|min:0',
                'edit_extra_charge_rate_map_per_hour' => 'required|numeric|min:0',

                'edit_extra_overtime_pay_rate_map_per_hour' => 'nullable|numeric|min:0|required_with:edit_extra_overtime_charge_rate_map_per_hour',
                'edit_extra_overtime_charge_rate_map_per_hour' => 'nullable|numeric|min:0|required_with:edit_extra_overtime_pay_rate_map_per_hour',
            ];

            $validator = Validator::make($params, $rules);
            if ($validator->errors()->messages()) {
                return self::validationError(collect($validator->errors()->messages())->map(function ($messageArray) {
                    return array_map(function ($message) {
                        return str_replace(['edit', 'extra'], '', $message);
                    }, $messageArray);
                })->toArray());
            }

            $array = [
                'pay_rate_name' => $params['edit_extra_pay_rate_map_name'],
                'pay_rate_short_code' => $params['edit_extra_pay_rate_map_short_code'],
                'color' => $params['edit_extra_pay_rate_map_bg_color'],
                'base_pay_rate' => $params['edit_extra_pay_rate_map_per_hour'],
                'base_charge_rate' => $params['edit_extra_charge_rate_map_per_hour'],
                'default_overtime_pay_rate' => $params['edit_extra_overtime_pay_rate_map_per_hour'],
                'default_overtime_charge_rate' => $params['edit_extra_overtime_charge_rate_map_per_hour'],
            ];

            if($params['edit_extra_pay_rate_map_type'] == 'current') {
                ExtraPayRateMap::query()->where('id', $params['edit_extra_pay_rate_map_id'])->update($array);
                $extra_pay_rate_details = ExtraPayRateMap::query()->where('id', $params['edit_extra_pay_rate_map_id'])->first();
            } else {
                TemporaryExtraPayRateMaps::query()->where('id', $params['edit_extra_pay_rate_map_id'])->update($array);
                $extra_pay_rate_details = TemporaryExtraPayRateMaps::query()->where('id', $params['edit_extra_pay_rate_map_id'])->first();
            }

            return self::responseWithSuccess('Extra pay rate map successfully updated.', [
                'extra_pay_rate_details' => $extra_pay_rate_details
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteExtraPayRateMapAction(Request $request) {
        DB::beginTransaction();
        try {
            $params = $request->input();
            $extraPayMap = ($params['type'] == 'current')
                ? ExtraPayRateMap::query()->where('id', $params['id'])->first()
                : TemporaryExtraPayRateMaps::query()->where('id', $params['id'])->first();

            if (!$extraPayMap) {
                throw new \Exception('Extra pay rate map details not found, please try again later.');
            }

            if ($params['type'] == 'current') {
                if ($extraPayMap['default_pay_rate_days_details']) {
                    foreach ($extraPayMap['default_pay_rate_days_details'] as $extra_pay_rate_day) {
                        $updateDaysArray = [];

                        // UPDATE EVENT
                        if ($extra_pay_rate_day['event']) {
                            $eventNode = json_decode($extra_pay_rate_day['event'], true);
                            $eventNode = array_values(array_filter($eventNode, function ($item) use ($params) {
                                return isset($item['extra_prm_id']) && $item['extra_prm_id'] != $params['id'];
                            }));
                            $updateDaysArray['event'] = json_encode($eventNode ?? [], true);
                        }

                        // UPDATE TIME COLUMN
                        for ($i = 0; $i < 24; $i++) {
                            $timeKey1 = str_pad($i * 100, 4, "0", STR_PAD_LEFT);
                            $timeKey2 = str_pad($i * 100 + 30, 4, "0", STR_PAD_LEFT);

                            $updateDaysArray[$timeKey1] = ($extra_pay_rate_day[$timeKey1] == $params['id']) ? 0 : $extra_pay_rate_day[$timeKey1];
                            $updateDaysArray[$timeKey2] = ($extra_pay_rate_day[$timeKey2] == $params['id']) ? 0 : $extra_pay_rate_day[$timeKey2];
                        }

                        // UPDATE QUERY
                        $updateDaysArray = array_filter(
                            $updateDaysArray,
                            fn($key) => is_string($key),
                            ARRAY_FILTER_USE_KEY
                        );
                        ExtraPayRateDay::query()->where('id', $extra_pay_rate_day['id'])->update($updateDaysArray);
                    }
                }
            }
            $extraPayMap->delete();

            DB::commit();
            return self::responseWithSuccess('Extra pay rate map successfully deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function createTemporaryUpcomingPrmEntry(Request $request) {
        DB::beginTransaction();
        try {
            $params = $request->input();
            $defaultPayRateMap = ClientJobPayRate::query()->where('id', $params['default_prm_id'])
                ->with(['extra_pay_rate_map_details', 'default_pay_rate_days_details'])
                ->first();

            if (!$defaultPayRateMap) {
                throw new \Exception('Default pay rate map not found please try again later.');
            }

            $entry = TemporaryExtraPayRateMaps::query()->create([
                'job_id' => $defaultPayRateMap['job_id'],
                'default_pay_rate_id' => $defaultPayRateMap['id'],
                'base_pay_rate' => $defaultPayRateMap['base_pay_rate'],
                'base_charge_rate' => $defaultPayRateMap['base_charge_rate'],
                'default_overtime_hours_threshold' => $defaultPayRateMap['default_overtime_hours_threshold'],
                'default_overtime_pay_rate' => $defaultPayRateMap['default_overtime_pay_rate'],
                'default_overtime_charge_rate' => $defaultPayRateMap['default_overtime_charge_rate'],
                'overtime_type' => $defaultPayRateMap['overtime_type'],
                'pay_rate_valid_from' => $defaultPayRateMap['pay_rate_valid_from']
            ]);

            $tempExtraPrmIdMap = [];
            foreach ($defaultPayRateMap['extra_pay_rate_map_details'] as $eprm_details) {
                $tempExtraPrm = TemporaryExtraPayRateMaps::query()->create([
                    'job_id' => $eprm_details['job_id'],
                    'parent_id' => $entry['id'],
                    'pay_rate_name' => $eprm_details['pay_rate_name'],
                    'pay_rate_short_code' => $eprm_details['pay_rate_short_code'],
                    'color' => $eprm_details['color'],
                    'base_pay_rate' => $eprm_details['base_pay_rate'],
                    'base_charge_rate' => $eprm_details['base_charge_rate'],
                    'default_overtime_pay_rate' => $eprm_details['default_overtime_pay_rate'],
                    'default_overtime_charge_rate' => $eprm_details['default_overtime_charge_rate'],
                ]);
                $tempExtraPrmIdMap[$eprm_details['id']] = $tempExtraPrm['id'];
            }

            if ($params['type'] == 'start_from_current_rate_pay_windows' && $defaultPayRateMap['extra_pay_rate_map_details']) {
                if ($defaultPayRateMap['default_pay_rate_days_details']) {
                    foreach ($defaultPayRateMap['default_pay_rate_days_details'] as $extra_pay_rate_day) {
                        $insertDaysArray = [
                            'default_pay_rate_id' => $entry['id'],
                            'day' => $extra_pay_rate_day['day'],
                        ];

                        if (!empty($extra_pay_rate_day['event'])) {
                            $events = json_decode($extra_pay_rate_day['event'], true);
                            foreach ($events as &$event) {
                                if (!empty($event['extra_prm_id']) && isset($tempExtraPrmIdMap[$event['extra_prm_id']])) {
                                    $event['extra_prm_id'] = $tempExtraPrmIdMap[$event['extra_prm_id']];
                                }
                            }
                            $insertDaysArray['event'] = json_encode($events, true);
                        }

                        for ($i = 0; $i < 24; $i++) {
                            $timeKey1 = str_pad($i * 100, 4, "0", STR_PAD_LEFT);
                            $timeKey2 = str_pad($i * 100 + 30, 4, "0", STR_PAD_LEFT);

                            $insertDaysArray[$timeKey1] = $tempExtraPrmIdMap[$extra_pay_rate_day[$timeKey1]] ?? 0;
                            $insertDaysArray[$timeKey2] = $tempExtraPrmIdMap[$extra_pay_rate_day[$timeKey2]] ?? 0;
                        }
                        TemporaryExtraPayRateDay::query()->create($insertDaysArray);
                    }
                }
            }

            DB::commit();
            return self::responseWithSuccess('Temporary Pay rate map entry successfully created.', [
                'tmp_prm_id' => $entry['id']
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function createUpcomingPayRateMap($id) {
        $pay_rate_map = TemporaryExtraPayRateMaps::query()->where('id', $id)->first();
        $extra_pay_rate_map = TemporaryExtraPayRateMaps::query()->where('parent_id', $id)->get();

        $job = ClientJob::query()->where('id', $pay_rate_map['job_id'])
            ->with(['client_details', 'site_details'])
            ->first();

        $events = PayRateMapHelper::getCalendarEventForPRM($id, 'temp_upcoming');
        $type = 'temp_upcoming';
        $minDate = JobHelper::validFromMinDate($pay_rate_map['job_id']);
        return view('clients.update_pay_rate_blank_map', compact('pay_rate_map', 'extra_pay_rate_map', 'job', 'events', 'type', 'minDate'));
    }

    public function storeUpcomingPrmCalendarEvent(Request $request) {
        DB::beginTransaction();
        try {
            $params = $request->input();
            $validator = Validator::make($params, [
                'pay_map_valid_from_date' => 'required',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $temp_default_prm = TemporaryExtraPayRateMaps::query()->where('id', $params['default_pay_rate_id'])->first();
            if (!$temp_default_prm) {
                return self::responseWithError('Upcoming default pay rate map details not found, please try again later.');
            }

            $temp_extra_prm = TemporaryExtraPayRateMaps::query()->where('parent_id', $params['default_pay_rate_id'])->get();
            if ($temp_extra_prm->isEmpty()) {
                return self::responseWithError('Upcoming extra pay rate map details not found, please try again later.');
            }

            $validFrom = Carbon::parse($params['pay_map_valid_from_date']);
            $ClientJobPayRate = ClientJobPayRate::query()->where('id', $temp_default_prm['default_pay_rate_id'])->first();
            $status = 'U';
            if ($ClientJobPayRate && $validFrom->isPast()) {
                $payRateValidFrom = Carbon::parse($ClientJobPayRate->pay_rate_valid_from);
                if ($payRateValidFrom->isBefore($validFrom)) {
                    $status = 'C';

                    $pay_rate_valid_to = $validFrom->copy()->subDay()->format('Y-m-d');
                    ClientJobPayRate::query()->where('id', $temp_default_prm['default_pay_rate_id'])
                        ->update([
                            'status' => 'P',
                            'pay_rate_valid_to' => $pay_rate_valid_to,
                        ]);
                }
            }

            $currentDefaultPrm = ClientJobPayRate::query()->create([
                'job_id' => $temp_default_prm['job_id'],
                'base_pay_rate' => $temp_default_prm['base_pay_rate'],
                'base_charge_rate' => $temp_default_prm['base_charge_rate'],
                'default_overtime_pay_rate' => $temp_default_prm['default_overtime_pay_rate'],
                'default_overtime_charge_rate' => $temp_default_prm['default_overtime_charge_rate'],
                'default_overtime_hours_threshold' => $temp_default_prm['default_overtime_hours_threshold'],
                'overtime_type' => $temp_default_prm['overtime_type'],
                'pay_rate_valid_from' => $validFrom->format('Y-m-d'),
                'status' => $status
            ]);

            $extraPrmIdMap = [];
            foreach ($temp_extra_prm as $tempexprm) {
                $currentExtraPrm = ExtraPayRateMap::query()->create([
                    'job_id' => $tempexprm['job_id'],
                    'default_pay_rate_id' => $currentDefaultPrm['id'],
                    'pay_rate_name' => $tempexprm['pay_rate_name'],
                    'pay_rate_short_code' => $tempexprm['pay_rate_short_code'],
                    'color' => $tempexprm['color'],
                    'base_pay_rate' => $tempexprm['base_pay_rate'],
                    'base_charge_rate' => $tempexprm['base_charge_rate'],
                    'default_overtime_pay_rate' => $tempexprm['default_overtime_pay_rate'],
                    'default_overtime_charge_rate' => $tempexprm['default_overtime_charge_rate'],
                ]);
                $extraPrmIdMap[$tempexprm['id']] = $currentExtraPrm['id'];
            }

            $calendarEvents = json_decode($params['calendar_events'], true);
            foreach ($calendarEvents as $key => $dayEvent) {

                for ($i = 0; $i < 24; $i++) {
                    $timeKey1 = str_pad($i * 100, 4, "0", STR_PAD_LEFT);
                    $timeKey2 = str_pad($i * 100 + 30, 4, "0", STR_PAD_LEFT);

                    $calendarEvents[$key][$timeKey1] = (isset($dayEvent[$timeKey1]))
                        ? ($extraPrmIdMap[$dayEvent[$timeKey1]] ?? 0)
                        : 0;

                    $calendarEvents[$key][$timeKey2] = (isset($dayEvent[$timeKey2]))
                        ? ($extraPrmIdMap[$dayEvent[$timeKey2]] ?? 0)
                        : 0;
                }

                $calendarEvents[$key]['default_pay_rate_id'] = $currentDefaultPrm['id'];
                if (!empty($dayEvent['event'])) {
                    $events = $dayEvent['event'];
                    foreach ($events as &$event) {
                        if (!empty($event['extra_prm_id']) && isset($extraPrmIdMap[$event['extra_prm_id']])) {
                            $event['extra_prm_id'] = $extraPrmIdMap[$event['extra_prm_id']];
                        }
                    }
                    $calendarEvents[$key]['event'] = $events;
                }
            }

            $insertArray = PayRateMapHelper::generatePayRateMapInsertArray(json_encode($calendarEvents, true));
            ExtraPayRateDay::query()->insert($insertArray);

            TemporaryExtraPayRateMaps::query()->where('id', $params['default_pay_rate_id'])->delete();
            TemporaryExtraPayRateMaps::query()->where('parent_id', $params['default_pay_rate_id'])->delete();
            TemporaryExtraPayRateDay::query()->where('default_pay_rate_id', $params['default_pay_rate_id'])->delete();

            DB::commit();
            return self::responseWithSuccess('Calendar event successfully saved.', [
                'default_prm_id' => $currentDefaultPrm['id'],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteUpcomingPayRateMapAction($id) {
        DB::beginTransaction();
        try {
            ClientJobPayRate::query()->where('id', $id)->delete();
            ExtraPayRateMap::query()->where('default_pay_rate_id', $id)->delete();
            ExtraPayRateDay::query()->where('default_pay_rate_id', $id)->delete();

            DB::commit();
            return self::responseWithSuccess('Upcoming pay rate successfully deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function payRateReadOnlyMap($id){
        $pay_rate_map = ClientJobPayRate::query()->where('id', $id)->first();
        $extra_pay_rate_map = ExtraPayRateMap::query()->where('default_pay_rate_id', $id)->get();
        $job = ClientJob::query()->where('id', $pay_rate_map['job_id'])->with(['client_details', 'site_details'])->first();
        $events = PayRateMapHelper::getCalendarEventForPRM($id, 'current');
        return view('clients.prm_read_only_pay_map', compact('pay_rate_map', 'extra_pay_rate_map', 'job', 'events'));
    }
}
