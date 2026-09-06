<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
        ];

        // Solo admin puede cambiar su propio correo y rol desde aquí; el resto de roles no.
        if ($user->hasRole('admin')) {
            $rules['email'] = 'required|email|unique:users,email,' . $user->id;
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null; // asignación directa: 'phone' no está en la lista fillable del modelo
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Datos actualizados.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('profile.edit')->with('success', 'Contraseña actualizada.');
    }
}