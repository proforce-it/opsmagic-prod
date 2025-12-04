<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .logo-container {
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }
        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333333;
        }
        p {
            color: #555555;
            font-size: 16px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 20px;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            margin-top: 20px;
            color: #777777;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 30px 0 10px;
        }
    </style>
</head>
<body>
<div class="logo-container">
    <img alt="Logo" src="{{ asset('assets/media/logos/email-logo.png') }}" class="h-150px" style="width: 300px;"/>
</div>
<div class="container">
    <h1>Dear {{ $confirmData->first_name }} {{ $confirmData->last_name }},</h1>
    <p>Thanks for joining {{ config('app.name') }}. Please confirm your email address to activate your account.</p>
    <p>Please click the button below to verify your email:</p>
    <a href="{{ $confirmData->confirm_link }}" class="btn">Confirm my email address</a>
    <p>If the button above doesn’t work, you can copy and paste the following link into your web browser:</p>
    <p><a href="{{ $confirmData->confirm_link }}">{{ $confirmData->confirm_link }}</a></p>
    <p>This step helps us keep your account secure and ensures you don’t miss important updates.</p>
    <p>We’re excited to have you on board.</p>
    <p>Best regards,<br>The {{ config('app.name') }} Team</p>

    <hr />
    <div class="footer">
        <p>PS: If you did not register with {{ config('app.name') }}, please ignore this email. No further action is required.</p>
    </div>
</div>
</body>
</html>
