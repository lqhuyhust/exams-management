<?php

namespace App\Filament\Resources\PrizeRecordResource\Pages;

use App\Filament\Resources\PrizeRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrizeRecord extends EditRecord
{
    protected static string $resource = PrizeRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
