<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\PrizeNotificationMail;

class SendPrizeNotificationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $examName;
    protected $prize;
    protected $address;

    /**
     * Create a new job instance.
     */
    public function __construct($examName, $prize, $address)
    {
        $this->examName = $examName;
        $this->prize = $prize;
        $this->address = $address;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->address)->queue(new PrizeNotificationMail($this->examName, $this->prize, $this->address));
    }
}
