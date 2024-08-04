<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExamQuestionsService;
use App\Services\ExamService;

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

    public function show($examID)
    {
        $examDetail = $this->examService->getExamDetail($examID, auth()->user()->id);

        return view('exam')->with([
            'registered' => $examDetail['registered'],
            'exam' => $examDetail['exam'],
        ]);
    }

    public function register($examID)
    {
        // send Email to user
        $examDetail = $this->examService->register($examID, auth()->user());

        // redirect to exam page
    }

    public function enroll(Request $request, $examID)
    {
        // Get query parameter 'token'
        $token = $request->query('token');

        $enrollData = $this->examService->enroll($examID, auth()->user()->id, $token);
    }

    public function submit(Request $request, $examID)
    {
        $this->examService->submit($examID, auth()->user()->id, $request->input('submission'));
    }
}
