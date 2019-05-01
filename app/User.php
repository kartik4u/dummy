<?php

namespace App;

use Illuminate\Support\Facades\Config;
//use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    public $timestamps = false;
    use Notifiable, HasApiTokens;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    protected $fillable = ['total_revenue','monthly_revenue','followers_count','gmail_id','fb_id','auth_token','role_id', 'signup_type','name', 'email', 'username', 'password', 'dob', 'address', 'city', 'description','postal_code', 'profile_image', 'profile_submited_at','slug', 'termsandcondition_version', 'phone', 'privacy_version', 'forgot_password_token',  'device_type', 'device_id','current_login','last_login','verify_token' ,'status', 'push_notification_status','created_at', 'updated_at','remember_token','synopsis','about_writer','monthly_revenue','stripe_id','is_writer_reader'];

     # function for get date time
     public function getCreatedAtAdminAttribute($value) {
         $value = date("m/d/Y", $value);
         return $value;
     }

     // path of image
    public function getProfileImageAttribute($value)
    {   
        $server_url = Config::get('variable.SERVER_URL');
        if (!empty($value) && file_exists(storage_path() . '/app/public/users/'.$this->id.'/'.$value)) {
            return $server_url . '/storage/users/'.$this->id.'/'.$value;
        } else {
            return $server_url . '/storage/dummy.jpg';
        }
    }

     // path of image
     public function getSynopsisFullAttribute($value)
     {   
         $server_url = Config::get('variable.SERVER_URL');
         if (!empty($value) && file_exists(storage_path() . '/app/public/users/'.$this->id.'/'.$value)) {
             return $server_url . '/storage/users/'.$this->id.'/'.$value;
         } else {
             return false;//$server_url . '/storage/dummy.jpg';
         }
     }

    

     # function for relation with notification model
    public function notification() {
        return $this->hasMany('App\Notification');
    }
    # function for count total unread notification
    public function unReadNotificationCount() {
         return $this->hasMany(Notification::class,'receiver_id','id')
                 ->selectRaw('receiver_id, count(*) as count')
                 ->where('is_read',0)
                         ->groupBy('receiver_id');
    }


 # function for relation with notification model
    public function un_read_noti() {
        return $this->hasMany(Notification::class,'receiver_id','id')->where('is_read',0)->where('status',1);
    }

    # function for relation with userRole model
    public function userRole() {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

	 //  // total reports
    public function totalFollowers() {
        return $this->hasOne('App\Models\Follower', 'followed_to', 'id')
                        ->selectRaw('followed_to, count(*) as count')->groupBy('followed_to');
    }

  

    public function CheckFollower() {
        return $this->hasOne('App\Models\Follower', 'followed_to', 'id');
    }



   //get user genre
   public function getUserGenre() {
    return $this->hasMany('App\Models\UserGenre', 'user_id', 'id');
    }


       //get stories
   public function getStories() {
    return $this->hasMany('App\Models\Story', 'user_id', 'id');
    }


         //get stories
   public function getFavAuthers() {
    return $this->hasMany('App\Models\Favourite', 'user_id', 'id')->where('type',1);
  }

    
   
    

      #Get user image complete path
      public function getAdminImageAttribute($value) {
         // return $value;
        if(empty($value)){
            $admin_image ='/assets/images/dummy_image.jpg';
        } else{
            $admin_image ='/assets/images/'.$value;
        }
        return $admin_image  ;
    } 

    



}