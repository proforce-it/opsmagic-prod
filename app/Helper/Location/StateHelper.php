<?php


namespace App\Helper\Location;


use App\Models\Location\State;

class StateHelper
{
    public static function stateOptionCountryWise($id)
    {
        $state = State::query()->where('country_id', $id)->get();

        $option = '<option value="">Select state</option>';
        if ($state) {
            foreach ($state as $row) {
                $option .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
            }
        }

        return $option;
    }
}
