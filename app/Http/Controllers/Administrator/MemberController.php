<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Builder;
use App\OBBI\Gambar;
use Carbon\Carbon;
use App\Model\User;
use App\Model\DetailUser;
use App\Model\Herobi;
use App\Model\ReferalHerobi;
use App\Model\ReferalOMerchant;
use App\Model\Kelurahan;
use DataTables;
use Session;
use App;

class MemberController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request, Builder $htmlBuilder) {
        $datas = User::with('detail.agama', 'detail.kelurahan.kecamatan.kota.provinsi', 'roles')
                     ->whereHas('roles', function ($role) {
                         $role->where('role_id', 1
                        );
                     })
                     ->get();

        if ( $request->ajax() ) {
            return Datatables::of($datas)
                             ->addColumn('no_telp', function ($data) {
                                 if ( empty($data->detail->phone) ) {
                                     return '-';
                                 }

                                 return $data->detail->phone;
                             })
                             ->addColumn('akses', function ($data) {
                                 if ( !empty($data->herobi) && $data->herobi->valid == 0 ) {
                                     return 'Request as Herobi';
                                 }

                                 return $data->akses();
                             })
                             ->addColumn('status_banned', function ($data) {
                                 if ( $data->status ) {
                                     return 'Aktif';
                                 }

                                 return 'Banned';
                             })
                             ->addColumn('alamat', function ($data) {
                                 if ( empty($data->detail->alamat) ) {
                                     return '-';
                                 }

                                 return $data->detail->alamat;
                             })
                             ->addColumn('agama', function ($data) {
                                 if ( empty($data->detail->agama_id) ) {
                                     return '-';
                                 }

                                 return $data->detail->agama->nama_agama;
                             })
                             ->addColumn('wilayah', function ($data) {
                                 if ( empty($data->detail->kelurahan) ) {
                                     return '-';
                                 }

                                 return $data->detail->kelurahan->kecamatan->kota->provinsi->nama_provinsi . ' - ' .
                                     $data->detail->kelurahan->kecamatan->kota->nama_kota . ' - ' .
                                     $data->detail->kelurahan->kecamatan->nama_kecamatan . ' - ' .
                                     $data->detail->kelurahan->nama_kelurahan;
                             })
                             ->addColumn('refferal', function ($data) {
                                 if ( empty($data->referal) ) {
                                     return '-';
                                 }

                                 return $data->referal;
                             })
                             ->addColumn('status_verification', function ($data) {
                                 if ( $data->is_verified ) {
                                     return 'Terverifikasi';
                                 }

                                 return 'Belum diverifikasi';
                             })
                             ->addColumn('action', function ($data) {
                                 return view('administrator.member._action', [
                                     'model'          => $data,
                                     'edit_url'       => route('member.edit', $data->id),
                                     'ubah_url'       => route('member.verify', $data->id),
                                     'ubah_message'   => "Ubah status member pada member " . $data->fullname . " ?",
                                     'revoke_url'     => route('member.revoke', $data->id),
                                     'revoke_message' => "Revoke member " . $data->fullname . " ?",
                                     'referal_url'    => route('member.herobi.referal', $data->id),
                                 ]);
                             })
                             ->addColumn('herobi', function ($data) {
                                 if ( empty($data->herobi) ) {
                                     return 'No Data';
                                 }
                                 if ( $data->herobi->valid == 0 ) {
                                     return view('administrator.member._herobi', [
                                         'model' => $data,
                                         'text'  => 'Show Data Herobi',
                                         'url'   => route('member.herobi', $data->id)
                                     ]);
                                 } else if ( $data->herobi->valid == 1 ) {
                                     return 'Approved';
                                 } else if ( $data->herobi->valid == 2 ) {
                                     return 'Denied';
                                 }
                             })
                             ->rawColumns([
                                 'action', 'herobi'
                             ])
                             ->make(true);
        }

        $html = $htmlBuilder
            ->addColumn([ 'data' => 'fullname', 'name' => 'fullname', 'title' => 'Full Name' ])
            ->addColumn([ 'data' => 'email', 'name' => 'email', 'title' => 'Email' ])
            ->addColumn([ 'data' => 'alamat', 'name' => 'alamat', 'title' => 'Alamat' ])
            ->addColumn([ 'data' => 'no_telp', 'name' => 'no_telp', 'title' => 'No. Telepon' ])
            ->addColumn([ 'data' => 'agama', 'name' => 'agama', 'title' => 'Agama' ])
            ->addColumn([ 'data' => 'wilayah', 'name' => 'wilayah', 'title' => 'Wilayah' ])
            ->addColumn([ 'data' => 'refferal', 'name' => 'refferal', 'title' => 'Kode Refferal' ])
            ->addColumn([ 'data' => 'akses', 'name' => 'akses', 'title' => 'Akses' ])
            ->addColumn([ 'data' => 'herobi', 'name' => 'herobi', 'title' => 'Herobi' ])
            ->addColumn([ 'data' => 'status_banned', 'name' => 'status_banned', 'title' => 'Status Banned' ])
            ->addColumn([
                'data'  => 'status_verification', 'name' => 'status_verification',
                'title' => 'Status Verifikasi'
            ])
            ->addColumn([
                'data'       => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false,
                'searchable' => false
            ]);

        return view('administrator.member.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create () {
        return redirect()->route('member.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request) {
        return redirect()->route('member.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show ($id) {
        return redirect()->route('member.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit ($id) {
        $data = User::with('detail.agama')
                    ->findOrFail($id);

        return view('administrator.member.edit', compact('data'));
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
        DB::beginTransaction();

        $member = User::findOrFail($id);

        $rules = [
            'fullname' => 'required',
            'email'    => 'required|email|unique:users,email,' . $member->id,
            'status'   => 'required',
        ];

        $messages = [
            'required' => 'Field :attribute harus diisi.',
            'email'    => 'Field :attribute harus berupa email.',
            'unique'   => 'Field :attribute sudah digunakan.',
            'numeric'  => 'Field :attribute harus berupa angka.',
            'exists'   => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        $member->fullname = $request->get('fullname');
        $member->email = $request->get('email');
        $member->status = $request->get('status');

        if ( $member->update() ) {
            DB::commit();

            Session::flash("flash_notification", [
                "level"   => "success",
                "message" => "Data berhasil diubah."
            ]);
        } else {
            DB::rollBack();

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Gagal mengubah data."
            ]);
        }

        return redirect()->route('member.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy ($id) {
        return redirect()->route('member.index');
    }

    public function verify (Request $request, $id) {
        $data = User::findOrFail($id);
        $data->is_verified = !$data->is_verified;
        $data->save();

        return redirect()->route('member.index');
    }

    public function revoke (Request $request, $id) {
        $result = DB::table('oauth_access_tokens')
                    ->where('user_id', $id)
                    ->where('revoked', 0)
                    ->update([ 'revoked' => 1 ]);

        return redirect()->route('member.index');
    }

    public function herobi ($id) {
        $data = User::with('herobi', 'detail.agama')
                    ->where('id', $id)
                    ->first();

        if ( !empty($data->herobi) && $data->herobi->valid == 1 ) {
            return redirect()->route('member.index');
        }

        $proses = [
            'approve_url'     => route('member.herobi.approve', $data->id),
            'approve_message' => "Approve " . $data->fullname . " as herobi?",
            'deny_url'        => route('member.herobi.deny', $data->id),
            'deny_message'    => "Deny " . $data->fullname . " as herobi?",
        ];

        $gambar = new Gambar();

        return view('administrator.member.herobi', compact('data', 'gambar'))
            ->with($proses);
    }

    public function approve (Request $request, $id) {
        $rules = [
            'detail_alamat' => 'required',
            'nik'           => 'required|numeric',
            'phone'         => 'required|numeric',
            'agama_id'      => 'required|exists:agamas,id',
        ];

        $messages = [
            'required' => 'Field :attribute harus diisi.',
            'email'    => 'Field :attribute harus berupa email.',
            'unique'   => 'Field :attribute sudah digunakan.',
            'numeric'  => 'Field :attribute harus berupa angka.',
            'exists'   => 'Field :attribute tidak ditemukan.',
        ];

        if ( empty($request->get('wilayah')) ) {
            $rules['kelurahan_id'] = 'required|exists:kelurahans,id';
            $rules['kecamatan_id'] = 'required|exists:kecamatans,id';
            $rules['kota_id'] = 'required|exists:kotas,id';
            $rules['provinsi_id'] = 'required|exists:provinsis,id';
        }

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Herobi::where('valid', 0)
                      ->where('user_id', $id)
                      ->first();
        $data->valid = 1;
        $data->nik = $request->get('nik');
        $data->update();

        $user = User::findOrFail($id);
        $user->referal = Str::upper(Str::random(10));
        $user->update();

        $referal = ReferalHerobi::where('valid', 0)
                                ->where('herobi_id', $data->id)
                                ->first();
        if ( !empty($referal) ) {
            $referal->valid = 1;
            $referal->update();
        }

        if ( empty($request->get('wilayah')) ) {
            $rules = [
                'kelurahan_id' => 'required|exists:kelurahans,id',
            ];

            $this->validate($request, $rules, $messages);
        }

        $detailuser = DetailUser::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'alamat'       => $request->get('detail_alamat'),
            'phone'        => $request->get('phone'),
            'kelurahan_id' => $request->get('kelurahan_id'),
            'agama_id'     => $request->get('agama_id'),
            'firebase'     => '',
            'valid'        => 1,
        ]);

        if ( $data->update() ) {
            DB::commit();

            Session::flash("flash_notification", [
                "level"   => "success",
                "message" => "Data berhasil diubah."
            ]);
        } else {
            DB::rollBack();

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Gagal mengubah data."
            ]);
        }

        return redirect()->route('member.index');
    }

    public function deny (Request $request, $id) {
        $rules = [
            'notes' => 'required',
        ];

        $messages = [
            'required' => 'Field :attribute harus diisi.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $notes = $request->get('notes');

        $data = Herobi::where('valid', 0)
                      ->where('user_id', $id)
                      ->first();
        $data->valid = 2;
        $data->notes = $notes;
        $data->update();

        $referal = ReferalHerobi::where('valid', 0)
                                ->where('herobi_id', $data->id)
                                ->first();
        if ( !empty($referal) ) {
            $referal->valid = 2;
            $referal->update();
        }

        $user = User::findOrFail($id);

        // Send Mail
        Mail::send('auth.email.herobi-reject', compact('user', 'notes'), function ($m) use ($user) {
            $m->to($user->email, $user->fullname)
              ->subject('Verifikasi akun OBBI');
        });

        if ( $data->update() ) {
            DB::commit();

            Session::flash("flash_notification", [
                "level"   => "success",
                "message" => "Data berhasil diubah."
            ]);
        } else {
            DB::rollBack();

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Gagal mengubah data."
            ]);
        }

        return redirect()->route('member.index');
    }

    public function referal_herobi (Request $request, Builder $htmlBuilder, $id) {
        $datas = ReferalHerobi::with('user', 'herobi.user')
                              ->where('user_id', $id)
                              ->get();

        $cek = Herobi::with('user')
                     ->where('user_id', $id)
                     ->first();
        if ( !empty($cek) && $cek->valid != 1 ) {
            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => $cek->user->fullname . " belum menjadi herobi. Silahkan proses terlebih dahulu."
            ]);

            return redirect()->route('member.index');
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
                                 return $data->herobi->user->fullname;
                             })
                             ->addColumn('email_referal', function ($data) {
                                 return $data->herobi->user->email;
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
            ->addColumn([ 'data' => 'email', 'name' => 'email', 'title' => 'Email Member' ])
            ->addColumn([ 'data' => 'referal', 'name' => 'referal', 'title' => 'Nama Referral' ])
            ->addColumn([ 'data' => 'email_referal', 'name' => 'email_referal', 'title' => 'Email Referral' ])
            ->addColumn([ 'data' => 'waktu', 'name' => 'waktu', 'title' => 'Waktu' ])
            ->addColumn([ 'data' => 'status', 'name' => 'status', 'title' => 'Status' ]);

        return view('administrator.member.referal_herobi')->with(compact('html'));
    }
}
