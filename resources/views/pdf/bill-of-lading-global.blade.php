<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bill of Lading {{ $bl_number ?? '' }}</title>
<style>
@page { size: 210mm 297mm; margin: 0; }
* { box-sizing: border-box; }
html, body { margin:0; padding:0; width:210mm; background:#fff; }
body { font-family:Arial,Helvetica,sans-serif; color:#111; font-size:7.5pt; }
.page { position:relative; width:210mm; height:297mm; }
.box { position:absolute; border:.3mm solid #222; overflow:hidden; }
.label { position:absolute; top:0.8mm; left:1.3mm; right:1.3mm; font-size:6.3pt; font-weight:bold; line-height:1.05; text-transform:uppercase; }
.value { position:absolute; left:1.5mm; right:1.5mm; top:5.5mm; bottom:1mm; font-size:7.5pt; line-height:1.2; white-space:pre-line; overflow:hidden; }

.logo-box { position:absolute; left:10mm; top:8mm; width:95mm; height:24mm; border:none; display:flex; align-items:flex-start; gap:3mm; overflow:hidden; }
.logo-img { max-height:24mm; max-width:34mm; flex:0 0 auto; }
.company-details { font-size:8.5pt; line-height:1.35; font-weight:700; flex:1 1 auto; margin-top:0; }
.title-box { position:absolute; left:140mm; top:8mm; width:60mm; height:24mm; border:none; text-align:right; }
.title-box .bol-title { font-size:15pt; font-weight:bold; }
.title-box .oti { font-size:10.5pt; font-weight:bold; margin-top:2mm; }

.doc-no { left:110mm; top:34mm; width:90mm; height:8mm; }
.shipper { left:10mm; top:34mm; width:100mm; height:50mm; }
.domestic-routing { left:110mm; top:42mm; width:90mm; height:42mm; }
.consignee { left:10mm; top:84mm; width:100mm; height:24mm; }
.export-references { left:110mm; top:84mm; width:90mm; height:17mm; }
.cargo-delivery { left:110mm; top:101mm; width:90mm; height:30mm; }
.notify { left:10mm; top:108mm; width:100mm; height:23mm; }
.forwarding-agent { left:110mm; top:131mm; width:90mm; height:10mm; }

.pre-carriage { left:10mm; top:141mm; width:50mm; height:10mm; }
.place-receipt { left:60mm; top:141mm; width:50mm; height:10mm; }
.exporting-carrier { left:10mm; top:151mm; width:50mm; height:10mm; }
.port-loading { left:60mm; top:151mm; width:50mm; height:10mm; }
.on-carriage { left:110mm; top:141mm; width:56mm; height:20mm; }
.no-bills { left:166mm; top:141mm; width:34mm; height:20mm; }
.port-discharge { left:10mm; top:161mm; width:63mm; height:10mm; }
.place-delivery { left:73mm; top:161mm; width:63mm; height:10mm; }
.final-destination { left:136mm; top:161mm; width:64mm; height:10mm; }

.cargo { left:10mm; top:171mm; width:190mm; height:78mm; }
.cargo-header { position:absolute; left:0; top:0; width:100%; height:7mm; display:grid; grid-template-columns:20% 8% 42% 15% 15%; }
.cargo-header > div { border-right:.3mm solid #222; display:flex; align-items:center; justify-content:center; text-align:center; font-size:6pt; font-weight:bold; text-transform:uppercase; padding:.5mm; }
.cargo-header > div:last-child { border-right:none; }
.cargo-grid { position:absolute; left:0; right:0; top:7mm; bottom:8mm; display:grid; grid-template-columns:20% 8% 42% 15% 15%; }
.cargo-col { border-right:.3mm solid #222; padding:1.5mm; font-size:7.3pt; line-height:1.25; white-space:pre-line; overflow:hidden; }
.cargo-col:last-child { border-right:none; }
.net-wt-row { position:absolute; left:65%; right:0; bottom:0; height:8mm; display:grid; grid-template-columns: 46.5% 26% 27.5%; border-top:.3mm solid #222; }
.net-wt-row > div { border-right:.3mm solid #222; display:flex; align-items:center; padding-left:1.5mm; font-size:6.5pt; font-weight:bold; }
.net-wt-row > div:last-child { border-right:none; font-weight:normal; }

.total-containers { position:absolute; left:10mm; top:249mm; width:190mm; height:6mm; border:none; font-size:6.5pt; font-weight:bold; padding-top:1mm; }

.legal-box { position:absolute; left:10mm; top:257mm; width:112mm; height:34mm; font-size:6.3pt; line-height:1.35; padding:1.5mm; }
.legal-box p { margin:0 0 1.3mm 0; }
.signature-line { margin-top:2mm; font-size:6.5pt; }

.freight-charges-label { position:absolute; left:122mm; top:257mm; width:26mm; height:6mm; text-align:center; font-size:6.3pt; font-weight:bold; padding-top:1mm; }
.prepaid-label { position:absolute; left:148mm; top:257mm; width:26mm; height:6mm; text-align:center; font-size:6.3pt; font-weight:bold; padding-top:1mm; }
.collect-label { position:absolute; left:174mm; top:257mm; width:26mm; height:6mm; text-align:center; font-size:6.3pt; font-weight:bold; padding-top:1mm; }
.freight-charges-value { left:122mm; top:263mm; width:26mm; height:16mm; font-size:7pt; padding:1.5mm; white-space:pre-line; }
.prepaid-value { left:148mm; top:263mm; width:26mm; height:16mm; font-size:7pt; padding:1.5mm; text-align:center; }
.collect-value { left:174mm; top:263mm; width:26mm; height:16mm; font-size:7pt; padding:1.5mm; text-align:center; }

.excess-value-row { position:absolute; left:122mm; top:279mm; width:78mm; height:6mm; border:none; font-size:6.5pt; font-weight:bold; padding-top:1mm; }
.total-charges-row { position:absolute; left:122mm; top:285mm; width:78mm; height:8mm; border:none; font-size:6.5pt; font-weight:bold; padding-top:1mm; }
</style>
</head>
<body>
<div class="page">

    <div class="logo-box">
        @if(!empty($logo_path))
            <img class="logo-img" src="{{ $logo_path }}" alt="Global Trade Business Corp">
        @endif
        <div class="company-details">
            {{ $company_address ?? '' }}<br>
            TEL: {{ $company_phone ?? '' }}<br>
            EMAIL: {{ $company_email ?? '' }}<br>
            Web: {{ $company_web ?? '' }}
        </div>
    </div>

    <div class="title-box">
        <div class="bol-title">BILL OF LADING</div>
        <div class="oti">{{ $bl_number ?? '' }}</div>
    </div>

    <div class="box shipper"><span class="label">Shipper / Exporter</span><span class="value">{{ $shipper ?? '' }}</span></div>
    <div class="box doc-no"><span class="label">Document No / Bill of Lading No.</span><span class="value" style="top:3.9mm; bottom:0.5mm; font-weight:700;">{{ $bl_number ?? '' }}</span></div>
    <div class="box domestic-routing"><span class="label">Domestic Routing / Export Instructions</span><span class="value">{{ $domestic_routing ?? '' }}</span></div>

    <div class="box consignee"><span class="label">Consignee</span><span class="value">{{ $consignee ?? '' }}</span></div>
    <div class="box export-references"><span class="label">Export References</span><span class="value">{{ $export_references ?? '' }}</span></div>
    <div class="box cargo-delivery"><span class="label">For Cargo Delivery Please Contact</span><span class="value">{{ $cargo_delivery_contact ?? '' }}</span></div>

    <div class="box notify"><span class="label">Notify Party</span><span class="value">{{ $notify_party ?? '' }}</span></div>
    <div class="box forwarding-agent"><span class="label">Forwarding Agent - References</span><span class="value" style="top:5.5mm;">{{ $forwarding_agent_references ?? '' }}</span></div>

    <div class="box pre-carriage"><span class="label">Precarriage By</span><span class="value">{{ $pre_carriage_by ?? '' }}</span></div>
    <div class="box place-receipt"><span class="label">Place of Receipt</span><span class="value">{{ $place_of_receipt ?? '' }}</span></div>
    <div class="box exporting-carrier"><span class="label">Exporting Carrier</span><span class="value">{{ $exporting_carrier ?? '' }}</span></div>
    <div class="box port-loading"><span class="label">Port of Loading</span><span class="value">{{ $port_of_loading_text ?? '' }}</span></div>
    <div class="box on-carriage"><span class="label">On-Carriage By</span><span class="value">{{ $on_carriage_by ?? '' }}</span></div>
    <div class="box no-bills"><span class="label">No. of Bills of Lading</span><span class="value">{{ $number_of_original_bsl ?? '' }}</span></div>
    <div class="box port-discharge"><span class="label">Port of Discharge</span><span class="value">{{ $port_of_discharge ?? '' }}</span></div>
    <div class="box place-delivery"><span class="label">Place of Delivery</span><span class="value">{{ $place_of_delivery ?? '' }}</span></div>
    <div class="box final-destination"><span class="label">Final Destination</span><span class="value">{{ $final_destination ?? '' }}</span></div>

    <div class="box cargo">
        <div class="cargo-header">
            <div>Marks and Numbers</div><div>No. of Pkgs.</div><div>Description of Packages and Goods</div><div>Gross Weight</div><div>Measurement</div>
        </div>
        <div class="cargo-grid">
            @foreach([0,1,2,3,4] as $col)
                <div class="cargo-col">
                    @foreach($cargo_items ?? [] as $item)
                        @if($col === 0){{ $item->marks_and_numbers ?? '' }}@if(!empty($item->container_seal_no))<br>{{ $item->container_seal_no }}@endif
                        @elseif($col === 1){{ $item->no_of_pkgs ?? '' }}
                        @elseif($col === 2){{ $item->description_of_goods ?? '' }}
                        @elseif($col === 3){{ $item->gross_weight ?? '' }}
                        @else{{ $item->measurement_cbm ?? '' }}
                        @endif
                        @if(!$loop->last)<div style="height:2mm"></div>@endif
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="net-wt-row">
            <div>NET WT.</div><div>{{ $net_weight ?? '' }}</div><div></div>
        </div>
    </div>

    <div class="total-containers">Total Number of Containers or Packages (in words): {{ $total_containers_words ?? '' }}</div>

    <div class="legal-box">
        <p>RECEIVED by the Carrier the Goods as specified above in apparent good order and condition unless otherwise stated, to be transported to such place as agreed, authorized or permitted hereon and subject to all the terms and conditions appearing on the front and reverse of this Bill of Lading to which the merchant agrees by accepting this Bill of Lading, any local privileges and customs notwithstanding.</p>
        <p>The particulars, given above as stated by the shipper and the weight, measure, quantity, condition, contents and value of the Goods are unknown to the Carrier.</p>
        <p>In WITNESS whereof one (1) original Bill of Lading has been signed if not otherwise stated above the same being accomplished the other if any to be void if required by the Carrier, one (1) Original Bill of Lading must be surrendered duly endorsed in exchange for the goods or delivery order.</p>
        <div class="signature-line">
            Place and date of issue: {{ $on_board_date ?? '' }} &nbsp;&nbsp;&nbsp; signed on behalf of the Carrier:<br>
            By: ______________________________
        </div>
    </div>

    <div class="freight-charges-label">Freight Charges</div>
    <div class="prepaid-label">Prepaid</div>
    <div class="collect-label">Collect</div>
    <div class="box freight-charges-value">{{ $freight_and_charges ?? '' }}</div>
    <div class="box prepaid-value">@if(($freight_type ?? '') === 'prepaid'){{ $freight_total ?? '' }}@endif</div>
    <div class="box collect-value">@if(($freight_type ?? '') === 'collect'){{ $freight_total ?? '' }}@endif</div>

    <div class="excess-value-row">Excess Value Charge: {{ $excess_value_declaration ?? '' }}</div>
    <div class="total-charges-row">Total Charges: {{ $total_charges ?? '' }}</div>

</div>
</body>
</html>