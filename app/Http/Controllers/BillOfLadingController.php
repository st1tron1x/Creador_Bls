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
        $query = BillOfLading::with('port')
            ->where('company_id', Auth::user()->activeCompany()->id)
            ->latest();

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
        abort_unless(
            Auth::user()->companies()->where('companies.id', $billOfLading->company_id)->exists(),
            403,
            'Este BL pertenece a otra empresa a la que no tienes acceso.'
        );

        $billOfLading->load('port', 'cargoItems', 'createdBy', 'voidedBy', 'voidRequests.requestedBy', 'voidRequests.resolvedBy', 'auditLogs.user');

        BlAuditLog::log($billOfLading, 'visto');

        return view('bl.show', ['bl' => $billOfLading]);
    }

    /**
     * Genera el PDF con Browsershot. Elige la plantilla según la empresa dueña del BL:
     * Trade & Business (formato BUNT) o Global Trade Business Corp (formato HBL propio).
     */
    public function pdf(Request $request, BillOfLading $billOfLading)
    {
        abort_unless(
            Auth::user()->companies()->where('companies.id', $billOfLading->company_id)->exists(),
            403,
            'Este BL pertenece a otra empresa a la que no tienes acceso.'
        );

        $billOfLading->load('port', 'cargoItems', 'company');

        if ($billOfLading->company->pdf_template === 'global') {
            $html = $this->renderGlobalHtml($billOfLading);
        } else {
            $html = $this->renderTradeHtml($billOfLading);
        }

        $fileName = $billOfLading->bl_number . '.pdf';
        $tempPath = storage_path('app/temp/' . $fileName);

        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        if ($billOfLading->company->pdf_template === 'global') {
            // El formato de Global ya no usa header/footer de Browsershot -- todo el
            // documento (A4 completo) vive dentro del propio HTML, sin margen externo.
            Browsershot::html($html)
                ->setChromePath('/usr/bin/chromium')
                ->noSandbox()
                ->format('A4')
                ->margins(0, 0, 0, 0)
                ->showBackground()
                ->save($tempPath);
        } else {
            // Formato Trade & Business: hoja 215.14 x 280 mm, sin margen externo. Las páginas de
            // continuación, la marca de agua "ORIGINAL" y los encabezados viven dentro del propio HTML.
            Browsershot::html($html)
                ->setChromePath('/usr/bin/chromium')
                ->noSandbox()
                ->paperSize(215.14, 280, 'mm')
                ->margins(0, 0, 0, 0)
                ->showBackground()
                ->save($tempPath);
        }

        if ($request->boolean('download')) {
            BlAuditLog::log($billOfLading, 'descargado');

            return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
        }

        BlAuditLog::log($billOfLading, 'descargado', 'previsualización en navegador');

        return response()->file($tempPath, ['Content-Type' => 'application/pdf']);
    }

    protected function renderTradeHtml(BillOfLading $billOfLading): string
    {
        return view('pdf.bill-of-lading', [
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
    }

    protected function renderGlobalHtml(BillOfLading $billOfLading): string
    {
        $company = $billOfLading->company;
        $logoPath = $company->logo_path ? public_path($company->logo_path) : null;

        return view('pdf.bill-of-lading-global', [
            'bl_number' => $billOfLading->bl_number,
            'fixed_prefix' => $company->fixed_prefix,
            'logo_path' => $logoPath,
            'company_address' => $company->address,
            'company_phone' => $company->phone,
            'company_email' => $company->email,
            'company_web' => 'www.tradebusiness.com.co',
            'shipper' => $billOfLading->shipper,
            'domestic_routing' => $billOfLading->domestic_routing,
            'consignee' => $billOfLading->consignee,
            'export_references' => $billOfLading->export_references,
            'cargo_delivery_contact' => $billOfLading->cargo_delivery_contact,
            'notify_party' => $billOfLading->notify_party,
            'forwarding_agent_references' => $billOfLading->forwarding_agent_references,
            'pre_carriage_by' => $billOfLading->pre_carriage_by,
            'place_of_receipt' => $billOfLading->place_of_receipt,
            'exporting_carrier' => $billOfLading->exporting_carrier,
            'port_of_loading_text' => $billOfLading->port_of_loading_text,
            'on_carriage_by' => $billOfLading->on_carriage_by,
            'number_of_original_bsl' => $billOfLading->number_of_original_bsl,
            'port_of_discharge' => $billOfLading->port_of_discharge,
            'place_of_delivery' => $billOfLading->place_of_delivery,
            'final_destination' => $billOfLading->final_destination,
            'net_weight' => $billOfLading->net_weight,
            'freight_type' => $billOfLading->freight_type,
            'freight_total' => $billOfLading->freight_total ? number_format((float) $billOfLading->freight_total, 2) : '',
            'freight_and_charges' => $billOfLading->freight_and_charges,
            'excess_value_declaration' => $billOfLading->excess_value_declaration,
            'total_charges' => $billOfLading->total_charges ? number_format((float) $billOfLading->total_charges, 2) : '',
            'on_board_date' => optional($billOfLading->on_board_date)->format('d-M-Y'),
            'cargo_items' => $billOfLading->cargoItems,
        ])->render();
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

        $billOfLading->status = 'anulacion_solicitada';
        $billOfLading->save();
        BlAuditLog::log($billOfLading, 'anulacion_solicitada', $validated['reason']);

        if (Auth::user()->hasRole('admin')) {
            $this->resolveVoidRequest($voidRequest, Auth::user(), true, 'Autoaprobado (solicitante es admin, máximo rango).');

            return redirect()->route('bl.show', $billOfLading)
                ->with('success', "BL {$billOfLading->bl_number} anulado (autoaprobado, el solicitante ya es admin).");
        }

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
            $billOfLading->status = 'emitido';
            $billOfLading->save();
            BlAuditLog::log($billOfLading, 'anulacion_rechazada', $note);
        }

        $voidRequest->requestedBy->notify(new \App\Notifications\VoidRequestResolved($voidRequest->fresh(), $approve));

        return $billOfLading;
    }
}
