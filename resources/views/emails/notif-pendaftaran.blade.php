<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Notification</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8fafc; line-height: 1.6;">
    <!-- Email Wrapper -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc;">
        <tr>
            <td style="padding: 20px 0;">
                <!-- Email Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg,rgb(102, 126, 234) 0%,rgb(115, 102, 234) 100%); padding: 40px 30px; text-align: center; border-radius: 8px 8px 0 0;">
                            <!-- Logo dengan fallback -->
                            @if(file_exists(public_path('assets/img/branding/logo.png')))
                                <img src="{{ config('app.url') }}/assets/img/branding/logo.png" alt="{{ config('app.name', 'Logo') }}" style="max-width: 70px; height: auto; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto;">
                            @else
                                <table style="margin: 0 auto 20px auto;">
                                    <tr>
                                        <td style="width: 120px; height: 60px; background-color: #ffffff; border-radius: 8px; text-align: center; vertical-align: middle; font-size: 18px; font-weight: bold; color: #667eea;">
                                            {{ config('app.name', 'LOGO') }}
                                        </td>
                                    </tr>
                                </table>
                            @endif
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 600;">Thank You!</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <div style="font-size: 18px; color: #374151; margin-bottom: 20px; font-weight: 500;">
                                Hello {{ $user->name }},
                            </div>

                            <div style="font-size: 16px; color: #6b7280; line-height: 1.7; margin-bottom: 30px;">
                                <p style="margin: 0 0 15px 0;">You have registered for the program <strong>{{ $program->name }}</strong>!</p>
                                
                                <p style="margin: 0 0 15px 0;">Please check your registration status regularly on the program page.</p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb; border-radius: 0 0 8px 8px;">
                            <p style="font-size: 14px; color: #9ca3af; margin: 0 0 10px 0;">
                                This email was sent automatically. If you did not register at {{ config('app.name') }}, 
                                please ignore this email.
                            </p>
                            <p style="font-size: 14px; color: #9ca3af; margin: 0;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <!-- Mobile Responsive -->
    <style>
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0 10px !important;
            }
        }
    </style>
</body>
</html> 