@extends('layouts.mailtemplate')
@section('content')
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td colspan='3' class="h2" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
                <label>STRUK {{$insert->notes}}</label>
            </td>
        </tr>
        <tr>
            <td>ID PELANGGAN</td>
            <TD>:</TD>
            <TD>{{ $json->idpel}}</TD>
        </tr>
        <tr>
            <td>NAMA</td>
            <td>:</td>
            <td>{{ $json->nama}}</td>
        </tr>
        <tr>
            <td>KODE AREA</td>
            <td>:</td>
            <td>{{ $json->kodeArea }}</td>
        </tr>
        <tr>
            <td>JUMLAH TAGIHAN</td>
            <td>:</td>
            <td>{{ $json->jumlahTagihan}}</td>
        </tr>
        <tr>
            <td>DIVRE/ DATEL</td>
            <td>:</td>
            <td>{{ $json->divre}}/ {{$json->datel}}</td>
        </tr>
        <tr>
            <td>DETAIL TAGIHAN</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td colspan='3'>
                <br>
                <table border='1' style='width: 100%;' cellpadding='2' cellspacing='0'>
                    <tr>
                        <th>Periode</th>
                        <th>Tagihan</th>
                        <th>Fee</th>
                        <th>Admin</th>
                        <th>Total</th>
                    </tr>
                    <?php $total_b = 0; ?>
                    @foreach ($json->tagihan as $d)
                        <?php
                        $tagihan = 0;
                        if ( isset($d->nilaiTagihan) )
                            $tagihan = $d->nilaiTagihan;

                        if ( isset($d->nilai_tagihan) )
                            $tagihan = $d->nilai_tagihan;

                        $fee = 0;
                        if ( isset($d->fee) )
                            $fee = $d->fee;

                        $admin = 0;
                        if ( isset($d->admin) )
                            $admin = $d->admin;

                        $total_a = $tagihan + $fee + $admin;
                        $total_b += $total_a;
                        ?>
                        <tr>
                            <td>{{ $d->periode }}</td>
                            <td style='text-align: right;'>
                                Rp {{ number_format($tagihan,0) }}
                            </td>
                            <td style='text-align: right;'>
                                Rp {{ number_format($fee, 0) }}
                            </td>
                            <td style='text-align: right;'>
                                Rp {{ number_format($admin, 0) }}
                            </td>
                            <td style='text-align: right;'>
                                Rp {{ number_format($total_a,0) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan='4' style='text-align: right;'>Total</td>
                        <td style='text-align: right;'>Rp {{ number_format($total_b,0) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center">
                <br>RINCIAN TAGIHAN DAPAT DIAKSES DI www.telkom.com ATAU GERAI TERDEKAT.
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <hr>
            </td>
        </tr>
        {{--<tr>
            <td>PERIODE</td>
            <td>:</td>
            <td>{{ $json->tagihan[0]periode}}</td>
        </tr>
        <tr>
            <td>DIVRE/ DATEL</td>
            <td>:</td>
            <td>{{ $json->divre}}/ {{$json->datel}}</td>
        </tr>
        <tr>
            <td>TOTAL TAGIHAN</td>
            <td>:</td>
            <td>Rp{{ number_format($json->tagihan,0)}}</td>
        </tr>
        <tr>
            <td>ADMIN</td>
            <td>:</td>
            <td>Rp{{ number_format($json->tagihan[0]->admin,0)}}</td>
        </tr>
        <td colspan="3" align="center">RINCIAN TAGIHAN DAPAT DIAKSES DI www.telkom.com ATAU GERAI TERDEKAT.</td>
        </tr>
        <tr>
            <td colspan="3">
                <hr>
            </td>
        </tr>
        </tbody>--}}
    </table>
@endsection