<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Html\Builder;
use App\Model\JualLicense;
use App\Model\License;
use App\Model\LicenseOwner;
use DataTables;
use Session;

class JualLicenseController extends Controller
{
    const FilePath = '/license_owners';
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = JualLicense::with('license','license_owner')->get();

            return Datatables::of($datas)
            // ->addColumn('wilayah',function($data){
            //     if(empty($data->kelurahan)){
            //         return '-';
            //     }
            //     return $data->kelurahan->kecamatan->kota->provinsi->nama_provinsi . ' - ' .
            //         $data->kelurahan->kecamatan->kota->nama_kota . ' - ' .
            //         $data->kelurahan->kecamatan->nama_kecamatan . ' - ' .
            //         $data->kelurahan->nama_kelurahan;
            // })
            // ->addColumn('kriteria_license',function($data){
            //     if(empty($data->kriteria_license)){
            //         return '-';
            //     }
            //     return $data->kriteria_license->nama_kriteria_license;
            // })
            // ->addColumn('file_ktp',function($data){
            //     return $this->setupImage($data->file_ktp);
            // })
            // ->addColumn('file_sertifikat',function($data){
            //     return $this->setupImage($data->file_sertifikat);
            // })
            ->addColumn('action',function($data){
                return view('administrator.juallicense._action',[
                    'model' =>$data,
                    'license_owner_url' =>route('juallicense.owner',$data->id),
                    'form_url' =>route('juallicense.destroy',$data->id),
                    'edit_url'=>route('juallicense.edit',$data->id),
                    'hibah_url'=>route('juallicense.hibah',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_license.' ?'
                ]);
            })
            // ->rawColumns([
            //     'file_ktp', 'file_sertifikat', 'action'
            // ])
            ->make(true);
        }
        
        $html=$htmlBuilder
            ->addColumn(['data'=>'tanggal_jual','name'=>'tanggal_jual','title'=>'Tanggal Jual'])
            ->addColumn(['data'=>'perolehan','name'=>'perolehan','title'=>'Perolehan'])
            ->addColumn(['data'=>'jenis_pembayaran','name'=>'jenis_pembayaran','title'=>'Jenis Pembayaran'])
            // ->addColumn(['data'=>'nomor_sertifikat','name'=>'nomor_sertifikat','title'=>'Nomor Sertifikat'])
            // ->addColumn(['data'=>'nomor_kartu','name'=>'nomor_kartu','title'=>'Nomor Kartu'])
            // ->addColumn(['data'=>'file_ktp','name'=>'file_ktp','title'=>'File Perjanjian'])
            // ->addColumn(['data'=>'file_sertifikat','name'=>'file_sertifikat','title'=>'File Sertifikat'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.juallicense.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.juallicense.create');
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
            'nik'               => 'required|numeric|unique:license_owners',
            'nama_depan'        => 'required|string',
            'nama_lengkap'      => 'required|string',
            'tanggal_lahir'     => 'required|date',
            'agama_id'          => 'required|exists:agamas,id',
            'status_pernikahan' => 'required|string',
            'jenis_kelamin'     => 'required|string',
            'alamat'            => 'required|string',
            'rt'                => 'required|string',
            'rw'                => 'required|string',
            'no_telp'           => 'required|string',
            'no_telp_2'         => 'required|string',
            'license_id'        => 'required|exists:licenses,id',
            'tanggal_jual'      => 'required|date',
            'perolehan'         => 'required|string',
            'jenis_pembayaran'  => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
            'date'      => 'Field :attribute harus berupa tanggal.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'unique'    => 'Field :attribute sudah digunakan.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $licenseowner = LicenseOwner::create($request->all());
        $juallicense = JualLicense::create(array_merge($request->all(), [
            'license_owner_id' => $licenseowner->id
        ]));

        $destinationPath = storage_path().DIRECTORY_SEPARATOR.'app/public'.self::FilePath; // menyimpan cover ke folder public /img/licenses


        if($request->hasFile('file_ktp')){ // isi field cover jika ada cover yang diupload
            $uploaded_cover_file_ktp = $request->file('file_ktp'); // Mengambil file yang diupload
            $extension_file_ktp = $uploaded_cover_file_ktp->getClientOriginalExtension(); // mengambil extension file
            $filename_file_ktp = str_random(30).'.'.$extension_file_ktp; // membuat nama file random berikut extension
            $uploaded_cover_file_ktp->move($destinationPath,$filename_file_ktp);
            $licenseowner->file_ktp = self::FilePath.DIRECTORY_SEPARATOR.$filename_file_ktp; // mengisi field cover di book dengan file name yang baru dibuat
        }

        if($juallicense->save() && $licenseowner->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('juallicense.index');
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
        $data = JualLicense::with('license_owner.agama')->findOrFail($id);
        return view('administrator.juallicense.edit', compact('data'));
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
            'nik'               => 'required|numeric|exists:license_owners,nik',
            'nama_depan'        => 'required|string',
            'nama_lengkap'      => 'required|string',
            'tanggal_lahir'     => 'required|date',
            'agama_id'          => 'required|exists:agamas,id',
            'status_pernikahan' => 'required|string',
            'jenis_kelamin'     => 'required|string',
            'alamat'            => 'required|string',
            'rt'                => 'required|string',
            'rw'                => 'required|string',
            'no_telp'           => 'required|string',
            'no_telp_2'         => 'required|string',
            'license_id'        => 'required',
            'tanggal_jual'      => 'required|date',
            'perolehan'         => 'required|string',
            'jenis_pembayaran'  => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
            'date'      => 'Field :attribute harus berupa tanggal.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'unique'    => 'Field :attribute sudah digunakan.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $licenseowner = LicenseOwner::updateOrCreate([
            'nik' => $request->get('nik')
        ], $request->all());

        $juallicense = JualLicense::updateOrCreate([
            'license_owner_id' => $licenseowner->id
        ], $request->all());

        $destinationPath = storage_path().DIRECTORY_SEPARATOR.'app/public'.self::FilePath; // menyimpan cover ke folder public /img/licenses

        if($request->hasFile('file_ktp')){ // isi field cover jika ada cover yang diupload
            $uploaded_cover_file_ktp = $request->file('file_ktp'); // Mengambil file yang diupload
            $extension_file_ktp = $uploaded_cover_file_ktp->getClientOriginalExtension(); // mengambil extension file
            $filename_file_ktp = str_random(30).'.'.$extension_file_ktp; // membuat nama file random berikut extension
            $uploaded_cover_file_ktp->move($destinationPath,$filename_file_ktp);
            $licenseowner->file_ktp = self::FilePath.DIRECTORY_SEPARATOR.$filename_file_ktp; // mengisi field cover di book dengan file name yang baru dibuat
        }

        if($juallicense->save() && $licenseowner->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('juallicense.index');
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
        $data = JualLicense::with('license_owner', 'license')->find($id);
        $nama = $data->license->nomor_sertifikat;

        $file_ktp = '';

        if($data->license_owner->file_ktp != '' || $data->license_owner->file_ktp != null){ // hapus cover lama, jika ada
            $file_ktp = storage_path().DIRECTORY_SEPARATOR.'app/public'.$data->license_owner->file_ktp;
        }
        
        if(!$data->license_owner->delete() && !$data->delete()) {
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
            File::delete($file_ktp);
        }catch(FileNotFoundException $e){
            //File sudah dihapus / tidak ada
        } // ganti field cover dengan cover yang baru

        Session::flash("flash_notification",[
            "level"=>"success",
            "message"=>"$nama successfully deleted."
        ]);
        return redirect()->route('juallicense.index');
    }

    public function owner($id)
    {
        $juallicense = JualLicense::findOrFail($id);
        $data = LicenseOwner::with('agama')->where('id', $juallicense->license_owner_id)->first();
        return view('administrator.juallicense.owner', compact('data'));
    }

    public function hibah($id)
    {
        $data = JualLicense::with('license_owner.agama')->findOrFail($id);
        return view('administrator.juallicense.hibah', compact('data'));
    }

    public function savehibah(Request $request, $id)
    {
        $rules      =   [
            'nik'               => 'required|numeric|unique:license_owners',
            'nama_depan'        => 'required|string',
            'nama_lengkap'      => 'required|string',
            'tanggal_lahir'     => 'required|date',
            'agama_id'          => 'required|exists:agamas,id',
            'status_pernikahan' => 'required|string',
            'jenis_kelamin'     => 'required|string',
            'alamat'            => 'required|string',
            'rt'                => 'required|string',
            'rw'                => 'required|string',
            'no_telp'           => 'required|string',
            'no_telp_2'         => 'required|string',
            'license_id'        => 'required|exists:licenses,id',
            'tanggal_jual'      => 'required|date',
            'perolehan'         => 'required|string',
            'jenis_pembayaran'  => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
            'date'      => 'Field :attribute harus berupa tanggal.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'unique'    => 'Field :attribute sudah digunakan.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $pewaris = LicenseOwner::where('nik', $request->get('nik_pewaris'))->first();
        $pewaris->valid = 0;

        $licenseowner = LicenseOwner::create(array_merge($request->all(),[
            'pewaris_id' => $pewaris->id
        ]));
        $juallicense = JualLicense::where('id', $id)->first();
        $juallicense->license_owner_id = $licenseowner->id;

        $destinationPath = storage_path().DIRECTORY_SEPARATOR.'app/public'.self::FilePath; // menyimpan cover ke folder public /img/licenses

        if($request->hasFile('file_ktp')){ // isi field cover jika ada cover yang diupload
            $uploaded_cover_file_ktp = $request->file('file_ktp'); // Mengambil file yang diupload
            $extension_file_ktp = $uploaded_cover_file_ktp->getClientOriginalExtension(); // mengambil extension file
            $filename_file_ktp = str_random(30).'.'.$extension_file_ktp; // membuat nama file random berikut extension
            $uploaded_cover_file_ktp->move($destinationPath,$filename_file_ktp);
            $licenseowner->file_ktp = self::FilePath.DIRECTORY_SEPARATOR.$filename_file_ktp; // mengisi field cover di book dengan file name yang baru dibuat
        }

        if($pewaris->update() && $licenseowner->update() && $juallicense->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('juallicense.index');
    }
}
