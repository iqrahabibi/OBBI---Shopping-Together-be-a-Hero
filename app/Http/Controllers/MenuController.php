<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Route;

class MenuController extends Controller
{
    public function current_menu($route){
        $path = explode('.', $route)[0];

        // Home
        $menu_home = array('home');

        // Administrator
        $menu_general = array('user', 'agama', 'finance', 'suplier', 'suplierbarang', 'gudang', 'version');
        $menu_wilayah = array('provinsi', 'kota', 'kecamatan', 'kelurahan');
        $menu_license = array('kriterialicense', 'license', 'juallicense');
        $menu_member = array('member', 'saldo');
        $menu_donasi = array('tipedonasi', 'targetdonasi', 'donasi');
        $menu_asset = array('asset', 'penyusutan');
        $menu_barang = array('group', 'category', 'barang', 'barangconversi', 'barangstok', 'barangstokopname', 'baranggambar', 'barangvarian');
        $menu_opf = array('opf', 'pengaduan', 'pengaduan_opf');
        $menu_usaha = array('usaha', 'omerchant', 'usahaomerchant', 'omerchantadmin', 'tabungan');
        $menu_po = array('po', 'voucher', 'pelunasan');
        
        // Admin Gudang
        $menu_po_gudang = array('admingudangmasuk');
        $menu_barang_gudang = array('baranginventory', 'baranggrosir', 'barangnasional');
        $menu_omerchant_gudang = array('admingudangomerchantpo');
        
        // OMerchant Owner
        $menu_monitoring = array('monitoring', 'omerchantowner');
        
        // OMerchant Admin
        $menu_omerchant_admin = array('omerchantpo', 'omerchantadminpo', 'omerchantbarangvarian', 'omerchantbaranggambar', 'omerchantbaranginventory', 'omerchantbaranggrosir', 'omerchantbarangpromosikategori');
        
        // Home
        if (in_array($path, $menu_home))
        {
            return 'menu-home';
        }
        
        // Administrator
        if (in_array($path, $menu_general))
        {
            return 'administrator-menu-general';
        }
        if (in_array($path, $menu_wilayah))
        {
            return 'administrator-menu-wilayah';
        }
        if (in_array($path, $menu_license))
        {
            return 'administrator-menu-license';
        }
        if (in_array($path, $menu_member))
        {
            return 'administrator-menu-member';
        }
        if (in_array($path, $menu_donasi))
        {
            return 'administrator-menu-donasi';
        }
        if (in_array($path, $menu_asset))
        {
            return 'administrator-menu-asset';
        }
        if (in_array($path, $menu_barang))
        {
            return 'administrator-menu-barang';
        }
        if (in_array($path, $menu_opf))
        {
            return 'administrator-menu-opf';
        }
        if (in_array($path, $menu_usaha))
        {
            return 'administrator-menu-usaha';
        }
        if (in_array($path, $menu_po))
        {
            return 'administrator-menu-purchasingorder';
        }

        // Admin Gudang
        if (in_array($path, $menu_po_gudang))
        {
            return 'admingudang-menu-purchasingorder';
        }
        if (in_array($path, $menu_barang_gudang))
        {
            return 'admingudang-menu-barang';
        }
        if (in_array($path, $menu_omerchant_gudang))
        {
            return 'admingudang-menu-omerchant';
        }

        // OMerchant Owner
        if (in_array($path, $menu_monitoring))
        {
            return 'omerchantowner-menu-omerchant';
        }

        // OMerchant Admin
        if (in_array($path, $menu_omerchant_admin))
        {
            return 'omerchantadmin-menu-omerchant';
        }

        return 'Unknown';
    }
}
