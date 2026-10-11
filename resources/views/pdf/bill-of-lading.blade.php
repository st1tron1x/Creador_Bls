<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Bill of Lading {{ $bl_number ?? 'BVT-3345' }}</title>
<style>
    @page { size: A4; margin: 10mm 9mm; }
    * { box-sizing: border-box; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 8pt;
        color: #111;
        margin: 0;
    }
    .sheet {
        border: 1.6pt solid #111;
        width: 100%;
    }
    .cell {
        border: 0.8pt solid #111;
        padding: 3px 5px;
        vertical-align: top;
        position: relative;
    }
    .label {
        font-size: 6.5pt;
        font-weight: bold;
        display: block;
        margin-bottom: 10px;
        text-transform: uppercase;
    }
    .value {
        font-size: 8pt;
        white-space: pre-line;
    }
    table { border-collapse: collapse; width: 100%; }

    /* Título BILL OF LADING */
    .title-row .cell {
        font-size: 11pt;
        font-weight: bold;
        padding: 4px 6px;
    }

    /* Bloque de logo/empresa */
    .brand-cell { text-align: left; }
    .brand-logo-img { display: block; height: 32px; width: auto; margin-bottom: 2px; }
    .brand-details { font-size: 7pt; margin-top: 4px; line-height: 1.5; }
    .bl-code-box { text-align: right; }
    .bl-code-prefix { font-size: 14pt; font-weight: 800; color: #c0392b; }
    .bl-code-no { font-size: 6.5pt; color:#c0392b; font-weight:bold; }

    /* Tabla de mercancía */
    .cargo-header .cell { font-size: 6.5pt; font-weight: bold; text-align: center; text-transform: uppercase; }
    .cargo-body .cell { height: 140px; }
    .cargo-total .cell { font-size: 6.5pt; font-weight: bold; }

    .freight-flags { display: flex; }
    .freight-flags .flag { flex: 1; border-left: 0.8pt solid #111; text-align:center; padding: 3px; }
    .freight-flags .flag:first-child { border-left: none; }
</style>
</head>
<body>
<table class="sheet">

    {{-- Fila 1: título + país origen / número de BL --}}
    <tr class="title-row">
        <td class="cell" style="width:60%;">BILL OF LADING</td>
        <td class="cell" style="width:40%;">
            <span class="label">Country Origin</span>
            <span class="value">{{ $country_origin ?? '' }}</span>
        </td>
    </tr>

    {{-- Fila 2: Shipper | Shipper's ref + logo/no BL --}}
    <tr>
        <td class="cell" rowspan="1" style="width:60%; height:70px;">
            <span class="label">1. Shipper</span>
            <span class="value">{{ $shipper ?? '' }}</span>
        </td>
        <td class="cell" style="width:40%;">
            <span class="label">Shipper's Ref</span>
            <span class="value">{{ $shipper_ref ?? '' }}</span>
        </td>
    </tr>

    {{-- Fila 3: Consignee | bloque de marca --}}
    <tr>
        <td class="cell" style="height:70px;">
            <span class="label">2. Consignee or Order</span>
            <span class="value">{{ $consignee ?? '' }}</span>
        </td>
        <td class="cell brand-cell" rowspan="2">
            <img class="brand-logo-img" src="{{ public_path('images/logo-trade-business.png') }}" alt="Trade & Business SAS">
            <div class="brand-details">
                Nit.: 900066219-4<br>
                Av 4 Norte No. 37an-29 La Flora<br>
                Tel.: (602) 4896901 &nbsp; Cel.: 323 322 1084<br>
                Email: dayana.ordonez@tradebusiness.com.co<br>
                Cali - Colombia
            </div>
            <div class="bl-code-box">
                <span class="bl-code-prefix">{{ $port_prefix ?? 'BVT' }}</span>
                <span class="bl-code-no">N&ordm; {{ $bl_consecutive ?? '0000' }}</span>
            </div>
        </td>
    </tr>

    {{-- Fila 4: Notify party --}}
    <tr>
        <td class="cell" style="height:70px;">
            <span class="label">3. Notify Party</span>
            <span class="value">{{ $notify_party ?? '' }}</span>
        </td>
    </tr>

    {{-- Fila 5: Pre-carriage / Place of receipt | Freight payable / Nº originales --}}
    <tr>
        <td class="cell" style="padding:0;">
            <table>
                <tr>
                    <td class="cell" style="border:none; border-right:0.8pt solid #111; width:50%;">
                        <span class="label">4. Pre-carriage By</span>
                        <span class="value">{{ $pre_carriage_by ?? '' }}</span>
                    </td>
                    <td class="cell" style="border:none; width:50%;">
                        <span class="label">5. Place of Receipt</span>
                        <span class="value">{{ $place_of_receipt ?? '' }}</span>
                    </td>
                </tr>
            </table>
        </td>
        <td class="cell">
            <span class="label">Freight Payable At / Number of Original Bs/L</span>
            <span class="value">{{ $freight_payable_at ?? '' }} &nbsp;/&nbsp; {{ $number_of_original_bsl ?? '' }}</span>
        </td>
    </tr>

    {{-- Fila 6: Ocean vessel / Port of loading | Final destination --}}
    <tr>
        <td class="cell" style="padding:0;">
            <table>
                <tr>
                    <td class="cell" style="border:none; border-right:0.8pt solid #111; width:50%;">
                        <span class="label">6. Ocean Vessel Voy. No.</span>
                        <span class="value">{{ $ocean_vessel_voy_no ?? '' }}</span>
                    </td>
                    <td class="cell" style="border:none; width:50%;">
                        <span class="label">7. Port of Loading</span>
                        <span class="value">{{ $port_of_loading ?? '' }}</span>
                    </td>
                </tr>
            </table>
        </td>
        <td class="cell" rowspan="2">
            <span class="label">Final Destination</span>
            <span class="value">{{ $final_destination ?? '' }}</span>
        </td>
    </tr>

    {{-- Fila 7: Port of discharge / Place of delivery --}}
    <tr>
        <td class="cell" style="padding:0;">
            <table>
                <tr>
                    <td class="cell" style="border:none; border-right:0.8pt solid #111; width:50%;">
                        <span class="label">8. Port of Discharge</span>
                        <span class="value">{{ $port_of_discharge ?? '' }}</span>
                    </td>
                    <td class="cell" style="border:none; width:50%;">
                        <span class="label">9. Place of Delivery</span>
                        <span class="value">{{ $place_of_delivery ?? '' }}</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Fila 8: encabezados de mercancía --}}
    <tr class="cargo-header">
        <td class="cell" colspan="2" style="padding:0;">
            <table>
                <tr class="cargo-header">
                    <td class="cell" style="border-top:none; border-left:none; width:22%;">Marks &amp; Number<br>Container / Seal No.</td>
                    <td class="cell" style="border-top:none; width:8%;">No. of Pkgs</td>
                    <td class="cell" style="border-top:none; width:40%;">Description of Goods</td>
                    <td class="cell" style="border-top:none; width:15%;">Gross Weight</td>
                    <td class="cell" style="border-top:none; border-right:none; width:15%;">Measurement (CBM)</td>
                </tr>
                <tr class="cargo-body">
                    @foreach($cargo_items ?? [['marks'=>'','pkgs'=>'','desc'=>'','weight'=>'','cbm'=>'']] as $item)
                    <td class="cell" style="border-left:none;">{{ $item['marks'] }}</td>
                    <td class="cell">{{ $item['pkgs'] }}</td>
                    <td class="cell">{{ $item['desc'] }}</td>
                    <td class="cell">{{ $item['weight'] }}</td>
                    <td class="cell" style="border-right:none;">{{ $item['cbm'] }}</td>
                    @endforeach
                </tr>
                <tr class="cargo-total">
                    <td class="cell" style="border-left:none; border-bottom:none;">TOTAL</td>
                    <td class="cell" colspan="4" style="border-right:none; border-bottom:none;"></td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Fila 9: Freight and charges | Temperature control --}}
    <tr>
        <td class="cell" style="padding:0; width:60%;">
            <table>
                <tr>
                    <td class="cell" style="border:none; border-right:0.8pt solid #111;">
                        <span class="label">11. Freight and Charges</span>
                        <span class="value">{{ $freight_and_charges ?? '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="cell" style="border:none; padding:0;">
                        <div class="freight-flags">
                            <div class="flag {{ ($freight_type ?? '') === 'prepaid' ? 'value' : '' }}">
                                <strong>PREPAID</strong><br>{{ $freight_type === 'prepaid' ? ($freight_total ?? '') : '' }}
                            </div>
                            <div class="flag">
                                <strong>COLLECT</strong><br>{{ $freight_type === 'collect' ? ($freight_total ?? '') : '' }}
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
        <td class="cell" style="width:40%;">
            <span class="label">10. Temperature Control Instructions</span>
            <span class="value">{{ $temperature_control ?? '' }}</span>
        </td>
    </tr>

    {{-- Fila 10: On board date / delivery agent | Excess value declaration --}}
    <tr>
        <td class="cell" style="padding:0;">
            <table>
                <tr>
                    <td class="cell" style="border:none; border-bottom:0.8pt solid #111;">
                        <span class="label">13. On Board Date</span>
                        <span class="value">{{ $on_board_date ?? '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="cell" style="border:none; border-bottom:none;">
                        <span class="label">14. Delivery Agent</span>
                        <span class="value">{{ $delivery_agent ?? '' }}</span>
                    </td>
                </tr>
            </table>
        </td>
        <td class="cell">
            <span class="label">12. Excess Value Declaration Refer to Clause 6 on Reverse Side</span>
            <span class="value">{{ $excess_value_declaration ?? '' }}</span>
        </td>
    </tr>

</table>
</body>
</html>
