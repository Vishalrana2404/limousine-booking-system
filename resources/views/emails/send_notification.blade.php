@component('mail::message')
# Welcome to {{ config('app.name') }}
{{ $data['message']}}

Thank you,<br>
The {{ config('app.name') }} Team
@endcomponent
