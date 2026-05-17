<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Exercise extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'exercises';
    
    protected $fillable = [
        'name', 'category', 'muscle_group', 'equipment', 
        'difficulty', 'instructions', 'tips', 'image_url',
        'video_url', 'calories_per_hour', 'benefits', 'precautions'
    ];
}