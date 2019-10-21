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
                <td>NAMA PELANGGAN</td>
                <td>:</td>
                <td>{{ $user->fullname}}<td>
            </tr>
            <tr>
                <td>NO. TRNASAKSI</td>
                <td>:</td>
                <td>{{ $value['trxID'] }}</td>
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
            
        @endforeach
    </tbody>
</table>
@endsection