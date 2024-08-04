<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exam;

class EndExamCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exam:end {exam_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle the end of an exam';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $examId = $this->argument('exam_id');
        $exam = Exam::find($examId);

        if ($exam) {
            $exam->status = 'ended';
            $exam->save();

            // Gửi thông báo hoặc bất kỳ hành động nào cần thiết
            // ...
        }
    }
}
