<?php

namespace App\Filament\Resources\ItemReturns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemReturnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('borrowing.user.name')
                    ->label('Borrower')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('borrowing.item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('return_date')
                    ->label('Return Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('fine')
                    ->label('Fine (Rp)')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                // ✅ Hanya Admin yang bisa edit
                EditAction::make()
                    ->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}