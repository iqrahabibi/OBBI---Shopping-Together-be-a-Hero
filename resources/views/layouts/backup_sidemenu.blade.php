<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Role Anda</h3>
    </div>
    <div class="list-group">
        <a href="" class="list-group-item">
            {{ Auth::user()->akses() }}
        </a>
    </div>
</div>

@can('admin-access')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Administrator - Menu General</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('user.index') }}" class="list-group-item">
            User Office
        </a>
        <a href="{{ route('agama.index') }}" class="list-group-item">
            Agama
        </a>
        <a href="{{ route('finance.index') }}" class="list-group-item">
            Finance
        </a>
        <a href="{{ route('suplier.index') }}" class="list-group-item">
            Suplier
        </a>
        <a href="{{ route('gudang.index') }}" class="list-group-item">
            Gudang
        </a>
        <a href="{{ route('version.index') }}" class="list-group-item">
            Versi
        </a>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Administrator - Menu Wilayah</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('provinsi.index') }}" class="list-group-item">
            Provinsi
        </a>
        <a href="{{ route('kota.index') }}" class="list-group-item">
            Kota
        </a>
        <a href="{{ route('kecamatan.index') }}" class="list-group-item">
            Kecamatan
        </a>
        <a href="{{ route('kelurahan.index') }}" class="list-group-item">
            Kelurahan
        </a>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Administrator - Menu License</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('kriterialicense.index') }}" class="list-group-item">
            Kriteria License
        </a>
        <a href="{{ route('license.index') }}" class="list-group-item">
            License
        </a>
        <a href="{{ route('juallicense.index') }}" class="list-group-item">
            Jual License
        </a>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Administrator - Menu Member</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('member.index') }}" class="list-group-item">
            Member
        </a>
        <a href="{{ route('saldo.index') }}" class="list-group-item">
            Saldo
        </a>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Administrator - Menu Donasi</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('tipedonasi.index') }}" class="list-group-item">
            Tipe Donasi
        </a>
        <a href="{{ route('targetdonasi.index') }}" class="list-group-item">
            Target Donasi
        </a>
        <a href="{{ route('donasi.index') }}" class="list-group-item">
            Donasi
        </a>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Administrator - Menu Asset</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('asset.index') }}" class="list-group-item">
            Asset
        </a>
        <a href="{{ route('penyusutan.index') }}" class="list-group-item">
            Penyusutan
        </a>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Administrator - Menu Barang</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('group.index') }}" class="list-group-item">
            Group Barang
        </a>
        <a href="{{ route('category.index') }}" class="list-group-item">
            Kategori Barang
        </a>
        <a href="{{ route('barang.index') }}" class="list-group-item">
            Barang

        </a>
        <a href="{{ route('barangconversi.index') }}" class="list-group-item">
            Barang Conversi
        </a>
        <a href="{{ route('barangstok.index') }}" class="list-group-item">
            Barang Stok
        </a>
        <a href="{{ route('barangstokopname.index') }}" class="list-group-item">
            Barang Stok Opname
        </a>
        <a href="{{ route('baranggambar.index') }}" class="list-group-item">
            Barang Gambar
        </a>
        <a href="{{ route('barangvarian.index') }}" class="list-group-item">
            Barang Varian
        </a>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Administrator - Menu OPF</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('opf.index')}}" class="list-group-item">
            OPF
        </a>
        <a href="{{ route('pengaduan.aduan')}}" class="list-group-item">
            Pengaduan Opf
        </a>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Administrator - Menu Usaha</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('usaha.index') }}" class="list-group-item">
            Nama Usaha
        </a>
        <a href="{{ route('omerchant.index') }}" class="list-group-item">
            Nama OMerchant
        </a>
        <a href="{{ route('usahaomerchant.index') }}" class="list-group-item">
            List Usaha OMerchant
        </a>
        <a href="{{ route('omerchantadmin.index') }}" class="list-group-item">
            Karyawan Toko Usaha
        </a>
        <a href="{{ route('tabungan.index') }}" class="list-group-item">
            Tabungan OMerchant
        </a>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Administrator - Menu Purchasing Order</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('po.index') }}" class="list-group-item">
            Purchasing Order
        </a>
        <a href="{{ route('voucher.index') }}" class="list-group-item">
            Voucher
        </a>
        <a href="{{ route('pelunasan.index') }}" class="list-group-item">
            Pelunasan
        </a>
    </div>
</div>
@endcan

@can('admin-gudang-access')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Admin Gudang - Menu Barang</h3>
    </div>
    <div class="list-group">
        <a href="{{ route('baranginventory.index') }}" class="list-group-item">
            Barang Inventory
        </a>
        <a href="{{ route('baranggrosir.index') }}" class="list-group-item">
            Barang Grosir
        </a>
        <a href="{{ route('barangnasional.index') }}" class="list-group-item">
            Barang Nasional
        </a>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Admin Gudang - Menu Purchasing Order</h3>
    </div>
    <div class="list-group">
        <a href="{{route('admingudang.pomasuk')}}" class="list-group-item">
            PO Masuk Gudang
        </a>
        <a href="{{route('admingudangpo.index')}}" class="list-group-item">
            OMerchant PO
        </a>
    </div>
</div>
@endcan

@can('omerchant-owner-access')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">OMerchant Owner - Menu OMerchant</h3>
    </div>
    <div class="list-group">
        <a href="{{route('omerchantpo.monitoring')}}" class="list-group-item">
            Monitoring OMerchant Purchasing Order
        </a>
        <a href="{{route('om_barang.index')}}" class="list-group-item">
            OMerchant Barang
        </a>
    </div>
</div>
@endcan

@can('omerchant-admin-access')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">OMerchant Admin - Menu OMerchant</h3>
    </div>
    <div class="list-group">
        <a href="{{route('omerchantpo.index')}}" class="list-group-item">
            OMerchant PO
        </a>
        <a href="{{route('omerchantadminpo.index')}}" class="list-group-item">
            OMerchant PO Masuk
        </a>
        <a href="{{route('omerchantbarangvarian.index')}}" class="list-group-item">
            OMerchant Barang Varian
        </a>
        <a href="{{route('omerchantbaranggambar.index')}}" class="list-group-item">
            OMerchant Barang Gambar
        </a>
        <a href="{{route('omerchantbaranginventory.index')}}" class="list-group-item">
            OMerchant Barang Inventory
        </a>
        <a href="{{route('omerchantbaranggrosir.index')}}" class="list-group-item">
            OMerchant Barang Grosir
        </a>
    </div>
</div>
@endcan
