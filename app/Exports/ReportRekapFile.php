<?php

namespace App\Exports;

use Illuminate\Support\Str;
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
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportRekapFile implements FromCollection, WithEvents, WithTitle, ShouldAutoSize
{
    use Exportable, RegistersEventListeners;

    public static $from;
    public static $to;

    /**
     * Call this method to get singleton
     *
     * @return UserFactory
     */
    public static function Instance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new ReportRekapFile();
        }
        return $instance;
    }

    /**
     * Private ctor so nobody else can instantiate it
     *
     */
    private function __construct()
    {

    }

    public static function range($from,$to){
        self::$from = $from;
        self::$to = $to;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Rekap';
    }

    public static function beforeExport(BeforeExport $event)
    {
        $event->writer->getProperties()->setCreator('Sistem Aplikasi Pelayanan Administrasi Pertanahan');
    }

    /**
     * @return Builder
     */
    public function collection()
    {
        // $datas = Tanah::with('pemilik', 'pembeli', 'heir.legatees')
        //     ->whereBetween('updated_at', [self::$from, self::$to])->get();

        // $collection = collect([
        //     'Header1'=>['REKAPITULASI'], 
        //     'Header2'=>[Carbon::createFromFormat('Y-m-d', self::$from)->format('j F Y') . ' - ' . Carbon::createFromFormat('Y-m-d', self::$to)->subDay()->format('j F Y')]
        // ]);

        // $headerTable1 = ['NO', 'NAMA PEMILIK TANAH', 'TEMPAT/TANGGAL LAHIR', 'NO. KTP', 'PEKERJAAN',
        //     'ALAMAT PEMILIK TANAH', 'ALAMAT OBJEK TANAH', 'LUAS TANAH (m2)', 
        //     'BATAS-BATAS TANAH', '','','',
        //     'STATUS TANAH', '',
        //     'LAMPIRAN BERKAS', ''];

        // $collection->put('Header Table 1', $headerTable1);

        // $headerTable2 = ['', '', '', '', '', '', '', 
        //     '', 'Sebelah Utara', 'Sebelah Selatan','Sebelah Barat','Sebelah Timur',
        //     'Status Adat', 'Status Garapan', 
        //     'KK', 'KTP'];

        // $collection->put('Header Table 2', $headerTable2);

        // $nomor = 1;
        // foreach($datas as $data){
        //     $no = $nomor . '.';
        //     $tgl = optional($data->pemilik)->tanggal_lahir_pemilik ? optional($data->pemilik)->tanggal_lahir_pemilik : '0000-00-00';
        //     $data = [
        //         $no,
        //         optional($data->pemilik)->nama_pemilik,
        //         $tgl != '0000-00-00' ? 
        //             optional($data->pemilik)->tempat_lahir_pemilik . ' / ' . Carbon::createFromFormat('Y-m-d', $tgl)->format('j F Y') :
        //             '-',
        //         optional($data->pemilik)->nik_pemilik,
        //         optional($data->pemilik)->pekerjaan_pemilik,
        //         optional($data->pemilik)->alamat_pemilik,
        //         optional($data)->alamat_tanah,
        //         optional($data)->luas,
        //         optional($data)->batas_utara,
        //         optional($data)->batas_selatan,
        //         optional($data)->batas_barat,
        //         optional($data)->batas_timur,
        //         optional($data)->status_adat,
        //         $this->checkNull(optional($data)->status_garapan),
        //         $this->checkNull(optional($data->pemilik)->lampiran_kk_pemilik),
        //         $this->checkNull(optional($data->pemilik)->lampiran_ktp_pemilik)
        //     ];

        //     $collection->put('Data ' . $nomor++, $data);
        // }

        // dd($collection);
        // return $collection;
        return [];
    }

    public function checkNull($field){
        if($field == '' || $field == null){
            $field = '-';
        }
        return $field;
    }

    public static function afterSheet(AfterSheet $event)
    {
        // $datas = Tanah::with('pemilik', 'pembeli', 'heir.legatees')
        //     ->whereBetween('updated_at', [self::$from, self::$to])->get();

        // $worksheet = $event->sheet->getDelegate();

        // $pageSetup = $worksheet->getPageSetup();
        // // $pageSetup->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        // // $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);

        // $style = $worksheet->getStyles();

        // $worksheet->mergeCells('A1:P1');
        // $worksheet->mergeCells('A2:P2');

        // $worksheet->mergeCells('I3:L3');
        // $worksheet->mergeCells('A3:A4');
        // $worksheet->mergeCells('B3:B4');
        // $worksheet->mergeCells('C3:C4');
        // $worksheet->mergeCells('D3:D4');
        // $worksheet->mergeCells('E3:E4');
        // $worksheet->mergeCells('F3:F4');
        // $worksheet->mergeCells('G3:G4');
        // $worksheet->mergeCells('H3:H4');
        // $worksheet->mergeCells('M3:N3');
        // $worksheet->mergeCells('O3:P3');

        // $worksheet->getStyle('A1:P4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $worksheet->getStyle('A1:P4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // $worksheet->getStyle('A1:P4')->applyFromArray([
        //     'font' => [
        //         'bold' => true
        //     ]
        // ]);
        // // $worksheet->getStyle('A3:P4')->applyFromArray([
        // //     'fill' => [
        // //         'color' => [
        // //             'rgb' => '969494'
        // //         ]
        // //     ]
        // // ]);
        // $border = 'A1:P1';
        // $worksheet->getStyle($border)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        // $worksheet->getStyle($border)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        // $worksheet->getStyle($border)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
        // $worksheet->getStyle($border)->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
        // $border = 'A2:P2';
        // $worksheet->getStyle($border)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        // $worksheet->getStyle($border)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        // $worksheet->getStyle($border)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
        // $worksheet->getStyle($border)->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
        
        // for($i = 8; $i<16; $i++){
        //     $border = self::$cellAZ[$i].'4';
        //     $worksheet->getStyle($border)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        //     $worksheet->getStyle($border)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        //     $worksheet->getStyle($border)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
        //     $worksheet->getStyle($border)->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
        // }
        
        // for($i = 0; $i<9; $i++){
        //     $border = self::$cellAZ[$i].'3:'.self::$cellAZ[$i].'4';
        //     $worksheet->getStyle($border)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        //     $worksheet->getStyle($border)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        //     $worksheet->getStyle($border)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
        //     $worksheet->getStyle($border)->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
        // }

        // $row = count($datas) + 4;
        // // $border = 'A5:P'.$row;
        // for($i = 0; $i<16; $i++){
        //     $border = self::$cellAZ[$i].'3:'.self::$cellAZ[$i].$row;
        //     $worksheet->getStyle($border)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        //     $worksheet->getStyle($border)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        //     $worksheet->getStyle($border)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
        //     $worksheet->getStyle($border)->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
        // }
    }
    
    private static $cellAZ = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private static function convertAZ(int $index){
        $hasil = '';

        if($index >= 26){
            $first = (int)($index / 26) - 1;
            $last = $index % 26;
            $hasil = self::$cellAZ[$first] . self::$cellAZ[$last];
        }else{
            $hasil = self::$cellAZ[$index];
        }
        return $hasil;
    }
}