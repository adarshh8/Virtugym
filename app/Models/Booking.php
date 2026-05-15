<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Booking extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'bookings';
    
    protected $fillable = [
        'trainee_id', 'trainer_id', 'session_date', 'session_time',
        'duration_minutes', 'status', 'amount', 'payment_id',
        'special_requests', 'cancelled_at', 'completed_at',
        'cancelled_by', 'cancellation_reason',
        'meeting_id', 'meeting_link', 'meeting_started', 'meeting_ended'
    ];
    
    protected $casts = [
        'session_date' => 'datetime',
        'amount' => 'float',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'meeting_started' => 'boolean',
'meeting_ended' => 'boolean',
    ];
    
    // Relationships (CO6 - Eloquent ORM Relationships)
    public function trainee()
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }
    
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
