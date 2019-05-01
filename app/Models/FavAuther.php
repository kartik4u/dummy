<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class FavAuther extends Model
{

    protected $fillable = ['user_id','fav_user_id','created_at'];

    public $timestamps = false;   
    
    

    #Get user data
    public function getUser() {
        return $this->hasOne('App\User','id','fav_user_id');
    }

}
