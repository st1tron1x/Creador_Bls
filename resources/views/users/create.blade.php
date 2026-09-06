<x-layout title="Nuevo usuario">
    <div class="card" style="max-width:500px;">
        <h2>Nuevo usuario</h2>

        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="field">
                <label>Nombre</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>Rol</label>
                <select name="role" required>
                    <option value="">-- Selecciona --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                @error('role') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>Contraseña</label>
                <input type="password" name="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label>Confirmar contraseña</label>
                <input type="password" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-success">Crear usuario</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</x-layout>