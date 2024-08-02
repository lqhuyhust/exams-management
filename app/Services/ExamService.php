<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Question;
use App\Models\ExamQuestion;

class ExamService
{
    public function updateExamQuestionStructure(ExamQuestion $record)
    {
        // get question list based on exam id 
        $examID = $record->exam_id;

        $questions = Question::whereIn('id', function ($query) use ($examID) {
            $query->select('question_id')
                ->from('exam_questions')
                ->where('exam_id', $examID);
        })->get();

        // init exam questions structure
        $questionsStructure = [];

        foreach ($questions as $question) {
            $choices = [];
            foreach ($question->choices as $choice) {
                $choices[] = $choice->name;
            }
            $questionsStructure[] = [
                'question' => $question->name,
                'choices' => $choices
            ];
        }

        // store exam questions structure to redis
        Redis::set("questions_structure_{$examID}", $questionsStructure);
    }
}