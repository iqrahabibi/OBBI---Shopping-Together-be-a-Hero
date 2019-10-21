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
                    <td>NO METER</td>
                    <TD>:</TD>
                    <TD>{{ $value['data']['msn']}}</TD>
                    <td>Materai</td>
                    <td>:</td>
                    <td>Rp {{ $value['data']['biayaMeterai']}}</td>
                </tr>
                <tr>
                    <td>IDPEL</td>
                    <td>:</td>
                    <td>{{ $value['data']['subscriberID']}}</td>
                    <td>PPN</td>
                    <td>:</td>
                    <td>Rp {{ $value['data']['ppn']}}</td>
                </tr>
                <tr>
                    <td>NAMA</td>
                    <td>:</td>
                    <td>{{ $value['data']['nama']}}</td>
                    <td>PPJ</td>
                    <td>:</td>
                    <td>Rp {{ $value['data']['ppj']}}</td>
                </tr>
                <tr>
                    <td>TARIF/DAYA</td>
                    <td>:</td>
                    <td>{{$value['data']['tarif']}} / {{ $value['data']['daya'] }} VA</td>
                    <td>ANGSURAN</td>
                    <td>:</td>
                    <td>Rp {{ $value['data']['angsuran']}}</td>
                </tr>
                <tr>
                    <td>NO REF</td>
                    <td>:</td>
                    <td>{{ $value['data']['ref'] }}</td>
                    <td>RP STROOM/TOKEN</td>
                    <td>:</td>
                    <td>Rp {{ $value['data']['rpToken']}}</td>
                </tr>
                <tr>
                    <td>RP BAYAR</td>
                    <td>:</td>
                    <td>Rp {{ number_format($value['data']['total'],0)}} </td>
                    <td>JML KWH</td>
                    <td>:</td>
                    <td>{{ $value['data']['kwh']}}</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td>ADMIN</td>
                    <td>:</td>
                    <td>Rp {{ number_format($value['data']['admin'],0)}}</td>
                </tr>
                <tr>
                    <td colspan="6" align="center" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:14px;line-height:24px;font-weight:bold;text-align:center;">
                        <label>STROOM/TOKEN : {{ $g }}</label>
                    </td>
                <tr>
                    <td colspan="6" align="center">RINCIAN TAGIHAN DAPAT DIAKSES DI www.pln.co.id ATAU PLN TERDEKAT.</td>
                </tr>
                <tr>
                    <td colspan="6"><hr></td>
                </tr>
            @else
                <tr>
                    <td colspan="6" align="center" style="color:black;font-weight:bold">Pesan :</td>
                </tr>
                <tr>
                    <td colspan="6" align="center" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:14px;line-height:24px;font-weight:bold;text-align:center;">
                        {{ $value["message"] }}
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" style="color:black;font-weight:bold">Advice ID :</td>
                </tr>
                <tr>
                    <td colspan="6" align="center" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:14px;line-height:24px;font-weight:bold;text-align:center;">
                        {{ $value["manualAdviceHashID"] }}
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
@endsection