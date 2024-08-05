<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use App\Services\ExamService;
use App\Services\ExamQuestionsService;

class EditExam extends EditRecord
{
    protected static string $resource = ExamResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        // set job time
        $examService = new ExamService(new ExamQuestionsService());
        $examService->setHandleExamResultJobTime($record->id, $record->end_time);
        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
