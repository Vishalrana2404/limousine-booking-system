@component('mail::message')
# Welcome to {{ config('app.name') }}

Dear {{ $user->first_name }} {{ $user->last_name }},

We have generated a password for your account. Please use the following password to log in:

**Password:** {{ $password }}

Please change your password after logging in for security reasons.

Thank you,<br>
The {{ config('app.name') }} Team
@endcomponent
