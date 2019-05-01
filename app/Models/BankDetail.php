<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class BankDetail extends Model
{

    protected $fillable = ['user_id','card_holder_name','cvv','year','month','card_name','card_no','created_at'];

    public $timestamps = false;

}
