<?php

namespace App\Http\Controllers;

use App\Support\ImageUpload;
use App\Support\Media;
use Illuminate\Http\Request;

class EditorImageController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate(['image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000']]);
        $file = $request->file('image');
        $path = app(ImageUpload::class)->store($file, 'reviews', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), 'image');

        return response()->json(['url' => str_starts_with($path, 'uploads/') ? '/storage/'.$path : Media::url($path, 1600)], 201);
    }
}
