<x-layout title="BL {{ $bl->bl_number ?? 'Borrador' }}">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h2>{{ $bl->bl_number ?? 'BL en borrador' }}</h2>
            <span style="padding:4px 12px; border-radius:10px; font-size:12px; font-weight:bold;
                background: {{ ['borrador'=>'#e2e5e9','emitido'=>'#eafaf0','anulado'=>'#fdecea','anulacion_solicitada'=>'#fff8e6'][$bl->status] }};
                color: {{ ['borrador'=>'#555','emitido'=>'#1a7a3c','anulado'=>'#c0392b','anulacion_solicitada'=>'#a67c00'][$bl->status] }};">
                {{ $bl->status === 'anulacion_solicitada' ? 'En revisión (anulación)' : ucfirst($bl->status) }}
            </span>
        </div>

        @if ($bl->status === 'anulacion_solicitada' && $pending = $bl->pendingVoidRequest())
            @if (auth()->user()->canApproveVoidRequestFrom($pending->requestedBy))
                <div style="background:#fff8e6; border:1.5px solid #e0b400; border-radius:6px; padding:14px 16px; margin-top:16px;">
                    <div style="font-weight:bold; font-size:14px; margin-bottom:4px;">⏳ Esta solicitud espera tu aprobación</div>
                    <div style="font-size:13px;">
                        <strong>{{ $pending->requestedBy->name }}</strong> solicitó anular este BL el {{ $pending->requested_at->format('d/m/Y H:i') }}.<br>
                        Motivo: {{ $pending->requested_reason }}
                    </div>
                    <div style="margin-top:12px; display:flex; gap:8px; align-items:center;">
                        <form method="POST" action="{{ route('bl.void-request.approve', $pending) }}" onsubmit="return confirm('¿Apruebas la anulación de este BL? Esta acción no se puede deshacer.');" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">✓ Aprobar anulación</button>
                        </form>
                        <form method="POST" action="{{ route('bl.void-request.reject', $pending) }}" style="display:inline-flex; gap:6px;">
                            @csrf
                            <input type="text" name="resolution_note" placeholder="Motivo del rechazo (opcional)" style="width:220px;">
                            <button type="submit" class="btn btn-danger">✗ Rechazar</button>
                        </form>
                    </div>
                </div>
            @else
                <div style="background:#fff8e6; border:1px solid #f0d98c; border-radius:6px; padding:12px 16px; margin-top:16px; font-size:13px;">
                    <strong>Anulación en revisión</strong> — solicitada por {{ $pending->requestedBy->name }} el {{ $pending->requested_at->format('d/m/Y H:i') }}.<br>
                    Motivo: {{ $pending->requested_reason }}<br>
                    <span style="color:#888;">Esperando aprobación de alguien con rango suficiente (no puedes aprobar tu propia solicitud, ni un auxiliar puede aprobar).</span>
                </div>
            @endif
        @endif

        <table style="width:100%; font-size:13px; margin-top:16px;">
            @if ($bl->port)
                <tr><td style="padding:5px 0; color:#666; width:180px;">Puerto</td><td>{{ $bl->port->code }} - {{ $bl->port->name }}</td></tr>
            @else
                {{-- Global no usa catálogo de puertos: lo escribe libre --}}
                <tr><td style="padding:5px 0; color:#666; width:180px;">Puerto de carga</td><td>{{ $bl->port_of_loading_text }}</td></tr>
                <tr><td style="padding:5px 0; color:#666;">Exporting carrier</td><td>{{ $bl->exporting_carrier }}</td></tr>
            @endif
            <tr><td style="padding:5px 0; color:#666;">Shipper</td><td style="white-space:pre-line;">{{ $bl->shipper }}</td></tr>
            <tr><td style="padding:5px 0; color:#666;">Consignee</td><td style="white-space:pre-line;">{{ $bl->consignee }}</td></tr>
            <tr><td style="padding:5px 0; color:#666;">Puerto de descarga</td><td>{{ $bl->port_of_discharge }}</td></tr>
            @if ($bl->port)
                <tr><td style="padding:5px 0; color:#666;">Buque / Viaje</td><td>{{ $bl->ocean_vessel_voy_no }}</td></tr>
            @else
                <tr><td style="padding:5px 0; color:#666;">On-carriage by</td><td>{{ $bl->on_carriage_by }}</td></tr>
            @endif
            <tr><td style="padding:5px 0; color:#666;">Flete</td><td>{{ ucfirst($bl->freight_type) }} — USD {{ number_format((float) ($bl->freight_total ?? $bl->total_charges ?? 0), 2) }}</td></tr>
            <tr><td style="padding:5px 0; color:#666;">Creado por</td><td>{{ $bl->createdBy->name ?? '—' }} el {{ $bl->created_at->format('d/m/Y H:i') }}</td></tr>
            @if ($bl->status === 'anulado')
                <tr><td style="padding:5px 0; color:#666;">Anulado por</td><td>{{ $bl->voidedBy->name ?? '—' }} el {{ optional($bl->voided_at)->format('d/m/Y H:i') }}</td></tr>
                <tr><td style="padding:5px 0; color:#666;">Motivo</td><td>{{ $bl->voided_reason }}</td></tr>
            @endif
        </table>

        <h3 style="margin-top:24px;">Líneas de carga</h3>
        <table style="width:100%; font-size:12px; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #e2e5e9;">
                    <th style="padding:6px 4px;">Marcas / Contenedor</th>
                    <th>Pkgs</th>
                    <th>Descripción</th>
                    <th>Peso bruto</th>
                    <th>Peso neto</th>
                    <th>CBM</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bl->cargoItems as $item)
                    <tr style="border-bottom:1px solid #f5f5f5;">
                        <td style="padding:6px 4px;">{{ $item->marks_and_numbers }}<br>{{ $item->container_seal_no }}</td>
                        <td>{{ $item->no_of_pkgs }}</td>
                        <td>{{ $item->description_of_goods }}</td>
                        <td>{{ $item->gross_weight }}</td>
                        <td>{{ $item->net_weight }}</td>
                        <td>{{ $item->measurement_cbm }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:24px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <a href="{{ route('bl.index') }}" class="btn btn-secondary">&larr; Volver al listado</a>

            @if ($bl->status === 'borrador')
                <a href="{{ route('bl.create', $bl) }}" class="btn btn-primary">Continuar editando</a>
            @endif

            @if ($bl->status === 'emitido')
                <a href="{{ route('bl.pdf', $bl) }}" target="_blank" class="btn btn-secondary">Ver PDF</a>
                <a href="{{ route('bl.pdf', $bl) }}?download=1" class="btn btn-primary">Descargar PDF</a>

                @can('bl.void.request')
                    <form method="POST" action="{{ route('bl.void-request', $bl) }}" onsubmit="return confirm('¿Confirmas solicitar la anulación de este BL?');" style="display:inline-flex; gap:6px;">
                        @csrf
                        <input type="text" name="reason" placeholder="Motivo de la solicitud" required style="width:220px;">
                        <button type="submit" class="btn btn-danger">Solicitar anulación</button>
                    </form>
                @endcan
            @endif
        </div>

        <h3 style="margin-top:28px;">Auditoría</h3>
        <table style="width:100%; font-size:12px; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #e2e5e9;">
                    <th style="padding:6px 4px;">Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bl->auditLogs as $log)
                    <tr style="border-bottom:1px solid #f5f5f5;">
                        <td style="padding:6px 4px;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user->name ?? '—' }}</td>
                        <td>{{ $log->labelForAction() }}</td>
                        <td>{{ $log->detail }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:12px; color:#888;">Sin actividad registrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>