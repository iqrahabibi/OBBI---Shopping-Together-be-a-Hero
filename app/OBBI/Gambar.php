<?php

namespace App\OBBI;

use App\OBBI\obbiHelper;

class Gambar
{
    public function setup($file){
        if($file == '' || $file == '-'){
            return 'No File Found';
        }

        if (strpos($file, '.jpg') !== false ||
        strpos($file, '.jpeg') !== false ||
        strpos($file, '.png') !== false) {
            return view('datatables._image',[
                'url' => obbiHelper::storage($file)
            ]);
        }

        return view('datatables._image',[
            'url' => obbiHelper::storage($file)
        ]);
        // return obbiHelper::storage($file);
    }
}
