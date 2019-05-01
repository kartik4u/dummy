<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class UserGenre extends Model
{

    protected $fillable = ['user_id','genre_id','created_at'];

    public $timestamps = false;


       #Get user data

    public function getMyGenre() {
        return $this->hasOne('App\Models\Genre', 'id', 'genre_id');
    }


    #Get get genre 

    public function getGenre() {
        return $this->hasOne('App\Models\Genre', 'id', 'genre_id');
    }

    

}
