<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class Seo
{
    public static function url(string $path = ''): string
    {
        $base = config('seo.url') ?: request()->root();

        return rtrim($base, '/').'/'.ltrim($path, '/');
    }

    public static function route(string $name, mixed $parameters = []): string
    {
        return self::url(route($name, $parameters, false));
    }

    public static function forPage(array $data): array
    {
        $route = request()->route()?->getName();
        $preview = $route === 'admin.reviews.preview';
        if ($preview) {
            $route = 'reviews.show';
        }
        [$title, $description] = match ($route) {
            'stores.index' => ['Store Coupons & Promo Codes — VibeTechCoupons', 'Browse our store directory for coupon codes, online deals and shopping reviews from your favorite brands.'],
            'categories.index' => ['Shop Deals by Category — VibeTechCoupons', 'Explore coupons, stores and buying guides by category, from outdoor gear and electronics to home and beauty.'],
            'reviews.index' => ['Product Reviews & Buying Guides — VibeTechCoupons', 'Read product reviews and practical buying guides to compare features, value and options before you shop.'],
            default => [$data['title'] ?? 'VibeTechCoupons — Coupons, Deals & Shopping Guides', $data['description'] ?? 'Discover coupon codes, online deals, store pages and practical shopping guides to help you save on your next purchase.'],
        };
        $breadcrumbs = [['name' => 'Home', 'url' => self::route('home')]];
        $entity = match ($route) {
            'stores.show' => $data['store'] ?? null,
            'categories.show' => $data['category'] ?? null,
            'reviews.show' => $data['review'] ?? null,
            default => null,
        };
        $image = null;
        $article = null;
        if ($entity instanceof Store) {
            $title = $entity->seo_title ?: $entity->name.' Coupons & Deals — VibeTechCoupons';
            $description = $entity->seo_description ?: ($entity->description ?: 'Browse '.$entity->name.' coupon codes, deals and shopping reviews on VibeTechCoupons.');
            $breadcrumbs[] = ['name' => 'Stores', 'url' => self::route('stores.index')];
            $breadcrumbs[] = ['name' => $entity->name, 'url' => self::route('stores.show', $entity)];
        } elseif ($entity instanceof Category) {
            $title = $entity->seo_title ?: $entity->name.' Coupons & Deals — VibeTechCoupons';
            $description = $entity->seo_description ?: ($entity->description ?: 'Discover '.$entity->name.' offers, stores and buying guides on VibeTechCoupons.');
            $breadcrumbs[] = ['name' => 'Categories', 'url' => self::route('categories.index')];
            $breadcrumbs[] = ['name' => $entity->name, 'url' => self::route('categories.show', $entity)];
        } elseif ($entity instanceof Review) {
            $article = $entity;
            $title = $entity->seo_title ?: $entity->title.' — VibeTechCoupons';
            $description = $entity->seo_description ?: ($entity->excerpt ?: $entity->content);
            $image = $entity->image_url;
            $breadcrumbs[] = ['name' => 'Reviews & guides', 'url' => self::route('reviews.index')];
            $breadcrumbs[] = ['name' => $entity->title, 'url' => self::route('reviews.show', $entity)];
        } elseif (in_array($route, ['stores.index', 'categories.index', 'reviews.index', 'search'])) {
            $breadcrumbs[] = ['name' => match ($route) {
                'stores.index' => 'Stores', 'categories.index' => 'Categories', 'reviews.index' => 'Reviews & guides', default => 'Search'
            }, 'url' => self::url(request()->path())];
        }
        $canonical = self::url(request()->path() === '/' ? '' : request()->path());
        if ($preview && $article) {
            $canonical = self::route('reviews.show', $article);
        }
        $paginator = $data['coupons'] ?? $data['reviews'] ?? null;
        if ($paginator instanceof LengthAwarePaginator && $paginator->currentPage() > 1) {
            $canonical .= '?page='.$paginator->currentPage();
            $title .= ' — Page '.$paginator->currentPage();
        }
        $description = Str::limit(Str::squish(strip_tags($description)), 160);
        $graph = [
            ['@type' => 'WebSite', '@id' => self::url().'#website', 'name' => 'VibeTechCoupons', 'url' => self::url()],
            ['@type' => $article ? 'WebPage' : 'CollectionPage', '@id' => $canonical.'#webpage', 'url' => $canonical, 'name' => $title, 'description' => $description, 'isPartOf' => ['@id' => self::url().'#website']],
        ];
        if (count($breadcrumbs) > 1) {
            $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => array_map(fn ($crumb, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $crumb['name'], 'item' => $crumb['url']], $breadcrumbs, array_keys($breadcrumbs))];
        }
        if ($article) {
            $graph[] = array_filter(['@type' => 'Article', 'headline' => $article->title, 'description' => $description, 'mainEntityOfPage' => ['@id' => $canonical.'#webpage'], 'datePublished' => $article->published_at?->toAtomString(), 'dateModified' => $article->updated_at?->toAtomString(), 'image' => $image, 'publisher' => ['@type' => 'Organization', 'name' => 'VibeTechCoupons', 'url' => self::url()]]);
        }

        return compact('title', 'description', 'canonical', 'breadcrumbs', 'image', 'article') + ['robots' => $data['robots'] ?? 'index,follow,max-image-preview:large', 'json' => json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)];
    }
}
