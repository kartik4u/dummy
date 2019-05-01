<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageLog extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = ['user_id', 'title','page_id', 'created_at', 'updated_at'];
    
    #Get page detail
    public function page() {   
        return $this->belongsTo('App\Page');
    }
}
