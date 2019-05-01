<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    public $timestamps = false;
    protected $fillable = ['challenge_id', 'sender_id', 'receiver_id', 'message', 'created_at', 'type', 'status', 'last','deleted'];

    public function senderData() {
        return $this->hasOne('App\User', 'id', 'sender_id')->select('id','name','email','profile_image');
    }

    public function receiverData() {
        return $this->hasOne('App\User', 'id', 'receiver_id')->select('id','name','email','profile_image');
    }

}
