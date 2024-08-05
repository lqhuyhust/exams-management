<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamQuestion;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Redis;
use App\Services\ExamService;
use App\Services\ExamQuestionsService;

class CreateExam extends CreateRecord
{
    protected static string $resource = ExamResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try 
        {
            $questionsGenerate = $data['questions_generate'];
            unset($data['questions_generate']);
            $createdExam = Exam::create($data);

            // init exam questions structure
            $questionsStructure = [];

            if (isset($questionsGenerate))
            {
                // get question list of `$data['questions_count']` records randomly 
                $questions = Question::inRandomOrder()->limit($data['questions_count'])->with('choices')->get();

                // create exam questions
                $exanQuestionsData = [];
                foreach ($questions as $question) {
                    $exanQuestionsData[] = [
                        'exam_id' => $createdExam->id,
                        'question_id' => $question->id,
                    ];
                }
                ExamQuestion::insert($exanQuestionsData);

                // generate exam questions structure
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
            }

            // store exam questions structure to redis
            Redis::set("questions_structure_{$createdExam->id}", $questionsStructure);

            // set job time
            $examService = new ExamService(new ExamQuestionsService());
            $examService->setHandleExamResultJobTime($createdExam->id, $createdExam->end_time);
            return $createdExam;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
