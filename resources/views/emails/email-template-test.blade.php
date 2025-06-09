<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $mailData['subject'] }}</title>
</head>
<body>
    {!! $mailData['header'] !!}

    <p>Dear {{ $mailData['name'] }},</p>

    <p>This is a test email to check the email template rendering.</p>

    {!! $mailData['footer'] !!}

    <p>Thanks,<br>{{ config('app.name') }} Team</p>
</body>
</html>
