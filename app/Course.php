<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Course extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $casts = ['fee' => 'integer'];
    protected $fillable = [
        'user_id','slug', 'course_name', 'duration_of_course', 'city', 'degree_level','visa_type','fee', 'average_income','drop_out_rate','next_intake', 'description',
        'status', 'created_at', 'updated_at'
    ];

    #get course details
    public function courseDetail(){
        return $this->hasMany('App\CourseDetail');
    }

    #get user detail
    public function user(){
        return $this->belongsTo('App\User');
    }

    // get course detail

    public function getCourseDetail() {
        return $this->hasMany('App\CourseDetail', 'course_id', 'id');
    }

    // get course prospectives

    public function getCourseProspectives() {
        return $this->hasMany('App\CourseProspect', 'course_id', 'id');
    }


    // get degree levels

    public function getDegreeLevels() {
        return $this->hasOne('App\DegreeLevel', 'id', 'degree_level');
    }


    // get user detail
    public function getProfile() {
        return $this->hasOne('App\UserDetail', 'user_id', 'user_id');
    }

    // get address
    public function address() {
        return $this->hasMany('App\UserAddress', 'user_id', 'user_id');
    }

      #Get next intake

      public function getNextIntakeAttribute($value) {        
        return  $php_timestamp_date = date("d F, Y", $value);
      }


      # favourite course
    public function favouriteCourse() {
        return $this->hasMany('App\FavouriteCourse', 'course_id', 'id');
    }
 



  }
