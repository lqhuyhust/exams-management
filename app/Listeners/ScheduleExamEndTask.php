<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;
use App\Events\ExamCreated;

class ScheduleExamEndTask
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExamCreated $event): void
    {
        $exam = $event->exam;
        $endTime = $exam->end_time;

        // Schedule a task at the time of end_time
        Artisan::call('schedule:run', ['--task' => 'ExamEnd', '--time' => $endTime]);
    }
}
