<?php

namespace App\Models;

use App\User;
use Config;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public $timestamps = false;
    protected $fillable = ['by_user_id','type','to_user_id', 'episode_id','rating','story_id','created_at'];

}
