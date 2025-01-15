<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
            'isAdmin' => ['boolean']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'isAdmin' => $request->isAdmin ?? false
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        // Reglas base de validación
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ];
    
        // Validación condicional para la contraseña
        if ($request->filled('password')) {
            $rules['password'] = ['required', Password::defaults()];
            $rules['password_confirmation'] = ['required', 'same:password'];
        }
    
        $validated = $request->validate($rules);
    
        // Preparar datos para actualización
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'isAdmin' => $request->boolean('isAdmin') // Aquí está el cambio importante
        ];
    
        // Actualizar contraseña solo si se proporcionó una nueva
        if (isset($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }
    
        $user->update($updateData);
    
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}