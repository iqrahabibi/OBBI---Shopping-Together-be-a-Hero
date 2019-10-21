<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Helper\Formatting;
use App\Model\DonasiSummary;
use App\Model\Saldo;
use App\Model\Herobi;
use App\Model\Usaha;
use Cookie;
use Gate;

class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct () {
        $this->middleware('auth');
        // $this->middleware('cekcookie');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index () {
        if ( Gate::allows('admin-access') ) {
            $summary = DonasiSummary::sum('total_donasi');
            $saik = Formatting::rupiah($summary);

            $saldo = Saldo::sum('saldo');
            $doi = Formatting::rupiah($saldo);

            $user = Herobi::count();

            $usaha = Usaha::count();

            return view('administrator.home.index', compact('saik', 'doi', 'user', 'usaha'));
        }
        if ( Gate::allows('admin-gudang-access') ) {
            // dd('admin-gudang-access');
        }

        return view('home');
    }

    public function adminpage () {
        return view('dashboard.index');
    }

    public function customerpage () {
        return redirect('/');
    }

}
