<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class AutherView extends Model
{

    protected $fillable = ['message','created_at','user_id'];

    public $timestamps = false;


    public function getUser() {
        return $this->hasOne('App\User', 'id', 'user_id')->select('id','name','profile_image');
    }


    # function for get date time
    // public function getCreatedAtAdminAttribute($value) {
    //     $value = date("m/d/Y", $value);
    //     return $value;
    // }

}
