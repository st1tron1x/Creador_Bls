<x-layout title="Notificaciones">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2>Notificaciones</h2>
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Marcar todas como leídas</button>
            </form>
        </div>

        @forelse ($notifications as $n)
            <a href="{{ route('notifications.mark-read', $n->id) }}" style="text-decoration:none; color:inherit;">
                <div style="padding:12px 14px; border-radius:4px; margin-bottom:8px; border:1px solid #e2e5e9;
                    background: {{ $n->read_at ? '#fff' : '#fff8e6' }};">
                    <div style="font-size:13px; font-weight:{{ $n->read_at ? 'normal' : 'bold' }};">
                        {{ $n->data['message'] }}
                    </div>
                    <div style="font-size:11px; color:#888; margin-top:4px;">
                        Motivo: {{ $n->data['reason'] }} — {{ $n->created_at->diffForHumans() }}
                    </div>
                </div>
            </a>
        @empty
            <p style="color:#888; font-size:13px;">No tienes notificaciones.</p>
        @endforelse

        <div style="margin-top:16px;">{{ $notifications->links() }}</div>
    </div>
</x-layout>
