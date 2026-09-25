<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Review;
use App\Models\Store;
use App\Support\Seo;

class SitemapController extends Controller
{
    private const SIZE = 5000;

    private function query(string $type)
    {
        return match ($type) {
            'stores' => Store::where('is_active', true),
            'categories' => Category::where('is_active', true),
            'reviews' => Review::published(),
            default => abort(404),
        };
    }

    public function index()
    {
        $entries = [['loc' => Seo::route('sitemap.pages', ['type' => 'pages', 'page' => 1])]];
        foreach (['stores', 'categories', 'reviews'] as $type) {
            $pages = (int) ceil($this->query($type)->count() / self::SIZE);
            for ($page = 1; $page <= $pages; $page++) {
                $entries[] = ['loc' => Seo::route('sitemap.pages', compact('type', 'page'))];
            }
        }

        return $this->xml('sitemapindex', 'sitemap', $entries);
    }

    public function pages(string $type, int $page)
    {
        abort_if($page < 1, 404);
        if ($type === 'pages') {
            abort_unless($page === 1, 404);
            $entries = array_map(fn ($route) => ['loc' => Seo::route($route)], ['home', 'stores.index', 'categories.index', 'reviews.index']);
        } else {
            $items = $this->query($type)->orderBy('id')->forPage($page, self::SIZE)->get();
            abort_if($items->isEmpty(), 404);
            $entries = $items->map(function ($item) use ($type) {
                $entry = ['loc' => Seo::route($type.'.show', $item)];
                // Collection pages also change when related offers change; avoid an inaccurate lastmod.
                if ($type === 'reviews') {
                    $entry['lastmod'] = $item->updated_at->toAtomString();
                }

                return $entry;
            })->all();
        }

        return $this->xml('urlset', 'url', $entries);
    }

    private function xml(string $root, string $element, array $entries)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<'.$root.' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($entries as $entry) {
            $xml .= '<'.$element.'>';
            foreach ($entry as $key => $value) {
                $xml .= '<'.$key.'>'.htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</'.$key.'>';
            }
            $xml .= '</'.$element.'>';
        }

        return response($xml.'</'.$root.'>', 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots()
    {
        return response("User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /go/\n\nSitemap: ".Seo::route('sitemap.index')."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
