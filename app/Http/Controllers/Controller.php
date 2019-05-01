<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Config;
/**
     * @SWG\Swagger(
     *     schemes={"http"},
     *     host="server.localepisodic.com",
     *     basePath="/api",
     *     @SWG\Info(
     *         version="1.0.0",
     *         title="Episode Api's",
     *         description="Swagger creates human-readable documentation for your APIs.",
     *     )
     * )
     */
class Controller extends BaseController
{
    //
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

      /* @Check requried data send or not here */

      public function validatedata($validator) {
        $error = $validator->errors()->all(); #if validation fail print error messages
        $data_error = array();
        foreach ($error as $key => $errors):
            $data_error['status'] = 400;
            $data_error['description'] = $errors;            
        endforeach;
        return $data_error; #Return data in json
    }
}
