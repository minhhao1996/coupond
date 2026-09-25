<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class ReviewContent
{
    public static function sanitize(string $html): string
    {
        $config = new HtmlSanitizerConfig;
        foreach (['p', 'br', 'h2', 'h3', 'strong', 'b', 'em', 'i', 'u', 's', 'blockquote', 'pre', 'code', 'ul', 'ol', 'li', 'figure', 'figcaption', 'table', 'caption', 'thead', 'tbody', 'tr', 'th', 'td'] as $tag) {
            $config = $config->allowElement($tag, []);
        }
        $config = $config->allowElement('a', ['href', 'title'])->allowLinkSchemes(['https', 'http', 'mailto'])->withMaxInputLength(200000);
        $config = $config->allowElement('img', ['src', 'alt', 'width', 'height'])
            ->allowMediaSchemes(['https', 'http'])->allowRelativeMedias()
            ->forceAttribute('img', 'loading', 'lazy')->forceAttribute('img', 'decoding', 'async');

        return (new HtmlSanitizer($config))->sanitize($html);
    }

    public static function render(?string $content, ?string $format): string
    {
        if ($format !== 'html') {
            return nl2br(e($content ?? ''));
        }

        return str_replace(['<table>', '</table>'], ['<div class="review-table-scroll"><table>', '</table></div>'], self::sanitize($content ?? ''));
    }
}
