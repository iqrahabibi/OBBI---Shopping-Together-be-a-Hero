@extends('layouts.mailtemplate')

@section('content')
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="h2" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
            Hallo <b>{{ $user->fullname }}</b>, pengajuan diri Anda menjadi Herobi kami tolak dikarenakan tidak sesuai dengan prosedur yang berlaku.</td>
    </tr>
    <tr>
        <td>Berikut ini catatan yang perlu diperhatikan: {{ $notes }}</td>
    </tr>
    <tr>
        <td class="bodycopy" style="color:#153643;font-family:sans-serif;font-size:16px;line-height:22px;">
            <center>Terima Kasih</center>
        </td>
    </tr>
</table>
@endsection