@extends('layouts.mailtemplate')

@section('content')

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td class="h2" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
                Hallo <b>{{ $user->fullname }}</b>, akun anda sudah menjadi HEROBI.</td>
        </tr>
    </tbody>
</table>
@endsection