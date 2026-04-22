<?php

namespace App\Filament\Resources\ItemReturns\Pages;

use App\Filament\Resources\ItemReturns\ItemReturnResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItemReturns extends ListRecords
{
    protected static string $resource = ItemReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
           
        ];
    }
}
