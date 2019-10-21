<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Html\Builder;
use App\Model\License;
use App\Model\KriteriaLicense;
use App\Model\Kelurahan;
use App\OBBI\obbiHelper;
use DataTables;
use Session;

class LicenseController extends Controller
{
    const FilePath = '/licenses';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = License::with('kelurahan.kecamatan.kota.provinsi','kriteria_license')->get();

            return Datatables::of($datas)
            ->addColumn('wilayah',function($data){
                if(empty($data->kelurahan)){
                    return '-';
                }
                return $data->kelurahan->kecamatan->kota->provinsi->nama_provinsi . ' - ' .
                    $data->kelurahan->kecamatan->kota->nama_kota . ' - ' .
                    $data->kelurahan->kecamatan->nama_kecamatan . ' - ' .
                    $data->kelurahan->nama_kelurahan;
            })
            ->addColumn('kriteria_license',function($data){
                if(empty($data->kriteria_license)){
                    return '-';
                }
                return $data->kriteria_license->nama_kriteria_license;
            })
            ->addColumn('file_perjanjian',function($data){
                return view('datatables._image',[
                    'url' => obbiHelper::storage($data->file_perjanjian)
                ]);
            })
            ->addColumn('file_sertifikat',function($data){
                return view('datatables._image',[
                    'url' => obbiHelper::storage($data->file_sertifikat)
                ]);
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('license.destroy',$data->id),
                    'edit_url'=>route('license.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_license.' ?'
                ]);
            })
            ->rawColumns([
                'file_perjanjian', 'file_sertifikat', 'action'
            ])->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'wilayah','name'=>'wilayah','title'=>'Wilayah'])
            ->addColumn(['data'=>'kriteria_license','name'=>'kriteria_license','title'=>'Kriteria License'])
            ->addColumn(['data'=>'nomor_sertifikat','name'=>'nomor_sertifikat','title'=>'Nomor Sertifikat'])
            ->addColumn(['data'=>'nomor_kartu','name'=>'nomor_kartu','title'=>'Nomor Kartu'])
            ->addColumn(['data'=>'file_perjanjian','name'=>'file_perjanjian','title'=>'File Perjanjian'])
            ->addColumn(['data'=>'file_sertifikat','name'=>'file_sertifikat','title'=>'File Sertifikat'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.license.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.license.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules      =   [ 
            'kriteria_license_id'   => 'required|exists:kriteria_licenses,id',
            'provinsi_id'           => 'required|exists:provinsis,id',
            'kota_id'               => 'required|exists:kotas,id',
            'kecamatan_id'          => 'required|exists:kecamatans,id',
            'kelurahan_id'          => 'required|exists:kelurahans,id',
            'nomor_sertifikat'      => 'required|numeric',
            'nomor_kartu'           => 'required|numeric'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = License::create($request->all());

        $destinationPath = storage_path().DIRECTORY_SEPARATOR.'app/public'.self::FilePath; // menyimpan cover ke folder public /img/licenses

        if($request->hasFile('file_perjanjian')){ // isi field cover jika ada cover yang diupload
            $uploaded_cover_file_perjanjian = $request->file('file_perjanjian'); // Mengambil file yang diupload
            $extension_file_perjanjian = $uploaded_cover_file_perjanjian->getClientOriginalExtension(); // mengambil extension file
            $filename_file_perjanjian = str_random(30).'.'.$extension_file_perjanjian; // membuat nama file random berikut extension
            $uploaded_cover_file_perjanjian->move($destinationPath,$filename_file_perjanjian);
            $data->file_perjanjian = self::FilePath.DIRECTORY_SEPARATOR.$filename_file_perjanjian; // mengisi field cover di book dengan file name yang baru dibuat
        }

        if($request->hasFile('file_sertifikat')){ // isi field cover jika ada cover yang diupload
            $uploaded_cover_file_sertifikat = $request->file('file_sertifikat'); // Mengambil file yang diupload
            $extension_file_sertifikat = $uploaded_cover_file_sertifikat->getClientOriginalExtension(); // mengambil extension file
            $filename_file_sertifikat = str_random(30).'.'.$extension_file_sertifikat; // membuat nama file random berikut extension
            $uploaded_cover_file_sertifikat->move($destinationPath,$filename_file_sertifikat);
            $data->file_sertifikat = self::FilePath.DIRECTORY_SEPARATOR.$filename_file_sertifikat; // mengisi field cover di book dengan file name yang baru dibuat
        }

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nomor_sertifikat') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('license.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = License::findOrFail($id);
        $kelurahan = Kelurahan::with('kecamatan.kota.provinsi')->where('id',$data->kelurahan_id)->first();
        $wilayah = $kelurahan->kecamatan->kota->provinsi->nama_provinsi . ' - ' .
                    $kelurahan->kecamatan->kota->nama_kota . ' - ' .
                    $kelurahan->kecamatan->nama_kecamatan . ' - ' .
                    $kelurahan->nama_kelurahan;
        return view('administrator.license.edit', compact('data', 'wilayah'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules      =   [ 
            'kriteria_license_id'   => 'required|exists:kriteria_licenses,id',
            'nomor_sertifikat'      => 'required|numeric',
            'nomor_kartu'           => 'required|numeric'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = License::findOrFail($id);
        if($request->get('kriteria_license_id')){
            $data->kriteria_license_id = $request->get('kriteria_license_id');
        }
        if($request->get('kelurahan_id')){
            $data->kelurahan_id = $request->get('kelurahan_id');
        }
        $data->nomor_sertifikat = $request->get('nomor_sertifikat');
        $data->nomor_kartu = $request->get('nomor_kartu');
        
        $destinationPath = storage_path().DIRECTORY_SEPARATOR.'app/public'.self::FilePath; // menyimpan cover ke folder public /img/licenses

        if($request->hasFile('file_perjanjian')){ // isi field cover jika ada cover yang diupload
            $uploaded_cover_file_perjanjian = $request->file('file_perjanjian'); // Mengambil file yang diupload
            $extension_file_perjanjian = $uploaded_cover_file_perjanjian->getClientOriginalExtension(); // mengambil extension file
            $filename_file_perjanjian = str_random(30).'.'.$extension_file_perjanjian; // membuat nama file random berikut extension
            $uploaded_cover_file_perjanjian->move($destinationPath,$filename_file_perjanjian);

            if($data->file_perjanjian != '' || $data->file_perjanjian != null){ // hapus cover lama, jika ada
                $filepath = storage_path().DIRECTORY_SEPARATOR.'app/public'.$data->file_perjanjian;
                try{
                    File::delete($filepath);
                }catch(FileNotFoundException $e){
                    //File sudah dihapus / tidak ada
                } // ganti field cover dengan cover yang baru
            }
            $data->file_perjanjian = self::FilePath.DIRECTORY_SEPARATOR.$filename_file_perjanjian; // mengisi field cover di book dengan file name yang baru dibuat
        }

        if($request->hasFile('file_sertifikat')){ // isi field cover jika ada cover yang diupload
            $uploaded_cover_file_sertifikat = $request->file('file_sertifikat'); // Mengambil file yang diupload
            $extension_file_sertifikat = $uploaded_cover_file_sertifikat->getClientOriginalExtension(); // mengambil extension file
            $filename_file_sertifikat = str_random(30).'.'.$extension_file_sertifikat; // membuat nama file random berikut extension
            $uploaded_cover_file_sertifikat->move($destinationPath,$filename_file_sertifikat);

            if($data->file_sertifikat != '' || $data->file_sertifikat != null){ // hapus cover lama, jika ada
                $filepath = storage_path().DIRECTORY_SEPARATOR.'app/public'.$data->file_sertifikat;
                try{
                    File::delete($filepath);
                }catch(FileNotFoundException $e){
                    //File sudah dihapus / tidak ada
                } // ganti field cover dengan cover yang baru
            }
            $data->file_sertifikat = self::FilePath.DIRECTORY_SEPARATOR.$filename_file_sertifikat; // mengisi field cover di book dengan file name yang baru dibuat
        }

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nomor_sertifikat') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('license.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();

        $data = License::find($id);
        $nama = $data->nomor_sertifikat;

        $filepath_file_perjanjian = '';
        $filepath_file_sertifikat = '';

        if($data->file_perjanjian != '' || $data->file_perjanjian != null){ // hapus cover lama, jika ada
            $filepath_file_perjanjian = storage_path().DIRECTORY_SEPARATOR.'app/public'.$data->file_perjanjian;
        }

        if($data->file_sertifikat != '' || $data->file_sertifikat != null){ // hapus cover lama, jika ada
            $filepath_file_sertifikat = storage_path().DIRECTORY_SEPARATOR.'app/public'.$data->file_sertifikat;
        }

        // $kota = Kota::where('kriteria_license_id', $data->id)->count();
        // if($kota > 0){
        //     Session::flash("flash_notification",[
        //         "level"=>"danger",
        //         "message"=>"Data already used."
        //     ]);
        //     return redirect()->back();
        // }

        if(!$data->delete()) {
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be deleted."
            ]);
            return redirect()->back();
        }
        if($request->ajax()) return response()->json(['id'=>$id]);

        DB::commit();
        
        try{
            File::delete($filepath_file_perjanjian);
            File::delete($filepath_file_sertifikat);
        }catch(FileNotFoundException $e){
            //File sudah dihapus / tidak ada
        } // ganti field cover dengan cover yang baru

        Session::flash("flash_notification",[
            "level"=>"success",
            "message"=>"$nama successfully deleted."
        ]);
        return redirect()->route('license.index');
    }

    private function setupImage($url){
        if($url == '' || $url == '-'){
            return 'No File Found';
        }

        $url = strtolower($url);
        if (strpos($url, '.jpg') !== false ||
            strpos($url, '.jpeg') !== false ||
            strpos($url, '.png') !== false) {
            return view('datatables._image',[
                'url' => config('app.api').$url
            ]);
        }

        return '';
    }
}
