<?php

namespace App\Http\Controllers\API\Belanja\Ongkir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;
use GuzzleHttp\Client;

use App\Model\Kecamatan;
use App\Model\Gudang;
use App\Model\Kelurahan;
use App\Model\Kurir;

use DB;
use Auth;

class Ongkir extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'destination','weight','origin','kurir','tipe'
        ]);

        $kurir = '';

        DB::beginTransaction();

        if($request->tipe == 'lokal'){

            $array['meta']  = array("code" => 200,"description" => "OK");
            $array['data']  = array(
                "code"  => "otw",
                "name"  => "On The Way (OTW)",
                "costs"  => [
                    ["kurir_id" => 1,"produk" => "normal","description" => "Normal","harga" => [['value' => 0,'etd' => '1-3','note' => '-']]],
                    ["kurir_id" => 2,"produk" => "Express","description" => "Express Cepat","harga" => [['value' => 10000,'etd' => '1-1','note' => '-']]]
                ]
            );

            return response()->json($array);
        }else if($request->tipe == "nasional"){

            $kecamatan  = Gudang::with('kelurahan.kecamatan')
            ->where('id',(string)$request->origin)
            ->first();

            $origin     = '';

            if(empty($kecamatan->kelurahan->kecamatan)){
                $origin = '6296';
            }

            $origin = (string)$kecamatan->kelurahan->kecamatan->rajaongkir_id;
            
            $client = new Client();
            $cost   = $client->post('https://pro.rajaongkir.com/api/cost',[
                'headers' => [
                    'key' => '478507f213ae0ba525a75487b3ca7568',
                ],
                'form_params' => [
                    'origin' => $origin,
                    'originType'    => 'subdistrict',
                    'destination'   => $request->destination,
                    'destinationType'=> 'subdistrict',
                    'weight'        => $request->weight,
                    'courier'       => $request->kurir
                ]
            ]);
            
            $datacost   = json_decode($cost->getBody()->getContents(),true);

            $array      = array();
            $data       = array();
            $harga    = array();
            $json       =  array();

            if(sizeof($datacost) > 0){
                // dd($datacost['rajaongkir']['status']);
                $array['meta']  = $datacost['rajaongkir']['status'];
                $data['code'] = $datacost['rajaongkir']['results'][0]['code'];
                $data['name']  = $datacost['rajaongkir']['results'][0]['name'];

                foreach($datacost['rajaongkir']['results'][0]['costs'] as $key2 => $value2){
                    $kurir  = Kurir::updateOrCreate(
                        [
                            'user_id' => $request->user_id,
                            'name' => $datacost['rajaongkir']['results'][0]['name'],
                            'origin' => $request->origin,
                            'product'  => $datacost['rajaongkir']['results'][0]['costs'][$key2]['service'],
                            'code'      => $datacost['rajaongkir']['results'][0]['code']
                        ],
                        [
                            'destination' => $request->destination,
                            'weight' => $request->weight,
                            'desc'   =>  $datacost['rajaongkir']['results'][0]['costs'][$key2]['description'],
                            'value' => $value2['cost'][0]['value'],
                            'etd'   => $value2['cost'][0]['etd'],
                        ]
                    );

                    $data['costs'][$key2] = [
                        "kurir_id"  => $kurir->id,
                        "produk" => $datacost['rajaongkir']['results'][0]['costs'][$key2]['service'],
                        "description" => $datacost['rajaongkir']['results'][0]['costs'][$key2]['description'],
                        "harga" => $value2['cost']
                    ];
                }

                DB::commit();
                
                $array['data'] = $data;
            }

            return response()->json($array);
        }
    }
}
