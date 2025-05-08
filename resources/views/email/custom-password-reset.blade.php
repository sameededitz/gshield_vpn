<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=display-width, initial-scale=1.0, maximum-scale=1.0," />
    <title>Password Reset | {{ config('app.name') }} </title>
</head>

<body style="font-family: 'Arial', sans-serif; background-color: #f9f9f9; margin: 0; padding: 0;">
    <div class="container"
        style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); padding: 20px;">
        <!-- Navbar -->
        <div class="navbar"
            style="background-color: #000; padding: 10px 20px; border-radius: 8px 8px 0 0; display: flex; justify-content: center; align-items: center;">
            <img src="{{ config('app.logo') ?? '#' }}" alt="Logo" style="width: 180px;">
        </div>

        <div class="content" style="padding: 20px 10px;">
            <h2 style="margin: 15px 0; text-align: center;">Password Reset Request</h2>
            <p>Dear {{ $user->name ?? 'User' }},</p>
            <p>We received a request to reset your password. Use the code below to reset your password:</p>
            <div class="bottom-bar"
                style="text-align: center; padding: 20px; background-color: #f1f1f1; border-radius: 8px; margin-top: 10px; display: flex; justify-content: center; align-items: center;">
                <p style="text-align: center; font-size: 24px; font-weight: bold; color: #007bff; margin: 0;">
                    {{ $resetCode ?? 'XXXXXX' }}</p>
            </div>
            <p>This code will expire in 15 minutes.</p>
            <p>If you did not request a password reset, no further action is required.</p>
            <p>Best regards,</p>
            <p>{{ config('app.name') }} Team</p>
        </div>

        <div class="footer" style="text-align: center; padding: 20px; background-color: #f1f1f1;">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>

        <!-- Bottom Bar -->
        <div class="bottom-bar"
            style="text-align: center; padding: 20px; background-color: #f1f1f1; border-radius: 0 0 8px 8px; margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <a href="#" style="color: #007bff; text-decoration: none; margin-right: 10px;">Terms of
                    Service</a>
                <a href="#" style="color: #007bff; text-decoration: none;">Privacy Policy</a>
            </div>
            @if (isset($viewInBrowserUrl))
                <div>
                    <a href="{{ $viewInBrowserUrl ?? '#' }}"
                        style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">View
                        in Browser</a>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
