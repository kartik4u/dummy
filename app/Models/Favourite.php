<?php

namespace App\Models;

use App\User;
use Config;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    public $timestamps = false;
    protected $fillable = ['favourite_by','user_id','type','story_id','created_at'];


       #Get user data
    public function getUser() {
        return $this->hasOne('App\User','id','Favourite_by');
    }


    #Get user data
    public function getStory() {
        return $this->hasMany('App\Models\Story','id','story_id');
    }

}
