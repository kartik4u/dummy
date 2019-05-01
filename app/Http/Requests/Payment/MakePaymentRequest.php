<?php

namespace App\Http\Requests\payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MakePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
           return [
            'token' => 'required',
            
        ];
    }

    public function messages()
    {
        return [
            'token' => 'Token is required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $data_error = [];
        $error = $validator->errors()->all(); #if validation fail print error messages
        foreach ($error as $key => $errors):
            $data_error['status'] = 400;
            $data_error['message'] = $errors;
        endforeach;
        //write your bussiness logic here otherwise it will give same old JSON response
        throw new HttpResponseException(response()->json($data_error));

    }
}
