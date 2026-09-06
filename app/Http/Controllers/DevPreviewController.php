<?php

namespace App\Http\Controllers;

use App\Models\Port;
use Illuminate\Http\Request;

class DevPreviewController extends Controller
{
    /**
     * Vista previa del formato del BL con datos de ejemplo, para probar
     * visualmente el layout con distintos puertos/navieras sin pasar por
     * el wizard ni el login. SOLO para desarrollo.
     *
     * Ejemplos:
     *   /dev/bl-preview/BVT
     *   /dev/bl-preview/CTG?vessel=MSC+ANDREA&consignee=OTRO+CLIENTE+LLC
     */
    public function blPreview(Request $request, string $portCode)
    {
        $port = Port::where('code', strtoupper($portCode))->firstOrFail();

        $cargoItems = collect([
            (object) [
                'marks_and_numbers' => 'CONT MSCU1234567',
                'container_seal_no' => 'SEAL: 998877',
                'no_of_pkgs' => '180 CTNS',
                'description_of_goods' => 'GENERAL MERCHANDISE' . "\n" . 'SAY: ONE HUNDRED EIGHTY (180) CARTONS ONLY',
                'gross_weight' => '3,420.00 KGS',
                'measurement_cbm' => '18.50 CBM',
            ],
        ]);

        return view('pdf.bill-of-lading', [
            'bl_number' => $port->code . '-0001',
            'country_origin' => 'COLOMBIA',
            'shipper' => "INDUSTRIAS DEL VALLE S.A.\nCra 10 # 15-20, Cali, Colombia\nNIT 900123456-1",
            'shipper_ref' => 'REF-DEMO-001',
            'bill_of_loading_ref' => $request->get('bl_ref', ''),
            'consignee' => $request->get('consignee', "IMPORT TRADING LLC\n1200 Brickell Ave, Miami, FL 33131, USA"),
            'notify_party' => 'SAME AS CONSIGNEE',
            'pre_carriage_by' => 'TRUCK',
            'place_of_receipt' => 'CALI, COLOMBIA',
            'ocean_vessel_voy_no' => $request->get('vessel', 'MSC LORETO / 412W'),
            'port_of_loading' => $port->name . ', COLOMBIA',
            'port_of_discharge' => $request->get('discharge', 'MIAMI, FL - USA'),
            'place_of_delivery' => $request->get('discharge', 'MIAMI, FL - USA'),
            'final_destination' => $request->get('discharge', 'MIAMI, FL - USA'),
            'freight_payable_at' => $port->name,
            'number_of_original_bsl' => '3/3',
            'temperature_control' => 'N/A',
            'freight_type' => 'prepaid',
            'freight_total' => '1,430.00',
            'freight_and_charges' => "OCEAN FREIGHT ................ USD 1,250.00\nTHC ............................. USD 180.00",
            'excess_value_declaration' => 'N/A',
            'on_board_date' => now()->format('d-M-Y'),
            'delivery_agent' => 'GLOBAL LOGISTICS INC.',
            'port_prefix' => $port->code,
            'bl_consecutive' => '0001',
            'cargo_items' => $cargoItems,
        ]);
    }
}
