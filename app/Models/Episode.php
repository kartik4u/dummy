<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;



class Episode extends Model
{

    protected $fillable = ['revnue','story_id','view_count','name','share_count','rating','created_at','synops','status','revenue_full_read'];
    public $timestamps = false;


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

}
