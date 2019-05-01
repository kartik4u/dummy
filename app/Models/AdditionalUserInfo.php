<?php

namespace App\Models;
use App\User;
use Config;

use Illuminate\Database\Eloquent\Model;

class AdditionalUserInfo extends Model
{

    public $timestamps = false;
    protected $fillable = [ 'what_do_you_read','user_id', 'read_time', 'where_do_you_read', 'created_at'];

}
