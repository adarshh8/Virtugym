<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Booking - VirtuGym</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2>New Booking Confirmed</h2>
    <p>Hi {{ $trainer->name }},</p>
    <p>{{ $trainee->name }} booked a training session with you.</p>

    <div style="background: #f3f4f6; border-radius: 8px; padding: 16px; margin: 16px 0;">
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->session_date)->format('F d, Y') }}</p>
        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->session_date)->format('h:i A') }}</p>
        <p><strong>Duration:</strong> {{ $booking->duration_minutes }} minutes</p>
        <p><strong>Amount:</strong> ₹{{ number_format($booking->amount) }}</p>
        @if($booking->special_requests)
            <p><strong>Special Requests:</strong> {{ $booking->special_requests }}</p>
        @endif
    </div>

    <p>Please join the video session 10 minutes before the scheduled time.</p>
    <p>Thanks,<br>VirtuGym</p>
</body>
</html>
