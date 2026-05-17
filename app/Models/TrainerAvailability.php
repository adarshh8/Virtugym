<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class TrainerAvailability extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'trainer_availabilities';
    
    protected $fillable = [
        'trainer_id', 'day_of_week', 'start_time', 'end_time',
        'is_recurring', 'specific_date', 'is_booked', 'booking_id', 'session_type'
    ];
    
    protected $casts = [
        'is_recurring' => 'boolean',
        'is_booked' => 'boolean',
        'specific_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}