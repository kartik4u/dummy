<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class StoryGenre extends Model
{

    protected $fillable = ['user_id','genre_id','created_at'];

    public $timestamps = false;

    public function getGenre() {
        return $this->hasOne('App\Models\Genre', 'id', 'genre_id');
    }

    
}
