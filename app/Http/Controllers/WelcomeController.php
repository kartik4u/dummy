<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
//use Input;
use Illuminate\Support\Facades\Input;
use Excel;
use DB;
use Config;
use QrCode;
use Response;
use App\Attribute;
use App\Currency;
use App\AttributeOption;
use App\Product;
use App\ProductDetail;
use App\ProductAttributeOption;

class WelcomeController extends Controller {

    public function importExport() {
        return view('welcome');
    }
}
