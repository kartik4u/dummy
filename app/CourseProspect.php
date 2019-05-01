<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CourseProspect extends Authenticatable {

    use Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = [
        'course_id', 'name', 'status', 'created_at', 'updated_at'
    ];
}
