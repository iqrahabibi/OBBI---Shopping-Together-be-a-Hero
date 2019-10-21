@extends('layouts.mailtemplate')

@section('content')
<table width="100%" class="border">
    <tr>
        <td class="h2" style="color:#000;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
            Dear Admin,
        </td>
    </tr>
    <tr class="bodycopy" style="color:#000;font-family:sans-serif;font-size:16px;line-height:22px;">

        <td> 
            {{ $content }}
        </td>
    </tr>
</table>
<p>Email ini dibuat secara otomatis. Mohon tidak mengirimkan balasan ke email ini.</p>

<table width="100%" style="padding:10px 5px 5px 10px;text-align:justify;font-family:sans-serif">
    <tr>
        <td class="bodycopy">
            Download Aplikasi OBBI <br>
            <a href="">
                <img src="https://play.google.com/intl/en_us/badges/images/generic/en_badge_web_generic.png" alt="" width="100">
            </a>
            <a href="">
                <img src="https://iupat.org/wp-content/uploads/app-store-logo.png" alt="" width="100">
            </a>
        </td>
        <td class="bodycopy">
            Ikuti Kami : <br>
            <a href="" style="color:#cfd8dc">
                <img src="https://www.shareicon.net/data/512x512/2015/08/30/92950_media_512x512.png" alt="" width="50">
            </a>
            <a href="" style="color:#cfd8dc">
                <img src="https://cdn4.iconfinder.com/data/icons/social-icons-6/40/instagram-512.png" alt="" width="50">
            </a>
            <a href="" style="color:#cfd8dc">
                <img src="http://cherrycapitalfoods.com/wp-content/uploads/2015/05/facebook-grey.png" alt="" width="50">
            </a>
            <a href="" style="color:#cfd8dc">
                <img src="http://legisperiti.ru/wp-content/uploads/2016/03/google_plus_grey.png" alt="" width="50">
            </a>

        </td>
    </tr>
</table>
@endsection
