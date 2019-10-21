<?php

namespace App\Http;

use App\Http\Middleware\appLocale;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Akses\LogWeb::class,
        ],

        'api' => [
            // 'throttle:100,1',
            'throttle:100,1',
            'bindings',
            \App\Http\Middleware\Logging::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'            => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic'      => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'        => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers'   => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'             => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'           => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed'          => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'        => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'before'          => \App\Http\Middleware\Before::class,
        'after'           => \App\Http\Middleware\After::class,
        'checkpin'        => \App\Http\Middleware\Akses\CheckPin::class,
        'checkpaid'       => \App\Http\Middleware\Akses\CheckSaldoPaid::class,
        'cekToken'        => \App\Http\Middleware\Akses\CekToken::class,
        'log'             => \App\Http\Middleware\Akses\Log::class,
        'ceklogin'        => \App\Http\Middleware\Akses\CekLogin::class,
        'opf_type'        => \App\Http\Middleware\Opf\user_type::class,
        'opf_exists'      => \App\Http\Middleware\Opf\data_exists::class,
        'graham'          => \GrahamCampbell\Throttle\Http\Middleware\ThrottleMiddleware::class,
        'cekcookie'       => \App\Http\Middleware\Akses\CheckCookie::class,
        'cek_total_saldo' => \App\Http\Middleware\Akses\CekTotalSaldo::class,
        'cek_herobi'      => \App\Http\Middleware\Akses\CekHerobi::class,
        'cek_detail_user' => \App\Http\Middleware\Akses\CekDetailUser::class,
        'cek_toko'        => \App\Http\Middleware\Akses\CekAksesToko::class,
        'app_locale'      => \App\Http\Middleware\appLocale::class
    ];
}
