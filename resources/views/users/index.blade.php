<x-layout title="Usuarios">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2>Usuarios</h2>
            <a href="{{ route('users.create') }}" class="btn btn-success">+ Nuevo usuario</a>
        </div>

        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="text-align:left; border-bottom:2px solid #e2e5e9;">
                    <th style="padding:8px 4px;">Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:8px 4px;">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @foreach ($u->roles as $role)
                                <span style="background:#e8f0fe; color:#1e6fb8; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:bold;">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            <span style="padding:2px 8px; border-radius:10px; font-size:11px; font-weight:bold;
                                background: {{ $u->is_active ? '#eafaf0' : '#fdecea' }};
                                color: {{ $u->is_active ? '#1a7a3c' : '#c0392b' }};">
                                {{ $u->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td style="display:flex; gap:8px; align-items:center;">
                            <a href="{{ route('users.edit', $u) }}">Editar</a>

                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('users.toggle-active', $u) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary" style="padding:3px 10px; font-size:11px;">
                                        {{ $u->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('¿Eliminar a {{ $u->name }}? Solo funciona si no tiene BL asociados.');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding:3px 10px; font-size:11px;">Eliminar</button>
                                </form>
                            @else
                                <span style="font-size:11px; color:#999;">(tú)</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:16px;">{{ $users->links() }}</div>
    </div>
</x-layout>