<?php

namespace App\Interfaces;

use App\Http\Requests\Pages\ContactRequest;
use App\Http\Requests\Pages\SendPageMailRequest;
use Illuminate\Http\Request;

interface PageInterface
{
    public function getAboutus(Request $request);
    public function getPrivacyPolicy(Request $request);
    public function getDonation(Request $request);
    public function getTermsAndConditions(Request $request);
    public function contact(ContactRequest $request);
    public function sendPageMail(SendPageMailRequest $request);


}
