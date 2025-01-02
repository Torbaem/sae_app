<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return Inertia::render('Admin/Brand/Index', [
            'brands' => $brands
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands'
        ]);

        Brand::create($request->all());
        return redirect()->back()->with('success', 'Marca creada exitosamente');
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $id
        ]);

        $brand->update($request->all());
        return redirect()->back()->with('success', 'Marca actualizada exitosamente');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();
        return redirect()->back()->with('success', 'Marca eliminada exitosamente');
    }
}