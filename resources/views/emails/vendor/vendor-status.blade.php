<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vendor Account Status</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .email-header {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .email-content {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .cta-button {
            background-color: #ffc107;
            color: #fff;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            display: inline-block;
            margin-bottom: 30px;
        }

        .footer {
            font-size: 14px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Logo -->
        <img src="{{ $message->embed(public_path('img/greatwall-logo.png')) }}" alt="Logo" class="logo"
            style="width: 80px; height: auto;">

        <div class="email-header">
            @if ($status === 'Approved')
                🎉 Congratulations, {{ $vendor->companyName }}!
            @else
                We're Sorry, {{ $vendor->companyName }}
            @endif
        </div>
        <div class="email-content">
            @if ($status === 'Approved')
                Your vendor account has been approved. You can now access the vendor portal and start managing your
                data.
            @else
                Unfortunately, your vendor account application has been rejected.
                @if (!empty($rejectionReason))
                    <br><br>
                    <strong>Reason:</strong> {{ $rejectionReason }}
                @endif
                <br><br>
                For more information or further assistance, please contact our support team.
            @endif
        </div>
        <a href="{{ url('https://logistic2.gwamerchandise.com/login') }}" class="cta-button">Visit</a>
        <div class="footer">
            Regards,<br>
            GreatWallArts ADMIN
        </div>
    </div>
</body>

</html>
