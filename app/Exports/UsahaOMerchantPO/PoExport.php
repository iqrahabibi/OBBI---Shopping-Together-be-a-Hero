<?php

namespace App\Exports\UsahaOMerchantPO;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Model\OMerchantPo;
use App\Model\OMerchantPoMasuk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PoExport implements FromCollection, ShouldAutoSize, WithTitle, WithEvents
{
    use Exportable, RegistersEventListeners;

    public static $id;
    public static $collection = array();

    public static function id($id){
        self::$id = $id;
    }

    /**
     * Call this method to get singleton
     *
     * @return UserFactory
     */
    public static function Instance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new PoExport();
        }
        return $instance;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Purchasing Order';
    }

    public static function beforeExport(BeforeExport $event)
    {
        $event->writer->getProperties()->setCreator('OBBI');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $po = OMerchantPo::with('o_merchant_po_detail')->where('id',self::$id)->first();
        $datas = OMerchantPoMasuk::with('o_merchant_po','barang_grosir.barang', 'barang_grosir.varian')
            ->where('o_merchant_po_id',$po->id)->get();

        $collection = collect([
            'Header1'=>['','Tanggal Purchase Order', Carbon::createFromFormat('Y-m-d', $po->tanggal)->format('j F Y')],
            'Header2'=>['','Tanggal Masuk', Carbon::createFromFormat('Y-m-d', $po->tanggal_po_masuk)->format('j F Y')],
            'Header3'=>['','Tanggal Batas Retur', Carbon::createFromFormat('Y-m-d', $po->tanggal_batas_retur)->format('j F Y')],
            'Header4'=>[''],
        ]);

        $headerTable1 = ['NO', 'NAMA BARANG', 'SATUAN KONVERSI', 'JUMLAH', 'HARGA'];

        $collection->put('Header Table 1', $headerTable1);
        
        $nomor = 1;
        foreach($datas as $data){
            $no = $nomor . '.';
            $data = [
                $no,
                $data->barang_grosir->barang->nama($data->barang_grosir->barang->nama_barang),
                // $data->barang_grosir->varian->varian_barang . ' - ' . $data->barang_grosir->qty,
                ' ( Varian ' . $data->barang_grosir->varian->varian_barang . ' ) ' .' ( Pembelian '.$data->barang_grosir->qty.' dengan harga '.$data->barang_grosir->harga_jual.'/pcs )',
                $data->jumlah,
                $data->harga,
            ];

            $collection->put('Data ' . $nomor++, $data);
        }
        
        return $collection;
    }
    
    public static function afterSheet(AfterSheet $event)
    {
    }
}
