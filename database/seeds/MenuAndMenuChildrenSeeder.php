<?php

use Illuminate\Database\Seeder;

class MenuAndMenuChildrenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            // Administrator
            App\Model\Menu::create(['kode'=>'administrator-menu-general', 'nama'=>'Administrator Menu General']);
            App\Model\Menu::create(['kode'=>'administrator-menu-wilayah', 'nama'=>'Administrator Menu Wilayah']);
            App\Model\Menu::create(['kode'=>'administrator-menu-license', 'nama'=>'Administrator Menu License']);
            App\Model\Menu::create(['kode'=>'administrator-menu-member', 'nama'=>'Administrator Menu Member']);
            App\Model\Menu::create(['kode'=>'administrator-menu-donasi', 'nama'=>'Administrator Menu Donasi']);
            App\Model\Menu::create(['kode'=>'administrator-menu-asset', 'nama'=>'Administrator Menu Asset']);
            App\Model\Menu::create(['kode'=>'administrator-menu-barang', 'nama'=>'Administrator Menu Barang']);
            App\Model\Menu::create(['kode'=>'administrator-menu-opf', 'nama'=>'Administrator Menu OPF']);
            App\Model\Menu::create(['kode'=>'administrator-menu-usaha', 'nama'=>'Administrator Menu Usaha']);
            App\Model\Menu::create(['kode'=>'administrator-menu-purchasingorder', 'nama'=>'Administrator Menu Purchasingorder']);

            // Admin Gudang
            App\Model\Menu::create(['kode'=>'admingudang-menu-purchasingorder', 'nama'=>'Admin Gudang Menu Purchasingorder']);
            App\Model\Menu::create(['kode'=>'admingudang-menu-barang', 'nama'=>'Admin Gudang Menu Barang']);
            App\Model\Menu::create(['kode'=>'admingudang-menu-omerchant', 'nama'=>'Admin Gudang Menu OMerchant']);

            // OMerchant Owner
            App\Model\Menu::create(['kode'=>'omerchantowner-menu-omerchant', 'nama'=>'OMerchant Owner Menu OMerchant']);

            // OMerchant Admin
            App\Model\Menu::create(['kode'=>'omerchantadmin-menu-omerchant', 'nama'=>'OMerchant Admin Menu OMerchant']);
    }
}
