<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommingSoon extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'user_id', 'created_at'];


    public function getUser() {
        return $this->hasOne('App\User', 'id', 'user_id')->select('id','name');
    }

}
