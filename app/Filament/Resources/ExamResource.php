<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Filament\Resources\ExamResource\RelationManagers;
use App\Models\Exam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description'),
                Forms\Components\DateTimePicker::make('start_time')
                    ->label('Start Time')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('End Time')
                    ->required(),
                Forms\Components\TextInput::make('questions_count')
                        ->label('Questions Count')
                        ->integer()
                        ->required(),
                Forms\Components\Toggle::make('questions_generate')
                    ->label('Questions Generate Automatically')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Questions Count'),
                Tables\Columns\TextColumn::make('start_time')
                    ->sortable()
                    ->label('Start Time'),
                Tables\Columns\TextColumn::make('end_time')
                    ->sortable()
                    ->label('End Time'),
                Tables\Columns\TextColumn::make('is_handled')
                    ->label('Is Handled'),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_handled')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ExamResource\RelationManagers\ExamQuestionsRelationManager::class,
            ExamResource\RelationManagers\PrizeRecordsRelationManager::class,
            ExamResource\RelationManagers\PrizesRelationManager::class,
            ExamResource\RelationManagers\SubmissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
