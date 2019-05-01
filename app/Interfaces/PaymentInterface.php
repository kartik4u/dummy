<?php

namespace App\Interfaces;

use App\Http\Requests\Payment\MakePaymentRequest;
use Illuminate\Http\Request;

interface PaymentInterface
{
    public function generateToken(Request $request);
    public function subcribePayment(MakePaymentRequest $request);
}


// use App\Http\Requests\payment\MakePaymentRequest;

// interface paymentInterface
// {
//     public function generateToken(Request $request);
//     public function subcribePayment(MakePaymentRequest $request);