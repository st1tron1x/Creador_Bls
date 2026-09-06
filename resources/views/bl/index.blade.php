<x-layout title="BL - Listado">
    <div class="card">
        <h2>Bills of Lading</h2>

        <form method="GET" style="margin-bottom:20px;">
            <div class="grid-2">
                <div class="field">
                    <label>Nº de BL</label>
                    <input type="text" name="bl_number" value="{{ request('bl_number') }}">
                </div>
                <div class="field">
                    <label>Shipper</label>
                    <input type="text" name="shipper" value="{{ request('shipper') }}">
                </div>
                <div class="field">
                    <label>Consignee</label>
                    <input type="text" name="consignee" value="{{ request('consignee') }}">
                </div>
                <div class="field">
                    <label>Puerto</label>
                    <select name="port_id">
                        <option value="">-- Todos --</option>
                        @foreach ($ports as $port)
                            <option value="{{ $port->id }}" @selected(request('port_id') == $port->id)>
                                {{ $port->code }} - {{ $port->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Estado</label>
                    <select name="status">
                        <option value="">-- Todos --</option>
                        <option value="borrador" @selected(request('status') === 'borrador')>Borrador</option>
                        <option value="emitido" @selected(request('status') === 'emitido')>Emitido</option>
                        <option value="anulacion_solicitada" @selected(request('status') === 'anulacion_solicitada')>Anulación solicitada</option>
                        <option value="anulado" @selected(request('status') === 'anulado')>Anulado</option>
                    </select>
                </div>
                <div class="field">
                    <label>Desde</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="field">
                    <label>Hasta</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="{{ route('bl.index') }}" class="btn btn-secondary">Limpiar</a>
            <a href="{{ route('bl.create') }}" class="btn btn-success" style="float:right;">+ Nuevo BL</a>
        </form>

        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="text-align:left; border-bottom:2px solid #e2e5e9;">
                    <th style="padding:8px 4px;">Nº BL</th>
                    <th>Puerto</th>
                    <th>Shipper</th>
                    <th>Consignee</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bills as $bl)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:8px 4px;">{{ $bl->bl_number ?? '(borrador)' }}</td>
                        <td>{{ $bl->port->code }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($bl->shipper, 30) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($bl->consignee, 30) }}</td>
                        <td>
                            <span style="padding:2px 8px; border-radius:10px; font-size:11px; font-weight:bold;
                                background: {{ ['borrador'=>'#e2e5e9','emitido'=>'#eafaf0','anulado'=>'#fdecea','anulacion_solicitada'=>'#fff8e6'][$bl->status] }};
                                color: {{ ['borrador'=>'#555','emitido'=>'#1a7a3c','anulado'=>'#c0392b','anulacion_solicitada'=>'#a67c00'][$bl->status] }};">
                                {{ ucfirst(str_replace('_', ' ', $bl->status)) }}
                            </span>
                        </td>
                        <td>{{ $bl->created_at->format('d/m/Y') }}</td>
                        <td><a href="{{ $bl->status === 'borrador' ? route('bl.create', $bl) : route('bl.show', $bl) }}">
                            {{ $bl->status === 'borrador' ? 'Continuar editando' : 'Ver' }} &rarr;
                        </a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="padding:20px; text-align:center; color:#888;">No hay BL que coincidan con la búsqueda.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:16px;">{{ $bills->links() }}</div>
    </div>
</x-layout>
