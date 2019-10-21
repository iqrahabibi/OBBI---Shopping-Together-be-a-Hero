<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('test_hello', function () {
    return ['hello'];
});
Route::get('/', function () {
    return response('Hello World!', 200)->header('Content-Type', 'text/plain');
});
Route::post('/version', 'API\Read');
Route::prefix('mobile')->middleware('app_locale')->group(function () {
    // Route: api/mobile/login
    Route::prefix('login')->middleware([ 'graham:2,1', 'ceklogin' ])->group(function () {
        Route::post('', 'API\Auth\Mobile\Login')->name('login');
        Route::post('gmail', 'API\Auth\Mobile\LoginGmail')->name('login.gmail');
    });

    Route::prefix('register')->group(function () {
        Route::post('/', 'API\Auth\Mobile\Register')->name('register');
        Route::post('/token', 'API\Auth\Mobile\Verify')->name('register.token');
        Route::post('/resend', 'API\Auth\Mobile\Resend')->name('register.resend');
        Route::post('/gmail', 'API\Auth\Mobile\RegisterGmail');
    });

    Route::post('/reset', 'API\Auth\Mobile\Forgot')->name('mobile.reset');
    Route::post('/logout', 'API\Auth\Mobile\Logout');
});

Route::prefix('toko')->group(function () {
    Route::post('/login', 'API\Auth\Toko\Login')->name('login')->middleware([ 'graham:2,1', 'ceklogin' ]);
});

// Login Web
Route::post('/web/login', 'API\Auth\Web\Login')->middleware([ 'graham:2,1', 'ceklogin' ]);
Route::get('/slider', 'API\Slider\Read');

// bugsnag route
Route::get('/bugsnag', 'API\Home@bugsnag');
Route::post('/barang/nasional', 'API\Barang\Nasional');
Route::group([ 'middleware' => [ 'auth:api', 'cekToken', 'log' ] ], function () {
    // Agama
    Route::post('/agama/read', 'API\Agama\Read');

    // Province
    Route::post('/provinsi/read', 'API\Province\Read');

    // City
    Route::prefix('kota')->group(function () {
        Route::post('/read', 'API\City\Read');
        Route::post('/search', 'API\City\Search');
    });

    // Subdistrict/kecamatan
    Route::prefix('kecamatan')->group(function () {
        Route::post('/read', 'API\Subdistrict\Read');
        Route::post('/search', 'API\Kecamatan\Search');
    });

    // Kelurahan
    Route::prefix('kelurahan')->group(function () {
        Route::post('/read', 'API\Kelurahan\Read');
        Route::post('/search', 'API\Kelurahan\Search');
    });

    /**
     * Toko Daerah
     */
    Route::prefix('toko')->group(function () {
        Route::prefix('daerah')->group(function () {
            Route::post('/read', 'API\Toko\Daerah\Read')
                 ->middleware('cek_toko');
        });

        Route::prefix('omerchant')->group(function () {
            Route::prefix('barang')->group(function () {
                Route::prefix('varian')->group(function () {
                    Route::post('/', 'API\Toko\OMercant\Barang\Varian\Read');
                    Route::post('/create', 'API\Toko\OMercant\Barang\Varian\Create');
                    Route::post('/update', 'API\Toko\OMercant\Barang\Varian\Update');
                    Route::post('/delete', 'API\Toko\OMercant\Barang\Varian\Delete');
                });

                Route::prefix('gambar')->group(function () {
                    Route::post('/', 'API\Toko\OMercant\Barang\Gambar\Read');
                    Route::post('/create', 'API\Toko\OMercant\Barang\Gambar\Create');
                    Route::post('/update', 'API\Toko\OMercant\Barang\Gambar\Update');
                    Route::post('/delete', 'API\Toko\OMercant\Barang\Gambar\Delete');
                });
            });
        });
    });

    // Herobi
    Route::prefix('herobi')->group(function () {
        Route::post('dokumen', 'API\Herobi\Dokumen');
        Route::post('referal', 'API\Herobi\Referal');
        Route::post('check-pengajuan', 'API\Herobi\PeriksaStatus');
    });

    Route::post('/barang/all', 'API\Barang\ReadAll');

    // User
    Route::prefix('user')->group(function () {
        Route::post('profile', 'API\User\Profile');
        Route::post('password', 'API\User\Password');
        Route::post('updatefoto', 'API\User\UpdateFoto');
        Route::prefix('firebase')->group(function () {
            Route::post('', 'API\User\Firebase\UpdateToken');
        });
    });

    Route::middleware('cek_herobi')->group(function () {
        Route::middleware('cek_detail_user')->group(function () {

            // Revoke token User
            Route::prefix('revoke')->group(function () {
                Route::post('/update', 'API\Revoke\Update@update');
                //Route::get('/getalltoken','RevokeController@showtoken');
                Route::get('/', 'API\Revoke\Read@index');
            });

            // Isi saldo
            Route::prefix('saldo')->group(function () {
                // ALL
                Route::get('/', 'API\Saldo\Read_saldo')->name('saldo');

                // mobile
                Route::post('/isisaldo', 'API\Saldo\Balance')->name('isisaldo')
                     ->middleware('cek_total_saldo');
                Route::post('/cek', 'API\Saldo\Check_saldo')->name('saldo.cek');
                Route::post('/cekhistori', 'API\Saldo\History')->name('saldo.cekhistori');
                // ->middleware('checkpin');
                // ->middleware(['checkpin']);

                // Dashboard
                Route::get('/transaksi', 'API\Saldo\Read_transaksi');
                Route::post('/edit', 'API\Saldo\Edit')->name('saldo.edit');
                Route::post('/delete', 'API\Saldo\Delete')->name('saldo.delete');
                Route::post('/validasi', 'API\Saldo\Grant')->name('saldo.validasi');
                // Route::post('/unvalid','API\Balance@unvalid')->name('saldo.unvalid');
            });

            // Donasi
            Route::prefix('donasi')->group(function () {
                Route::post('/alokasi', 'API\Donasi\Alocation');
                Route::post('/target_donasi', 'API\Donasi\Target');
                Route::post('/saldo/daerah', 'API\Donasi\SaldoDonasiDaerah');
                Route::post('/saldo/nasional', 'API\Donasi\SaldoDonasiNasional');

                Route::prefix('report')->group(function () {
                    Route::post('/all', 'API\Donasi\Report@report_all');
                    Route::post('/history', 'API\Donasi\Report@history_saik');
                });
            });

            // Saldo Amal
            Route::prefix('amal')->group(function () {
                Route::post('/all', 'API\Amal\Saldo@all');
                Route::post('/pribadi', 'API\Amal\Saldo@pribadi');
            });

            // PIN
            Route::post('/user/pin/create', 'API\PIN\Create@index');
            Route::middleware([ 'cek_herobi' ])->prefix('bakoel')->group(function () {
                // Pulsa
                Route::prefix('pulsa')->group(function () {

                    Route::prefix('voucher')->group(function () {
                        Route::post('/', 'API\Bakoel\Pulsa\Voucher\Read');
                        Route::post('/history', 'API\Bakoel\Pulsa\Voucher\History');
                    });

                    Route::prefix('paketdata')->group(function () {
                        Route::post('/', 'API\Bakoel\Pulsa\Paket\Read');
                        Route::post('/history', 'API\Bakoel\Pulsa\Paket\History');
                    });

                    Route::post('/purchase', 'API\Bakoel\Pulsa\Purchase')->middleware([ 'checkpin', 'graham:1,1' ]);
                    // ->middleware(['checkpin']);;
                    Route::post('/cektrxID', 'API\Bakoel\Pulsa\Cek_trxid');
                });

                Route::prefix('pln')->group(function () {
                    // PLN Pasca
                    Route::prefix('pasca')->group(function () {
                        Route::post('/', 'API\Bakoel\PLN\Pasca\Inquiry');
                        Route::post('/history', 'API\Bakoel\PLN\Pasca\History');
                        Route::post('/payment', 'API\Bakoel\PLN\Pasca\Payment')
                             ->middleware([ 'checkpin', 'graham:1,1' ]);
                    });

                    // PLN Prabayar
                    Route::prefix('token')->group(function () {
                        Route::post('/', 'API\Bakoel\PLN\Token\Inquiry');
                        Route::post('/history', 'API\Bakoel\PLN\Token\History');
                        Route::post('/payment', 'API\Bakoel\PLN\Token\Payment')
                             ->middleware([ 'checkpin', 'graham:1,1' ]);

                        Route::post('/manualadvice', 'API\Bakoel\PLN\Token\Manual_advice');
                    });

                });

                Route::prefix('bpjs')->group(function () {
                    // BPJS Kesehatan
                    Route::post('/inquiry', 'API\Bakoel\BPJS\Inquiry')->name('bakoel.bpjs.inquiry');
                    Route::post('/payment', 'API\Bakoel\BPJS\Payment')->name('bakoel.bpjs.payment')
                         ->middleware([ 'graham:1,1', 'checkpin' ]);
                    Route::post('/log', 'API\Bakoel\BPJS\Log')->name('bakoel.bpjs.log');
                    Route::post('/history', 'API\Bakoel\BPJS\History')->name('bakoel.bpjs.history');
                });

                Route::prefix('pdam')->group(function () {
                    // PDAM
                    Route::post('/inquiry', 'API\Bakoel\PDAM\Inquiry')->name('bakoel.pdam.inquiry');
                    Route::post('/log', 'API\Bakoel\PDAM\Log')->name('bakoel.pdam.log');
                    Route::post('/history', 'API\Bakoel\PDAM\History')->name('bakoel.pdam.history');
                    Route::post('/payment', 'API\Bakoel\PDAM\Payment')
                         ->name('bakoel.pdam.payment')
                         ->middleware([ 'graham:1,1', 'checkpin' ]);
                });

                Route::prefix('telkom')->group(function () {
                    Route::post('/inquiry', 'API\Bakoel\Telkom\Inquiry');
                    Route::post('/history', 'API\Bakoel\Telkom\History');
                    Route::post('/payment', 'API\Bakoel\Telkom\Payment')
                         ->middleware([ 'graham:1,1', 'checkpin' ]);
                });

                Route::prefix('etoll')->group(function () {
                    Route::post('/inquiry', 'API\Bakoel\Etoll\Inquiry');
                    Route::post('/history', 'API\Bakoel\Etoll\History');
                    Route::post('/payment', 'API\Bakoel\Etoll\Payment');
                });

                Route::prefix('kai')->group(function () {
                    Route::post('/stasiun', 'API\Bakoel\KAI\Stasiun');
                    Route::post('/jadwal', 'API\Bakoel\KAI\Jadwal');
                    Route::post('/kursi', 'API\Bakoel\KAI\Kursi');
                    Route::post('/subclass', 'API\Bakoel\KAI\Subclass');
                    Route::post('/booking', 'API\Bakoel\KAI\Booking');
                });
            });

            Route::prefix('dibayarin')->group(function () {
                // Dibayarin
                Route::post('/inquiry', 'API\Paid\Inquiry');
                Route::post('/payment', 'API\Paid\Payment')->middleware('graham:1,1');
                // ->middleware('checkpin');
            });

            // OPF
            Route::prefix('opf')->group(function () {
                Route::prefix('create')->group(function () {
                    Route::post('aduan', 'API\OPF\CreateAduan');
                });
                Route::post('/profile', 'API\OPF\Profile');
                Route::post('list-referal', 'API\OPF\ListReferal');
            });

            // User Alamat
            Route::prefix('alamat')->group(function () {
                Route::post('/', 'API\Alamat\Read');
                Route::post('/create', 'API\Alamat\Create');
                Route::post('/delete', 'API\Alamat\Delete');
            });

            // Barang
            Route::prefix('barang')->group(function () {
                Route::post('/daerah', 'API\Barang\ReadDaerah');
            });

            // Cart
            Route::prefix('cart')->group(function () {
                Route::post('/', 'API\Belanja\Cart\Create');
                Route::post('/read', 'API\Belanja\Cart\Read');
                Route::post('/delete', 'API\Belanja\Cart\Delete');
                Route::post('/ongkir', 'API\Belanja\Ongkir\Ongkir');
                Route::post('/kurir', 'API\Belanja\Ongkir\Kurir');
                Route::post('/checkout', 'API\Belanja\Pembayaran');
                Route::post('/showinvoice', 'API\Belanja\ReadInvoice');
            });
        });
    });
});