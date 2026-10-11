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
        $company = Auth::user()->activeCompany();
        $companyId = $company->id;
        $base = fn () => BillOfLading::where('company_id', $companyId);

        $counts = [
            'borrador' => $base()->where('status', 'borrador')->count(),
            'emitido' => $base()->where('status', 'emitido')->count(),
            'anulacion_solicitada' => $base()->where('status', 'anulacion_solicitada')->count(),
            'anulado' => $base()->where('status', 'anulado')->count(),
        ];

        $recientes = $base()->with('port')->latest()->take(5)->get();

        $misBl = $base()->where('created_by', Auth::id())->count();

        // El desglose por puerto solo aplica a empresas que numeran por puerto (Trade & Business).
        $porPuerto = $company->numbering_scheme === 'port_based'
            ? Port::withCount(['billsOfLading' => fn ($q) => $q->where('company_id', $companyId)])->orderBy('name')->get()
            : collect();

        $emitidosEsteMes = $base()->whereNotNull('issued_at')
            ->whereMonth('issued_at', now()->month)
            ->whereYear('issued_at', now()->year)
            ->count();

        $pendientesDeAprobar = collect();
        if (Auth::user()->can('bl.void.approve')) {
            $pendientesDeAprobar = \App\Models\BlVoidRequest::with('billOfLading', 'requestedBy')
                ->whereHas('billOfLading', fn ($q) => $q->where('company_id', $companyId))
                ->where('status', 'pendiente')
                ->get()
                ->filter(fn ($vr) => Auth::user()->canApproveVoidRequestFrom($vr->requestedBy));
        }

        // --- Las 4 mejoras gerenciales ---

        $topShippers = $base()->select('shipper', DB::raw('count(*) as total'))
            ->whereNotNull('shipper')->where('shipper', '!=', '')
            ->groupBy('shipper')->orderByDesc('total')->take(5)->get();

        $topConsignees = $base()->select('consignee', DB::raw('count(*) as total'))
            ->whereNotNull('consignee')->where('consignee', '!=', '')
            ->groupBy('consignee')->orderByDesc('total')->take(5)->get();

        $emitidos = $base()->whereNotNull('issued_at')->get(['created_at', 'issued_at']);
        $promedioHoras = $emitidos->isNotEmpty()
            ? round($emitidos->avg(fn ($bl) => $bl->created_at->diffInHours($bl->issued_at)), 1)
            : null;

        $tendenciaMensual = collect(range(5, 0))->map(function ($monthsAgo) use ($base) {
            $date = now()->subMonths($monthsAgo);
            $total = $base()->whereNotNull('issued_at')
                ->whereMonth('issued_at', $date->month)
                ->whereYear('issued_at', $date->year)
                ->count();

            return ['label' => $date->translatedFormat('M Y'), 'total' => $total];
        });
        $maxTendencia = max(1, $tendenciaMensual->max('total'));

        $borradoresViejos = $base()->with('createdBy')
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
