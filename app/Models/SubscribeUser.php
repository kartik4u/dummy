<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribeUser extends Model
{
    //

    public $timestamps = false;


    protected $fillable = ['user_id', 'plan_id', 'current_period_start',
         'current_period_end','canceled_at','cancel_at_period_end', 'subcribe_id', 'amount',
        'created_at', 'status'];

}
