<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Store;
use App\Support\AdminResources;
use App\Support\ImageUpload;
use App\Support\ReviewContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function previewReview(int $id)
    {
        return response()->view('pages.review-show', [
            'review' => Review::with(['store', 'category'])->findOrFail($id),
            'related' => collect(),
            'robots' => 'noindex,nofollow',
        ])->header('X-Robots-Tag', 'noindex, nofollow')->header('Cache-Control', 'private, no-store');
    }

    public function dashboard()
    {
        $stats = collect(AdminResources::all())->map(fn ($definition) => [
            ...$definition,
            'total' => $definition['model']::count(),
            'published' => $definition['model']::where('is_active', true)->count(),
        ]);

        return view('admin.dashboard', [
            'stats' => $stats,
            'clicks' => Coupon::sum('clicks'),
            'recent' => Coupon::with('store')->latest('updated_at')->limit(6)->get(),
        ]);
    }

    public function index(Request $request, string $resource)
    {
        $definition = AdminResources::get($resource);
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:255'], 'status' => ['nullable', Rule::in(['published', 'hidden'])]]);
        $query = $definition['model']::query();
        if ($term = trim($filters['q'] ?? '')) {
            $query->where($definition['title'], 'like', '%'.addcslashes($term, '%_\\').'%');
        }
        if ($status = $filters['status'] ?? null) {
            $query->where('is_active', $status === 'published');
        }

        return view('admin.index', [
            'resource' => $resource,
            'definition' => $definition,
            'items' => $query->latest('updated_at')->paginate(15)->withQueryString(),
        ]);
    }

    public function create(string $resource)
    {
        $definition = AdminResources::get($resource);
        $item = new $definition['model'];
        $item->is_active = true;
        $item->sort_order = 0;
        $item->accent = 'emerald';
        $item->type = $resource === 'reviews' ? 'review' : 'code';

        return $this->form($resource, $definition, $item);
    }

    public function edit(string $resource, int $id)
    {
        $definition = AdminResources::get($resource);

        return $this->form($resource, $definition, $definition['model']::findOrFail($id));
    }

    private function form(string $resource, array $definition, $item)
    {
        return view('admin.form', [
            'resource' => $resource,
            'definition' => $definition,
            'item' => $item,
            'stores' => Store::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, string $resource)
    {
        return $this->save($request, $resource);
    }

    public function update(Request $request, string $resource, int $id)
    {
        return $this->save($request, $resource, $id);
    }

    private function save(Request $request, string $resource, ?int $id = null)
    {
        $definition = AdminResources::get($resource);
        $item = $id ? $definition['model']::findOrFail($id) : new $definition['model'];
        if (! $request->filled('slug')) {
            $title = $request->input($definition['title']);
            if (is_string($title)) {
                $request->merge(['slug' => Str::slug($title)]);
            }
        }

        $rules = [];
        foreach ($definition['fields'] as $field => $spec) {
            $type = explode('|', $spec)[1] ?? 'text';
            $rules[$field] = match ($type) {
                'checkbox' => ['sometimes', 'boolean'],
                'image', 'url' => ['nullable', 'url:http,https', 'max:255'],
                'textarea' => ['nullable', 'string', 'max:50000'],
                'datetime-local' => ['nullable', 'date'],
                'number' => ['nullable', 'integer', 'min:0', 'max:1000000'],
                'store' => ['nullable', 'integer', 'exists:stores,id'],
                'category' => ['nullable', 'integer', 'exists:categories,id'],
                'categories' => ['sometimes', 'array'],
                'offer_type' => ['required', Rule::in(['code', 'deal'])],
                'article_type' => ['required', Rule::in(['review', 'guide'])],
                default => ['nullable', 'string', 'max:255'],
            };
        }
        $rules[$definition['title']] = ['required', 'string', 'max:255'];
        $rules['slug'] = ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique($item->getTable(), 'slug')->ignore($item->getKey())];
        if ($resource === 'coupons') {
            $rules['store_id'] = ['required', 'integer', 'exists:stores,id'];
            $rules['code'] = ['nullable', 'required_if:type,code', 'string', 'max:255'];
            if ($request->filled('starts_at')) {
                $rules['expires_at'][] = 'after_or_equal:starts_at';
            }
        }
        if ($resource === 'reviews') {
            $rules['content'] = ['required', 'string', 'max:200000'];
            $rules['excerpt'] = ['nullable', 'string', 'max:500'];
            $rules['rating'] = ['nullable', 'numeric', 'between:0,5'];
            $rules['affiliate_url'] = ['nullable', 'url:http,https', 'max:2048'];
            $rules['affiliate_label'] = ['nullable', 'string', 'max:80'];
        }
        if ($resource === 'categories') {
            $rules['accent'] = ['nullable', 'string', 'max:30'];
        }
        if ($resource === 'stores') {
            $rules['category_ids.*'] = ['integer', 'distinct', 'exists:categories,id'];
        }

        $imageField = match ($resource) {
            'stores' => 'logo',
            'reviews' => 'image',
            default => null
        };
        if ($imageField) {
            $rules[$imageField.'_upload'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'];
            $rules[$imageField.'_remove'] = ['sometimes', 'boolean'];
        }
        if ($resource === 'reviews') {
            $rules['content_format'] = ['sometimes', Rule::in(['text', 'html'])];
        }
        $data = $request->validate($rules);
        if ($resource === 'reviews' && ($data['content_format'] ?? $item->content_format ?? 'text') === 'html') {
            $data['content'] = ReviewContent::sanitize($data['content']);
            if (preg_replace('/[\s\x{00A0}]+/u', '', html_entity_decode(strip_tags($data['content']), ENT_QUOTES | ENT_HTML5, 'UTF-8')) === '' && ! preg_match('/<img[^>]+src="[^"]+"/i', $data['content'])) {
                throw ValidationException::withMessages(['content' => 'Please enter article content.']);
            }
        }
        $newImage = null;
        if ($imageField) {
            $data[$imageField] = $request->boolean($imageField.'_remove') ? null : (($data[$imageField] ?? null) ?: $item->{$imageField});
            if ($request->hasFile($imageField.'_upload')) {
                $newImage = app(ImageUpload::class)->store($request->file($imageField.'_upload'), $resource, $data[$definition['title']], $imageField.'_upload');
                $data[$imageField] = $newImage;
            }
            unset($data[$imageField.'_upload'], $data[$imageField.'_remove']);
        }
        foreach ($definition['fields'] as $field => $spec) {
            if (str_ends_with($spec, '|checkbox')) {
                $data[$field] = $request->boolean($field);
            }
        }
        if ($resource === 'reviews' && $data['is_active']) {
            $data['published_at'] ??= now();
        }
        if (isset($definition['fields']['sort_order'])) {
            $data['sort_order'] ??= 0;
        }
        if ($resource === 'categories') {
            $data['accent'] ??= 'emerald';
        }
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        try {
            DB::transaction(function () use ($item, $data, $resource, $categoryIds) {
                $item->fill($data)->save();
                if ($resource === 'stores') {
                    $item->categories()->sync($categoryIds);
                }
            });
        } catch (\Throwable $error) {
            if ($newImage) {
                app(ImageUpload::class)->discardNewUpload($newImage);
            }
            throw $error;
        }

        return redirect()->route('admin.edit', [$resource, $item->id])->with('status', ucfirst($definition['singular']).' saved successfully.');
    }

    public function confirmDelete(string $resource, int $id)
    {
        $definition = AdminResources::get($resource);

        return view('admin.delete', ['resource' => $resource, 'definition' => $definition, 'item' => $definition['model']::findOrFail($id)]);
    }

    public function destroy(string $resource, int $id)
    {
        $definition = AdminResources::get($resource);
        $item = $definition['model']::findOrFail($id);
        if (in_array($resource, ['stores', 'categories']) && ($item->coupons()->exists() || $item->reviews()->exists() || ($resource === 'categories' && $item->stores()->exists()))) {
            return back()->withErrors(['delete' => 'This item still has linked content. Unpublish it, or move the linked content before deleting.']);
        }
        $item->delete();

        return redirect()->route('admin.index', $resource)->with('status', ucfirst($definition['singular']).' deleted.');
    }
}
