<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Config;

class FavouriteCourse extends Model {

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'course_id', 'status', 'created_at', 'updated_at'
    ];

    // get user detail
    public function getUser() {
        return $this->hasOne('App\User','id','user_id');
    }
    
    // get course detail
    public function course() {
        return $this->hasOne('App\Course','id','course_id');
    }


}
