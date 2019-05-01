<?php

namespace App\Models;

use App\User;
use Config;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = false;
    protected $fillable = ['commented_by','commented_to','comment','story_id', 'created_at'];


       #Get user data
    public function getUser() {
        return $this->hasOne('App\User','id','commented_by');
    }

     #Get story data
     public function getStory() {
        return $this->hasOne('App\Models\Story','id','story_id');
    }

}
