<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .message {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .notification-type {
            display: inline-block;
            padding: 4px 8px;
            background-color: #e9ecef;
            border-radius: 3px;
            font-size: 12px;
            color: #495057;
            margin-bottom: 15px;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Yapa</div>
            <p>Your Social Networking Platform</p>
        </div>

        @if($user)
            <p>Hello {{ $user->name }},</p>
        @else
            <p>Hello,</p>
        @endif

        @if($notificationLog && $notificationLog->type)
            <div class="notification-type">
                {{ ucwords(str_replace('_', ' ', $notificationLog->type)) }}
            </div>
        @endif

        <div class="message">
            {!! nl2br(e($message)) !!}
        </div>

        @if($notificationLog && $notificationLog->type === 'otp')
            <div style="text-align: center; margin: 30px 0;">
                <div style="font-size: 24px; font-weight: bold; color: #007bff; background-color: #f8f9fa; padding: 15px; border-radius: 5px; display: inline-block; letter-spacing: 3px;">
                    @if($notificationLog->metadata && isset($notificationLog->metadata['otp']))
                        {{ $notificationLog->metadata['otp'] }}
                    @endif
                </div>
                <p style="font-size: 12px; color: #666; margin-top: 10px;">This code expires in 5 minutes</p>
            </div>
        @endif

        @if($notificationLog && in_array($notificationLog->type, ['batch_full', 'ad_approval', 'transaction_success']))
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ config('app.url') }}" class="button">Open Yapa App</a>
            </div>
        @endif

        <div class="footer">
            <p>This is an automated message from Yapa. Please do not reply to this email.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} Yapa. All rights reserved.</p>
            
            @if($user && $user->whatsapp_notifications_enabled)
                <p style="margin-top: 15px; font-size: 11px;">
                    You're receiving this email as a backup to your WhatsApp notification.
                    <br>
                    To manage your notification preferences, please visit your account settings.
                </p>
            @endif
        </div>
    </div>
</body>
</html>