@extends('layouts.mailtemplate')

@section('content')

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        @foreach ($responses as $key => $value)

            @if($value['responseCode'] == 00)
                <tr>
                    <td colspan='6' class="h2" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
                        <label>STRUK {{$insert->notes}}</label>
                    </td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <TD>:</TD>
                    <TD>{{ $value['nama']}}</TD>
                    <td>No Va</td>
                    <td>:</td>
                    <td>{{ $value['noVA']}}</td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td>:</td>
                    <td>{{ $value['jumlahPeriode']}} Bulan</td>
                    <td>Jumlah Peserta</td>
                    <td>:</td>
                    <td> {{ $value['jumlahPeserta']}} <span style="font-weight: bold">Orang</span></td>
                </tr>
                <tr>
                    <td>Tagihan</td>
                    <td>:</td>
                    <td>Rp{{ number_format($value['tagihan'],0,".",".")}}</td>
                    <td>Admin</td>
                    <td>:</td>
                    <td>Rp{{ number_format($value['admin'],0,".",".") }}</td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td>:</td>
                    <td>Rp{{ number_format($value['total'],0,".",".") }} </td>
                    <td>Info</td>
                    <td>:</td>
                    <td>{{ $value['info']}}</td>
                </tr>
                <tr>
                    <td colspan="6" align="center" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:14px;line-height:24px;font-weight:bold;text-align:center;">
                        <label>Nomor Referensi : {{ $value['noReferensi'] }}</label>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="font-weight: bold; font-size: 16px; color:black; text-align: center">
                            BPJS KESEHATAN MENYATAKAN STRUK INI SEBAGAI BUKTI PEMBAYARAN YANG SAH
                    </td>
                </tr>
                <tr>
                    <td colspan="6"><hr></td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>

@endsection