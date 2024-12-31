<?php

namespace App\Http\Controllers;

use App\Models\AboutContent;
use App\Models\AboutImage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class NosotrosController extends Controller
{
    public function index()
    {
        // Incluir las imágenes en la consulta usando with()
        $contents = AboutContent::with('images')->get();

        return Inertia::render('User/Nosotros', [
            'contents' => $contents,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:card,container',
            'styles' => 'nullable|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $content = AboutContent::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'styles' => $validated['styles'] ?? null,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Generar nombre único para la imagen
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                
                // Mover la imagen a la carpeta public/about_images
                $image->move(public_path('about_images'), $uniqueName);
                
                AboutImage::create([
                    'about_content_id' => $content->id,
                    'image_path' => 'about_images/' . $uniqueName,
                    'alt_text' => $content->title
                ]);
            }
        }

        return redirect()->back()->with('success', 'Contenido creado exitosamente');
    }

    public function update(Request $request, AboutContent $content)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'styles' => 'nullable|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $content->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'styles' => $validated['styles'] ?? null,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                
                // Mover la imagen a la carpeta public/about_images
                $image->move(public_path('about_images'), $uniqueName);
                
                AboutImage::create([
                    'about_content_id' => $content->id,
                    'image_path' => 'about_images/' . $uniqueName,
                    'alt_text' => $content->title
                ]);
            }
        }

        return redirect()->back()->with('success', 'Contenido actualizado exitosamente');
    }

    public function destroyImage(AboutImage $image)
    {
        // Verificar si el archivo existe antes de intentar eliminarlo
        if (file_exists(public_path($image->image_path))) {
            unlink(public_path($image->image_path));
        }
        
        $image->delete();
        
        return response()->json(['message' => 'Imagen eliminada exitosamente']);
    }

    public function destroy(AboutContent $content)
    {
        // Eliminar todas las imágenes asociadas
        foreach ($content->images as $image) {
            if (file_exists(public_path($image->image_path))) {
                unlink(public_path($image->image_path));
            }
            $image->delete();
        }

        $content->delete();
        
        return redirect()->back()->with('success', 'Contenido eliminado exitosamente');
    }
}