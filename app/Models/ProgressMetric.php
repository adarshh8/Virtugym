<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ProgressMetric extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'progress_metrics';
    
    protected $fillable = [
        'user_id', 'date', 'weight', 'body_fat_percentage',
        'chest', 'waist', 'hips', 'biceps', 'thighs', 'arms',
        'muscle_mass', 'bmr', 'bmi', 'notes', 'progress_photo'
    ];
    
    protected $casts = [
        'date' => 'datetime',
        'weight' => 'float',
        'body_fat_percentage' => 'float',
        'chest' => 'float',
        'waist' => 'float',
        'hips' => 'float',
        'biceps' => 'float',
        'thighs' => 'float',
        'arms' => 'float',
        'muscle_mass' => 'float',
        'bmr' => 'float',
        'bmi' => 'float'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}