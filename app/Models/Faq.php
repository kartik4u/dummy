<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use Sluggable;

    public $timestamps = false;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'question',
            ],
        ];
    }
    
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question', 'slug', 'answer', 'status', 'created_at', 'updated_at',
    ];
}
