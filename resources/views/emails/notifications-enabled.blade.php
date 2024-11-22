<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications Enabled</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50;">Notifications Enabled</h1>

        <p>Dear {{ $user->name }},</p>

        <p>We're writing to confirm that you've successfully enabled notifications for your account. You'll now receive updates about important activities and events related to your account.</p>

        <p>If you didn't make this change or if you have any questions, please don't hesitate to contact our support team.</p>

        <p>Thank you for using our service!</p>

        <p>Best regards,<br>{{ config('app.name') }} Team</p>

        <div style="margin-top: 30px;">
            <a href="{{ route('settings.index') }}" style="background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Manage Your Settings</a>
        </div>
    </div>
</body>
</html>
