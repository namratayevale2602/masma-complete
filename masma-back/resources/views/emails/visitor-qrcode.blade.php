{{-- resources/views/emails/visitor-qrcode.blade.php --}}
<x-mail::message>
# Your Visitor ID Card

Hello **{{ $visitor->visitor_name }}**,

Thank you for registering with **{{ $appName }}**. Your visitor ID card is ready.

<x-mail::panel>
### Visitor Information
- **Name:** {{ $visitor->visitor_name }}
- **Email:** {{ $visitor->email }}
- **Mobile:** {{ $visitor->mobile }}
@if($visitor->bussiness_name)
- **Business:** {{ $visitor->bussiness_name }}
@endif
- **Registration Date:** {{ $visitor->created_at->format('F d, Y') }}
- **Visitor ID:** #{{ str_pad($visitor->id, 6, '0', STR_PAD_LEFT) }}
</x-mail::panel>

### Your QR Code
Your unique QR code is attached to this email. Please keep it safe as you'll need it for check-in and verification.

<x-mail::button :url="route('visitor.qrcode.download', $visitor->id)" color="primary">
Download QR Code
</x-mail::button>

<x-mail::button :url="route('visitor.idcard.download', $visitor->id)" color="success">
Download ID Card (PDF)
</x-mail::button>

<x-mail::button :url="$cardUrl" color="secondary">
View Digital ID Card
</x-mail::button>

## Important Notes:
1. **Carry this ID card** when visiting our premises
2. **Show the QR code** at reception for check-in
3. **This ID is personal** - do not share with others
4. **Valid for 1 year** from date of issue

If you have any questions, please contact our support team.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>