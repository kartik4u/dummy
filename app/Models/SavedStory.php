<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class SavedStory extends Model
{

    protected $fillable = ['story_id','user_id','created_at'];

    public $timestamps = false;

}
