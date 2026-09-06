<?php

namespace App\Http\Controllers;

use App\Models\BillOfLading;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected array $assignableRoles = ['admin', 'gerencia', 'coordinacion', 'auxiliar'];

    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create', ['roles' => $this->assignableRoles]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', $this->assignableRoles),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->email_verified_at = now(); // asignación directa: no está en la lista fillable del modelo
        $user->save();

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', "Usuario {$user->name} creado con rol {$validated['role']}.");
    }

    public function edit(User $user)
    {
        return view('users.edit', ['user' => $user, 'roles' => $this->assignableRoles]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:' . implode(',', $this->assignableRoles),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', "Usuario {$user->name} actualizado.");
    }

    public function toggleActive(User $user)
    {
        abort_if($user->id === Auth::id(), 422, 'No puedes desactivar tu propia cuenta.');

        $user->is_active = ! $user->is_active; // asignación directa: 'is_active' no está en la lista fillable del modelo
        $user->save();

        $estado = $user->is_active ? 'activado' : 'desactivado';

        return redirect()->route('users.index')->with('success', "Usuario {$user->name} {$estado}.");
    }

    public function destroy(User $user)
    {
        abort_if($user->id === Auth::id(), 422, 'No puedes eliminar tu propia cuenta.');

        $tieneRegistros = BillOfLading::where('created_by', $user->id)
            ->orWhere('updated_by', $user->id)
            ->orWhere('voided_by', $user->id)
            ->exists();

        if ($tieneRegistros) {
            return redirect()->route('users.index')
                ->with('error', "No se puede eliminar a {$user->name}: tiene BL asociados en el sistema. Desactívalo en su lugar.");
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}