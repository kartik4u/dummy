<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class UserSignupRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'user_type' => 'required|in:reader,writer',
            'username' => 'unique:users',
            'password_confirmation' => 'required',
            'password' => 'required|confirmed|min:6|max:15'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email',
            'email.unique' => 'Your email is already registered with us. Please login',
            'password.required' => 'Please enter password',
            'user_type.in' => 'Please select the valid type : writter or reader'
        ];
    }
    /**
     * [failedValidation [Overriding the event validator for custom error response]]
     * @param  Validator $validator [description]
     * @return [object][object of various validation errors]
     */
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
