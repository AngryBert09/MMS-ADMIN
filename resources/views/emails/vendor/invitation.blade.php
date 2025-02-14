<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Invitation</title>
    <style>
        /* Reset styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f6f7;
            color: #2c3e50;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            padding: 30px 20px;
            border: 1px solid #e1e1e1;
        }

        .email-header {
            font-size: 28px;
            font-weight: 700;
            color: #1abc9c;
            margin-bottom: 20px;
            text-transform: capitalize;
        }

        .email-content {
            font-size: 16px;
            line-height: 1.6;
            color: #34495e;
            margin-bottom: 30px;
        }

        .cta-button {
            display: inline-block;
            padding: 14px 24px;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.5);
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .cta-button:hover {
            background-color: #2980b9;
        }

        .email-footer {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 40px;
            border-top: 1px solid #e5e5e5;
            padding-top: 20px;
        }

        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 20px;
        }

        .highlight {
            color: #e74c3c;
            font-weight: bold;
        }

        @media (max-width: 480px) {
            .email-container {
                padding: 20px 15px;
            }

            .email-header {
                font-size: 24px;
            }

            .cta-button {
                padding: 12px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Logo (optional) -->
        <img src="{{ asset('img/greatwall-logo.png') }}" alt="Logo" class="logo">

        <!-- Header -->
        <div class="email-header">🎉 Hello, {{ $vendorName }}!</div>

        <!-- Content -->
        <div class="email-content">
            You've been invited to join our <span class="highlight">Vendor Portal</span>.
            Click the button below to get started and access all the tools you need to streamline your experience.
        </div>

        <!-- Call to Action Button -->
        <a href="{{ $inviteLink }}" class="cta-button">Join Now 🚀</a>

        <!-- Footer -->
        <div class="email-footer">
            If you didn't request this invitation, please ignore this email.
            <br><br>
            Regards,
            <strong>GreatWallArts ADMIN</strong>
        </div>
    </div>
</body>

</html>
