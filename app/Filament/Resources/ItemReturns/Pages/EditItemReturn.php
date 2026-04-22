<?php

namespace App\Filament\Resources\ItemReturns\Pages;

use App\Filament\Resources\ItemReturns\ItemReturnResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditItemReturn extends EditRecord
{
    protected static string $resource = ItemReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
