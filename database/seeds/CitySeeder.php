<?php

use Illuminate\Database\Seeder;
use App\Model\Kota;
use App\Model\Kecamatan;
use GuzzleHttp\Client;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client     = new Client(['base_url' => 'https://pro.rajaongkir.com/api/city']);
        $province   = $client->get('https://pro.rajaongkir.com/api/city',[
            'headers' => [
                'key' => '478507f213ae0ba525a75487b3ca7568',
            ]
        ]);
        $province2  = [];
        $province2  = json_decode($province->getBody()->getContents(),true);
        $data = [];
        $json = [];
        
        foreach($province2['rajaongkir']['results'] as $pr){
            $data["province_id"]= $pr['province_id'];
            $data["id"]         = $pr['city_id'];
            $data['city_name']  = $pr['city_name'];
            // array_push($json,$data);
            $json[] = $data;

            $kota   = null;
            if($pr['type'] == "Kabupaten"){
                $kota = Kota::where([
                    ['nama_kota','=',$pr['city_name']],
                    ['tipe','=','Kab.']
                ])->first();

            }else if($pr['type'] == "Kota"){
                $kota = Kota::where([
                    ['nama_kota','=',$pr['city_name']],
                    ['tipe','=','kota']
                ])->first();
            }
            //
            if(!empty($kota)){
                $kota->rajaongkir_id   = $pr['city_id'];

                $kota->update();
            }

            $client2     = new Client(['base_url' => 'https://pro.rajaongkir.com/api/subdistrict?city='.$data["id"]]);
            $subdistrict = $client2->get('https://pro.rajaongkir.com/api/subdistrict?city='.$data["id"],[
                'headers' => [
                    'key' => '478507f213ae0ba525a75487b3ca7568',
                ]
            ]);
            $subdistrict2  = json_decode($subdistrict->getBody()->getContents(),true);

            foreach($subdistrict2['rajaongkir']['results'] as $pr2){
                $data2["subdistrict_id"]     = $pr2['subdistrict_id'];
                $data2["city_id"]            = $pr2['city_id'];
                $data2['subdistrict_name']   = $pr2['subdistrict_name'];
                // array_push($json,$data);
                $json[] = $data2;

                $kecamatan  = Kecamatan::where('nama_kecamatan',$pr2['subdistrict_name'])->first();

                if(!empty($kecamatan)){
                    $kecamatan->rajaongkir_id   = $pr2['subdistrict_id'];

                    $kecamatan->update();
                }
            }

        }
    }
}
