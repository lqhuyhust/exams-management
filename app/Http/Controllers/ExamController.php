<?php

namespace App\Http\Controllers;

use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use App\Services\ExamQuestionsService;
use App\Services\ExamService;
use Carbon\Carbon;

class ExamController extends Controller
{
    protected $examService;

    /**
     * ExamController constructor.
     *
     * @param ExamService $examService An instance of ExamService.
     * @return void
     */
    public function __construct(ExamService $examService)
    {
        // Assign the instance of ExamService to the $examService property.
        $this->examService = $examService;
    }

    public function index()
    {
        $exams = $this->examService->getAvailableExams();
        return view('exams')->with(['exams' => $exams]);
    }

    public function show(Request $request, $examID)
    {
        // Get query parameter 'token'
        $token = $request->query('token');

        if ($token) {
            $examDetail = $this->examService->enroll($examID, auth()->user()->id, $token);

            // calculate exam duration
            $now = Carbon::now();
            $endTime = Carbon::parse($examDetail['exam']->end_time);
            $remainTime = $endTime->diffInSeconds($now);
            return view('questions')->with([
                'canEnroll' => $examDetail['canEnroll'],
                'examQuestions' => $examDetail['examQuestions'],
                'exam' => $examDetail['exam'],
                'remainTime' => $remainTime,
            ]);
        } else {
            $examDetail = $this->examService->getExamDetail($examID, auth()->user()->id);

            return view('exam')->with([
                'registered' => $examDetail['registered'],
                'exam' => $examDetail['exam'],
                'examURL' => $examDetail['examURL'],
                'message' => $examDetail['message'],
            ]);
        }
    }

    public function register($examID)
    {
        // send Email to user
        $examDetail = $this->examService->register($examID, auth()->user());

        // redirect to exam page
        return redirect(route('exams.show', $examID));
    }

    public function submit(Request $request, $examID)
    {
        $submission = $request->input('submission');
        $userID = auth()->user()->id;
        $submittedSubmission = $this->examService->submit($examID, $userID, $submission);
        return $submittedSubmission;
    }

    public function submitFromRedis($examID, $userID)
    {
        $this->examService->submitFromRedis($examID, $userID);
    }

    public function storeSubmissionToRedis(Request $request, $examID)
    {
        $submission = $request->input('submission');
        $userID = auth()->user()->id;
        $storedSubmission = $this->examService->storeSubmissionToRedis($examID, $userID, $submission);
        return $storedSubmission;
    }
}
