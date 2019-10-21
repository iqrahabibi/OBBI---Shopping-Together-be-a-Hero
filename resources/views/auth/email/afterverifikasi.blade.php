@extends('layouts.mailtemplate')

@section('content')
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="h2" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
            Selamat bergabung dengan OBBI!
        </td>
        <td><hr style="border-color:1px solid #153643"></td>
    </tr>
    <tr>

        <td class="bodycopy" style="color:#153643;font-family:sans-serif;font-size:16px;line-height:22px;">
            <br>
            hai,<b> {{ $check->fullname }}</b><br>
            Akun Anda sudah kami aktifkan. Anda mendaftar menggunakan email<br>
            {{ $check->email }}. Masukan email dan password yang Anda daftarkan <br>
            tersebut setiap kali log in OBBI.

            <br><br>
        </td>
    </tr>
    <tr>
        <td style="padding: 40px 0 0 0;">
            <center>
                <p style="font-family:sans-serif;font-size:16px;line-height:22px;">Siap mencari produk dari ribuan <br>
                penjual online di Daerah kamu?</p>
                <table class="buttonwrapper" bgcolor="#1e88e5" border="0" cellspacing="0" cellpadding="0" style='align-content: center;'>
                    <tr>
                        <td class="button" height="45" style='text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px;'>
                            <a href="" style='color: #ffffff; text-decoration: none;'>Cari disini</a>
                        </td>
                    </tr>
                </table>
                <p style="font-family:sans-serif;font-size:16px;line-height:22px;">Atau Mau membuka toko bersama</p>
                <table class="buttonwrapper" bgcolor="#1e88e5" border="0" cellspacing="0" cellpadding="0" style='align-content: center;'>
                    <tr>
                        <td class="button" height="45" style='text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px;'>
                            <a href="" style='color: #ffffff; text-decoration: none;'>Buka Toko</a>
                        </td>
                    </tr>
                </table>
                <p style="font-family:sans-serif;font-size:16px;line-height:22px;">Atau mau membuka gudang bersama</p>
                <table class="buttonwrapper" bgcolor="#1e88e5" border="0" cellspacing="0" cellpadding="0" style='align-content: center;'>
                    <tr>
                        <td class="button" height="45" style='text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px;'>
                            <a href="" style='color: #ffffff; text-decoration: none;'>Buka Gudang</a>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
</table>
@endsection
