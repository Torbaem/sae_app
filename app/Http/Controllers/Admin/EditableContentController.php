<?php

namespace App\Http\Controllers;

use App\Models\EditableContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EditableContentController extends Controller
{
    public function getContentBySection(Request $request)
    {
        $validatedData = $request->validate([
            'page' => 'required|string',
            'section_key' => 'required|string'
        ]);

        $contents = EditableContent::where('page', $validatedData['page'])
            ->where('section_key', $validatedData['section_key'])
            ->where('is_active', true)
            ->orderBy('item_order')
            ->get();

        return response()->json([
            'contents' => $contents,
            'isEmpty' => $contents->isEmpty()
        ]);
    }

    public function createContent(Request $request)
    {
        $validatedData = $request->validate([
            'page' => 'required|string',
            'section_key' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string',
            'video_type' => 'nullable|in:youtube,local,iframe',
        ]);

        // Manejar subida de imagen
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('content_images', 'public');
        }

        $content = EditableContent::create([
            'page' => $validatedData['page'],
            'section_key' => $validatedData['section_key'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'video_url' => $validatedData['video_url'],
            'video_type' => $validatedData['video_type'],
            'image_path' => $imagePath,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'is_active' => true,
            'item_order' => EditableContent::where('page', $validatedData['page'])
                ->where('section_key', $validatedData['section_key'])
                ->count() + 1
        ]);

        return response()->json($content, 201);
    }
}