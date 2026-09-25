@props(['review'])
@if($review->affiliate_url && filter_var($review->affiliate_url, FILTER_VALIDATE_URL) && in_array(strtolower((string) parse_url($review->affiliate_url, PHP_URL_SCHEME)), ['http', 'https']))
    <aside class="review-offer" aria-label="Product offer">
        <div class="review-offer-copy">
            <p class="review-offer-title">Ready to try it?</p>
            <p>Check the current price and availability on the seller’s website.</p>
        </div>
        <a class="review-offer-button" href="{{ $review->affiliate_url }}" target="_blank" rel="sponsored nofollow noopener noreferrer">{{ $review->affiliate_label ?: 'Buy now' }} <span aria-hidden="true">↗</span></a>
        <p class="review-offer-disclosure">We may earn a commission if you buy through this link, at no extra cost to you.</p>
    </aside>
@endif
