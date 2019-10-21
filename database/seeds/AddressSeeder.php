<?php

use Illuminate\Database\Seeder;
use App\Model\Provinsi;
use GuzzleHttp\Client;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client     = new Client(['base_url' => 'https://pro.rajaongkir.com/api/province']);
        $province   = $client->get('https://pro.rajaongkir.com/api/province',[
            'headers' => [
                'key' => '478507f213ae0ba525a75487b3ca7568',
            ]
        ]);
        $province2  = [];
        $province2  = json_decode($province->getBody()->getContents(),true);
        $data = [];
        $json = [];
        $i=0;
        
        foreach($province2['rajaongkir']['results'] as $pr){
            

            $provinsi   = Provinsi::where('nama_provinsi',$pr['province'])->first();

            if(!empty($provinsi)){
                $provinsi->rajaongkir_id    = $pr['province_id'];

                $provinsi->update();
            }
        }
    }
}
