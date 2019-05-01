<?php
namespace App\Http\Requests\Admin\Role;

use App\Http\Requests\Request;
use Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRolesRequest extends FormRequest
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
            'role_id' => 'required|numeric|exists:roles,id',
            'name' => 'required|unique:roles,name,' . $this->role_id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Permission Name is required',
            'name.unique' => 'Role name is alredy exists.',
            'permissions.exists' => 'Permission name does not exists.',
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
        throw new HttpResponseException(response()->json($data_error, 400));
    }
}
