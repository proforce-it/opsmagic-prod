<?php

namespace App\Http\Controllers\Location;

use App\Helper\Location\CityHelper;
use App\Helper\Location\StateHelper;
use App\Http\Controllers\Controller;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;

class CountryStateCityController extends Controller
{
    use JsonResponse;
    public function getStateCityOption(Request $request) {
        try {
            return self::responseWithSuccess('State - city options', [
                'options' => (($request->input('type') == 'country'))
                                ? StateHelper::stateOptionCountryWise($request->input('id'))
                                : CityHelper::cityOptionStateWise($request->input('id'))
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
