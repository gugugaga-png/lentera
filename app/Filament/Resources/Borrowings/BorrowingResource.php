<?php

namespace App\Filament\Resources\Borrowings;

use App\Filament\Resources\Borrowings\Pages\CreateBorrowing;
use App\Filament\Resources\Borrowings\Pages\EditBorrowing;
use App\Filament\Resources\Borrowings\Pages\ListBorrowings;
use App\Filament\Resources\Borrowings\Schemas\BorrowingForm;
use App\Filament\Resources\Borrowings\Tables\BorrowingsTable;
use App\Models\Borrowing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BorrowingResource extends Resource
{
    protected static ?string $model = Borrowing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    protected static ?string $navigationLabel = 'All Borrowings';
    
    protected static ?int $navigationSort = 2;

    // Staff dan Admin bisa lihat menu ini
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isStaff());
    }

    public static function form(Schema $schema): Schema
    {
        return BorrowingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BorrowingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    // ✅ Permission sesuai soal ujian
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isStaff());
    }

    // ✅ Hanya Admin yang bisa membuat peminjaman
    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
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

    public static function getPages(): array
    {
        return [
            'index' => ListBorrowings::route('/'),
            'create' => CreateBorrowing::route('/create'),
            'edit' => EditBorrowing::route('/{record}/edit'),
        ];
    }
}