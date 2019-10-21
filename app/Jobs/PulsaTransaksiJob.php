<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Model\DigiPay;

use Log;

class PulsaTransaksiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $digi_pay;
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DigiPay $digi_pay)
    {
        $this->digi_pay  = $digi_pay;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = DigiPay::make($this->digi_pay);
        
        $result->save();
    }
}
