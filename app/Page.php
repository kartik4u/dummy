<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = ['slug', 'title', 'content','version','status', 'created_at', 'updated_at'];
    
    #Get created at date
    public function getCreatedAtAttribute($value) {   
        return date("d F, Y h:i", $value);
    }
}
