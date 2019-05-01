<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

    use Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $hidden = [
        'password'
    ];
    protected $fillable = [
        'role_id', 'slug','name', 'email', 'password', 'verification_code','forgot_password_code','status', 'auth_token', 'created_at', 'updated_at','last_logged','last_searched'
    ];
    
    // get user detail
    public function getProfile() {
        return $this->hasOne('App\UserDetail', 'user_id', 'id');
    }

    // get user detail
    public function userDetail() {
        return $this->hasOne('App\UserDetail');
    }
    
    // get course detail
    public function course() {
        return $this->hasMany('App\Course');
    }
    
    // get university users address
    public function address() {
        return $this->hasMany('App\UserAddress');
    }
    
    // get university first address
    public function singleAddress() {
        return $this->hasOne('App\UserAddress');
    }

    #Get last searched

    public function getLastSearchedAttribute($value) {        
        if(empty(!$value))      
        return  $php_timestamp_date = date("d F, Y", $value);
    }

    #Get last logged

    public function getLastLoggedAttribute($value) {  
        if(empty(!$value))      
        return  $php_timestamp_date = date("d F, Y", $value);
    }
    
    #Searched Analytics Data
    public function searchedAnalytics() {         
        return $this->hasMany('App\SearchAnalytic');
    }
    
    #favourite courses 
    public function favouriteCourses() {
        return $this->hasMany('App\FavouriteCourse');
    }
    
    #page logs 
    public function pageLogs() {
        return $this->hasMany('App\PageLog');
    }
    
}
