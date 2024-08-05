<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Exam;
use App\Models\Submission;
use App\Models\Choice;
use App\Models\ExamQuestion;
use App\Models\Prize;
use App\Models\PrizeRecord;
use App\Models\User;
use App\Services\ExamQuestionsService;
use App\Mail\PrizeNotificationMail;
use App\Mail\ExamRegistrationMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Jobs\HandleExamResultJob;

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
        $examURL = '';
        $message = '';

        // get exam detail
        $exam = Exam::find($examID);

        // check user not registered yet
        $submission = Submission::where('user_id', $userID)->where('exam_id', $examID)->first();
        if ($submission) {
            $registered = true;

            if ($submission->status == 0) {
                $message = 'Check your mail to verify your exam registration';
            } 

            // Case 1: exam not started yet
            if ($submission->status == 1) {
                $prefix = env('REDIS_PREFIX', 'laravel_database_');
                $token = Redis::get("{$prefix}registration_token_{$examID}_{$userID}");
                $examURL = env('APP_URL') . "/exam/{$examID}?token={$token}";
            } 

            // Case 2: exam submitted and not published
            if ($submission->status == 2 && !$submission->score) {
                $message = 'Your submission has been submitted. Thank you for participating in our exam. Exam result will be published soon.';
            }

            // Case 3: exam submitted and published
            if ($submission->status == 2 && $submission->score) {
                // get exam url
                $message = 'Thank you for participating in our exam. This is your exam result: ' . $submission->score;
            }
        }

        return [
            'registered' => $registered,
            'exam' => $exam,
            'examURL' => $examURL,
            'message' => $message,
        ];
    }

    public function register($examID, $user)
    {
        try
        {
            // create submission if not exists
            $submission = Submission::firstOrCreate([
                'user_id' => $user->id,
                'exam_id' => $examID,
                'submission' => '',
            ]);

            // get exam detail
            $exam = Exam::find($examID);
            // create token in redis
            $token = Str::random(32);
            $prefix = env('REDIS_PREFIX', 'laravel_database_');
            Redis::set("{$prefix}registration_token_{$examID}_{$user->id}", $token);
            // send mail to user
            $mail = Mail::to($user->email)->queue(new ExamRegistrationMail($exam, $token));
            return $mail;
        }
        catch (\Exception $e)
        {
            return $e;
        }
    }

    public function enroll($examID, $userID, $token)
    {
        // get exam detail
        $exam = Exam::find($examID);

        // check user have permission to enroll in exam
        $prefix = env('REDIS_PREFIX', 'laravel_database_');
        $receivedToken = Redis::get("{$prefix}registration_token_{$examID}_{$userID}");
        if ($receivedToken != $token) {
            return [
                'canEnroll' => false,
                'examQuestions' => [],
                'examName' => $exam->name,
            ];
        }

        // update status of submission is VERIFIED (1)
        $submission = Submission::where('user_id', $userID)
            ->where('exam_id', $examID)
            ->update(['status' => 1]);
        

        // init response data
        $canEnroll = false;
        $examQuestions = [];

        // check exam time
        if ($exam->start_time < now() && $exam->end_time > now() && $submission->status == 1) {
            $canEnroll = true;
            // get exam questions from redis
            $examQuestionsData = $this->examQuestionsService->getExamQuestionsStructure($examID);
            $examQuestions = json_encode($examQuestionsData, true);
        }

        return [
            'canEnroll' => $canEnroll,
            'examQuestions' => $examQuestions,
            'exam' => $exam,
        ];
    }

    public function submit($examID, $userID, $submission)
    {
        try
        {
            Submission::where('user_id', $userID)
                ->where('exam_id', $examID)
                ->update(['submission' => $submission, 'status' => 2]);
            
            // remove redis data
            $prefix = env('REDIS_PREFIX', 'laravel_database_');
            Redis::del("{$prefix}submission_{$examID}_{$userID}");
            Redis::del("{$prefix}registration_token_{$examID}_{$userID}");

            return [
                'success' => true,
                'message' => 'Exam submitted successfully',
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function storeSubmissionToRedis($examID, $userID, $submission)
    {
        try
        {
            // store exam questions structure to redis
            $prefix = env('REDIS_PREFIX', 'laravel_database_');
            Redis::set("{$prefix}submission_{$examID}_{$userID}", $submission);
            return [
                'success' => true,
                'message' => 'Submission stored successfully',
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
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

    public function submitAutomatically($examID)
    {
        $unsubmittedList = Submission::where('exam_id', $examID)->where('status', 1)->get();
        foreach ($unsubmittedList as $submission) {
            $this->submitFromRedis($examID, $submission->user_id);
        }
    }

    public function handleExamResult($examID)
    {
        $unpublishedList = Submission::where('exam_id', $examID)->where('status', 2)->get();
        foreach ($unpublishedList as $submission) {
            $result = json_decode($submission->score, true);

            $correct = 0;
            foreach ($result as $key => $value) {
                $questionID = explode('-', $key)[1];

                // get answer of question $questionID
                $answers = Choice::where('question_id', $questionID)->where('is_correct', 1)->pluck('id')->toArray();
                
                if (empty(array_diff($answers, $value)) && empty(array_diff($value, $answers))) {
                    $correct += 1;
                }
            }

            // update score
            $submission->score = $correct;
            $submission->save();
        }
    }

    public function setPrizes($examID)
    {
        // get exam result
        $result = Submission::where('exam_id', $examID)->orderBy('score', 'desc')->get();

        // get prizes of the exam
        $prizes = Prize::where('exam_id', $examID)->orderBy('priority', 'asc')->get();
        $offset = 0;

        foreach ($prizes as $prize) {
            for ($i = 0; $i < $prize->quantity; $i++) {
                // create prize record
                PrizeRecord::create([
                    'user_id' => $result[$offset]->user_id,
                    'prize_id' => $prize->id,
                ]);

                $offset += 1;

                // send mail to user
                $user = User::find($result[$offset]->user_id);
                Mail::to($user->email)->queue(new PrizeNotificationMail($examID, $prize, $user->email));

                if ($offset >= count($result)) {
                    break;
                }
            }
        }
    }

    public function removeExamFromRedis($examID)
    {
        $prefix = env('REDIS_PREFIX', 'laravel_database_');
        Redis::del("{$prefix}submission_{$examID}_*");
        Redis::del("{$prefix}registration_token_{$examID}_*");
        Redis::del("{$prefix}questions_structure_{$examID}");
    }

    public function setHandleExamResultJobTime($examID, $endTime)
    {
        $endTime = Carbon::parse($endTime);
        $now = Carbon::now();

        if ($endTime->isFuture()) {
            $delay = $endTime->diffInSeconds($now);

            // Đưa job vào hàng đợi với thời gian trì hoãn
            HandleExamResultJob::dispatch($examID)->delay($delay);
        }
    }
}