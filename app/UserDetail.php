<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Config;

class UserDetail extends Model {

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'phone_number', 'ranking', 'website', 'about_scholarship', 'date_of_origin', 'about_university', 'logo', 'banner_image', 'profile_image', 'qualification', 'certificates', 'achievements', 'dob', 'gender', 'status', 'created_at', 'updated_at'
    ];

    #Get profile image complete path

    public function getProfileImageAttribute($value) {
        $server_url = \Config::get('variable.SERVER_URL');
        $imge_path = $server_url . 'images/student/profile_images/medium/';
        $default_image = $server_url . 'images/dummy_user.png';
        if (isset($value) && file_exists(public_path() . '/images/student/profile_images/medium/' . $value)) {
            return $imge_path . $value;
        } else {
            return $default_image;
        }
    }

    #Get logo complete path

    public function getLogoAttribute($value) {
        if ($value != NULL) {
            $server_url = \Config::get('variable.SERVER_URL');
            $imge_path = $server_url . 'images/university/logo/medium/';
            $default_image = $server_url . 'images/dummy_user.png';
            if (file_exists(public_path() . '/images/university/logo/medium/' . $value)) {
                return $imge_path . $value;
            } else {
                return $default_image;
            }
        } else {
            return NULL;
        }
    }

    #Get logo complete path

    public function getBannerImageAttribute($value) {
        if ($value != NULL) {
            $server_url = \Config::get('variable.SERVER_URL');
            $imge_path = $server_url . 'images/university/banner/';
            $default_image = $server_url . 'images/dummy_user.png';
            if (file_exists(public_path() . '/images/university/banner/' . $value)) {
                return $imge_path . $value;
            } else {
                return $default_image;
            }
        } else {
            return NULL;
        }
    }

}
