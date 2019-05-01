<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class Page extends Model
{

    protected $fillable = ['version','name','meta_key','meta_value','status','created_at','updated_at'];

    public $timestamps = false;



    # function for get date time
    public function getCreatedAtAdminAttribute($value) {
        $value = date("m/d/Y", $value);
        return $value;
    }

    # function for get date time
    public function getMetaKeyAdminAttribute($value) {
      if($value=='about'){
        $value= 'About us';
      } else if($value=='privacy'){
          $value= 'Privacy policy';
      } else if($value=='faq'){
          $value= 'FAQ';
      }
      return $value;
    }


}
