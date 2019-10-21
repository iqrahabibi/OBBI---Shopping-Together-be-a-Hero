<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::get('/', function () {
    /*$insert = (object) [
        'notes' => 'NAMA STRUKS'
    ];

    $json = json_decode(<<<EOD
{
    "responseCode": "00",
    "message": "Approved",
    "idpel": "0751675732",
    "kodeArea": "0751",
    "divre": "01",
    "datel": "0006",
    "nama": "JAORAH",
    "jumlahTagihan": "3",
    "tagihan": [
        {
            "periode": "MEI 2014",
            "nilaiTagihan": "49870",
            "admin": "2500",
            "total": 52370
        },
        {
            "periode": "JUN 2014",
            "nilaiTagihan": "44870",
            "admin": "2500",
            "total": 47370
        },
        {
            "periode": "JUL 2014",
            "nilaiTagihan": "34870",
            "admin": "2500",
            "total": 37370
        }
    ],
    "totalTagihan": 137110
}
EOD
    );

    return view('auth.email.telkom', compact('insert', 'json'));*/
    return response('Hello World', 200)->header('Content-Type', 'text/plain');
});

//Route::get('/', function () {
//    $user = \App\Model\User::where([
//        [ 'id', '=', '7995' ]
//    ])->first();
//
//    $image = \App\Helper\ObbiAssets::get_asset(0, $user->image);
//    echo $image;
//    echo '<br>';
//    echo "<img src='{$image}'/>";
//});

Auth::routes();

Route::group([ 'middleware' => 'auth' ], function () {
    Route::get('/home', 'HomeController@index')
         ->name('home');
    Route::get('/currentmenu/{route}', 'MenuController@current_menu')
         ->name('currentmenu');
    // Route::resource('currentmenu', 'MenuController');

    /**
     * Super Admin and Administrator
     */
    Route::group([ 'middleware' => 'can:super-and-admin-access' ], function () {
        /**
         * Menu User
         */
        Route::resource('user', 'Administrator\UserController');
        Route::get('/user/role/{id}', 'Administrator\UserController@role')
             ->name('user.role');
        Route::post('/user/role', 'Administrator\UserController@setrole')
             ->name('user.setrole');
    });

    Route::group([ 'middleware' => 'can:super-access' ], function () {
        /**
         * Data Menu
         */
        Route::resource('menu', 'Administrator\MenuController');

        /**
         * Data Role
         */
        Route::resource('role', 'Administrator\RolesController');
        Route::get('/role/menu/{id}', 'Administrator\RolesController@menu')
             ->name('role.menu');
        Route::get('/role/menu/create/{id}', 'Administrator\RolesController@menu_create')
             ->name('role.menu.create');
        Route::post('/role/menu/store', 'Administrator\RolesController@menu_store')
             ->name('role.menu.store');
        Route::delete('/role/menu/destroy/{id}', 'Administrator\RolesController@menu_destroy')
             ->name('role.menu.destroy');

        /**
         * Menu Version
         */
        Route::resource('version', 'Administrator\VersionController');

        /**
         * Menu Log
         */
        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    });

    Route::group([ 'middleware' => 'can:admin-access' ], function () {
        /**
         * Menu Lokasi
         */
        Route::resource('provinsi', 'Administrator\ProvinsiController');
        Route::resource('kota', 'Administrator\KotaController');
        Route::resource('kecamatan', 'Administrator\KecamatanController');
        Route::resource('kelurahan', 'Administrator\KelurahanController');

        Route::get('/kota/data/{id}', 'Administrator\KotaController@list');
        Route::get('/kecamatan/data/{id}', 'Administrator\KecamatanController@list');
        Route::get('/kelurahan/data/{id}', 'Administrator\KelurahanController@list');

        /**
         * Menu Finance
         */
        Route::resource('finance', 'Administrator\FinanceController');

        /**
         * Menu Agama
         */
        Route::resource('agama', 'Administrator\AgamaController');

        /**
         * Menu Suplier
         */
        Route::resource('suplier', 'Administrator\SuplierController');

        /**
         * Master Jabatan
         */
        Route::resource('divisi', 'HumanResource\DivisiController');

        /**
         * Master Divisi
         */
        Route::resource('jabatan', 'HumanResource\JabatanController');

        /**
         * Master Karyawan
         */
        Route::resource('karyawan', 'HumanResource\KaryawanController');

        /**
         * Master Salary
         */
        Route::resource('salary', 'HumanResource\SalaryController');

        /**
         * Master Absensi
         */
        Route::resource('absensi', 'HumanResource\AbsensiController');

        /**
         * Menu Suplier Barang
         */
        Route::resource('suplierbarang', 'Administrator\SuplierBarangController');
        Route::get('/suplierbarang/listbarang/{id}', 'Administrator\SuplierBarangController@listbarang')
             ->name('suplierbarang.listbarang');

        /**
         * Menu Donasi
         */
        Route::resource('tipedonasi', 'Administrator\TipeDonasiController');
        Route::resource('targetdonasi', 'Administrator\TargetDonasiController');
        Route::get('/targetdonasi/data/{id}', 'Administrator\TargetDonasiController@list');
        Route::resource('donasi', 'Administrator\DonasiController');
        // Route::get('/donasi/data/{id}', 'Transaksi\DonasiController@list');
        Route::get('/donasi/create/kelurahan', 'Administrator\DonasiController@create_kelurahan')
             ->name('donasi.create_kelurahan');
        Route::post('/donasi/create/kelurahan', 'Administrator\DonasiController@store_kelurahan')
             ->name('donasi.store_kelurahan');

        // Menu Group Barang

        Route::resource('group', 'Administrator\GroupController');

        // Menu Kategori Barang

        Route::resource('category', 'Administrator\CategoryController');

        /**
         * Menu License
         */
        Route::resource('kriterialicense', 'Administrator\KriteriaLicenseController');
        Route::resource('license', 'Administrator\LicenseController');

        /**
         * Menu Usaha & OMerchant
         */
        Route::resource('usaha', 'Administrator\UsahaController');
        Route::get('usaha/ubah/status/{id}', 'Administrator\UsahaController@ubah_status')
             ->name('usaha.ubah_status');
        Route::resource('omerchant', 'Administrator\OMerchantController');
        Route::get('omerchant/random/referal', 'Administrator\OMerchantController@randomreferal')
             ->name('omerchant.randomreferal');
        Route::get('omerchant/referal/{id}', 'Administrator\OMerchantController@referal_omerchant')
             ->name('omerchant.referal');

        Route::resource('usahaomerchant', 'Administrator\UsahaOMerchantController');
        Route::resource('omerchantadmin', 'Administrator\OMerchantAdminController');

        Route::resource('omvoucher', 'Administrator\OMVoucherController');

        Route::resource('ompelunasan', 'Administrator\OMPelunasanController');

        /**
         * Menu Barang
         */
        Route::resource('barang', 'Administrator\BarangController');
        Route::resource('barangconversi', 'Administrator\BarangConversiController');

        /**
         * Menu Gudang
         */
        Route::resource('gudang', 'Administrator\GudangController');
        Route::resource('gudangkurir', 'Administrator\GudangKurirController');

        /**
         * Menu Barang Gambar
         */
        Route::resource('baranggambar', 'Administrator\BarangGambarController');

        /**
         * Menu Barang Varian
         */
        Route::resource('barangvarian', 'Administrator\BarangVarianController');

        /**
         * Menu Member and Herobi
         */
        Route::resource('member', 'Administrator\MemberController');
        Route::post('/member/verify/{id}', 'Administrator\MemberController@verify')
             ->name('member.verify');
        Route::post('/member/revoke/{id}', 'Administrator\MemberController@revoke')
             ->name('member.revoke');
        Route::get('/member/herobi/{id}', 'Administrator\MemberController@herobi')
             ->name('member.herobi');
        Route::post('/member/herobi/approve/{id}', 'Administrator\MemberController@approve')
             ->name('member.herobi.approve');
        Route::post('/member/herobi/deny/{id}', 'Administrator\MemberController@deny')
             ->name('member.herobi.deny');
        Route::get('/member/herobi/referal/{id}', 'Administrator\MemberController@referal_herobi')
             ->name('member.herobi.referal');

        /**
         * Menu Asset
         */
        Route::resource('asset', 'Administrator\AssetController');
        Route::resource('penyusutan', 'Administrator\PenyusutanController');

        /**
         * Menu OPF
         */
        Route::resource('opf', 'Administrator\OpfController');
        Route::get('referal/{id}/create', 'Administrator\OpfController@createreferal')
             ->name('opf.createreferal');
        Route::post('referal/{id}/create', 'Administrator\OpfController@savereferal')
             ->name('opf.savereferal');
        Route::get('referal/{id}', 'Administrator\OpfController@referal')
             ->name('opf.referal');
        Route::get('pengaduan', 'Administrator\OpfController@aduan')
             ->name('pengaduan.aduan');
        Route::get('pengaduan/{id}/edit', 'Administrator\OpfController@editaduan')
             ->name('pengaduan.editaduan');
        Route::post('pengaduan/{id}/update', 'Administrator\OpfController@updateaduan')
             ->name('pengaduan.updateaduan');

        /**
         * Menu Saldo
         */
        Route::resource('saldo', 'Administrator\SaldoController');
        Route::get('/saldo/history/{id}', 'Administrator\SaldoController@history')
             ->name('saldo.history');
        Route::post('/saldo/history/{id}', 'Administrator\SaldoController@validasi')
             ->name('saldo.validasi');

        /**
         * Menu License
         */
        Route::resource('juallicense', 'Administrator\JualLicenseController');
        Route::get('/juallicense/owner/{id}', 'Administrator\JualLicenseController@owner')
             ->name('juallicense.owner');
        Route::get('/juallicense/hibah/{id}', 'Administrator\JualLicenseController@hibah')
             ->name('juallicense.hibah');
        Route::patch('/juallicense/hibah/{id}', 'Administrator\JualLicenseController@savehibah')
             ->name('juallicense.savehibah');

        /**
         * Menu Tabungan
         */
        Route::resource('tabungan', 'Administrator\TabunganController');
        Route::get('/tabungan/usahaomerchant/{id}', 'Administrator\TabunganController@usahaomerchant')
             ->name('tabungan.usahaomerchant');

        /**
         * Menu Purchasing Order
         */
        Route::resource('po', 'Administrator\PurchasingOrderController');
        Route::get('/po/detail/{id}', 'Administrator\PurchasingOrderController@detail')
             ->name('po.detail');
        Route::get('/po/masuk/{id}', 'Administrator\PurchasingOrderController@pomasuk')
             ->name('po.masuk');
        Route::post('/po/masuk/{id}', 'Administrator\PurchasingOrderController@pomasuksave')
             ->name('po.masuksave');
        Route::get('/po/retur/{id}', 'Administrator\PurchasingOrderController@retur')
             ->name('po.retur');
        Route::get('/po/cetak/{id}', 'Administrator\PurchasingOrderController@cetak')
             ->name('po.cetak');
        Route::get('/po/closed/{id}', 'Administrator\PurchasingOrderController@closed')
             ->name('po.closed');

        Route::resource('voucher', 'Administrator\VoucherController');
        Route::resource('pelunasan', 'Administrator\PelunasanController');

        /**
         * Transaksi Checkout Nasional
         */
        Route::resource('checkoutnasional', 'Administrator\ChekoutNasionalController');

        /**
         * Transaksi Checkout Daerah
         */
        Route::resource('checkoutdaerah', 'Administrator\CheckoutDataDaerahController');

    });

    Route::group([ 'middleware' => 'can:admin-gudang-access' ], function () {

        Route::resource('admingudangmasuk', 'AdminGudang\MasukController');
        Route::get('/admingudangmasuk/pomasuk', 'AdminGudang\MasukController@pomasuk')
             ->name('admingudangmasuk.pomasuk');
        Route::get('/admingudangmasuk/prosespomasuk/{id}', 'AdminGudang\MasukController@prosespomasuk')
             ->name('admingudangmasuk.prosespomasuk');
        Route::post('/admingudangmasuk/prosespomasuk/{id}', 'AdminGudang\MasukController@prosespomasuksave')
             ->name('admingudangmasuk.prosespomasuksave');
        Route::get('/admingudangmasuk/cetak/{id}', 'AdminGudang\MasukController@cetak')
             ->name('admingudangmasuk.cetak');
        Route::get('/admingudangmasuk/closed/{id}', 'AdminGudang\MasukController@closed')
             ->name('admingudangmasuk.closed');

        /**
         * Master Barang Inventory
         */
        Route::resource('baranginventory', 'AdminGudang\BarangInventoryController');

        /**
         * Master Barang Grosir
         */
        Route::resource('baranggrosir', 'AdminGudang\BarangGrosirController');

        /**
         * List Barang Varian
         */
        Route::get('/admingudang/barangvarian/data/{id}', 'AdminGudang\BarangGrosirController@listbarangvarian');

        /**
         * Transaksi Barang Nasional
         */
        Route::resource('barangnasional', 'AdminGudang\BarangNasionalController');

        /**
         * OMerchant Purchasing Order for Gudang
         */
        Route::resource('admingudangomerchantpo', 'AdminGudang\OMerchantPoController');
        Route::get('/admingudangomerchantpo/verify/{id}/{status}', 'AdminGudang\OMerchantPoController@verify')
             ->name('admingudangomerchantpo.verify');

        /**
         * Checkout Admin Gudang
         */
        Route::resource('transaksinasional', 'AdminGudang\CheckoutDataNasional');
        Route::get('transaksinasional/sending/{id}', 'AdminGudang\CheckoutDataNasional@sending')
             ->name('transaksinasional.sending');

        /**
         * Transaksi Stok Opname
         */
        Route::resource('barangstokopname', 'Administrator\BarangStokOpnameController');
    });

    Route::group([ 'middleware' => 'can:omerchant-owner-access' ], function () {
        Route::get('/monitoring/', 'OMerchantOwner\OMerchantPurchasingOrderController@monitoring')
             ->name('omerchantowner.monitoring');
        Route::get('/monitoring/{id}', 'OMerchantOwner\OMerchantPurchasingOrderController@monitoringdetil')
             ->name('omerchantowner.monitoringdetil');

        /**
         * Barang O-Merchant
         */
        // Route::resource('/om_barang','Transaksi\OMerchantBarangController');
        // Route::get('/om_barang/status/{id}','Transaksi\OMerchantBarangController@status')->name('om_barang.status');
    });

    Route::group([ 'middleware' => 'can:omerchant-admin-access' ], function () {
        /**
         * Transaksi O-Merchant Purchasing Order
         */
        Route::resource('omerchantpo', 'OMerchantAdmin\OMerchantPurchasingOrderController');
        Route::get('/omerchantpo/masuk/{id}', 'OMerchantAdmin\OMerchantPurchasingOrderController@masuk')
             ->name('omerchantpo.masuk');
        Route::post('/omerchantpo/masuk/{id}', 'OMerchantAdmin\OMerchantPurchasingOrderController@masuksave')
             ->name('omerchantpo.masuksave');
        Route::get('/omerchantpo/processed/{id}', 'OMerchantAdmin\OMerchantPurchasingOrderController@processed')
             ->name('omerchantpo.processed');
        Route::get('/omerchantpo/closed/{id}', 'OMerchantAdmin\OMerchantPurchasingOrderController@closed')
             ->name('omerchantpo.closed');

        Route::get('/omerchantadminpo', 'OMerchantAdmin\OMerchantAdminPoController@index')
             ->name('omerchantadminpo.index');
        Route::get('/omerchantadminpo/index/{id}', 'OMerchantAdmin\OMerchantAdminPoController@show')
             ->name('omerchantadminpo.show');
        Route::get('/omerchantadminpo/masuk/{id}', 'OMerchantAdmin\OMerchantAdminPoController@pomasuk')
             ->name('omerchantadminpo.masuk');
        Route::post('/omerchantadminpo/masuk/{id}', 'OMerchantAdmin\OMerchantAdminPoController@pomasuksave')
             ->name('omerchantadminpo.masuksave');
        Route::get('/omerchantadminpo/retur/{id}', 'OMerchantAdmin\OMerchantAdminPoController@retur')
             ->name('omerchantadminpo.retur');
        Route::get('/omerchantadminpo/cetak/{id}', 'OMerchantAdmin\OMerchantAdminPoController@cetak')
             ->name('omerchantadminpo.cetak');

        /**
         * OMerchant Barang Varian
         */
        Route::resource('omerchantbarangvarian', 'OMerchantAdmin\OMerchantBarangVarianController');

        /**
         * OMerchant Barang Gambar
         */
        Route::resource('omerchantbaranggambar', 'OMerchantAdmin\OMerchantBarangGambarController');

        /**
         * OMerchant Barang Inventory
         */
        Route::resource('omerchantbaranginventory', 'OMerchantAdmin\OMerchantBarangInventoryController');

        /**
         * OMerchant Barang Grosir
         */
        Route::resource('omerchantbaranggrosir', 'OMerchantAdmin\OMerchantBarangGrosirController');
        Route::get('/omerchantadmin/barangvarian/data/{id}', 'OMerchantAdmin\OMerchantBarangGrosirController@listombarangvarian');

        /**
         * Transaksi OMerchant Barang Promisi Kategori
         */
        Route::resource('omerchantbarangpromosikategori', 'OMerchantAdmin\OMerchantBarangPromosiKategoriController');

        /**
         * Transaksi OMerchant Barang Promosi
         */
        Route::resource('omerchantbarangpromosi', 'OMerchantAdmin\OMerchantBarangPromosiController');

        /**
         * Transaksi Daerah
         */
        Route::resource('transaksidaerah', 'OMerchantAdmin\CheckoutDaerahController');
        Route::get('transaksidaerah/sending/{id}', 'OMerchantAdmin\CheckoutDaerahController@sending')
             ->name('transaksidaerah.sending');

        /**
         * Transaksi Offline
         */
        Route::resource('kasir', 'OMerchantAdmin\CheckoutOfflineController');
        Route::post('/kasir/harga_satuan', 'OMerchantAdmin\CheckoutOfflineController@show_harga_satuan');
        Route::post('/kasir/barang', 'OMerchantAdmin\CheckoutOfflineController@show_barang');
    });

    Route::group([ 'middleware' => 'can:admin-herobi-opf' ], function () {
        /**
         * Menu Member and Herobi
         */
        Route::resource('member', 'Administrator\MemberController');
        Route::post('/member/verify/{id}', 'Administrator\MemberController@verify')
             ->name('member.verify');
        Route::post('/member/revoke/{id}', 'Administrator\MemberController@revoke')
             ->name('member.revoke');
        Route::get('/member/herobi/{id}', 'Administrator\MemberController@herobi')
             ->name('member.herobi');
        Route::post('/member/herobi/approve/{id}', 'Administrator\MemberController@approve')
             ->name('member.herobi.approve');
        Route::post('/member/herobi/deny/{id}', 'Administrator\MemberController@deny')
             ->name('member.herobi.deny');
        Route::get('/member/herobi/referal/{id}', 'Administrator\MemberController@referal_herobi')
             ->name('member.herobi.referal');

        /**
         * Menu OPF
         */
        Route::resource('opf', 'Administrator\OpfController');
        Route::get('referal/{id}/create', 'Administrator\OpfController@createreferal')
             ->name('opf.createreferal');
        Route::post('referal/{id}/create', 'Administrator\OpfController@savereferal')
             ->name('opf.savereferal');
        Route::get('referal/{id}', 'Administrator\OpfController@referal')
             ->name('opf.referal');
        Route::get('pengaduan', 'Administrator\OpfController@aduan')
             ->name('pengaduan.aduan');
        Route::get('pengaduan/{id}/edit', 'Administrator\OpfController@editaduan')
             ->name('pengaduan.editaduan');
        Route::post('pengaduan/{id}/update', 'Administrator\OpfController@updateaduan')
             ->name('pengaduan.updateaduan');
    });

    Route::group([ 'middleware' => 'can:purchasing' ], function () {
        /**
         * Menu Purchasing Order
         */
        Route::resource('po', 'Administrator\PurchasingOrderController');
        Route::get('/po/detail/{id}', 'Administrator\PurchasingOrderController@detail')
             ->name('po.detail');
        Route::get('/po/masuk/{id}', 'Administrator\PurchasingOrderController@pomasuk')
             ->name('po.masuk');
        Route::post('/po/masuk/{id}', 'Administrator\PurchasingOrderController@pomasuksave')
             ->name('po.masuksave');
        Route::get('/po/retur/{id}', 'Administrator\PurchasingOrderController@retur')
             ->name('po.retur');
        Route::get('/po/cetak/{id}', 'Administrator\PurchasingOrderController@cetak')
             ->name('po.cetak');
        Route::get('/po/closed/{id}', 'Administrator\PurchasingOrderController@closed')
             ->name('po.closed');

        Route::resource('voucher', 'Administrator\VoucherController');
        Route::resource('pelunasan', 'Administrator\PelunasanController');

        /**
         * Menu Suplier
         */
        Route::resource('suplier', 'Administrator\SuplierController');

        /**
         * Menu Suplier Barang
         */
        Route::resource('suplierbarang', 'Administrator\SuplierBarangController');
        Route::get('/suplierbarang/listbarang/{id}', 'Administrator\SuplierBarangController@listbarang')
             ->name('suplierbarang.listbarang');
    });
});