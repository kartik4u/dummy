<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchAnalytic extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = ['user_id','cache_id','ip_address','ip_origin', 'location', 'university','course','degree_level', 'visa','entry_requirement','ranking',
        'annual_fee','status','created_at', 'updated_at'];

    #get degree level
    public function degreeLevel(){
        return $this->belongsTo('App\DegreeLevel','degree_level','id');
    }
}


