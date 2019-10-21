@extends('layouts.mailtemplate')

@section('content')

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        @foreach ($responses as $key => $value)
            @foreach ($value['detilTagihan'] as $key2 => $value2)
            <tr>
                <td colspan='6' class="h2" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
                    <label>STRUK PEMBAYARAN TAGIHAN LISTRIK</label>
                </td>
            </tr>
            <tr>
                <td>IDPEL</td>
                <td>:</td>
                <td>{{ $value['subscriberID']}}</td>
                <td>BL/TH</td>
                <td>:</td>
                <td>{{ date('M Y',strtotime($value2['periode']))}}</td>
            </tr>
            <tr>
                <td>NAMA</td>
                <td>:</td>
                <td>{{ $value['nama']}}</td>
                <td>STAND METER</td>
                <td>:</td>
                <td>{{ $value2['meterAwal']}} - {{ $value2['meterAkhir']}}</td>
            </tr>
            <tr>
                <td>TARIF/DAYA</td>
                <td>:</td>
                <td colspan="4">{{$value['tarif']}} / {{ $value['daya'] }} VA</td>
            </tr>
            <tr>
                <td>RP TAG PLN</td>
                <td>:</td>
                <td colspan="4">Rp. {{ number_format($value2['nilaiTagihan'],0) }}</td>
            </tr>
            <tr>
                <td>NO REF</td>
                <td>:</td>
                <td colspan="4">{{ $value['refnumber']}} </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:14px;line-height:24px;font-weight:bold;text-align:center;">
                    <label><b>PLN menyatakan struk ini sebagai bukti pembayaran yang sah.</b></label>
                </td>
            </tr>
            <tr>
                <td>DENDA</td>
                <td>:</td>
                <td colspan="4">Rp. {{ number_format($value2['denda'],0) }}</td>
            </tr>
            <tr>
                <td>ADMIN</td>
                <td>:</td>
                <td colspan="4">Rp. {{ number_format($value2['admin'],0) }}</td>
            </tr>
            <tr>
                <td>TOTAL BAYAR</td>
                <td>:</td>
                <td colspan="4">Rp {{ number_format($value2['total'],0)}}</td>
            </tr>
            @if($value['lembarTagihanSisa'] > 0)
            <tr>
                <td colspan="6" align="center">Anda masih memiliki sisa tunggakan {{count($value['lembarTagihanSisa'])}} bulan.</td>
            </tr>
            @else
            <tr>
                <td colspan="6" align="center">TERIMA KASIH</td>
            </tr>
            @endif
            <tr>
                <td colspan="6" align="center">RINCIAN TAGIHAN DAPAT DIAKSES DI www.pln.co.id ATAU PLN TERDEKAT.</td>
            </tr>
            <tr>
                <td colspan="6"><hr></td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
@endsection