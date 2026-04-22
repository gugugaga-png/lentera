<?php

namespace App\Filament\Resources\ItemReturns;

use App\Filament\Resources\ItemReturns\Pages\EditItemReturn;
use App\Filament\Resources\ItemReturns\Pages\ListItemReturns;
use App\Filament\Resources\ItemReturns\Schemas\ItemReturnForm;
use App\Filament\Resources\ItemReturns\Tables\ItemReturnsTable;
use App\Models\ItemReturn;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ItemReturnResource extends Resource
{
    protected static ?string $model = ItemReturn::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';
    
    protected static ?string $navigationLabel = 'Item Returns';
    
    protected static ?int $navigationSort = 6;

    // ✅ Staff dan Admin bisa lihat menu ini
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isStaff());
    }

    // ✅ Hilangkan tombol CREATE (tidak boleh buat manual)
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canCreateAnother(): bool
    {
        return false;
    }

    // ✅ Staff dan Admin bisa view
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isStaff());
    }

    // ✅ Staff dan Admin bisa view detail
    public static function canView($record): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isStaff());
    }

    // ✅ Hanya Admin yang bisa edit
    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    // ✅ Hanya Admin yang bisa delete
    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return ItemReturnForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemReturnsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItemReturns::route('/'),
            'edit' => EditItemReturn::route('/{record}/edit'),
        ];
    }
}