<?php

namespace App\Models;

use App\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;


class Story extends Model
{

    protected $fillable = ['synops','image','query_letter','query_letter','total_revenue','favourite_count','name','rating','chapters_count','description','duration','url','user_id','created_at','status','type'];

    public $timestamps = false;

    
       #Get saved story

    public function getSavedStory() {
        return $this->hasMany('App\Models\SavedStory', 'story_id', 'id');
    }

    public function getViewStory() {
        return $this->hasMany('App\Models\ViewedStory', 'story_id', 'id');
    }

    public function getStoryGenre() {
        return $this->hasMany('App\Models\StoryGenre', 'story_id', 'id');
    }

    public function myFav() {
        return $this->hasMany('App\Models\Favourite', 'story_id', 'id')->where('type',2);
    }


    public function getEpisodes() {
        return $this->hasMany('App\Models\Episode', 'story_id', 'id');
    }

     
       #Get user detail

    public function getUserDetail() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }


    // path of image
    public function getSynopsAttribute($value)
    {   
        $server_url = Config::get('variable.SERVER_URL');
        if (!empty($value) && file_exists(storage_path() . '/app/public/users/'.$this->user_id.'/'.$value)) {
            return $server_url . '/storage/users/'.$this->id.'/'.$value;
        } else {
            return false; //$server_url . '/storage/dummy.jpg';
        }
    }



    // path of url
    public function getUrlAttribute($value)
    {   
        $server_url = Config::get('variable.SERVER_URL');
        if (!empty($value) && file_exists(storage_path() . '/app/public/users/'.$this->user_id.'/'.$value)) {
            return $server_url . '/storage/users/'.$this->id.'/'.$value;
        } else {
            return $server_url . '/storage/dummy.jpg';
        }
    }

    public function getAvgAttribute($value)
    {   
        return $view_data = $this->get_view_story[0];
    }

    
    

}
