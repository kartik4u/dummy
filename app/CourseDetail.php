<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CourseDetail extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = [
        'course_id', 'degree', 'grades', 'status', 'created_at', 'updated_at'
    ];

    // get course table data
    public function course(){
        return $this->belongsTo('App\Course');
    }

    public function getCourseProspectives() {
        return $this->hasMany('App\CourseProspect', 'course_id', 'id');
    }
}
