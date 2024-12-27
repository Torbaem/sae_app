<?php

namespace App\Http\Controllers;

use App\Models\EditableContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EditableContentController extends Controller
{
    public function getContentBySection(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error en getContentBySection: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al obtener el contenido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createContent(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'page' => 'required|string',
                'section_key' => 'required|string',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video_url' => 'nullable|string',
                'video_type' => 'nullable|in:youtube,local,iframe',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('content_images', 'public');
            }

            $content = EditableContent::create([
                'page' => $validatedData['page'],
                'section_key' => $validatedData['section_key'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'] ?? null,
                'video_url' => $validatedData['video_url'] ?? null,
                'video_type' => $validatedData['video_type'] ?? null,
                'image_path' => $imagePath,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'is_active' => true,
                'item_order' => EditableContent::where('page', $validatedData['page'])
                    ->where('section_key', $validatedData['section_key'])
                    ->count() + 1
            ]);

            return response()->json($content, 201);
        } catch (\Exception $e) {
            Log::error('Error en createContent: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al crear el contenido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateContent(Request $request, $id)
    {
        try {
            $content = EditableContent::findOrFail($id);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video_url' => 'nullable|string',
                'video_type' => 'nullable|in:youtube,local,iframe',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('image')) {
                // Eliminar imagen anterior si existe
                if ($content->image_path) {
                    Storage::disk('public')->delete($content->image_path);
                }
                $validatedData['image_path'] = $request->file('image')->store('content_images', 'public');
            }

            $content->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'] ?? $content->description,
                'video_url' => $validatedData['video_url'] ?? $content->video_url,
                'video_type' => $validatedData['video_type'] ?? $content->video_type,
                'image_path' => $validatedData['image_path'] ?? $content->image_path,
                'updated_by' => Auth::id()
            ]);

            return response()->json($content);
        } catch (\Exception $e) {
            Log::error('Error en updateContent: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al actualizar el contenido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteContent($id)
    {
        try {
            $content = EditableContent::findOrFail($id);
            
            if ($content->image_path) {
                Storage::disk('public')->delete($content->image_path);
            }
            
            $content->delete();
            
            return response()->json(['message' => 'Contenido eliminado correctamente']);
        } catch (\Exception $e) {
            Log::error('Error en deleteContent: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al eliminar el contenido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleContent($id)
    {
        try {
            $content = EditableContent::findOrFail($id);
            $content->is_active = !$content->is_active;
            $content->save();
            
            return response()->json($content);
        } catch (\Exception $e) {
            Log::error('Error en toggleContent: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al cambiar el estado del contenido',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}