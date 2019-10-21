<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;

use App\Model\User;
use App\Model\Role;
use App\Model\RoleUser;
use DataTables;
use Session;

class UserController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request, Builder $htmlBuilder) {

        if ( $request->ajax() ) {
            $datas = RoleUser::with('user', 'role')
                             ->whereHas('user', function ($user) {
                                 $user->whereNotNull('id');
                             });

            if ( $request->has('roles') ) {
                $role = Role::where('name', $request->get('roles'))->first();

                if ( !empty($role) ) {
                    $datas->where('role_id', $role->id);
                } else if ( $role != 'All' ) {
                    $datas->where('role_id', 2)
                          ->orWhere('role_id', 3)
                          ->orWhere('role_id', 7)
                          ->orWhere('role_id', 8)
                          ->orWhere('role_id', 10);
                }
            } else {
                $datas->where('role_id', 2)
                      ->orWhere('role_id', 3)
                      ->orWhere('role_id', 7)
                      ->orWhere('role_id', 8)
                      ->orWhere('role_id', 10);
            }

            $datas->get();

            return Datatables::of($datas)
                             ->addColumn('fullname', function ($data) {
                                 if ( empty($data->user) ) {
                                     return 'Data not found';
                                 }

                                 return $data->user->fullname;
                             })
                             ->addColumn('email', function ($data) {
                                 if ( empty($data->user) ) {
                                     return 'Data not found';
                                 }

                                 return $data->user->email;
                             })
                             ->addColumn('role', function ($data) {
                                 if ( empty($data->role) ) {
                                     return 'Data not found';
                                 }

                                 return $data->role->name;
                             })
                             ->addColumn('action', function ($data) {
                                 return view('administrator.user._action', [
                                     'model'           => $data,
                                     'form_url'        => route('user.destroy', $data->id),
                                     'confirm_message' => 'Yakin mau menghapus ' . $data->user->fullname . ' role ?'
                                 ]);
                             })->make(true);
        }

        $html = $htmlBuilder
            ->addColumn([ 'data' => 'fullname', 'name' => 'fullname', 'title' => 'Fullname' ])
            ->addColumn([ 'data' => 'email', 'name' => 'email', 'title' => 'Email' ])
            ->addColumn([ 'data' => 'role', 'name' => 'role', 'title' => 'Role' ])
            ->addColumn([
                'data'       => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false,
                'searchable' => false
            ]);

        return view('administrator.user.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create () {
        return view('administrator.user.create');
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
            'fullname' => 'required|string',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string',
            'phone'    => 'required|string',
            'role_id'  => 'required|exists:roles,id',
        ];

        $messages = [
            'required' => 'Field :attribute harus diisi.',
            'email'    => 'Field :attribute harus berupa email.',
            'unique'   => 'Field :attribute sudah digunakan, :attribute harus unik.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = User::create(array_merge($request->all(), [
            'fullname'    => strtoupper($request->get('fullname')),
            'email'       => strtoupper($request->get('email')),
            'password'    => bcrypt($request->get('password')),
            'status'      => '1',
            'is_verified' => 1,
        ]));
        $data->roles()->attach($request->get('role_id'));

        if ( $data->save() ) {
            DB::commit();

            Session::flash("flash_notification", [
                "level"   => "success",
                "message" => $data->fullname . " successfully saved."
            ]);
        } else {
            DB::rollBack();

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Data failed to be saved."
            ]);
        }

        return redirect()->route('user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show ($id) {
        return redirect()->route('user.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit ($id) {
        return redirect()->route('user.index');
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
        return redirect()->route('user.index');
    }


    public function destroy (Request $request, $id) {
        DB::beginTransaction();
        $query = DB::table('role_user')
                   ->where([
                       [ 'id', '=', $id ]
                   ])
                   ->delete();

        if ( !$query ) {
            DB::rollBack();
            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Data failed to be deleted."
            ]);
        } else {
            DB::commit();
            Session::flash("flash_notification", [
                "level"   => "success",
                "message" => "User berhasil di hapus"
            ]);
        }

        return redirect()->route('user.index');
    }

    public function role (Request $request, $id) {
        $data = User::findOrFail($id);

        return view('administrator.user.role', compact('data'));
    }
}
