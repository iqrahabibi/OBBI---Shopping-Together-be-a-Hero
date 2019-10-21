<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Html\Builder;
use App\OBBI\obbiHelper;

use Carbon\Carbon;
use App\Model\Opf;
use App\Model\User;
use App\Model\ReferalOpf;
use App\Model\PengaduanOpf;

use DataTables;
use Session;
use App;

class OpfController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request, Builder $htmlBuilder) {

        if ( $request->ajax() ) {
            $datas = Opf::with('user')->get();

            return Datatables::of($datas)
                             ->addColumn('user', function ($data) {
                                 return $data->user->fullname;
                             })
                             ->addColumn('email', function ($data) {
                                 return $data->user->email;
                             })
                             ->addColumn('valid', function ($data) {
                                 if ( $data->valid == 1 ) {
                                     return 'Yes';
                                 }

                                 return 'No';
                             })
                             ->addColumn('gambar', function ($data) {
                                 return view('datatables._image', [
                                     'url' => App\Helper\ObbiAssets::get_asset(App\Helper\ObbiAssets::USER_OPF, $data->foto)
                                 ]);
                             })
                             ->addColumn('action', function ($data) {
                                 return view('administrator.opf._action', [
                                     'model'           => $data,
                                     'form_url'        => route('opf.destroy', $data->id),
                                     'edit_url'        => route('opf.edit', $data->id),
                                     'referal_url'     => route('opf.referal', $data->user->id),
                                     'confirm_message' => 'Yakin mau menghapus ' . $data->fullname . ' ?'

                                 ]);
                             })
                             ->rawColumns([
                                 'gambar', 'action'
                             ])->make(true);
        }

        $html = $htmlBuilder
            ->addColumn([ 'data' => 'user', 'name' => 'user', 'title' => 'Nama User' ])
            ->addColumn([ 'data' => 'email', 'name' => 'email', 'title' => 'E-mail User' ])
            ->addColumn([ 'data' => 'handphone', 'name' => 'handphone', 'title' => 'No Handphone' ])
            ->addColumn([ 'data' => 'referal', 'name' => 'referal', 'title' => 'Kode Referal' ])
            ->addColumn([ 'data' => 'valid', 'name' => 'valid', 'title' => 'Valid' ])
            ->addColumn([ 'data' => 'gambar', 'name' => 'gambar', 'title' => 'Foto' ])
            ->addColumn([
                'data'       => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false,
                'searchable' => false
            ]);

        return view('administrator.opf.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create () {
        $data = '';

        return view('administrator.opf.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request) {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'referal' => 'required|unique:opfs'
        ];

        $messages = [
            'required' => 'Field :attribute harus diisi.',
            'string'   => 'Field :attribute harus berupa karakter.',
            'exists'   => 'Field :attribute tidak ditemukan.',
            'unique'   => 'Field :attribute sudah ada.'
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $opf = new Opf();
        $opf->user_id = $request['user_id'];
        $opf->valid = "1";
        $opf->handphone = $request['handphone'];
        $opf->referal = $request->referal;

        // Disini proses mendapatkan judul dan memindahkan letak gambar ke folder image
        /*$file = $request->file('foto');
        $extension = $file->getClientOriginalExtension();
        $fileName = md5(time()) . '.' . $extension;
        $destination = storage_path() . DIRECTORY_SEPARATOR . 'app/public' . DIRECTORY_SEPARATOR . 'opf';
        $file->move($destination, $fileName);

        $opf->foto = '/opf/' . $fileName;*/
        $upload = $helper = (new App\Helper\FileUploader(7559, App\Helper\FileUploader::USER_OPF, 'foto'))
            ->setMime([
                'image/jpeg', 'image/jpg', 'image/png'
            ])
            ->doUpload($request);
        if ( $upload['meta']['code'] == 200 ) {
            $opf->foto = $upload['data']['path'];
        }
        $opf->save();

        if ( $request->input('referal_opf') ) {
            $data_opf = Opf::where('referal', $request->referal_opf)->first();

            if ( !empty($data_opf) ) {
                $opf_referal = ReferalOpf::updateOrCreate(
                    [ 'opf_id' => $opf->id ],
                    [ 'user_id' => $data_opf->user_id, 'valid' => 1 ]
                );
            }
        }


        if ( $opf ) {
            DB::commit();

            Session::flash("flash_notification", [
                "level"   => "success",
                "message" => "Data successfully saved."
            ]);
        } else {
            DB::rollBack();

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Data failed to be saved."
            ]);
        }

        return redirect()->route('opf.index');
    }


    public function createreferal ($id) {
        $data = User::findOrfail($id);

        return view('administrator.opf.createreferal', compact('id', 'data'));
    }

    public function savereferal (Request $request, $id) {
        $user = User::findOrFail($request->get('user_id'));
        $rules = [
            'user_id' => 'required|exists:users,id'
        ];

        $messages = [
            'required' => 'Field :attribute harus diisi.',
            'string'   => 'Field :attribute harus berupa karakter.',
            'exists'   => 'Field :attribute tidak ditemukan.'
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();
        $file = $request->file('foto');
        $extension = $file->getClientOriginalExtension();
        $fileName = md5(time()) . '.' . $extension;
        $destination = base_path() . '/public/image';
        $request->file('foto')->move($destination, $file);

        $opf = Opf::updateOrCreate([
            'user_id' => $user->id,
            'valid'   => 1,
        ], [
            'foto'  => $fileName,
            'valid' => 0,
        ]);


        $referal = $request->get('referal');

        if ( !empty($referal) ) {
            $user_referee = User::where('referal', $referal)->first();
            if ( empty($user_referee) ) {
                Session::flash("flash_notification", [
                    "level"   => "warning",
                    "message" => "Kode referal tidak ditemukan.."
                ]);
            } else {
                $referal = ReferalOpf::where('opf_id', $opf->id)
                                     ->where('user_id', $user_referee->id)
                                     ->first();

                if ( !empty($referal) && $referal->valid != 2 ) {
                    Session::flash("flash_notification", [
                        "level"   => "warning",
                        "message" => "Anda sudah melakukan referal."
                    ]);
                    //return response()->json(['meta'=> $success]);
                } else {
                    $referalherobi = ReferalOpf::updateOrCreate([
                        'opf_id'  => $opf->id,
                        'user_id' => $user_referee->id
                    ], [
                        'valid' => 1
                    ]);
                }
            }

        }

        if ( $opf->save() ) {
            DB::commit();

            Session::flash("flash_notification", [
                "level"   => "success",
                "message" => " successfully saved."
            ]);
        } else {
            DB::rollBack();

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Data failed to be saved."
            ]);
        }

        return redirect()->route('opf.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show ($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit ($id) {
        $data = Opf::findOrFail($id);

        return view('administrator.opf.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, $id) {
        $rules = [
            //'foto'      => 'required'
        ];

        $messages = [
            'required' => 'Field :attribute harus diisi.',
            'string'   => 'Field :attribute harus berupa karakter.',
            'exists'   => 'Field :attribute tidak ditemukan.'
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Opf::findOrFail($id);
        //dd($data->foto);
        //$data->foto         = $request->file('foto');
        //$data->handphone    = $request->get('handphone');

        if ( !empty($data->foto) ) {
            File::delete('storage' . $data->foto);
        }


        $file = $request->file('foto');
        if ( $file ) {
            $extension = $file->getClientOriginalExtension();
            $fileName = md5(time()) . '.' . $extension;
            $destination = storage_path() . DIRECTORY_SEPARATOR . 'app/public' . DIRECTORY_SEPARATOR . 'opf';
            $file->move($destination, $fileName);

            $data->foto = '/opf/' . $fileName;
        }


        if ( $data->update() ) {
            DB::commit();

            Session::flash("flash_notification", [
                "level"   => "success",
                "message" => $request->get('user_id') . " successfully updated."
            ]);
        } else {
            DB::rollBack();

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Data failed to be updated."
            ]);
        }

        return redirect()->route('opf.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy (Request $request, $id) {
        DB::beginTransaction();

        $data = Opf::find($id);
        $nama = $data->fullname;

        if ( !empty($data->foto) ) {
            File::delete('storage' . $data->foto);
        }

        $user = 0;
        if ( $user > 0 ) {
            Session::flash("flash_notification", [
                "level"   => "danger",
                "message" => "Data already used."
            ]);

            return redirect()->back();
        }

        if ( !$data->delete() ) {
            DB::rollBack();

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Data failed to be deleted."
            ]);

            return redirect()->back();
        }
        if ( $request->ajax() )
            return response()->json([ 'id' => $id ]);

        DB::commit();

        Session::flash("flash_notification", [
            "level"   => "success",
            "message" => "$nama successfully deleted."
        ]);

        return redirect()->route('opf.index');
    }

    public function referal (Request $request, Builder $htmlBuilder, $id) {
        $datas = ReferalOpf::with('user', 'opf.user')
                           ->where('user_id', $id)
                           ->get();
        $cek = Opf::with('user')->where('id', $id)->first();

        if ( !empty($cek) && $cek->valid != 1 ) {
            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => $cek->user->fullname . " belum menjadi herobi. Silahkan proses terlebih dahulu."
            ]);

            return redirect()->route('opf.index');
        }
        if ( $request->ajax() ) {
            return Datatables::of($datas)
                             ->addColumn('user', function ($data) {
                                 return $data->user->fullname;
                             })
                             ->addColumn('email', function ($data) {
                                 return $data->user->email;
                             })
                             ->addColumn('referal', function ($data) {
                                 return $data->opf->user->fullname;
                             })
                             ->addColumn('email_referal', function ($data) {
                                 return $data->opf->user->email;
                             })
                             ->addColumn('waktu', function ($data) {
                                 return Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)
                                              ->format('j F Y, H:i:s');
                             })
                             ->addColumn('status', function ($data) {
                                 if ( $data->valid == 0 ) {
                                     return 'Waiting Approval';
                                 } else if ( $data->valid == 1 ) {
                                     return 'Approved';
                                 } else if ( $data->valid == 2 ) {
                                     return 'Rejected';
                                 }

                                 return '';
                             })
                             ->make(true);
        }

        $html = $htmlBuilder
            ->addColumn([ 'data' => 'user', 'name' => 'user', 'title' => 'User Member' ])
            ->addColumn([ 'data' => 'email', 'name' => 'email', 'title' => 'E-mail Member' ])
            ->addColumn([ 'data' => 'referal', 'name' => 'referal', 'title' => 'User Referral' ])
            ->addColumn([ 'data' => 'email_referal', 'name' => 'email_referal', 'title' => 'E-mail Referral' ])
            ->addColumn([ 'data' => 'waktu', 'name' => 'waktu', 'title' => 'Waktu' ])
            ->addColumn([ 'data' => 'status', 'name' => 'status', 'title' => 'Status' ]);


        return view('administrator.opf.referal')->with(compact('html', 'id'));
    }

    public function aduan (Request $request, Builder $htmlBuilder) {
        if ( $request->ajax() ) {

            $datas = PengaduanOpf::with('user')->get();

            return Datatables::of($datas)
                             ->addColumn('user', function ($data) {
                                 return $data->user->fullname;
                             })
                             ->addColumn('opf', function ($data) {
                                 return $data->opf->user->fullname;
                             })
                             ->addColumn('valid', function ($data) {
                                 if ( $data->valid == 1 ) {
                                     return 'Yes';
                                 }

                                 return 'No';
                             })
                             ->addColumn('action', function ($data) {
                                 if ( $data->valid == 1 ) {
                                     return 'No Action';
                                 }

                                 return view('administrator.pengaduan_opf._action', [
                                     'model'    => $data,
                                     'edit_url' => route('pengaduan.editaduan', $data->id)
                                 ]);
                             })
                             ->make(true);
        }


        $html = $htmlBuilder
            ->addColumn([ 'data' => 'user', 'name' => 'user', 'title' => 'Nama User' ])
            ->addColumn([ 'data' => 'opf', 'name' => 'opf', 'title' => 'Nama Opf' ])
            ->addColumn([ 'data' => 'aduan', 'name' => 'aduan', 'title' => 'Aduan' ])
            ->addColumn([ 'data' => 'valid', 'name' => 'valid', 'title' => 'Valid' ])
            ->addColumn([
                'data'       => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false,
                'searchable' => false
            ]);

        return view('administrator.pengaduan_opf.index')->with(compact('html'));
    }

    public function editaduan ($id) {
        $data = PengaduanOpf::findOrFail($id);

        return view('administrator.pengaduan_opf.edit', compact('data'));
    }

    public function updateaduan (Request $request, $id) {
        $rules = [

        ];

        $messages = [
            'required' => 'Field :attribute harus diisi.',
            'string'   => 'Field :attribute harus berupa karakter.',
            'exists'   => 'Field :attribute tidak ditemukan.'
        ];

        $this->validate($request, $rules, $messages);
        DB::beginTransaction();

        $data = PengaduanOpf::findOrFail($id);
        $data->valid = $request->get('valid');

        if ( $data->update() ) {
            DB::commit();

            Session::flash("flash_notification", [
                "level"   => "success",
                "message" => "Data successfully updated."
            ]);
        } else {
            DB::rollBack();

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Data failed to be updated."
            ]);
        }

        return redirect()->route('pengaduan.aduan');
    }
}
