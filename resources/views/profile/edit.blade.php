<x-layout title="Mi perfil">
    <div class="card" style="max-width:500px; margin-bottom:20px;">
        <h2>Mis datos</h2>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="field">
                <label>Nombre completo</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>Teléfono</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                @error('phone') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" value="{{ $user->email }}" disabled style="background:#f4f5f7; color:#888;">
                <div style="font-size:11px; color:#888; margin-top:3px;">
                    El correo no se puede cambiar aquí. Si necesitas actualizarlo, pídeselo a un administrador.
                </div>
            </div>
            <div class="field">
                <label>Rol</label>
                <input type="text" value="{{ ucfirst($user->getRoleNames()->first()) }}" disabled style="background:#f4f5f7; color:#888;">
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>

    <div class="card" style="max-width:500px;">
        <h2>Cambiar contraseña</h2>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')
            <div class="field">
                <label>Contraseña actual</label>
                <input type="password" name="current_password" required>
                @error('current_password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>Nueva contraseña</label>
                <input type="password" name="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
        </form>
    </div>
</x-layout>
