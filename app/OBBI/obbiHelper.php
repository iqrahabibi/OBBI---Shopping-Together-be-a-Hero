<?php
namespace App\OBBI;
use DB;

class obbiHelper
{
    public static function response($code,$desc,$data = [])
    {
        $array  = [];

        $array['code']  = (string) $code;
        $array['desc']  = $desc;
        $array['data']  = $data;
 
        return $array;
    }

    public static function convertdate(){
        date_default_timezone_set('Asia/Jakarta');
        $date = date('dmy');
        return $date;
    }

    public static function storage($image){
        return $image;
    }

    public static function autonumber($barang,$primary,$prefix){
        $q=DB::table($barang)->select(DB::raw('MAX(RIGHT('.$primary.',5)) as kd_max'));
        $prx=$prefix.obbiHelper::convertdate();
        if($q->count()>0)
        {
            foreach($q->get() as $k)
            {
                $tmp = ((int)$k->kd_max)+1;
                $kd = $prx.sprintf("%03s", $tmp);
            }
        }
        else
        {
            $kd = $prx."001";
        }

        return $kd;
    }

    public static function autonumber_omerchant($table,$primary,$prefix){
        // $q=DB::table($barang)->select(DB::raw('MAX(RIGHT('.$primary.',5)) as kd_max'));
        $q=DB::table($table)->select(DB::raw('MAX(SUBSTR('.$primary.', 4, LENGTH('.$primary.'))) as kd_max'));
        // dd($q->get());
        $prx=$prefix;
        if($q->count()>0)
        {
            foreach($q->get() as $k)
            {
                $tmp = ((int)$k->kd_max)+1;
                $kd = $prx.$tmp;
            }
        }
        else
        {
            $kd = $prx."1";
        }
        // dd($kd);
        return $kd;
    }

    public static function invoice($barang,$primary,$prefix)
    {
        $q=DB::table($barang)->where('invoice','!=',null)->select(DB::raw('MAX('.$primary.') as kd_max'));
        if($q->count()>0)
        {
            foreach($q->get() as $k)
            {
                $tmp = ((int)$k->kd_max)+1;
                $kd = $prefix.sprintf("%04s", $tmp);
            }
        }
        else
        {
            $kd = $prefix."001";
        }

        return $kd;
    }

    public static function donation($barang,$primary,$prefix)
    {
        $q=DB::table($barang)->select(DB::raw('MAX(SUBSTR('.$primary.',5,9)) as kd_max'));
        if($q->count()>0)
        {
            foreach($q->get() as $k)
            {
                $tmp = ((int)$k->kd_max)+1;
                $kd = $prefix.sprintf("%05s", $tmp);
            }
        }
        else
        {
            $kd = $prefix."00001";
        }

        return $kd;
    }

    public static function autonumber_po($table,$primary,$prefix){
        $q=DB::table($table)->select(DB::raw('MAX(SUBSTR('.$primary.', 4, LENGTH('.$primary.'))) as kd_max'));
        // dd($q->get());
        $prx=$prefix;
        if($q->count()>0)
        {
            foreach($q->get() as $k)
            {
                $tmp = ((int)$k->kd_max)+1;
                $kd = $prx.$tmp;
            }
        }
        else
        {
            $kd = $prx."1";
        }
        // dd($kd);
        return $kd;
    }

    public static function autonumber_om_po($table,$primary,$prefix){
        $q=DB::table($table)->select(DB::raw('MAX(SUBSTR('.$primary.', 6, LENGTH('.$primary.'))) as kd_max'));
        // dd($q->get());
        $prx=$prefix;
        if($q->count()>0)
        {
            foreach($q->get() as $k)
            {
                $tmp = ((int)$k->kd_max)+1;
                $kd = $prx.$tmp;
            }
        }
        else
        {
            $kd = $prx."1";
        }
        // dd($kd);
        return $kd;
    }

    public static function auto_invoice_cart($table, $primary, $prefix){
        $q=DB::table($table)->select(DB::raw('MAX(SUBSTR('.$primary.', 4, LENGTH('.$primary.'))) as kd_max'));
        // dd($q->get());
        $prx=$prefix;
        if($q->count()>0)
        {
            foreach($q->get() as $k)
            {
                $tmp = ((int)$k->kd_max)+1;
                $kd = $prx.$tmp;
            }
        }
        else
        {
            $kd = $prx."1";
        }

        return $kd;
    }

    public static function auto_invoice_checkout($table, $primary, $prefix){
        $q=DB::table($table)->select(DB::raw('MAX(SUBSTR('.$primary.', 6, LENGTH('.$primary.'))) as kd_max'));
        // dd($q->get());
        $prx=$prefix;
        if($q->count()>0)
        {
            foreach($q->get() as $k)
            {
                $tmp = ((int)$k->kd_max)+1;
                $kd = $prx.$tmp;
            }
        }
        else
        {
            $kd = $prx."1";
        }

        return $kd;
    }
}
?>
