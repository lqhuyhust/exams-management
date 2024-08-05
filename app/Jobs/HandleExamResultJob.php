<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ExamService;

class HandleExamResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $examID;
    protected $examService;

    /**
     * Create a new job instance.
     */
    public function __construct(ExamService $examService, $examID)
    {
        $this->examService = $examService;
        $this->examID = $examID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // submit unsubmitted records automatically
        $this->examService->submitAutomatically($this->examID);

        // handle exam result
        $this->examService->handleExamResult($this->examID);

        // set prizes for the exam
        $this->examService->setPrizes($this->examID);

        // remove exam from redis
        $this->examService->removeExamFromRedis($this->examID);
    }
}
