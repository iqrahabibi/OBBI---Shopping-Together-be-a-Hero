<?php

namespace App\Model;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname', 'email', 'password', 'phone', 'status', 'is_verified', 'token_gmail'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    protected $searchable = [
        'users.id', 'fullname', 'email'
    ];

    protected $orderable = [
        'users.id', 'fullname', 'email'
    ];

    public function get_searchable () {
        return $this->searchable;
    }

    public function get_orderable () {
        return $this->orderable;
    }

    public function roles () {
        return $this->belongsToMany(Role::class);
        // return $this->hasMany(RoleUser::class, 'user_id', 'id')->with('role');
    }

    public function roles_2 () {
        return $this->hasMany(RoleUser::class, 'user_id', 'id')
                    ->with('role');
    }

    public function detail () {
        return $this->hasOne(DetailUser::class)
                    ->with('kelurahan')
                    ->where('valid', 1)
                    ->select([
                        'id', 'user_id', 'kelurahan_id', 'agama_id', 'alamat', 'phone', 'firebase', 'valid',
                        'created_at', 'updated_at'
                    ]);
    }

    public function herobi () {
        return $this->hasOne(Herobi::class);
    }

    public function device () {
        return $this->belongsToMany('App\Model\Device');
    }

    public function digipay () {
        return $this->belongsTo('App\Model\DigiPay');
    }

    public function saldo () {
        return $this->hasOne('App\Model\Saldo');
    }

    public function opf () {
        return $this->hasMany(Opf::class);
    }

    public function referal_opf () {
        return $this->hasOne(Opf::class)
                    ->with('referal_opf');
    }

    public function alamat () {
        return $this->hasMany('App\Model\Alamat_user');
    }

    public function aduan () {
        return $this->hasMany('App\Model\PengaduanOpf');
    }

    public function om_po () {
        return $this->hasMany('App\Model\OMerchantPo');
    }

    public function gudang () {
        return $this->hasOne('App\Model\Gudang');
    }

    public function authorizeRoles ($roles) {
        if ( is_array($roles) ) {

            return $this->hasAnyRole($roles) ||
                abort(401, 'This action is unauthorized.');

        }

        return $this->hasRole($roles) ||
            abort(401, 'This action is unauthorized.');
    }

    public function hasAnyRole ($roles) {
        return null !== $this->roles()
                             ->whereIn('name', $roles)
                             ->first();
    }

    public function hasRole ($role) {
        return null !== $this->roles()
                             ->where('name', $role)
                             ->first();
    }

    public function akses () {
        $akses = [];
        foreach ( $this->roles()
                       ->get() as $role ) {
            $akses[] = $role->name;
        }

        return implode(',', $akses);
    }

    public function setupDocument ($url) {
        if ( $url == '' || $url == '-' ) {
            return 'No File Found';
        }

        if ( strpos($url, '.jpg') !== false ||
            strpos($url, '.jpeg') !== false ||
            strpos($url, '.png') !== false ) {
            return view('layouts._gambar', [
                'url' => config('app.api') . $url
            ]);
        }

        return '';
    }

    public static function list ($id) {
        $list = [];
        $datas = self::where('id', '!=', $id)
                     ->get();
        foreach ( $datas as $data ) {
            $list[$data->id] = $data->fullname;
        }
    }

    public static function list_for_opf () {
        $list = [];
        $datas = self::with('roles')
                     ->whereHas('roles', function ($role) {
                         $role->where('role_id', 1);
                     })
                     ->get();
        foreach ( $datas as $data ) {
            $list[$data->id] = $data->fullname . ' - ' . $data->email;
        }

        return $list;
    }

    public static function list_for_omerchant () {
        $list = [];
        $datas = self::with('roles')
                     ->whereHas('roles', function ($role) {
                         $role->where('role_id', 1);
                     })
                     ->get();
        foreach ( $datas as $data ) {
            $list[$data->id] = $data->fullname . ' - ' . $data->email;
        }

        return $list;
    }

    public static function list_for_admin_omerchant () {
        $list = [];
        $datas = self::with('roles')
                     ->whereHas('roles', function ($role) {
                         $role->where('role_id', 1);
                     })
                     ->get();
        foreach ( $datas as $data ) {
            $list[$data->id] = $data->fullname . ' - ' . $data->email;
        }

        return $list;
    }

    public static function list_for_usaha () {
        $list = [];
        $datas = self::with('roles')
                     ->whereHas('roles', function ($role) {
                         $role->where('role_id', 1);
                     })
                     ->get();
        foreach ( $datas as $data ) {
            $list[$data->id] = $data->fullname . ' - ' . $data->email;
        }

        return $list;
    }

    public static function list_for_gudang ($current_user) {
        $list = [];
        $datas = self::with('roles')
                     ->whereHas('roles', function ($role) {
                         $role->where('role_id', 2); // Administrator
                     });

        if ( $current_user != null ) {
            $datas = $datas->orWhere('id', $current_user);
        }

        $datas = $datas->get();
        foreach ( $datas as $data ) {
            $list[$data->id] = $data->fullname . ' - ' . $data->email;
        }

        return $list;
    }
}
