@extends('layouts.mailtemplate')

@section('content')

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        @foreach ($responses as $key => $value)

            <tr>
                <td colspan='3' class="h2" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
                    <label>STRUK {{$insert->notes}}</label>
                </td>
            </tr>
            <tr>
                <td>ID PELANGGAN</td>
                <TD>:</TD>
                <TD>{{ $value['idpel']}}</TD>
            </tr>
            <tr>
                <td>NAMA</td>
                <td>:</td>
                <td>{{ $value['nama']}}</td>
            </tr>
            <tr>
                <td>JUMLAH TAGIHAN</td>
                <td>:</td>
                <td>{{ $value['jumlahTagihan']}}</td>
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
            @foreach($value['tagihan'] as $key2 => $value2)
            <tr>
                <td style="font-weight:bold">PERIODE</td>
                <td style="font-weight:bold">:</td>
                <td style="font-weight:bold">{{ $value2['periode']}}</td>
            </tr>
            <tr>
                <td>PEMAKAIAN</td>
                <td>:</td>
                <td>{{ $value2['pemakaian']}}</td>
            </tr>
            <tr>
                <td>METER AWAL</td>
                <td>:</td>
                <td>{{ $value2['meterAwal'] }}</td>
            </tr>
            <tr>
                <td>METER AKHIR</td>
                <td>:</td>
                <td>{{ $value2['meterAkhir']}}</td>
            </tr>
            <tr>
                <td>TARIF</td>
                <td>:</td>
                <td>{{$value2['tarif']}}</td>
            </tr>
            <tr>
                <td>ALAMAT</td>
                <td>:</td>
                <td>{{ $value2['alamat']}}</td>
            </tr>
            <tr>
                <td>ADMIN</td>
                <td>:</td>
                <td>Rp{{ number_format($value2['admin'],0)}}</td>
            </tr>
            <tr>
                <td>DENDA</td>
                <td>:</td>
                <td>Rp{{ number_format($value2['penalty'],0)}}</td>
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
            @endforeach
            <tr>
                <td>NO REFF</td>
                <td>:</td>
                <td>{{ $value['reff'] }}</td>
            </tr>
            <tr>
                <td>TOTAL TAGIHAN</td>
                <td>:</td>
                <td>Rp{{ number_format($value['totalTagihan'],0)}}</td>
            </tr>
            <tr>
                <td colspan="3" align="center">{{$cost->deskripsi}} MENYATAKAN STRUK INI SEBAGAI BUKTI PEMBAYARAN YANG SAH.</td>
            </tr>

            <tr>
                <td colspan="3"><hr></td>
            </tr>
            
        @endforeach
    </tbody>
</table>
@endsection