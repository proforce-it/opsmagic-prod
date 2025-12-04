<?php

namespace App\Http\Requests;

use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    use JsonResponse;
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'company_name'    => 'required',
            'company_number'     => 'required',
            'vat_number' => 'required|digits:10',
            'address_line_one' => 'required|email',
            'city_town'      => 'required',
            'county'  => 'required',
            'postcode' => 'required',
            'company_phone' => 'required',
            'company_email' => 'required',
            'company_category' => 'required',
            'contact_info_name' => 'required',
            'contact_info_surname' => 'required',
            'contact_info_email' => 'required',
            'contact_info_phone_number' => 'required',
            'contact_info_job_title' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(self::validationError($validator->getMessageBag()), 422);
    }
}
