<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use App\Models\Question;
use App\Models\ExamQuestion;
use App\Services\ExamService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Redis;

class ExamQuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'examQuestions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('question_id')
                    ->label('Question')
                    ->options(Question::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Hidden::make('exam_id')
                    ->default(123),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_id')
            ->columns([
                Tables\Columns\TextColumn::make('question_id'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (ExamQuestion $record): void {
                        try 
                        {
                            $examService = new ExamService();
                            $examService->updateExamQuestionStructure($record);
                        } catch (\Exception $e) {
                            throw $e;
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function (ExamQuestion $record): void {
                        try 
                        {
                            $examService = new ExamService();
                            $examService->updateExamQuestionStructure($record);
                        } catch (\Exception $e) {
                            throw $e;
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function (ExamQuestion $record): void {
                        try 
                        {
                            $examService = new ExamService();
                            $examService->updateExamQuestionStructure($record);
                        } catch (\Exception $e) {
                            throw $e;
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
