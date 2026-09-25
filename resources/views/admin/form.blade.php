@extends('layouts.admin')
@section('title', ($item->exists ? 'Edit ' : 'Add ').$definition['singular'])
@section('content')
    <a href="{{ route('admin.index', $resource) }}" class="text-sm font-bold text-emerald-800">← {{ $definition['label'] }}</a>
    <h1 class="mt-5 text-3xl font-black tracking-tight">{{ $item->exists ? 'Edit' : 'Add' }} {{ $definition['singular'] }}</h1>
    <p class="mt-3 text-stone-500">{{ $item->exists ? 'Update the details below, then save your changes.' : 'Create a new entry for your content library.' }}</p>
    <form action="{{ $item->exists ? route('admin.update', [$resource, $item->id]) : route('admin.store', $resource) }}" method="POST" enctype="multipart/form-data" class="mt-7 max-w-4xl rounded-2xl border border-stone-200 bg-white p-6 sm:p-8">
        @csrf
        @if($item->exists) @method('PUT') @endif
        <div class="grid gap-6 sm:grid-cols-2">
            @foreach($definition['fields'] as $field => $spec)
                @php
                    [$label, $type] = array_pad(explode('|', $spec), 2, 'text');
                    $value = old($field, $item->{$field});
                    if ($value instanceof \DateTimeInterface) $value = $value->format('Y-m-d\TH:i');
                    $required = $field === $definition['title'] || ($field === 'store_id' && $resource === 'coupons') || $field === 'content';
                @endphp
                <div class="{{ in_array($type, ['textarea', 'categories', 'image']) ? 'sm:col-span-2' : '' }}">
                    @if($type === 'checkbox')
                        <input type="hidden" name="{{ $field }}" value="0">
                        <label class="flex items-center gap-3 rounded-xl border border-stone-200 px-4 py-3 text-sm font-semibold"><input type="checkbox" name="{{ $field }}" value="1" @checked($value) class="size-4 accent-emerald-800">{{ $label }}</label>
                    @else
                        <label for="{{ $field }}" class="admin-label">{{ $label }}@if($required)<span class="text-red-600"> *</span>@endif</label>
                        @if($type === 'image')
                            <div data-image-picker class="rounded-xl border border-dashed border-stone-300 p-4">
                                <img data-image-preview src="{{ \App\Support\Media::url($item->{$field}) }}" alt="Current {{ $label }}" class="mb-4 h-36 max-w-full rounded-lg object-contain" @if(!\App\Support\Media::url($item->{$field})) hidden @endif>
                                <input id="{{ $field }}" name="{{ $field }}_upload" type="file" accept="image/jpeg,image/png,image/webp" data-image-input class="admin-input">
                                <p class="mt-2 text-xs text-stone-500">JPG, PNG or WebP · Up to 5 MB · Maximum 6000 × 6000 px. Select again if the form has validation errors.</p>
                                @if(config('media.driver') === 'cloudinary')<p class="mt-2 text-xs text-emerald-800">Uploaded images are saved to Cloudinary as optimized WebP.</p>@else<p class="mt-2 text-xs text-emerald-800">Uploaded images are resized and saved as WebP on this server.</p>@endif
                                <label class="admin-label mt-4" for="{{ $field }}_url">Or use an image URL</label>
                                <input id="{{ $field }}_url" name="{{ $field }}" type="url" value="{{ old($field, str_starts_with($item->{$field} ?? '', 'uploads/') ? '' : $item->{$field}) }}" class="admin-input" placeholder="https://…">
                                <p class="mt-2 text-xs text-stone-500">A selected file takes priority over the URL.</p>
                                @if($item->{$field})<label class="mt-3 flex items-center gap-2 text-sm"><input type="checkbox" name="{{ $field }}_remove" value="1" @checked(old($field.'_remove'))> Remove current image</label>@endif
                                @error($field.'_upload')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                            </div>
                        @elseif($type === 'textarea')
                            <textarea id="{{ $field }}" name="{{ $field }}" rows="{{ $field === 'content' ? 14 : 3 }}" class="admin-input" @if($field === 'content') data-review-editor data-upload-url="{{ route('admin.images.store') }}" data-format="{{ old('content_format', $item->content_format ?? 'text') }}" @endif @required($required)>{{ $field === 'content' && old('content_format', $item->content_format) === 'html' ? \App\Support\ReviewContent::sanitize($value ?? '') : $value }}</textarea>
                            @if($field === 'content')<input type="hidden" name="content_format" value="{{ old('content_format', $item->content_format ?? 'text') }}">@endif
                        @elseif(in_array($type, ['store', 'category', 'offer_type', 'article_type', 'categories']))
                            @php
                                $options = match($type) {
                                    'store' => $stores->pluck('name', 'id'),
                                    'category', 'categories' => $categories->pluck('name', 'id'),
                                    'offer_type' => ['code' => 'Coupon code', 'deal' => 'Online deal'],
                                    'article_type' => ['review' => 'Review', 'guide' => 'Guide'],
                                };
                                $selected = $type === 'categories' ? old('category_ids', session()->hasOldInput() ? [] : ($item->exists ? $item->categories->modelKeys() : [])) : [$value];
                            @endphp
                            <select id="{{ $field }}" name="{{ $field }}{{ $type === 'categories' ? '[]' : '' }}" class="admin-input" @if($type === 'categories') multiple size="5" @endif @required($required)>
                                @if(in_array($type, ['store', 'category']))<option value="">{{ $required ? 'Choose an option' : 'None' }}</option>@endif
                                @foreach($options as $optionValue => $optionLabel)<option value="{{ $optionValue }}" @selected(in_array($optionValue, (array) $selected))>{{ $optionLabel }}</option>@endforeach
                            </select>
                            @if($type === 'categories')<p class="mt-2 text-xs text-stone-500">Hold Command (Mac) or Ctrl (Windows) to select multiple categories.</p>@endif
                        @else
                            <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ $value }}" class="admin-input" @required($required) @if($type === 'number') min="0" step="{{ $field === 'rating' ? '0.1' : '1' }}" @endif @if($field === 'rating') max="5" @endif>
                        @endif
                        @if($field === 'slug')<p class="mt-2 text-xs text-stone-500">Leave blank to generate from the name or title. Use lowercase letters, numbers and hyphens.</p>@endif
                        @if($field === 'affiliate_url')<p class="mt-2 text-xs text-stone-500">Paste your full affiliate URL, including tracking parameters. Shows a highlighted offer above and below the article. Leave empty to hide it.</p>@endif
                        @if($field === 'content')<p class="mt-2 text-xs text-stone-500">Add headings, images with captions, comparison tables and quotes. Double-click an image or table to edit or remove it.</p>@endif
                    @endif
                    @error($field)<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
        <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-stone-100 pt-6"><button class="admin-primary">Save {{ $definition['singular'] }}</button>@if($resource === 'reviews' && $item->exists)<a class="admin-secondary" href="{{ route('admin.reviews.preview', $item->id) }}" target="_blank" rel="noopener">Preview saved article ↗</a>@endif<a href="{{ route('admin.index', $resource) }}" class="admin-secondary">Cancel</a>@if($item->exists)<a href="{{ route('admin.confirm-delete', [$resource, $item->id]) }}" class="ml-auto text-sm font-semibold text-red-700">Delete {{ $definition['singular'] }}</a>@endif</div>
    </form>
@endsection
