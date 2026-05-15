<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Booking;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $trainer = User::where('role', 'trainer')->first();
        $trainee = User::where('role', 'trainee')->first();

        if ($trainer && $trainee) {
            Booking::create([
                'trainer_id' => $trainer->id,
                'trainee_id' => $trainee->id,
                'session_date' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
                'status' => 'confirmed',
                'amount' => 500,
                'payment_status' => 'paid'
            ]);
            
            echo "Booking created between {$trainer->name} and {$trainee->name}\n";
        } else {
            echo "Trainer or Trainee not found.\n";
        }
    }
}
