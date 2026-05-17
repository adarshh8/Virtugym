<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class WaterIntake extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'water_intakes';
    
    protected $fillable = [
        'user_id', 'amount_ml', 'date'
    ];
    
    protected $casts = [
        'amount_ml' => 'integer',
        'date' => 'date'
    ];
}
