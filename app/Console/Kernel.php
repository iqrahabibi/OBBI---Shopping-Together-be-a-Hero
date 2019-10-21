<?php

namespace App\Console;

use App\Model\Checkout;
use App\Model\DigiPay;
use App\Model\Donasi;
use App\Model\DonasiSummary;
use App\Model\Finance;
use App\Model\Herobi;
use App\Model\Kelurahan;
use App\Model\Opf;
use App\Model\ReferalHerobi;
use App\Model\RoleUser;
use App\Model\Saldo;
use App\Model\User;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $this->topup();
            $this->checkout();
        })->everyFiveMinutes();
    }

    private function checkout()
    {
        $select = Checkout::where('status', 'waiting')
            ->get();

        DB::beginTransaction();

        foreach ($select as $key => $value) {
            $expired_at = Carbon::createFromFormat('Y-m-d H:i:s', $value->expired_at);
            $now = Carbon::now('Asia/Jakarta');

            if ($now->greaterThan($expired_at)) {
                $value->status = "cancelled";
                $value->update();
            }
        }

        DB::commit();

        $this->donasi();

    }

    private function donasi()
    {
        $select = Kelurahan::with('target.donasi')
            ->wherehas('target', function ($query) {
                $query->whereHas('donasi', function ($query2) {
                    $query2->whereMonth('created_at', Carbon::now()->format('m'));
                    $query2->whereYear('created_at', Carbon::now()->format('Y'));
                });
            })->get();

        $total_donasi = 0;
        $array = array();

        DB::beginTransaction();

        foreach ($select as $key => $value) {
            foreach ($value->target as $key2 => $value2) {
                foreach ($value2->donasi as $key3 => $value3) {

                    if ($value->id == $value2->kelurahan_id) {
                        $total_donasi += $value3->jumlah;
                    }

                    $donasi = DonasiSummary::where('kelurahan_id', $value->id)
                        ->whereMonth('created_at', '=', Carbon::now()->format('m'))
                        ->whereYear('created_at', '=', Carbon::now()->format('Y'))
                        ->first();

                    Log::channel('schedule')->debug($donasi);

                    if (!empty($donasi)) {
                        $donasi->total_donasi = $total_donasi;
                        $donasi->kelurahan_id = $value->id;
                        $donasi->update();
                    } else {
                        $donasi = new DonasiSummary();
                        $donasi->total_donasi = $total_donasi;
                        $donasi->kelurahan_id = $value->id;
                        $donasi->save();
                    }
                }
            }

            $total_donasi = 0;
        }

        DB::commit();

        $this->syncDataUser();

    }

    private function topup()
    {

        $client = new Client();

        $response = $client->request('GET', "https://app.moota.co/api/v1/bank/" . config('app.bank') . "/mutation/recent/60", [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . config('app.moota'),
            ],
        ]);

        $responsemoota = json_decode($response->getBody()->getContents(),true);

        if(!empty($responsemoota)){

        foreach ($responsemoota as $value) {
            $digipay = DigiPay::where([
                ['valid', '=', 0],
                ['jumlah', '=', $value['amount']],
                ['kode', '=', substr($value['amount'], -3)],
                ['invoice', '!=', null],
            ])->first();

            if (!empty($digipay)) {

            $email = User::where('id', $digipay->user_id)->first();

            $content = "Pembayaran Sudah Kami Terima sejumlah Rp. " . $value['amount'] . " dari Bank BRI dengan kode invoice " . $digipay->invoice;
            // $isi2 = "Hai Admin, Pembayaran Sudah Kami Terima sejumlah Rp. " . $value['amount'] . " dari Bank BRI dengan kode invoice " . $digipay->invoice . "\n
            //             Email = " . $email->email . "\n User ID = " . $email->id;

            DB::beginTransaction();  

                $digipay->valid = 1;
                $digipay->kode = 0;

                $digipay->save();

                $saldo = Saldo::where('user_id', $digipay->user_id)->first();

                if (!empty($saldo)) {
                    $totalsaldo = $saldo->saldo + $digipay->jumlah;
                    $totalkeuntungan = $saldo->keuntungan;
                    $totalamal = $saldo->amal;

                    $saldo->saldo = $totalsaldo;
                    $saldo->keuntungan = $totalkeuntungan;
                    $saldo->amal = $totalamal;

                    $saldo->update();

                } else {
                    $insert = new Saldo();
                    $insert->user_id = $digipay->user_id;
                    $insert->saldo = $digipay->jumlah;
                    $insert->keuntungan = 0;
                    $insert->amal = 0;

                    $insert->save();
                }

                DB::commit();
                Mail::send('bodyemail', compact('content'), function ($m) use ($email) {
                    $m->to($email->email, $email->fullname)
                        ->subject('[OBBI] Notification Pembayaran Diterima');
                });

                // Mail::send('bodyemail', compact('isi2'), function ($m) {
                //     $m->to('iqrahabibi03@gmail.com', 'Iqra Habibi')
                //         ->subject('Notification Response From Moota');
                // });
                Log::channel('schedule');
            }
            }
        }
    }

    private function syncDataUser()
    {
        DB::beginTransaction();

        $users = DB::connection('mysql_old')->table('users')->whereNull('sync')->get();
        foreach ($users as $user) {
            $old_detail_user = DB::connection('mysql_old')->table('detal_users')->whereNull('sync')->where([
                ['valid', '=', 1],
                ['user_id', '=', $user->id],
            ])->first();

            $status = $user->status == '1' ? '0' : '1';
            DB::connection('mysql')->table('users')->insert([
                'fullname' => $user->fullname,
                'email' => $user->email,
                'password' => $user->password,
                'phone' => $user->phone,
                'image' => $user->profile_pic,
                'status' => $status,
                'verification_token' => $user->verification_token,
                'is_verified' => $user->is_verified,
                'token_gmail' => '',
                'referal' => $user->referal,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);

            $newUser = DB::connection('mysql')->table('users')->where('email', $user->email)->first();

            // user_type = 1 : user biasa
            // user_type = 2 : user yang udah terverifikasi ktp
            // untuk database baru role_id yang 1 dan 2 : herobi (user_type = 1)
            if ($user->user_type == 1 || $user->user_type == 2) {
                DB::connection('mysql')->table('role_user')->insert([
                    'role_id' => 1,
                    'user_id' => $newUser->id,
                    'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                ]);
            }

            // user_type = 3 : admin
            // role_id  = 2 : admin di databas yang baru
            if ($user->user_type == 3) {
                DB::connection('mysql')->table('role_user')->insert([
                    'role_id' => 2,
                    'user_id' => $newUser->id,
                    'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                ]);
            }

            if (!empty($old_detail_user)) {
                DB::connection('mysql')->table('detail_users')->insert([
                    'user_id' => $newUser->id,
                    'kelurahan_id' => $old_detail_user->kelurahan_id,
                    'alamat' => $old_detail_user->alamat,
                    'phone' => $old_detail_user->phone,
                    'valid' => $old_detail_user->valid,
                ]);

                /// kata bang joko : semua user harus di backup
                DB::connection('mysql_old')->table('detal_users')->where([
                    ['user_id', '=', $user->id],
                    ['valid', '=', 1],
                ])->update([
                    'sync' => 1,
                ]);
            }

            DB::connection('mysql_old')->table('users')->where('id', $user->id)->update([
                'sync' => 1,
            ]);
        }

        // $cek            = new LG();
        // $cek->user_id   = 2;
        // $cek->route     = 'migrasi';
        // $cek->method    = 'syncDataUser';
        // $cek->parameter = $users->count();
        // $cek->save();

        DB::commit();

        $this->syncDataHerobi();
    }

    private function syncDataHerobi()
    {
        DB::beginTransaction();
        /// valid harus 1 karena kalo misalnya 0 (foto ktp/foto muka/foto muka+ktp) di tolak
        $herobis = DB::connection('mysql_old')->table('herobis')->whereNull('sync')->where('valid', 1)->get();
        foreach ($herobis as $herobi) {
            $user_lama = DB::connection('mysql_old')->table('users')->where('id', $herobi->user_id)->first();
            if (empty($user_lama)) {
                continue;
            }

            /// kalau udah pernah di backup (check email) maka skip untuk pemeriksaan backup ini menggunakan email
            /// dikarenakan auto_increment di db lama dan baru tidak sinkron
            $user_baru = DB::connection('mysql')->table('users')->where('email', $user_lama->email)->first();
            if (empty($user_baru)) {
                continue;
            }

            /// Migrasi Data Herobi
            Herobi::create([
                'user_id' => $user_baru->id,
                'ktp' => $herobi->image1,
                'kk' => $herobi->image2,
                'selfi' => $herobi->image3,
                'nik' => $herobi->noktp,
                'valid' => $herobi->valid,
                'created_at' => $herobi->created_at,
                'updated_at' => $herobi->updated_at,
            ]);

            DB::connection('mysql_old')->table('herobis')->where('id', $herobi->id)->update([
                'sync' => 1,
            ]);
        }

        // $cek            = new LG();
        // $cek->user_id   = 2;
        // $cek->route     = 'migrasi';
        // $cek->method    = 'syncDataHerobi';
        // $cek->parameter = $herobis->count();
        // $cek->save();

        ///
        /// valid 0 (herobis) lagi ngajuin dokumen tapi belom di check oleh admin, kalau sudah di approve oleh admin
        /// maka valid akan menjadi 1. kalau di database lama dokumen di tolak maka valid = 3 dan di database baru formatnya adalah
        ///
        /// --------------------- format yang lama ---------------------
        /// 0 : belum dicheck
        /// 1 : diterima
        /// 3 : ditolak
        ///
        /// --------------------- format yang baru ---------------------
        /// 0 : belum di check
        /// 1 : diterima / valid
        /// 2 : ditolak
        ///
        $herobis = DB::connection('mysql_old')->table('herobis')->whereNull('sync')->where('valid', 0)->orderBy('created_at', 'desc')->get();
        foreach ($herobis as $herobi) {
            $user_lama = DB::connection('mysql_old')->table('users')->where('id', $herobi->user_id)->first();
            if (empty($user_lama)) {
                continue;
            }

            /// kalau udah pernah di backup (check email) maka skip untuk pemeriksaan backup ini menggunakan email
            /// dikarenakan auto_increment di db lama dan baru tidak sinkron
            $user_baru = DB::connection('mysql')->table('users')->where('email', $user_lama->email)->first();
            if (empty($user_baru)) {
                continue;
            }

            $cari = Herobi::where('user_id', $user_baru->id)->first();
            if (empty($cari)) {
                Herobi::create([
                    'user_id' => $user_baru->id,
                    'ktp' => $herobi->image1,
                    'kk' => $herobi->image2,
                    'selfi' => $herobi->image3,
                    'nik' => $herobi->noktp,
                    'valid' => $herobi->valid,
                    'created_at' => $herobi->created_at,
                    'updated_at' => $herobi->updated_at,
                ]);

                DB::connection('mysql_old')->table('herobis')->where('id', $herobi->id)->update([
                    'sync' => 1,
                ]);
            }
        }

        // $cek            = new LG();
        // $cek->user_id   = 2;
        // $cek->route     = 'migrasi';
        // $cek->method    = 'syncDataHerobi';
        // $cek->parameter = $herobis->count();
        // $cek->save();

        $herobis = DB::connection('mysql_old')->table('herobis')->whereNull('sync')->where('valid', 3)->orderBy('created_at', 'desc')->get();
        foreach ($herobis as $herobi) {
            $user_lama = DB::connection('mysql_old')->table('users')->where('id', $herobi->user_id)->first();
            if (empty($user_lama)) {
                continue;
            }
            $user_baru = DB::connection('mysql')->table('users')->where('email', $user_lama->email)->first();
            if (empty($user_baru)) {
                continue;
            }

            $cari = Herobi::where('user_id', $user_baru->id)->first();
            if (empty($cari)) {
                Herobi::create([
                    'user_id' => $user_baru->id,
                    'ktp' => $herobi->image1,
                    'kk' => $herobi->image2,
                    'selfi' => $herobi->image3,
                    'nik' => $herobi->noktp,
                    'valid' => 2,
                    'created_at' => $herobi->created_at,
                    'updated_at' => $herobi->updated_at,
                ]);

                $updateherobi = DB::connection('mysql_old')->table('herobis')->where('id', $herobi->id)->update([
                    'sync' => 1,
                ]);
            }
        }

        // $cek            = new LG();
        // $cek->user_id   = 2;
        // $cek->route     = 'migrasi';
        // $cek->method    = 'syncDataHerobi';
        // $cek->parameter = $herobis->count();
        // $cek->save();

        DB::commit();

        $this->syncDataReferalHerobi();
    }

    private function syncDataReferalHerobi()
    {
        DB::beginTransaction();

        $herobis = DB::connection('mysql_old')->table('herobis')->where('sync', 1)->get();

        foreach ($herobis as $herobi) {
            $user_lama = DB::connection('mysql_old')->table('users')->where('id', $herobi->user_id)->first();
            if (empty($user_lama)) {
                continue;
            }
            $user_baru = DB::connection('mysql')->table('users')->where('email', $user_lama->email)->first();
            if (empty($user_baru)) {
                continue;
            }

            // Migrasi Data Referal Herobi
            $referal_lamas = DB::connection('mysql_old')->table('referals')->where('code_referal', strtoupper($user_baru->referal))->where('is_active', 1)->get();
            foreach ($referal_lamas as $referal_lama) {
                $cari_herobi_lama = DB::connection('mysql_old')->table('herobis')->where('id', $referal_lama->herobi_id)->first();
                $cari_user_lama = DB::connection('mysql_old')->table('users')->where('id', $cari_herobi_lama->user_id)->first();

                $cari_user_baru = DB::connection('mysql')->table('users')->where('email', $cari_user_lama->email)->first();
                $cari_herobi_baru = DB::connection('mysql')->table('herobis')->where('user_id', $cari_user_baru->id)->first();

                if (empty($cari_herobi_baru)) {
                    continue;
                }

                ReferalHerobi::create([
                    'user_id' => $user_baru->id,
                    'herobi_id' => $cari_herobi_baru->id,
                    'valid' => $referal_lama->is_active, // Value = 1
                    'created_at' => $referal_lama->created_at,
                    'updated_at' => $referal_lama->updated_at,
                ]);

                $updatereferal = DB::connection('mysql_old')->table('referals')->where('id', $referal_lama->id)->update([
                    'sync' => 1,
                ]);
            }

            // $cek            = new LG();
            // $cek->user_id   = 2;
            // $cek->route     = 'migrasi';
            // $cek->method    = 'syncDataReferalHerobi';
            // $cek->parameter = $referal_lamas->count();
            // $cek->save();

            $referal_lamas = DB::connection('mysql_old')->table('referals')->where('code_referal', $user_baru->referal)->where('is_active', 0)->get();
            foreach ($referal_lamas as $referal_lama) {
                $cari_herobi_lama = DB::connection('mysql_old')->table('herobis')->where('id', $referal_lama->herobi_id)->first();
                $cari_user_lama = DB::connection('mysql_old')->table('users')->where('id', $cari_herobi_lama->user_id)->first();

                $cari_user_baru = DB::connection('mysql')->table('users')->where('email', $cari_user_lama->email)->first();
                $cari_herobi_baru = DB::connection('mysql')->table('herobis')->where('user_id', $cari_user_baru->id)->first();

                if (empty($cari_herobi_baru)) {
                    continue;
                }

                $cari = ReferalHerobi::where('user_id', $user_baru->id)->where('herobi_id', $cari_herobi_baru->id)->first();
                if (empty($cari)) {
                    ReferalHerobi::create([
                        'user_id' => $user_baru->id,
                        'herobi_id' => $cari_herobi_baru->id,
                        'valid' => $referal_lama->is_active, // Value = 0
                        'created_at' => $referal_lama->created_at,
                        'updated_at' => $referal_lama->updated_at,
                    ]);

                    $updatereferal = DB::connection('mysql_old')->table('referals')->where('id', $referal_lama->id)->update([
                        'sync' => 1,
                    ]);
                }
            }

            // $cek            = new LG();
            // $cek->user_id   = 2;
            // $cek->route     = 'migrasi';
            // $cek->method    = 'syncDataReferalHerobi';
            // $cek->parameter = $referal_lamas->count();
            // $cek->save();
        }

        DB::commit();

        $this->syncRoleUser();
    }

    private function syncRoleUser()
    {
        DB::beginTransaction();

        $herobis = DB::connection('mysql_old')->table('herobis')->where('sync', 1)->where('valid', 1)->get();
        foreach ($herobis as $herobi) {
            $user_lama = DB::connection('mysql_old')->table('users')->where('id', $herobi->user_id)->first();
            if (empty($user_lama)) {
                continue;
            }
            $user_baru = DB::connection('mysql')->table('users')->where('email', $user_lama->email)->first();
            if (empty($user_baru)) {
                continue;
            }

            RoleUser::updateOrCreate([
                'user_id' => $user_baru->id,
                'role_id' => 1,
            ], [
                'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
            ]);
        }

        // $cek            = new LG();
        // $cek->user_id   = 2;
        // $cek->route     = 'migrasi';
        // $cek->method    = 'syncRoleUser';
        // $cek->parameter = $herobis->count();
        // $cek->save();

        $cek['user_id'] = 2;
        $cek['route'] = 'migrasi';
        $cek['method'] = 'syncRoleUser';
        $cek['parameter'] = $herobis->count();

        Log::channel('schedule')->debug($cek);

        DB::commit();

        $this->syncUserBalances();
    }

    private function syncUserBalances()
    {
        DB::beginTransaction();

        $user_lamas = DB::connection('mysql_old')->table('users')->where('sync', 1)->get();

        foreach ($user_lamas as $user_lama) {
            $userbalances = DB::connection('mysql_old')->table('userbalances')->where('user_id', $user_lama->id)->where('valid', 1)->whereNull('sync')->get();

            $temp_saldo = 0;
            $temp_keuntungan = 0;

            foreach ($userbalances as $userbalance) {

                $cari_user_baru = DB::connection('mysql')->table('users')->where('email', $user_lama->email)->first();
                if (empty($cari_user_baru)) {
                    continue;
                }

                $kode = '';
                if (preg_match("#\b(PULSA|PAKET|pulsa|paket)\b#", $userbalance->notes)) {
                    $kode = 'VCH';
                } else if (preg_match("#\b(PASCA)\b#", $userbalance->notes)) {
                    $kode = 'PLNP';
                } else if (preg_match("#\b(TOKEN)\b#", $userbalance->notes)) {
                    $kode = 'PPLN';
                } else if (preg_match("#\b(BPJS)\b#", $userbalance->notes)) {
                    $kode = 'BPJSKES';
                } else if (preg_match("#\b(PDAM)\b#", $userbalance->notes)) {
                    $kode = 'PDAM';
                } else {

                }

                $finance = Finance::where('kode', $kode)->first();
                $finance_id = null;
                if (!empty($finance)) {
                    $finance_id = $finance->id;
                }

                DB::connection('mysql')->table('digi_pays')->insert([
                    'user_id' => $cari_user_baru->id,
                    'finance_id' => $finance_id,
                    'invoice' => $userbalance->invoice,
                    'awal' => 0,
                    'jumlah' => $userbalance->balance,
                    'akhir' => 0,
                    'trxid' => $userbalance->trxid,
                    'notes' => $userbalance->notes,
                    'phone' => $userbalance->notelp,
                    'tipe_token' => $userbalance->token_type,
                    'token_number' => $userbalance->token_number,
                    'kode' => $userbalance->code,
                    'valid' => $userbalance->valid,
                ]);

                $temp_saldo += $userbalance->balance;
                $temp_keuntungan += $userbalance->keuntungan;

                $cari_saldo_user_baru = DB::connection('mysql')->table('saldos')->where('user_id', $cari_user_baru->id)->first();
                if (empty($cari_saldo_user_baru)) {
                    DB::connection('mysql')->table('saldos')->insert([
                        'user_id' => $cari_user_baru->id,
                        'saldo' => $temp_saldo,
                        'amal' => 0,
                        'keuntungan' => $temp_keuntungan,
                        'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    DB::connection('mysql')->table('saldos')->where('user_id', $cari_user_baru->id)->update([
                        'saldo' => $temp_saldo,
                        'keuntungan' => $temp_keuntungan,
                        'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    ]);
                }

                $updateuserbalance = DB::connection('mysql_old')->table('userbalances')->where('id', $userbalance->id)->update([
                    'sync' => 1,
                ]);
            }

            // $cek            = new LG();
            // $cek->user_id   = 2;
            // $cek->route     = 'migrasi';
            // $cek->method    = 'syncUserBalances';
            // $cek->parameter = $userbalances->count();
            // $cek->save();

            $cek['user_id'] = 2;
            $cek['route'] = 'migrasi';
            $cek['method'] = 'syncUserBalances';
            $cek['parameter'] = $userbalances->count();

            Log::channel('schedule')->debug($cek);
        }

        DB::commit();

        $this->syncSaldoAmals();
    }

    private function syncSaldoAmals()
    {
        DB::beginTransaction();

        $user_lamas = DB::connection('mysql_old')->table('users')->where('sync', 1)->get();

        foreach ($user_lamas as $user_lama) {
            $saldoamals = DB::connection('mysql_old')->table('saldoamals')->where('user_id', $user_lama->id)->whereNull('sync')->get();

            $temp_amal = 0;

            foreach ($saldoamals as $saldoamal) {
                $cari_user_baru = DB::connection('mysql')->table('users')->where('email', $user_lama->email)->first();
                if (empty($cari_user_baru)) {
                    continue;
                }

                $temp_amal += $saldoamal->jumlah;

                $cari_saldo_user_baru = DB::connection('mysql')->table('saldos')->where('user_id', $cari_user_baru->id)->first();
                if (empty($cari_saldo_user_baru)) {
                    DB::connection('mysql')->table('saldos')->insert([
                        'user_id' => $cari_user_baru->id,
                        'saldo' => 0,
                        'amal' => $temp_amal,
                        'keuntungan' => 0,
                        'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    DB::connection('mysql')->table('saldos')->where('user_id', $cari_user_baru->id)->update([
                        'amal' => $temp_amal,
                        'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    ]);
                }

                $updatesaldoamal = DB::connection('mysql_old')->table('saldoamals')->where('id', $saldoamal->id)->update([
                    'sync' => 1,
                ]);
            }

            // $cek            = new LG();
            // $cek->user_id   = 2;
            // $cek->route     = 'migrasi';
            // $cek->method    = 'syncSaldoAmals';
            // $cek->parameter = $saldoamals->count();
            // $cek->save();
            $cek['user_id'] = 2;
            $cek['route'] = 'migrasi';
            $cek['method'] = 'syncSaldoAmals';
            $cek['parameter'] = $saldoamals->count();

            Log::channel('schedule')->debug($cek);
        }

        DB::commit();
    }

    private function syncOpfs()
    {
        DB::beginTransaction();

        $user_lamas = DB::connection('mysql_old')->table('users')->where('sync', 1)->get();

        foreach ($user_lamas as $user_lama) {
            $opfs = DB::connection('mysql_old')->table('opfs')->where('user_id', $user_lama->id)->whereNull('sync')->get();

            foreach ($opfs as $opf) {
                $cari_user_baru = DB::connection('mysql')->table('users')->where('email', $user_lama->email)->first();
                if (empty($cari_user_baru)) {
                    continue;
                }

                Opf::updateOrCreate([
                    'user_id' => $cari_user_baru->id,
                ], [
                    'foto' => $opf->image,
                    'handphone' => $opf->phone,
                    'referal' => $opf->kode_referal_opf,
                    'valid' => $opf->valid,
                ]);

                $opf = DB::connection('mysql_old')->table('opfs')->where('id', $opf->id)->update([
                    'sync' => 1,
                ]);
            }

            // $cek            = new LG();
            // $cek->user_id   = 2;
            // $cek->route     = 'migrasi';
            // $cek->method    = 'syncOpfs';
            // $cek->parameter = $opfs->count();
            // $cek->save();

            $cek['user_id'] = 2;
            $cek['route'] = 'migrasi';
            $cek['method'] = 'syncOpfs';
            $cek['parameter'] = $opfs->count();

            Log::channel('schedule')->debug($cek);
        }

        DB::commit();

        $this->syncOpfs();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
