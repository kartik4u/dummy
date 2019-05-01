<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;
    protected $fillable = ['sender_id', 'receiver_id', 'type', 'story_id' ,'created_at','episode_id'];
    


    //users
    public function sender() {
        return $this->hasOne('App\User', 'id', 'sender_id');
    }

    //users
    public function receiver() {
        return $this->hasOne('App\User', 'id', 'receiver_id');
    }

    public function story() {
        return $this->hasOne('App\Models\Story', 'id', 'story_id');
    }

    


     # function for make message

   public function getMsgAttribute($value) {
    if($value == 1)    #new story fav user
    {
      $name = isset($this->sender->name)? $this->sender->name: 'User';
      $value = $name.' has published a new story,'.$this->story->name;
      return $value;
    }
    if($value == 2)    # reading story
    {
        $name = isset($this->sender->name)? $this->sender->name: 'User';
        $value = $name.' has published a new episode for story,'.$this->story->name;
        return $value;
    }
    if($value == 3)    #  fav. stroy chapter
    {
        $name = isset($this->sender->name)? $this->sender->name: 'User';
        $value = $name.' has published a new episode,'.$this->story->name;
        return $value;
    }
     if($value == 4)    # commment
    {
        $name = isset($this->sender->name)? $this->sender->name: 'User';
        $value = $name.' has commented on story,'.$this->story->name;
        return $value;
    }

    if($value == 5)    # message
    {
        $name = isset($this->sender->name)? $this->sender->name: 'User';
        $value = $name.' has send you a message.';
        return $value;
    }
}
    
}
