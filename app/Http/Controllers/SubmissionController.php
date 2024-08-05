<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubmissionService;

class SubmissionController extends Controller
{
    protected $submissionService;

    /**
     * SubmissionController constructor.
     *
     * @param SubmissionService $submissionService An instance of SubmissionService.
     * @return void
     */
    public function __construct(SubmissionService $submissionService)
    {
        // Assign the instance of SubmissionService to the $submissionService property.
        $this->submissionService = $submissionService;
    }

    public function submit(Request $request, $examID)
    {
        $this->submissionService->submit($examID, auth()->user()->id, $request->input('submission'));
    }

    public function submitFromRedis($examID, $userID)
    {
        $this->submissionService->submitFromRedis($examID, $userID);
    }

    public function storeSubmissionToRedis($examID, $userID, $submission)
    {
        $this->submissionService->storeSubmissionToRedis($examID, $userID, $submission);
    }
}
