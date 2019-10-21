<?php

namespace App\Http\Controllers\API\Slider;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

use App\Model\Slider;

class Read extends Controller {
    public function __invoke (Request $request) {
        $slide = Slider::where('valid', 1)
                       ->get();
        $tmp = [];
        foreach ( $slide as $data ) {
            $tmp[] = [
                'id'         => $data->id,
                'judul'      => $data->judul,
                'image'      => config('app.api').Storage::url('slider/'.$data->image),
                'valid'      => $data->valid,
                'created_at' => date('Y-m-d H:i:s', strtotime($data->created_at)),
                'updated_at' => date('Y-m-d H:i:s', strtotime($data->updated_at)),
            ];
        }

        return (new \Data)->respond([
            'slide' => $tmp
        ]);
    }
}
