<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class Genre extends Model
{

    protected $fillable = ['name','url','created_at'];

    public $timestamps = false;

}
