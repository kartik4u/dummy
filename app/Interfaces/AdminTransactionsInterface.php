<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface AdminTransactionsInterface {

    public function getAllTransactions(Request $request);
}   

