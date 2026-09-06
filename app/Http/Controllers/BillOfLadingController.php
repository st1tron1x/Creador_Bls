<?php

namespace App\Http\Controllers;

use App\Models\BillOfLading;
use App\Models\BlAuditLog;
use App\Models\BlVoidRequest;
use App\Models\Port;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Browsershot\Browsershot;

class BillOfLadingController extends Controller
{
    public function index(Request $request)
    {
        $query = BillOfLading::with('port')->latest();

        if ($request->filled('bl_number')) {
            $query->where('bl_number', 'ilike', '%' . $request->bl_number . '%');
        }
        if ($request->filled('shipper')) {
            $query->where('shipper', 'ilike', '%' . $request->shipper . '%');
        }
        if ($request->filled('consignee')) {
            $query->where('consignee', 'ilike', '%' . $request->consignee . '%');
        }
        if ($request->filled('port_id')) {
            $query->where('port_id', $request->port_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bills = $query->paginate(20)->withQueryString();
        $ports = Port::orderBy('name')->get();

        return view('bl.index', compact('bills', 'ports'));
    }

    public function show(BillOfLading $billOfLading)
    {
        $billOfLading->load('port', 'cargoItems', 'createdBy', 'voidedBy', 'voidRequests.requestedBy', 'voidRequests.resolvedBy', 'auditLogs.user');

        BlAuditLog::log($billOfLading, 'visto');

        return view('bl.show', ['bl' => $billOfLading]);
    }

    /**
     * Genera el PDF con Browsershot a partir de la misma plantilla Blade
     * que usamos en /dev/bl-preview, y lo descarga.
     */
    public function pdf(Request $request, BillOfLading $billOfLading)
    {
        $billOfLading->load('port', 'cargoItems');

        $html = view('pdf.bill-of-lading', [
            'bl_number' => $billOfLading->bl_number,
            'country_origin' => $billOfLading->country_origin,
            'bill_of_loading_ref' => $billOfLading->bill_of_loading_ref,
            'shipper' => $billOfLading->shipper,
            'shipper_ref' => $billOfLading->shipper_ref,
            'consignee' => $billOfLading->consignee,
            'notify_party' => $billOfLading->notify_party,
            'pre_carriage_by' => $billOfLading->pre_carriage_by,
            'place_of_receipt' => $billOfLading->place_of_receipt,
            'ocean_vessel_voy_no' => $billOfLading->ocean_vessel_voy_no,
            'port_of_loading' => $billOfLading->port->name . ', COLOMBIA',
            'port_of_discharge' => $billOfLading->port_of_discharge,
            'place_of_delivery' => $billOfLading->place_of_delivery,
            'final_destination' => $billOfLading->final_destination,
            'freight_payable_at' => $billOfLading->freight_payable_at,
            'number_of_original_bsl' => $billOfLading->number_of_original_bsl,
            'temperature_control' => $billOfLading->temperature_control_instructions,
            'freight_type' => $billOfLading->freight_type,
            'freight_total' => number_format((float) $billOfLading->freight_total, 2),
            'freight_and_charges' => $billOfLading->freight_and_charges,
            'excess_value_declaration' => $billOfLading->excess_value_declaration,
            'on_board_date' => optional($billOfLading->on_board_date)->format('d-M-Y'),
            'delivery_agent' => $billOfLading->delivery_agent,
            'port_prefix' => $billOfLading->port->code,
            'bl_consecutive' => str_pad((string) $billOfLading->sequence_number, 4, '0', STR_PAD_LEFT),
            'cargo_items' => $billOfLading->cargoItems,
        ])->render();

        $fileName = $billOfLading->bl_number . '.pdf';
        $tempPath = storage_path('app/temp/' . $fileName);

        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        // Encabezado repetido en cada página (para cuando la mercancía ocupa más de una hoja):
        // usa el mecanismo nativo de Chromium/Puppeteer, no depende de que la tabla HTML "se corte bonito".
        $headerHtml = view('pdf.bill-of-lading-header', [
            'bl_number' => $billOfLading->bl_number,
            'shipper_short' => \Illuminate\Support\Str::limit(explode("\n", $billOfLading->shipper ?? '')[0], 40),
            'consignee_short' => \Illuminate\Support\Str::limit(explode("\n", $billOfLading->consignee ?? '')[0], 40),
        ])->render();

        //Browsershot::html($html)
        //    ->setChromePath('/usr/bin/chromium')
        //    ->noSandbox()
        //    ->format('A4')
        //    ->margins(20, 9, 12, 9) // margen superior más amplio para dejar espacio al encabezado repetido
        //    ->showBackground()
        //    ->showBrowserHeaderAndFooter()
        //    ->headerHtml($headerHtml)
        //    ->footerHtml('<div></div>') // sin pie de página por ahora
        //    ->save($tempPath);
        Browsershot::html($html)
            ->setChromePath('/usr/bin/chromium')
            ->noSandbox()
            // El BUNT NO es A4: 215.14 x 280 mm.
            ->paperSize(215.14, 280, 'mm')
            // El Blade ya controla toda la geometría; no agregar márgenes externos.
            ->margins(0, 0, 0, 0, 'mm')
            ->showBackground()
            // IMPORTANTE: no usar showBrowserHeaderAndFooter().
            // El encabezado anterior introducía 20 mm arriba y desplazaba toda la plantilla.
            ->save($tempPath);

        // ?download=1 fuerza la descarga; sin ese parámetro, se abre en el visor del navegador (previsualización)
        if ($request->boolean('download')) {
            return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
        }

        // ?download=1 fuerza la descarga; sin ese parámetro, se abre en el visor del navegador (previsualización)
        if ($request->boolean('download')) {
            BlAuditLog::log($billOfLading, 'descargado');

            return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
        }

        BlAuditLog::log($billOfLading, 'descargado', 'previsualización en navegador');

        return response()->file($tempPath, ['Content-Type' => 'application/pdf']);
    }

    /**
     * Cualquier usuario autenticado (incluido auxiliar) puede solicitar la anulación,
     * explicando el motivo. No anula nada todavía -- solo deja la solicitud pendiente.
     */
    public function requestVoid(Request $request, BillOfLading $billOfLading)
    {
        abort_unless(Auth::user()->can('bl.void.request'), 403);
        abort_if($billOfLading->status !== 'emitido', 422, 'Solo se puede solicitar anulación de un BL emitido.');
        abort_if($billOfLading->pendingVoidRequest(), 422, 'Ya existe una solicitud de anulación pendiente para este BL.');

        $validated = $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $voidRequest = BlVoidRequest::create([
            'bill_of_lading_id' => $billOfLading->id,
            'requested_by' => Auth::id(),
            'requested_reason' => $validated['reason'],
            'requested_at' => now(),
            'status' => 'pendiente',
        ]);

        $billOfLading->status = 'anulacion_solicitada'; // asignación directa: 'status' no está en $fillable a propósito
        $billOfLading->save();
        BlAuditLog::log($billOfLading, 'anulacion_solicitada', $validated['reason']);

        // Caso especial: si quien solicita ya es admin, no existe nadie de rango superior -> autoaprobación.
        if (Auth::user()->hasRole('admin')) {
            $this->resolveVoidRequest($voidRequest, Auth::user(), true, 'Autoaprobado (solicitante es admin, máximo rango).');

            return redirect()->route('bl.show', $billOfLading)
                ->with('success', "BL {$billOfLading->bl_number} anulado (autoaprobado, el solicitante ya es admin).");
        }

        // Notificar a todos los que puedan aprobar esta solicitud (rango suficiente, personas distintas al solicitante).
        $requester = Auth::user();
        \App\Models\User::where('is_active', true)
            ->where('id', '!=', $requester->id)
            ->get()
            ->filter(fn ($u) => $u->canApproveVoidRequestFrom($requester))
            ->each(fn ($u) => $u->notify(new \App\Notifications\VoidRequestSubmitted($voidRequest)));

        return redirect()->route('bl.show', $billOfLading)
            ->with('success', 'Solicitud de anulación enviada. Queda pendiente de aprobación.');
    }

    public function approveVoid(BlVoidRequest $voidRequest)
    {
        return $this->resolveVoid($voidRequest, true);
    }

    public function rejectVoid(Request $request, BlVoidRequest $voidRequest)
    {
        $validated = $request->validate(['resolution_note' => 'nullable|string']);

        return $this->resolveVoid($voidRequest, false, $validated['resolution_note'] ?? null);
    }

    protected function resolveVoid(BlVoidRequest $voidRequest, bool $approve, ?string $note = null)
    {
        $approver = Auth::user();

        abort_unless($approver->can('bl.void.approve'), 403);
        abort_if($voidRequest->status !== 'pendiente', 422, 'Esta solicitud ya fue resuelta.');
        abort_unless($approver->canApproveVoidRequestFrom($voidRequest->requestedBy), 403,
            'No tienes rango suficiente para aprobar esta solicitud, o es tu propia solicitud.');

        $this->resolveVoidRequest($voidRequest, $approver, $approve, $note);

        return redirect()->route('bl.show', $voidRequest->bill_of_lading_id)
            ->with('success', $approve ? 'Anulación aprobada.' : 'Solicitud de anulación rechazada.');
    }

    /**
     * Lógica compartida para aplicar la resolución (usada tanto por el flujo normal
     * como por la autoaprobación cuando el solicitante es admin).
     */
    protected function resolveVoidRequest(BlVoidRequest $voidRequest, $approver, bool $approve, ?string $note = null)
    {
        $voidRequest->update([
            'status' => $approve ? 'aprobada' : 'rechazada',
            'resolved_by' => $approver->id,
            'resolved_at' => now(),
            'resolution_note' => $note,
        ]);

        $billOfLading = $voidRequest->billOfLading;

        if ($approve) {
            $billOfLading->finalizeVoid($approver, $voidRequest->requested_reason);
            BlAuditLog::log($billOfLading, 'anulacion_aprobada', $note);
        } else {
            $billOfLading->status = 'emitido'; // asignación directa: idem, 'status' no es masivo-asignable
            $billOfLading->save();
            BlAuditLog::log($billOfLading, 'anulacion_rechazada', $note);
        }

        // Avisar a quien solicitó, sin importar si fue aprobada o rechazada -- y sin importar
        // si el propio solicitante era admin (autoaprobación), para que quede el registro igual.
        $voidRequest->requestedBy->notify(new \App\Notifications\VoidRequestResolved($voidRequest->fresh(), $approve));

        return $billOfLading;
    }
}