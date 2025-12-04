<?php

namespace App\Helper\PayRate;

use App\Models\Job\ExtraPayRateDay;
use App\Models\Job\TemporaryExtraPayRateDay;

class PayRateMapHelper
{
    public static function generatePayRateMapInsertArray($calendar_events) {
        $calendarData = json_decode($calendar_events, true);
        $insertData = [];

        foreach ($calendarData as $dayData) {
            $record = [];
            $record['day'] = $dayData['day'] ?? null;
            $record['default_pay_rate_id'] = $dayData['default_pay_rate_id'] ?? null;

            for ($i = 0; $i < 24; $i++) {
                $timeKey1 = str_pad($i * 100, 4, "0", STR_PAD_LEFT);
                $timeKey2 = str_pad($i * 100 + 30, 4, "0", STR_PAD_LEFT);

                $record[$timeKey1] = $dayData[$timeKey1] ?? 0;
                $record[$timeKey2] = $dayData[$timeKey2] ?? 0;
            }

            $record['event'] = json_encode($dayData['event'] ?? []);
            $insertData[] = $record;
        }

        return $insertData;
    }

    public static function getCalendarEventForPRM($id, $type) {
        $extraPayRateDays = ($type == 'current')
            ? ExtraPayRateDay::query()->select(['id', 'default_pay_rate_id', 'day', 'event'])->where('default_pay_rate_id', $id)->get()
            : TemporaryExtraPayRateDay::query()->select(['id', 'default_pay_rate_id', 'day', 'event'])->where('default_pay_rate_id', $id)->get();

        $dayToNumber = [
            'Sunday'    => 0,
            'Monday'    => 1,
            'Tuesday'   => 2,
            'Wednesday' => 3,
            'Thursday'  => 4,
            'Friday'    => 5,
            'Saturday'  => 6,
        ];

        $events = [];
        foreach ($extraPayRateDays as $eprd) {
            $dayNumber = $dayToNumber[$eprd['day']] ?? null;
            $eventItems = json_decode($eprd['event'], true);

            foreach ($eventItems as $item) {
                $events[] = [
                    'title' => $item['title'],
                    'daysOfWeek' => [$dayNumber],
                    'extra_prm_id' => (string)$item['extra_prm_id'],
                    'startTime' => $item['start'],
                    'endTime' => $item['end'],
                    'classNames' => ['rate-block'],
                    'backgroundColor' => $item['bgColor'],
                    'borderColor' => $item['bgColor'],
                    'textColor' => '#fff',
                    'editable' => true,
                    'selectable' => true,
                    'eventResizableFromStart' => true,
                    'eventStartEditable' => true,
                    'eventDurationEditable' => true,
                    'selectMirror' => true,
                    'extendedProps' => [
                        'isDefault' => false
                    ]
                ];
            }
        }
        return $events;
    }
}