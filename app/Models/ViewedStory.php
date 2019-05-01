<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class ViewedStory extends Model
{

    protected $fillable = ['story_id','user_id','episode_id','created_at','type','is_full_watched','updated_at'];

    public $timestamps = false;


    public function getEpisodeView() {
        return $this->hasMany('App\Models\ViewedStory', 'story_id', 'story_id')->where('type',2);
    }

}
