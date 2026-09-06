<?php

namespace App\Http\Controllers;

use App\Models\BillOfLading;
use App\Models\Port;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = [
            'borrador' => BillOfLading::where('status', 'borrador')->count(),
            'emitido' => BillOfLading::where('status', 'emitido')->count(),
            'anulacion_solicitada' => BillOfLading::where('status', 'anulacion_solicitada')->count(),
            'anulado' => BillOfLading::where('status', 'anulado')->count(),
        ];

        $recientes = BillOfLading::with('port')->latest()->take(5)->get();

        $misBl = BillOfLading::where('created_by', Auth::id())->count();

        $porPuerto = Port::withCount('billsOfLading')->orderBy('name')->get();

        $emitidosEsteMes = BillOfLading::whereNotNull('issued_at')
            ->whereMonth('issued_at', now()->month)
            ->whereYear('issued_at', now()->year)
            ->count();

        $pendientesDeAprobar = collect();
        if (Auth::user()->can('bl.void.approve')) {
            $pendientesDeAprobar = \App\Models\BlVoidRequest::with('billOfLading', 'requestedBy')
                ->where('status', 'pendiente')
                ->get()
                ->filter(fn ($vr) => Auth::user()->canApproveVoidRequestFrom($vr->requestedBy));
        }

        // --- Las 4 mejoras gerenciales ---

        // 1. Top 5 shippers y consignees más frecuentes
        $topShippers = BillOfLading::select('shipper', DB::raw('count(*) as total'))
            ->whereNotNull('shipper')->where('shipper', '!=', '')
            ->groupBy('shipper')->orderByDesc('total')->take(5)->get();

        $topConsignees = BillOfLading::select('consignee', DB::raw('count(*) as total'))
            ->whereNotNull('consignee')->where('consignee', '!=', '')
            ->groupBy('consignee')->orderByDesc('total')->take(5)->get();

        // 2. Tiempo promedio entre creación y emisión
        $emitidos = BillOfLading::whereNotNull('issued_at')->get(['created_at', 'issued_at']);
        $promedioHoras = $emitidos->isNotEmpty()
            ? round($emitidos->avg(fn ($bl) => $bl->created_at->diffInHours($bl->issued_at)), 1)
            : null;

        // 3. Tendencia mensual, últimos 6 meses
        $tendenciaMensual = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $total = BillOfLading::whereNotNull('issued_at')
                ->whereMonth('issued_at', $date->month)
                ->whereYear('issued_at', $date->year)
                ->count();

            return ['label' => $date->translatedFormat('M Y'), 'total' => $total];
        });
        $maxTendencia = max(1, $tendenciaMensual->max('total'));

        // 4. Borradores viejos (más de 3 días sin completarse)
        $borradoresViejos = BillOfLading::with('createdBy')
            ->where('status', 'borrador')
            ->where('created_at', '<=', now()->subDays(3))
            ->orderBy('created_at')
            ->get();

        return view('dashboard', compact(
            'counts', 'recientes', 'pendientesDeAprobar', 'misBl', 'porPuerto', 'emitidosEsteMes',
            'topShippers', 'topConsignees', 'promedioHoras', 'tendenciaMensual', 'maxTendencia', 'borradoresViejos'
        ));
    }
}
