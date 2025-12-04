<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Shift Cancelled Email</title>
</head>
<body>
<h2>job({{ $invitation->job_name }}) shift, This shift is cancelled</h2>
<p>Hello {{ $invitation->worker_name }},</p>
<p>You are informed that this shift is cancelled. job({{ $invitation->job_name }}) shift, date - $invitation->date, start time - $invitation->start_time, working hour is $invitation->shift_length_hr and $invitation->shift_length_min minutes.</p>
<p>Best regards,</p>
<p>My Team</p>
</body>
</html>