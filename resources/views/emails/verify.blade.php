<x-mail::message>
<h1 style="text-align: center; color: #333; font-size: 24px;">
    {{ config('app.name') }} Email Verification
</h1>

<span style="color: #333; text-transform: capitalize; font-weight: bold; font-size: 18px;">Hello {{ $user->name }}</span>,

Thank you for registering! Please verify your email address by clicking the button below:

<x-mail::button :url="$verificationUrl" color="primary">
Verify Email Address
</x-mail::button>

If you did not create an account, no further action is required.

---

{{-- Fallback URL in a panel --}}
<x-mail::panel>
If you’re having trouble clicking the “Verify Email Address” button, copy and paste the URL below into your web browser:

<span style="word-break: break-all; color: #555; font-size: 12px;">
    {{ $verificationUrl }}
</span>
</x-mail::panel>

Best Regards,<br>
{{ config('app.name') }} Team
</x-mail::message>
