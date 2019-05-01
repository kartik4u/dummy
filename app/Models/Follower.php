<?php

namespace App\Models;

use App\User;
use Config;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    public $timestamps = false;
    protected $fillable = ['followed_by','followed_to', 'created_at'];


       #Get user data
    public function getUser() {
        return $this->hasMany('App\User','id','followed_by');
    }

}
