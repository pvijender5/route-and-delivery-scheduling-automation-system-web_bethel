@component('mail::message')
# User Invitation

Please click the below link and enter your 4 digit OTP to  create your account password.

Your four-digit OTP is {{$pin}}<br>

<a href="{{ $url }}">Click here to enter your OTP, it will expire after 1 day</a> 

Thanks,<br>
{{ config('app.name') }}
@endcomponent