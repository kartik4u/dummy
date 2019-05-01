<?php

namespace App\Models;

use App\User;
use Config;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    public $timestamps = false;
    protected $fillable = ['story_id','user_id','created_at'];


       #Get user data
    public function getUser() {
        return $this->hasMany('App\User','id','user_id');
    }


    #Get user data
    public function getStory() {
        return $this->hasMany('App\Models\Story','id','story_id');
    }

}
