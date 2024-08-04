<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExamRegistrationMail;

class SendExamRegistrationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $exam;
    protected $token;
    protected $address;

        /**
         * Create a new job instance.
         */
    public function __construct($exam, $token, $address)
    {
        $this->exam = $exam;
        $this->token = $token;
        $this->address = $address;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->address)->queue(new ExamRegistrationMail($this->exam, $this->token));
    }
}
