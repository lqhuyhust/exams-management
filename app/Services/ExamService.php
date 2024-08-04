<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Exam;
use App\Models\Submission;
use App\Models\ExamQuestion;
use App\Services\ExamQuestionsService;

class ExamService
{
    protected $examQuestionsService;

    /**
     * ExamService constructor.
     *
     * @param ExamQuestionsService $examQuestionsService An instance of ExamQuestionsService.
     * @return void
     */
    public function __construct(ExamQuestionsService $examQuestionsService)
    {
        // Assign the instance of ExamQuestionsService to the $examQuestionsService property.
        $this->examQuestionsService = $examQuestionsService;
    }

    public function getAvailableExams()
    {
        $exams = Exam::where('end_time', '>', now())->paginate(1);
        return $exams;
    }

    public function getExamDetail($examID, $userID)
    {
        $registered = false;

        // get exam detail
        $exam = Exam::find($examID);

        // check user not registered yet
        $submission = Submission::where('user_id', $userID)->where('exam_id', $examID)->first();
        if ($submission) {
            $registered = true;
        }

        return [
            'registered' => $registered,
            'exam' => $exam
        ];
    }

    public function register($examID, $user)
    {
        // create token in redis

        // create url of exam

        // send mail to user
    }

    public function enroll($examID, $userID, $token)
    {
        // check user have permission to enroll in exam
        $prefix = env('REDIS_PREFIX', 'laravel_database_');
        $receivedToken = Redis::get("{$prefix}registration_token_{$userID}");
        if ($receivedToken != $token) {
            return [
                'canEnroll' => false,
                'examQuestions' => []
            ];
        }

        // update status of submission is VERIFIED (2)
        Submission::where('user_id', $userID)
            ->where('exam_id', $examID)
            ->update(['status' => 2]);
        
        // get exam detail
        $exam = Exam::find($examID);

        // init response data
        $canEnroll = false;
        $examQuestions = [];

        // check exam time
        if ($exam->start_time < now() && $exam->end_time > now()) {
            $canEnroll = true;
            // get exam questions from redis
            $examQuestions = $this->examQuestionsService->getExamQuestionsStructure($examID);
        }

        return [
            'canEnroll' => $canEnroll,
            'examQuestions' => $examQuestions
        ];
    }

    public function submit($examID, $userID, $submission)
    {
        Submission::where('user_id', $userID)
            ->where('exam_id', $examID)
            ->update(['submission' => $submission, 'status' => 2]);
        
        // remove redis data
        $prefix = env('REDIS_PREFIX', 'laravel_database_');
        Redis::del("{$prefix}submission_{$examID}_{$userID}");
    }

    public function submitFromRedis($examID, $userID)
    {
        $prefix = env('REDIS_PREFIX', 'laravel_database_');
        $submission = Redis::get("{$prefix}submission_{$examID}_{$userID}");
        
        Submission::where('user_id', $userID)
            ->where('exam_id', $examID)
            ->update(['submission' => $submission, 'status' => 2]);

        // remove redis data
        Redis::del("{$prefix}submission_{$examID}_{$userID}");
    }
}