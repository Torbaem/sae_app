<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use App\Models\ProducerImage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HistoriasController extends Controller
{
    public function index()
    {
        $producers = Producer::with('images')->get();
        return Inertia::render('User/Historias', [
            'producers' => $producers,
        ]);
    }
    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'youtube_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Crear nuevo productor
        $producer = Producer::create([
            'name' => $request->name,
            'content' => $request->content,
            'youtube_url' => $request->youtube_url,
        ]);
    
        // Manejo de la imagen similar al ProductController
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Generar nombre único para la imagen
            $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            // Guardar la imagen en la carpeta pública
            $image->move('producer_images', $uniqueName);
            
            // Crear el registro en la tabla producer_images
            ProducerImage::create([
                'producer_id' => $producer->id,
                'image_path' => 'producer_images/' . $uniqueName,
            ]);
        }
    
        return redirect()->route('historias')->with('success', 'Productor creado exitosamente.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'youtube_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $producer = Producer::findOrFail($id);
        
        $producer->update([
            'name' => $request->name,
            'content' => $request->content,
            'youtube_url' => $request->youtube_url,
        ]);
    
        // Manejo de la imagen
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move('producer_images', $uniqueName);
    
            // Crear nuevo registro de imagen
            ProducerImage::create([
                'producer_id' => $producer->id,
                'image_path' => 'producer_images/' . $uniqueName,
            ]);
        }
    
        return redirect()->route('historias')->with('success', 'Productor actualizado exitosamente.');
    }


    

    public function deleteImage($id)
{
    $image = ProducerImage::findOrFail($id);
    
    // Eliminar el archivo físico
    if (file_exists(public_path($image->image_path))) {
        unlink(public_path($image->image_path));
    }
    
    // Eliminar el registro de la base de datos
    $image->delete();
    
    return response()->json(['message' => 'Imagen eliminada exitosamente']);
}
    
    public function destroy($id)
    {
        $producer = Producer::findOrFail($id);
    
        // Eliminar las imágenes físicas y sus registros
        foreach ($producer->images as $image) {
            if (file_exists(public_path($image->image_path))) {
                unlink(public_path($image->image_path));
            }
            $image->delete();
        }
    
        $producer->delete();
    
        return redirect()->route('historias')->with('success', 'Productor eliminado exitosamente.');
    }
}
