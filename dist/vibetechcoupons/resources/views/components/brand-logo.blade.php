@props(['panel' => false])
<span {{ $attributes->class(['brand-logo', 'brand-logo-panel' => $panel]) }}>
    <img src="{{ asset('images/vibetechcoupons-logo.webp') }}" alt="VibeTechCoupons" width="1600" height="533" decoding="async">
</span>
