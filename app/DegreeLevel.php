<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Config;

class DegreeLevel extends Model {

    public $timestamps = false;
    protected $fillable = [
        'id', 'name',
    ];


}
