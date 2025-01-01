<?php

namespace App\Http\Controllers;

use App\Models\ContactContent;
use App\Models\ContactImage;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ContactanosController extends Controller
{
    public function index()
    {
        // Incluir las imágenes en la consulta usando with()
        $contents = ContactContent::with('images')->get();

        return Inertia::render('User/Contactanos', [
            'contents' => $contents,
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Validación para los mensajes de contacto
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string'
            ]);

            ContactMessage::create($validated);

            return redirect()->back()->with('success', 'Mensaje enviado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo.']);
        }
    }

    // Método para crear contenido editable
    public function createContent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'map_url' => 'nullable|string',
            'type' => 'required|in:card,container',
            'styles' => 'nullable|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Crear el contenido
        $content = ContactContent::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'map_url' => $validated['map_url'],
            'type' => $validated['type'],
            'styles' => $validated['styles'] ?? null,
        ]);

        // Si hay imágenes, procesarlas
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // Crear el directorio si no existe
                if (!file_exists(public_path('contact_images'))) {
                    mkdir(public_path('contact_images'), 0777, true);
                }

                $image->move(public_path('contact_images'), $uniqueName);

                ContactImage::create([
                    'contact_content_id' => $content->id,
                    'image_path' => 'contact_images/' . $uniqueName,
                    'alt_text' => $content->title
                ]);
            }
        }

        return redirect()->back()->with('success', 'Contenido creado exitosamente');
    }

    // Método para actualizar el contenido editable
    public function update(Request $request, ContactContent $content)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'map_url' => 'nullable|string',
            'type' => 'required|in:card,container',
            'styles' => 'nullable|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $content->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'map_url' => $validated['map_url'] ?? $content->map_url,
            'type' => $validated['type'],
            'styles' => $validated['styles'] ?? null,
        ]);

        // Si hay nuevas imágenes, procesarlas
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // Crear el directorio si no existe
                if (!file_exists(public_path('contact_images'))) {
                    mkdir(public_path('contact_images'), 0777, true);
                }

                $image->move(public_path('contact_images'), $uniqueName);

                ContactImage::create([
                    'contact_content_id' => $content->id,
                    'image_path' => 'contact_images/' . $uniqueName,
                    'alt_text' => $content->title
                ]);
            }
        }

        return redirect()->back()->with('success', 'Contenido actualizado exitosamente');
    }

    // Método para eliminar una imagen
    public function destroyImage(ContactImage $image)
    {
        if (file_exists(public_path($image->image_path))) {
            unlink(public_path($image->image_path));
        }

        $image->delete();

        return response()->json(['message' => 'Imagen eliminada exitosamente']);
    }

    // Método para eliminar el contenido
    public function destroy(ContactContent $content)
    {
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
