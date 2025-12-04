<?php


namespace App\Helper\Location;


use App\Models\Location\City;

class CityHelper
{
    public static function cityOptionStateWise($id)
    {
        $city = City::query()->where('state_id', $id)->get();

        $option = '<option value="">Select city</option>';
        if ($city) {
            foreach ($city as $row) {
                $option .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
            }
        }

        return $option;
    }
}
