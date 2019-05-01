<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Config;

class SearchAnalytics extends Model {
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = [
        'user_id','status', 'created_at', 'updated_at'
    ];
    
}
