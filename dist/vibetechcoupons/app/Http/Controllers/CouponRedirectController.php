<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;

class CouponRedirectController extends Controller
{
    public function __invoke(Coupon $coupon): RedirectResponse
    {
        abort_unless($coupon->is_active, 404);

        $destination = trim((string) $coupon->destination_url);
        $scheme = strtolower((string) parse_url($destination, PHP_URL_SCHEME));

        if ($destination === '' || ! in_array($scheme, ['http', 'https'], true)) {
            return redirect()->route('stores.show', $coupon->store);
        }

        $coupon->increment('clicks');

        return redirect()->away($destination);
    }
}
