<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserAddress extends Authenticatable {

    use Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = [
        'user_id', 'campus_name', 'postal_code', 'city', 'country','latitude','longitude', 'address','address2','status', 'created_at', 'updated_at'
    ];
    
    #get user detail
    public function user(){
        return $this->belongsTo('App\User');
    }
}
