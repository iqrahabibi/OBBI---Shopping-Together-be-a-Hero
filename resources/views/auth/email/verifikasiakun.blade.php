@extends('layouts.mailtemplate')

@section('content')
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="h2" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
            Selamat bergabung dengan OBBI!
        </td>
    </tr>
    <tr>

        <td class="bodycopy" style="color:#153643;font-family:sans-serif;font-size:16px;line-height:22px;">
            <br>
            hai, <b> {{ $user->fullname }}</b><br>
            Terima kasih sudah mendaftar akun OBBI<br>
            Silakan masukan kode verifikasi tersebut :

            <br><br>
        </td>
    </tr>
    <tr>
        <td style="padding: 40px 0 0 0;">
            <center>
                <table class="buttonwrapper" bgcolor="#1e88e5" border="0" cellspacing="0" cellpadding="0" style='align-content: center;'>
                    <tr>
                        <td class="button" height="45" style='text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px;'>
                            <a style='color: #ffffff; text-decoration: none;'>{{ $user->verification_token }}</a>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
</table>
@endsection
