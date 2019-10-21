@extends('layouts.mailtemplate')

@section('content')

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        

        <tr>
            <td colspan='3' class="h2" style="color:#153643;font-family:sans-serif;padding:0 0 15px 0;font-size:18px;line-height:24px;font-weight:bold;text-align:center;">
                <label>TRANSAKSI BELANJA </label>
            </td>
        </tr>
        <tr>
            <td>NAMA PELANGGAN</td>
            <TD>:</TD>
            <TD>{{ $user->fullname}}</TD>
        </tr>
        <tr>
            <td>E-MAIL PELANGGAN</td>
            <td>:</td>
            <td>{{ $user->email}}<td>
        </tr>
        <tr>
            <td>NO. INVOICE</td>
            <td>:</td>
            <td>{{ $data_barang->invoice }}</td>
        </tr>
        @if(!empty($data_barang->alamat))
            <tr>
                <td>ALAMAT PENGIRIMAN</td>
                <td>:</td>
                <td>{{ $data_barang->alamat->alamat}}</td>
            </tr>
            <tr>
                <td>NO. TELEPON PENGIRIMAN</td>
                <td>:</td>
                <td>{{ $data_barang->alamat->phone}}</td>
            </tr>
            @foreach($data_barang->cart as $key => $value)
                @if($data_barang->tipe_belanja == "lokal")
                    <tr>
                        <td>NAMA BARANG</td>
                        <td>:</td>
                        <td>{{$value->barang->nama_barang}}</td>
                    </tr>
                    <tr>
                        <td>BRAND</td>
                        <td>:</td>
                        <td>{{ $value->barang->brand}}</td>
                    </tr>
                    <tr>
                        <td>SKU</td>
                        <td>:</td>
                        <td>{{ $value->barang->sku}}</td>
                    </tr>
                @endif
            @endforeach
        @else
            <tr>
                <td colspan="3">Data not found.</td>
            </tr>
        @endif
        <tr>
            <td>KURIR</td>
            <td>:</td>
            <td>{{ $data_barang->kurir }}</td>
        </tr>
        <tr>
            <td>HARGA KIRIM</td>
            <td>:</td>
            <td>{{ $data_barang->harga_kirim}}</td>
        </tr>
        <tr>
            <td>TOTAL BELANJA</td>
            <td>:</td>
            <td>Rp{{ number_format($data_barang->total_belanja,0,',',',')}}</td>
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
            
    </tbody>
</table>
@endsection