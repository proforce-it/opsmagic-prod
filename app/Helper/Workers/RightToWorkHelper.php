<?php

namespace App\Helper\Workers;

use Carbon\Carbon;

class RightToWorkHelper
{
    public static function getLatestDate($array)
    {
        $latestRTWExpiryDate = '';
        $latestDate = collect($array)->filter(function ($item) {
                return !is_null($item['end_date']);
            })->sortByDesc('end_date')->first();

        if ($latestDate) {
            $latestRTWExpiryDate = $latestDate['end_date'];
        }

        return $latestRTWExpiryDate;
    }

    public static function getLatestStartDate($array)
    {
        $latestRTWStartDate = '';
        $latestDate = collect($array)->filter(function ($item) {
            return !is_null($item['start_date']);
        })->sortByDesc('start_date')->first();

        if ($latestDate) {
            $latestRTWStartDate = $latestDate['start_date'];
        }

        return $latestRTWStartDate;
    }

    public static function getIdDocumentLatestDate($array)
    {
        $latestIdDocumentExpiryDate = '';
        $latestDate = collect($array)->filter(function ($item) {
            return !is_null($item['expiry_date']);
        })->sortByDesc('expiry_date')->first();

        if ($latestDate) {
            $latestIdDocumentExpiryDate = $latestDate['expiry_date'];
        }

        return $latestIdDocumentExpiryDate;
    }
}