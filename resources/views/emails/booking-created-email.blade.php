@component('mail::message')
    Dear {{ $mailData['name'] }},

    {{ $mailData['changedBy'] }} {{ $mailData['logs'] }} #{{ $mailData['bookingId'] }}.

    Thanks,
    {{ config('app.name') }} Team
@endcomponent
