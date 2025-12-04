<?php


namespace App\Helper\Location;


use App\Models\Location\Country;

class CountryHelper
{
    public static function countryOption()
    {
        $country = Country::query()->get();

        $option = '<option value="">Select country</option>';
        if ($country) {
            foreach ($country as $row) {
                $option .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
            }
        }

        return $option;
    }
}
