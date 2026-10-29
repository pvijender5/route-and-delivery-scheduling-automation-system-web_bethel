@component('mail::message')
<div style="text-align: center; margin-bottom: 20px;">
    <a href="{{ url('/') }}">
        <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo" style="max-width: 172px; height: auto;">
    </a>
</div>

Hello {{$userName}},

You've requested a password reset for your account. Please use the following 4-digit OTP code to complete the process:

Your OTP: {{$pin}}

If you didn't request this, please disregard this email.

Best regards,<br>
{{ config('app.name') }}
@endcomponent
