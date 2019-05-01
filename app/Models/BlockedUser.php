<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class BlockedUser extends Model
{

    protected $fillable = ['blocked_by_user_id','blocked_user_id','created_at'];

    public $timestamps = false;


  #Get user data

    public function getSingleUser() {
        return $this->hasOne('App\User', 'id', 'blocked_by_user_id');
    }

}
