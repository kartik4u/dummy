<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewProfile extends Model
{
    public $timestamps = false;
    protected $fillable = ['to_user_id', 'by_user_id', 'created_at'];

    
     #Get user data

     public function getUser() {
        return $this->hasOne('App\User', 'id', 'to_user_id');
    }

}
