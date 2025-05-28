@component('mail::message')

    Dear {{ $mailData['name'] }},

    {{ $mailData['changedBy'] }} made changes on booking #{{ $mailData['bookingId'] }}. Below are some details:

        @foreach ($mailData['logs'] as $log)
            - {{ $log }}
        @endforeach

    Thanks,
    {{ config('app.name') }} Team
@endcomponent
