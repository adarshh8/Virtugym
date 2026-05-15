<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MindfulnessContent extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'mindfulness_contents';
    
    protected $fillable = [
        'title', 'category', 'description', 'content', 
        'media_type', 'media_url', 'image_url', 'duration_minutes'
    ];
    
    protected $casts = [
        'duration_minutes' => 'integer'
    ];
}
