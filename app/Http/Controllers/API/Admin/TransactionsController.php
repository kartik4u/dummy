<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\AdminTransactionsInterface;
use App\Models\Transaction;
use Config;
use Illuminate\Http\Request;
use Response;

class TransactionsController extends Controller implements AdminTransactionsInterface
{

     /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/getAllTransactions",
     *   summary="get all Transactions",
     *   produces={"application/json"},
     *   tags={"ADMIN TRANSACTION APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */

     /*
     * Function : function to get all Transactions
     * Input:
     * Output: success, error
     */
    public function getAllTransactions(Request $request)
    {
        $requested_data = $request->all();
        $response = Transaction::orderby('updated_at', 'desc')
            ->paginate(config('variable.page_per_record'));
        if ($response) {
            $data = \Config::get('admin_success.record_found');
            $data['data'] = $response;
        } else {
            $data = \Config::get('admin_error.no_record_found');
        }
        return Response::json($data);
    }



}
