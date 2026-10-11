<x-layout title="Dashboard">
    <div class="card">
        <h2>Hola, {{ auth()->user()->name }}</h2>
        <p style="color:#666; font-size:13px;">Rol: {{ auth()->user()->getRoleNames()->first() }}</p>

        <div style="display:flex; gap:10px; margin:20px 0;">
            <a href="{{ route('bl.create') }}" class="btn btn-success">+ Nuevo BL</a>
            <a href="{{ route('bl.index') }}" class="btn btn-primary">Ver todos los BL</a>
            @can('users.manage')
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Usuarios</a>
            @endcan
        </div>

        <div class="grid-2" style="margin-bottom:24px;">
            <div style="background:#e8f0fe; border-radius:6px; padding:14px; text-align:center;">
                <div style="font-size:26px; font-weight:bold; color:#1e6fb8;">{{ $misBl }}</div>
                <div style="font-size:12px; color:#888;">Mis BL (creados por mí)</div>
            </div>
            <div style="background:#f4f5f7; border-radius:6px; padding:14px; text-align:center;">
                <div style="font-size:26px; font-weight:bold; color:#555;">{{ $emitidosEsteMes }}</div>
                <div style="font-size:12px; color:#888;">Emitidos este mes</div>
            </div>
            <div style="background:#f4f5f7; border-radius:6px; padding:14px; text-align:center;">
                <div style="font-size:26px; font-weight:bold; color:#555;">{{ $counts['borrador'] }}</div>
                <div style="font-size:12px; color:#888;">Borradores</div>
            </div>
            <div style="background:#eafaf0; border-radius:6px; padding:14px; text-align:center;">
                <div style="font-size:26px; font-weight:bold; color:#1a7a3c;">{{ $counts['emitido'] }}</div>
                <div style="font-size:12px; color:#888;">Emitidos (activos)</div>
            </div>
            <div style="background:#fff8e6; border-radius:6px; padding:14px; text-align:center;">
                <div style="font-size:26px; font-weight:bold; color:#a67c00;">{{ $counts['anulacion_solicitada'] }}</div>
                <div style="font-size:12px; color:#888;">Anulación solicitada</div>
            </div>
            <div style="background:#fdecea; border-radius:6px; padding:14px; text-align:center;">
                <div style="font-size:26px; font-weight:bold; color:#c0392b;">{{ $counts['anulado'] }}</div>
                <div style="font-size:12px; color:#888;">Anulados</div>
            </div>
        </div>

        @if ($porPuerto->isNotEmpty())
            <h3>BL por puerto</h3>
            <table style="width:100%; font-size:13px; margin-bottom:24px;">
                @foreach ($porPuerto as $port)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:6px 0; width:60px;"><strong>{{ $port->code }}</strong></td>
                        <td>{{ $port->name }}</td>
                        <td style="text-align:right; font-weight:bold;">{{ $port->bills_of_lading_count }}</td>
                    </tr>
                @endforeach
            </table>
        @endif

        @if ($borradoresViejos->isNotEmpty())
            <h3>⚠️ Borradores sin completar (+3 días)</h3>
            <table style="width:100%; font-size:13px; margin-bottom:24px;">
                @foreach ($borradoresViejos as $bl)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:6px 0;">{{ $bl->shipper ?: '(sin shipper aún)' }}</td>
                        <td>{{ $bl->createdBy->name ?? '—' }}</td>
                        <td>{{ $bl->created_at->diffForHumans() }}</td>
                        <td><a href="{{ route('bl.create', $bl) }}">Continuar &rarr;</a></td>
                    </tr>
                @endforeach
            </table>
        @endif

        <div class="grid-2" style="margin-bottom:24px;">
            <div>
                <h3>Tendencia mensual (emitidos)</h3>
                <div style="display:flex; align-items:flex-end; gap:6px; height:100px; padding:0 4px;">
                    @foreach ($tendenciaMensual as $m)
                        <div style="flex:1; text-align:center;">
                            <div style="background:#1e6fb8; border-radius:3px 3px 0 0; height:{{ $maxTendencia > 0 ? max(4, ($m['total'] / $maxTendencia) * 80) : 4 }}px;"></div>
                            <div style="font-size:9px; color:#888; margin-top:4px;">{{ $m['label'] }}</div>
                            <div style="font-size:10px; font-weight:bold;">{{ $m['total'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div>
                <h3>Tiempo promedio creación &rarr; emisión</h3>
                <div style="background:#f4f5f7; border-radius:6px; padding:20px; text-align:center;">
                    <div style="font-size:30px; font-weight:bold; color:#1e6fb8;">
                        {{ $promedioHoras !== null ? $promedioHoras . ' hrs' : '—' }}
                    </div>
                    <div style="font-size:12px; color:#888;">
                        {{ $promedioHoras !== null ? 'promedio, sobre BL ya emitidos' : 'aún no hay BL emitidos' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-2" style="margin-bottom:24px;">
            <div>
                <h3>Top 5 Shippers</h3>
                <table style="width:100%; font-size:13px;">
                    @foreach ($topShippers as $s)
                        <tr style="border-bottom:1px solid #f0f0f0;">
                            <td style="padding:5px 0;">{{ \Illuminate\Support\Str::limit($s->shipper, 35) }}</td>
                            <td style="text-align:right; font-weight:bold;">{{ $s->total }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div>
                <h3>Top 5 Consignees</h3>
                <table style="width:100%; font-size:13px;">
                    @foreach ($topConsignees as $c)
                        <tr style="border-bottom:1px solid #f0f0f0;">
                            <td style="padding:5px 0;">{{ \Illuminate\Support\Str::limit($c->consignee, 35) }}</td>
                            <td style="text-align:right; font-weight:bold;">{{ $c->total }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        @if ($pendientesDeAprobar->isNotEmpty())
            <h3>Pendientes de tu aprobación</h3>
            <table style="width:100%; font-size:13px; margin-bottom:24px;">
                @foreach ($pendientesDeAprobar as $vr)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:6px 0;">{{ $vr->billOfLading->bl_number }}</td>
                        <td>Solicitado por {{ $vr->requestedBy->name }}</td>
                        <td><a href="{{ route('bl.show', $vr->bill_of_lading_id) }}">Revisar &rarr;</a></td>
                    </tr>
                @endforeach
            </table>
        @endif

        <h3>Últimos BL</h3>
        <table style="width:100%; font-size:13px;">
            @foreach ($recientes as $bl)
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:6px 0;">{{ $bl->bl_number ?? '(borrador)' }}</td>
                    <td>{{ $bl->port->code }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $bl->status)) }}</td>
                    <td><a href="{{ $bl->status === 'borrador' ? route('bl.create', $bl) : route('bl.show', $bl) }}">Ver &rarr;</a></td>
                </tr>
            @endforeach
        </table>
    </div>
</x-layout>
