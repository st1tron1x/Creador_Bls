<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Bill of Lading {{ $bl_number ?? '' }}</title>
<style>
@page { size: 215.14mm 280mm; margin: 0; }
* { box-sizing: border-box; }
html, body { margin:0; padding:0; width:215.14mm; background:#fff; }
body { font-family:Arial,Helvetica,sans-serif; color:#111; font-size:7pt; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
.page { position:relative; width:215.14mm; height:280mm; page-break-after:always; break-after:page; overflow:hidden; background:#fff; }
.page:last-child { page-break-after:auto; break-after:auto; }
.watermark { position:absolute; inset:0; z-index:0; overflow:hidden; pointer-events:none; }
.watermark span { position:absolute; display:block; width:40mm; font-size:14pt; line-height:1; font-weight:700; color:rgba(0,0,0,.11); white-space:nowrap; text-align:center; transform:rotate(-25deg); transform-origin:center center; }
.box { position:absolute; z-index:2; border:.32mm solid #222; border-radius:1.8mm; background:transparent; overflow:hidden; }
.box.transparent { background:transparent; }
.label { position:absolute; top:1.1mm; left:1.5mm; right:1.5mm; font-size:6.15pt; line-height:1.05; font-weight:400; text-transform:uppercase; white-space:nowrap; z-index:2; background:rgba(255,255,255,.85); }
.value { position:absolute; left:1.8mm; right:1.8mm; top:6.8mm; bottom:1.5mm; font-size:7.7pt; line-height:1.2; white-space:pre-line; overflow:hidden; z-index:2; }
.value.has-content { background:rgba(255,255,255,.85); }
.title { left:9.7mm; top:8.3mm; width:195.3mm; height:6.7mm; }
.title .label { top:1.35mm; font-size:8.5pt; font-weight:400; }
.shipper { left:9.7mm; top:15.2mm; width:80.3mm; height:28.3mm; }
.country { left:90.5mm; top:15.2mm; width:61.4mm; height:6.7mm; }
.bl-no { left:152.1mm; top:15.2mm; width:52.9mm; height:6.7mm; }
.shipper-ref { left:90.5mm; top:22.6mm; width:114.5mm; height:6.8mm; }
.consignee { left:9.7mm; top:44mm; width:80.3mm; height:20.4mm; }
.notify { left:9.7mm; top:64.7mm; width:80.3mm; height:20mm; }
.brand { left:90.5mm; top:29.9mm; width:114.5mm; height:64.5mm; background:transparent; }
.brand-logo { position:absolute; left:18mm; top:6mm; width:62mm; height:auto; max-height:30mm; object-fit:contain; z-index:2; }
.brand-details { position:absolute; left:12.2mm; top:34mm; font-size:8.1pt; line-height:1.25; white-space:nowrap; z-index:2; background:rgba(255,255,255,.85); }
.brand-code { position:absolute; right:10.5mm; top:8.5mm; text-align:center; color:#c0392b; font-size:18pt; line-height:1.05; font-weight:800; white-space:nowrap; z-index:2; }
.brand-code .prefix, .brand-code .sequence { display:block; color:#c0392b; font-weight:800; }
.brand-code .sequence { font-size:8.5pt; margin-top:1mm; }
.small-field { width:42.3mm; height:10.8mm; }
.small-field .label { font-size:6pt; }
.small-field .value { top:5.4mm; font-size:7.2pt; }
.pre-carriage { left:9.7mm; top:85.6mm; }
.place-receipt { left:53mm; top:85.6mm; width:36.9mm; }
.vessel { left:9.7mm; top:97.3mm; }
.loading { left:53mm; top:97.3mm; width:36.9mm; }
.discharge { left:9.7mm; top:109mm; }
.delivery { left:53mm; top:109mm; width:36.9mm; }
.freight-payable { left:90.5mm; top:95.2mm; width:60.4mm; height:23.1mm; }
.freight-payable .label { font-size:5.65pt; }
.freight-payable .value { top:7mm; font-size:7.1pt; }
.final-destination { left:151.8mm; top:95.2mm; width:53.2mm; height:23.1mm; }
/* Cargo -- altura corregida: terminaba 11.6mm mas abajo de lo real (214.6mm medido) */
.cargo { left:9.7mm; top:122.7mm; width:195.3mm; height:91.9mm; overflow:hidden; }
.cargo-header { position:absolute; left:0; top:0; width:100%; height:7mm; display:grid; grid-template-columns:22.5% 7% 44.5% 11.9% 14.1%; z-index:3; }
.cargo-header > div { border-right:.28mm solid #222; display:flex; align-items:center; justify-content:center; text-align:center; font-size:5.8pt; line-height:1.05; text-transform:uppercase; padding:.6mm; background:rgba(255,255,255,.85); }
.cargo-header > div:last-child { border-right:0; }
.cargo-grid { position:absolute; left:0; right:0; top:7mm; bottom:0; display:grid; grid-template-columns:22.5% 7% 44.5% 11.9% 14.1%; z-index:3; }
.cargo-col { border-right:.28mm solid #222; padding:1.7mm 1.4mm; font-size:7.3pt; line-height:1.25; white-space:pre-line; overflow:hidden; }
.cargo-col:last-child { border-right:0; }
.cargo-row { margin:0 0 1mm 0; overflow:hidden; }
.cargo-total { position:absolute; left:0; bottom:0; height:7mm; width:22.5%; border-top:.28mm solid #222; display:flex; align-items:center; padding-left:1.5mm; font-size:6.2pt; text-transform:uppercase; z-index:4; background:rgba(255,255,255,.85); }

/* ===== Zona inferior: coordenadas remedidas a partir del PDF base BUNT ===== */
.total-cargo { left:9.7mm; top:214.6mm; width:43.9mm; height:7.2mm; }
.total-cargo .label { font-size:6.2pt; }
.temp { left:53.6mm; top:214.6mm; width:66.4mm; height:7.2mm; }
.temp .label { font-size:6pt; }
.temp .value { top:4.8mm; font-size:6.8pt; }

.freight { left:9.7mm; top:221.8mm; width:42.7mm; height:28.4mm; }
.freight .label { font-size:6.2pt; }
.freight-value { position:absolute; left:1.7mm; right:1.7mm; top:7mm; height:16mm; font-size:7.5pt; line-height:1.2; overflow:hidden; z-index:2; }

.prepaid-box { left:52.6mm; top:221.8mm; width:27.7mm; height:28.4mm; }
.prepaid-box .label { font-size:6pt; text-align:center; text-transform:uppercase; }
.collect-box { left:80.7mm; top:221.8mm; width:39mm; height:28.4mm; }
.collect-box .label { font-size:6pt; text-align:center; text-transform:uppercase; }
.freight-option-value { position:absolute; left:1.7mm; right:1.7mm; top:7mm; font-size:7.5pt; text-align:center; z-index:2; }

.excess { left:120.1mm; top:221.8mm; width:84.9mm; height:49.9mm; }
.excess .label { font-size:5.9pt; text-transform:none; }
.excess .value { top:6.5mm; font-size:7pt; }

.on-board { left:9.7mm; top:250.2mm; width:110.2mm; height:7.3mm; }
.delivery-agent { left:9.7mm; top:257.5mm; width:110.2mm; height:14.2mm; }
.on-board .value, .delivery-agent .value { top:5.2mm; }

/* Continuation pages */
.cont-header { position:absolute; left:9.7mm; right:9.7mm; top:8.3mm; height:11mm; border:.32mm solid #222; border-radius:1.8mm; background:rgba(255,255,255,.85); z-index:2; padding:2mm 3mm; font-size:7pt; }
.cont-header strong { color:#c0392b; font-size:10pt; }
.cont-meta { float:right; font-size:6.5pt; }
.cont-cargo { position:absolute; left:9.7mm; top:22mm; width:195.3mm; height:246mm; border:.32mm solid #222; border-radius:1.8mm; overflow:hidden; z-index:2; background:transparent; }
.cont-cargo-head { height:8mm; display:grid; grid-template-columns:22.5% 7% 44.5% 11.9% 14.1%; border-bottom:.28mm solid #222; background:rgba(255,255,255,.85); }
.cont-cargo-head > div { border-right:.28mm solid #222; display:flex; align-items:center; justify-content:center; text-align:center; font-size:5.8pt; line-height:1.05; text-transform:uppercase; padding:.6mm; }
.cont-cargo-head > div:last-child { border-right:0; }
.cont-cargo-body { display:grid; grid-template-columns:22.5% 7% 44.5% 11.9% 14.1%; height:calc(100% - 8mm); }
.cont-cargo-col { border-right:.28mm solid #222; padding:2mm 1.5mm; font-size:7.3pt; line-height:1.25; white-space:pre-line; overflow:hidden; }
.cont-cargo-col:last-child { border-right:0; }
.cont-page-footer { position:absolute; left:9.7mm; right:9.7mm; bottom:7mm; text-align:right; font-size:6.5pt; z-index:2; }
</style>
</head>
<body>
@php
    $rawBl = trim((string)($bl_number ?? ''));
    $parts = preg_split('/[-\s]+/', $rawBl, -1, PREG_SPLIT_NO_EMPTY);
    $derivedPrefix = $parts[0] ?? ($port_prefix ?? '');
    $derivedSequence = $parts[1] ?? ($bl_consecutive ?? '');
    if ($derivedSequence !== '') {
        $derivedSequence = str_pad((string)$derivedSequence, 4, '0', STR_PAD_LEFT);
    }

    $wrapText = function ($text, $chars = 68) {
        $text = str_replace(["\r\n", "\r"], "\n", (string)$text);
        $paragraphs = preg_split('/\n/', $text);
        $lines = [];
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') { $lines[] = ''; continue; }
            $wrapped = wordwrap($paragraph, $chars, "\n", true);
            foreach (explode("\n", $wrapped) as $line) { $lines[] = $line; }
        }
        return $lines;
    };

    $items = collect($cargo_items ?? [])->values();
    $rows = [];
    foreach ($items as $item) {
        $descriptionLines = $wrapText($item->description_of_goods ?? '', 68);
        $marksText = trim((string)($item->marks_and_numbers ?? ''));
        if (!empty($item->container_seal_no)) {
            $marksText .= ($marksText !== '' ? "\n" : '') . $item->container_seal_no;
        }
        $marksLines = $wrapText($marksText, 28);
        $otherLines = max(count($marksLines), count($descriptionLines), 1);
        $rows[] = [
            'item' => $item,
            'marksLines' => $marksLines,
            'descriptionLines' => $descriptionLines,
            'lines' => $otherLines,
            'no_of_pkgs' => (string)($item->no_of_pkgs ?? ''),
            'gross_weight' => (string)($item->gross_weight ?? ''),
            'measurement_cbm' => (string)($item->measurement_cbm ?? ''),
        ];
    }

    $firstCapacity = 24;
    $continuationCapacity = 66;
    $chunks = [];
    $current = [];
    $capacity = $firstCapacity;
    $used = 0;

    foreach ($rows as $row) {
        $remainingLines = $row['lines'];
        $offset = 0;
        $firstPiece = true;
        while ($offset < $remainingLines) {
            $space = $capacity - $used;
            if ($space <= 0) {
                $chunks[] = $current;
                $current = [];
                $used = 0;
                $capacity = $continuationCapacity;
                $space = $capacity;
            }
            $take = min($remainingLines - $offset, $space);
            $piece = $row;
            $piece['firstPiece'] = $firstPiece;
            $piece['lineOffset'] = $offset;
            $piece['takeLines'] = $take;
            $piece['marksLinesPiece'] = array_slice($row['marksLines'], $offset, $take);
            $piece['descriptionLinesPiece'] = array_slice($row['descriptionLines'], $offset, $take);
            $current[] = $piece;
            $used += $take;
            $offset += $take;
            $firstPiece = false;
        }
    }
    if (!empty($current) || empty($chunks)) { $chunks[] = $current; }
    $totalPages = max(1, count($chunks));
@endphp

@foreach($chunks as $pageIndex => $pageRows)
    @php $isFirst = $pageIndex === 0; $isLast = $pageIndex === $totalPages - 1; @endphp
    <div class="page">
        <div class="watermark">
            @for ($row = 0; $row < 15; $row++)
                @for ($col = 0; $col < 8; $col++)
                    <span style="top:{{ $row * 21 - 15 }}mm; left:{{ $col * 32 - (($row % 2 === 0) ? 0 : 16) }}mm;">ORIGINAL</span>
                @endfor
            @endfor
        </div>

        @if($isFirst)
            <div class="box title"><span class="label">BILL OF LADING</span></div>
            <div class="box shipper"><span class="label">1. SHIPPER</span><span class="value has-content">{{ $shipper ?? '' }}</span></div>
            <div class="box country"><span class="label">COUNTRY ORIGIN</span><span class="value has-content">{{ $country_origin ?? '' }}</span></div>
            <div class="box bl-no"><span class="label">BILL OF LOADING Nº</span><span class="value has-content">{{ $bill_of_loading_ref ?? '' }}</span></div>
            <div class="box shipper-ref"><span class="label">SHIPPER'S REF</span><span class="value has-content">{{ $shipper_ref ?? '' }}</span></div>
            <div class="box consignee"><span class="label">2. CONSIGNEE OR ORDER</span><span class="value has-content">{{ $consignee ?? '' }}</span></div>
            <div class="box notify"><span class="label">3. NOTIFY PARTY</span><span class="value has-content">{{ $notify_party ?? '' }}</span></div>

            <div class="box brand">
                <img class="brand-logo" src="{{ public_path('images/logo-trade-business.png') }}" alt="Trade &amp; Business SAS">
                <div class="brand-code">
                    <span class="prefix">{{ $derivedPrefix }}</span>
                    <span class="sequence">Nº {{ $derivedSequence }}</span>
                </div>
                <div class="brand-details">
                    Nit.: 900066219-4<br>
                    Av 4 Norte No. 37an-29 La Flora<br>
                    Tel.: (602) 4896901<br>
                    Cel.: 323 322 1084<br>
                    Email: dayana.ordonez@tradebusiness.com.co<br>
                    Cali - Colombia
                </div>
            </div>

            <div class="box small-field pre-carriage"><span class="label">4. PRE-CARRIAGE BY</span><span class="value has-content">{{ $pre_carriage_by ?? '' }}</span></div>
            <div class="box small-field place-receipt"><span class="label">5. PLACE OF RECEIPT</span><span class="value has-content">{{ $place_of_receipt ?? '' }}</span></div>
            <div class="box small-field vessel"><span class="label">6. OCEAN VESSEL. VOY. NO.</span><span class="value has-content">{{ $ocean_vessel_voy_no ?? '' }}</span></div>
            <div class="box small-field loading"><span class="label">7. PORT OF LOADING</span><span class="value has-content">{{ $port_of_loading ?? '' }}</span></div>
            <div class="box small-field discharge"><span class="label">8. PORT OF DISCHARGE</span><span class="value has-content">{{ $port_of_discharge ?? '' }}</span></div>
            <div class="box small-field delivery"><span class="label">9. PLACE OF DELIVERY</span><span class="value has-content">{{ $place_of_delivery ?? '' }}</span></div>
            <div class="box freight-payable"><span class="label">FREIGHT PAYABLE AT &nbsp;&nbsp; NUMBER OF ORIGINAL BS/L</span><span class="value has-content">{{ $freight_payable_at ?? '' }} &nbsp;/&nbsp; {{ $number_of_original_bsl ?? '' }}</span></div>
            <div class="box final-destination"><span class="label">FINAL DESTINATION</span><span class="value has-content">{{ $final_destination ?? '' }}</span></div>

            <div class="box cargo">
                <div class="cargo-header">
                    <div>MARKS &amp; NUMBER<br>CONTAINER / SEAL NO.</div><div>NO. OF PKGS</div><div>DESCRIPTION OF GOODS</div><div>GROSS WEIGHT</div><div>MEASUREMENT (CBM)</div>
                </div>
                <div class="cargo-grid">
                    @foreach([0,1,2,3,4] as $col)
                        <div class="cargo-col">
                            @foreach($pageRows as $r)
                                @if($col === 0)
                                    {{ implode("\n", $r['marksLinesPiece']) }}
                                @elseif($col === 1)
                                    {{ $r['firstPiece'] ? $r['no_of_pkgs'] : '' }}
                                @elseif($col === 2)
                                    {{ implode("\n", $r['descriptionLinesPiece']) }}
                                @elseif($col === 3)
                                    {{ $r['firstPiece'] ? $r['gross_weight'] : '' }}
                                @else
                                    {{ $r['firstPiece'] ? $r['measurement_cbm'] : '' }}
                                @endif
                                @if(!$loop->last)<div style="height:1mm"></div>@endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="cargo-total">TOTAL</div>
            </div>

            <div class="box total-cargo"><span class="label">TOTAL</span></div>
            <div class="box temp"><span class="label">10. TEMPERATURE CONTROL INSTRUCTIONS</span><span class="value has-content">{{ $temperature_control ?? '' }}</span></div>

            <div class="box freight"><span class="label">11. FREIGHT AND CHARGES</span><div class="freight-value">{{ $freight_and_charges ?? '' }}</div></div>
            <div class="box prepaid-box">
                <span class="label">PREPAID</span>
                <span class="freight-option-value">@if(($freight_type ?? '') === 'prepaid'){{ $freight_total ?? '' }}@endif</span>
            </div>
            <div class="box collect-box">
                <span class="label">COLLECT</span>
                <span class="freight-option-value">@if(($freight_type ?? '') === 'collect'){{ $freight_total ?? '' }}@endif</span>
            </div>
            <div class="box excess"><span class="label">12. Excess Value Declaration Refer to Clause 6 on Reverse Side</span><span class="value has-content">{{ $excess_value_declaration ?? '' }}</span></div>
            <div class="box on-board"><span class="label">13. ON BOARD DATE</span><span class="value has-content">{{ $on_board_date ?? '' }}</span></div>
            <div class="box delivery-agent"><span class="label">14. DELIVERY AGENT</span><span class="value has-content">{{ $delivery_agent ?? '' }}</span></div>
        @else
            <div class="cont-header">
                <strong>{{ $derivedPrefix }} Nº {{ $derivedSequence }}</strong>
                <span> BILL OF LADING — CONTINUACIÓN</span>
                <span class="cont-meta">Página {{ $pageIndex + 1 }} de {{ $totalPages }}</span>
            </div>
            <div class="cont-cargo">
                <div class="cont-cargo-head"><div>MARKS &amp; NUMBER<br>CONTAINER / SEAL NO.</div><div>NO. OF PKGS</div><div>DESCRIPTION OF GOODS</div><div>GROSS WEIGHT</div><div>MEASUREMENT (CBM)</div></div>
                <div class="cont-cargo-body">
                    @foreach([0,1,2,3,4] as $col)
                        <div class="cont-cargo-col">
                            @foreach($pageRows as $r)
                                @if($col === 0)
                                    {{ implode("\n", $r['marksLinesPiece']) }}
                                @elseif($col === 1)
                                    {{ $r['firstPiece'] ? $r['no_of_pkgs'] : '' }}
                                @elseif($col === 2)
                                    {{ implode("\n", $r['descriptionLinesPiece']) }}
                                @elseif($col === 3)
                                    {{ $r['firstPiece'] ? $r['gross_weight'] : '' }}
                                @else
                                    {{ $r['firstPiece'] ? $r['measurement_cbm'] : '' }}
                                @endif
                                @if(!$loop->last)<div style="height:1.2mm"></div>@endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="cont-page-footer">BL {{ $rawBl }} &nbsp;—&nbsp; Página {{ $pageIndex + 1 }} de {{ $totalPages }}</div>
        @endif
    </div>
@endforeach
</body>
</html>