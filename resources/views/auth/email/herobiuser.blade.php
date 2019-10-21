@extends('layouts.mailtemplate')

@section('content')
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="h2" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
            Halo {{$user['fullname']}}, saat ini permintaan Anda sedang di proses oleh Manajemen OBBI dalam waktu 1 - 3 hari kerja ( tidak termasuk sabtu, minggu dan hari libur nasional). Terima kasih :)
        </td>
    </tr>
</table>
@endsection