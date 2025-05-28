@component('mail::message')

    Dear {{ $mailData['name'] }},

    {{ $mailData['changedBy'] }}

    {{ $mailData['logs'] }}

    Thanks,
    {{ config('app.name') }} Team
@endcomponent
