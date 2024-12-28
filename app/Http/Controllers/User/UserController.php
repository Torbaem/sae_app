<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\HeroContent;
use App\Models\HeroImage;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $heroContent = HeroContent::with('images')->first();
        $products = Product::with('brand', 'category', 'product_images')
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();
        
        return Inertia::render('User/Index', [
            'heroContent' => $heroContent,
            'products' => $products,
            'canLogin' => app('router')->has('login'),
            'canRegister' => app('router')->has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    public function update(Request $request)  // Removemos el parámetro $id
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
    
        // Obtener el primer registro de HeroContent (o crear uno si no existe)
        $heroContent = HeroContent::firstOrFail();
        
        // Actualizar contenido de texto
        $heroContent->update([
            'title' => $request->title,
            'description' => $request->description,
            'url' => $request->url,
        ]);
    
        // Manejar imagen de fondo
        if ($request->hasFile('background_image')) {
            $this->handleImageUpload(
                $request->file('background_image'),
                'background_image',
                $heroContent
            );
        }
    
        // Manejar imagen de producto
        if ($request->hasFile('product_image')) {
            $this->handleImageUpload(
                $request->file('product_image'),
                'product_image',
                $heroContent
            );
        }
    
        return response()->json([
            'message' => 'Hero actualizado exitosamente',
            'heroContent' => $heroContent->load('images'),
        ]);
    }

    private function handleImageUpload($image, $type, $heroContent)
    {
        // Generar nombre único para la imagen
        $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        
        // Mover la imagen a la carpeta hero_images
        $image->move('hero_images', $uniqueName);
        
        // Eliminar imagen anterior del mismo tipo si existe
        $existingImage = $heroContent->images()->where('type', $type)->first();
        if ($existingImage) {
            if (file_exists(public_path($existingImage->image_path))) {
                unlink(public_path($existingImage->image_path));
            }
            $existingImage->delete();
        }
        
        // Crear nuevo registro de imagen
        HeroImage::create([
            'hero_content_id' => $heroContent->id,
            'image_path' => 'hero_images/' . $uniqueName,
            'type' => $type
        ]);
    }

    public function deleteImage($id)
    {
        $image = HeroImage::findOrFail($id);
        
        // Eliminar archivo físico
        if (file_exists(public_path($image->image_path))) {
            unlink(public_path($image->image_path));
        }
        
        // Eliminar registro de la base de datos
        $image->delete();
        
        return response()->json(['message' => 'Imagen eliminada exitosamente']);
    }
}