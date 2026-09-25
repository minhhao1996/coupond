# CouponHub Starter

Public-facing coupon / deal / store / review website inspired by the supplied screenshots, but redesigned with a cleaner editorial + commerce look.

## Stack

- Laravel 13
- PHP 8.3+
- Livewire 4
- Tailwind CSS 4.3
- Alpine.js (bundled with Livewire)
- Vite 7
- MySQL 8+ recommended

## Included

- Homepage with hero, latest coupons/deals, featured stores, categories, and editorial reviews
- Store directory grouped A-Z
- Store detail page with coupons and reviews
- Category directory
- Category detail landing pages
- Reviews / guides listing
- Review detail pages
- Livewire global search
- Livewire coupon reveal interaction
- SEO-ready title/description/canonical/OpenGraph layout
- MySQL migrations + `database/schema.sql`
- Demo seed data
- Responsive Tailwind UI

## Install

```bash
cp .env.example .env
composer install
php artisan key:generate

# Create a MySQL database named couponhub (or update .env)
php artisan migrate --seed

npm install
npm run dev

php artisan serve
```

Open http://127.0.0.1:8000

For production:

```bash
npm run build
php artisan optimize
```

## Main routes

### Public SEO

- `/sitemap.xml` is a dynamic sitemap index. Child sitemaps contain up to 5,000 URLs each and include public directories, active stores/categories, and published articles. Admin, search, outbound redirects, drafts, and scheduled articles are excluded.
- `/robots.txt` advertises the sitemap. Search and login remain crawlable so crawlers can read their `noindex` metadata; admin and outbound redirect paths are disallowed.
- Store, category, and article pages use their admin SEO title/description fields, with content-based fallbacks. Public pages include canonical URLs, Open Graph/Twitter tags, breadcrumbs and JSON-LD. Articles include publication/modification dates and their supplied image.
- Pagination keeps its own canonical URL; tracking parameters are stripped. Out-of-range pages return 404.
- An active article with a future publication date stays hidden until that date. Saving an active article without a date publishes it immediately.

For deployment set `APP_URL` and `SEO_URL` to the real HTTPS site origin, then refresh the configuration cache. `SEO_URL` pins canonical/sitemap URLs to that origin; when unset, local previews use the current request origin. Submit the public `/sitemap.xml` URL in Google Search Console after deployment. No external indexing submission is performed automatically.

- `/`
- `/stores`
- `/store/{slug}`
- `/categories`
- `/category/{slug}`
- `/reviews`
- `/reviews/{slug}`

## Database model

- `categories`
- `stores`
- `category_store`
- `coupons`
- `reviews`

## Administration

For the local development site, run `php artisan db:seed --class=AdminSeeder`.
This creates `admin@couponhub.local` with a random temporary password printed once in the terminal.
Sign in at `/login`, then change the password at `/admin/account`.
Rerunning this seeder leaves existing accounts, passwords, and permissions unchanged.
It is also included in the initial `DatabaseSeeder` run and is skipped outside local/testing environments.
Use only the `AdminSeeder` command on an already populated site to avoid reseeding demo content.

Run `php artisan migrate` to add the users table, then create an administrator:

```bash
php artisan admin:create owner@example.com --name="Site Owner"
```

The command prompts privately for a password and confirmation. Use at least 12 characters including letters and numbers. It does not overwrite existing accounts or create default credentials.

Sign in at `/login`, then open `/admin`. Only users explicitly marked as administrators can enter; public registration is disabled.

The management studio includes:

- Overview with content totals, publication status, and offer interactions.
- Searchable, paginated lists for coupons, stores, categories, and reviews/guides.
- Create/edit forms, publication and featured switches, category assignments, SEO fields, and validated offer URLs.
- A separate deletion confirmation page. Stores/categories with linked content cannot be deleted; unpublish or reassign their content first.
- Sign out and password changes at `/admin/account`.
- Login throttling, CSRF-protected writes, and administrator authorization on every management route.

### Images and review editor

Store and article forms support JPG, PNG and WebP uploads (up to 5 MB and 6000 × 6000 px), preview, replacement, removal and an optional external image URL. Uploads are stored on the public disk under `uploads/stores` or `uploads/reviews` with generated filenames. Run `php artisan storage:link` on each deployment and ensure `storage/app/public` is writable and backed up. Replacing/removing an image detaches the old file without deleting it, so any existing shared links remain intact.

Reviews use a locally bundled Quill editor for headings, emphasis, lists, quotes, code and links. HTML is filtered on the server before saving and rendering. Existing plain-text articles retain their original line breaks and become formatted HTML when saved through the editor. The editor loads only on review edit/create forms; the public site does not download it.

Use a secure HTTPS connection when deploying. No public password recovery flow is configured; administrator creation is available through the server command above.

Run the isolated test suite with `vendor/bin/phpunit`. Tests use an in-memory SQLite database and never modify the site's content database.

## Suggested next production upgrades

1. Add a real image upload/storage flow for store logos and review images.
2. Add coupon click/outbound redirect tracking in a dedicated events table rather than only a counter.
3. Add sitemap index + per-content sitemaps.
4. Add JSON-LD for BreadcrumbList, Organization, Article/Review where applicable.
5. Cache home/category/store query blocks with Redis.
6. Add scheduled coupon expiration cleanup.

### Article layouts

The review editor includes **Image & caption** (upload or URL, alt text and photo credit) and **Comparison table** (up to 6 columns and 15 rows). Double-click an inserted image or table to edit/remove it. Uploaded inline images use the same public disk and file validation as cover images; `/admin/media/images` requires administrator authentication. Save the article, then use **Preview saved article** to inspect its public layout without publishing. Draft preview URLs require admin access and return noindex headers.

### Analytics country and store filters

`/admin/analytics` filters totals, rankings, and logs by date, store, and country. Event type and IP filters apply only to logs. Store associations are captured for store pages, linked reviews, and coupon copies; general pages have no store. Administrators and bots are excluded from tracking.

Country lookups use the bundled DB-IP Lite file at `storage/app/geoip/dbip-country-lite.mmdb` through `maxmind-db/reader`. No visitor IP is sent to an external service. Deploy that file with the app and keep the DB-IP attribution link on the analytics page. See `storage/app/geoip/README.md` for licensing and monthly data refresh instructions. Override the file path with `ANALYTICS_COUNTRY_DATABASE` or disable lookups with `ANALYTICS_COUNTRY_LOOKUP_ENABLED=false`.

Run `php artisan migrate` when deploying. To fill missing countries on recent existing events with retained IPs, run `php artisan analytics:enrich-countries --limit=500` (maximum 5000 per run). Private/local IPs, missing databases, and unresolved addresses display **Unknown**. Old records whose IP has already been removed cannot be geolocated. Country is approximate, including for VPN/proxy traffic.

### Cloudinary uploads and WebP

The admin upload flow supports Cloudinary for store logos, review covers, and images inserted into the article editor. Create your own Cloudinary Image and Video APIs account, then find your credentials at https://console.cloudinary.com/settings/api-keys. Configure these values in the server's `.env` (never in browser JavaScript or chat):

```dotenv
MEDIA_DRIVER=cloudinary
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret
```

Run `php artisan config:clear` after editing configuration (or rebuild the configuration cache on a deployment that uses it). Upload a small test image from the store or review form. The saved URL should begin with `https://res.cloudinary.com/YOUR-CLOUD/image/upload/` and end in `.webp`.

Uploads go through the authenticated admin server endpoint to Cloudinary using HTTPS authentication. Uploaded images are converted to WebP with automatic quality and proportional size limits: 512 × 512 for logos, 1600 × 1600 for article images, without upscaling. Descriptive public IDs include a random suffix to avoid overwriting existing files. Public logo delivery is capped at 192px; review covers provide responsive 480/720/1200/1600px WebP variants. Existing alt text, image dimensions, lazy loading and the main article image's high priority are preserved. Article SEO metadata uses the optimized cover URL.

`MEDIA_DRIVER=local` is the default until Cloudinary is configured. Local uploads also convert to real WebP (quality 82) and resize proportionally to the same 512px logo / 1600px article limits, without upscaling. This requires PHP GD with WebP support; transparent backgrounds are preserved and JPEG EXIF orientation is applied. A configured Cloudinary upload failure is shown as a form error; it does not silently save locally or replace an existing image. API credentials and raw provider errors are not returned to the browser. Removing/replacing an image detaches the old URL without deleting a potentially shared asset. A new upload is cleaned up if its database save fails.

Existing local images and URLs on other accounts remain accessible; they are not automatically uploaded, converted, or deleted. To move an existing image, re-upload its file after connecting Cloudinary. Pasting a third-party image URL does not upload it to your account. The reference account from another website is not a storage account you can upload to.

The Cloudinary Free tier has usage limits for storage, transformations, and delivery; monitor usage in your account. See https://cloudinary.com/pricing for current limits. WebP and smaller images help page performance but do not by themselves guarantee search rankings.
