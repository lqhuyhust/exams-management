<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Question;
use App\Models\ExamQuestion;

class ExamQuestionsService
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
        $prefix = env('REDIS_PREFIX', 'laravel_database_');
        Redis::set("{$prefix}questions_structure_{$examID}", $questionsStructure);
    }

    public function getExamQuestionsStructure($examID)
    {
        $prefix = env('REDIS_PREFIX', 'laravel_database_');
        $questionsStructure = Redis::get("{$prefix}questions_structure_{$examID}");
        return $questionsStructure ? json_decode($questionsStructure) : [];
    }
}