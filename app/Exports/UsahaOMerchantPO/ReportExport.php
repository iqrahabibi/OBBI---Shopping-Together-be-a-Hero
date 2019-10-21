<?php

namespace App\Exports\UsahaOMerchantPO;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;

class ReportExport implements WithMultipleSheets, ShouldAutoSize
{
    use Exportable;

    protected $po_id;

    public function __construct(){}

    public static function po(int $id)
    {
        $instance = new self();
        $instance->po_id = $id;
        return $instance;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $po = PoExport::Instance($this->po_id);
        PoExport::id($this->po_id);
        $sheets[] = $po;
        return $sheets;
    }
}