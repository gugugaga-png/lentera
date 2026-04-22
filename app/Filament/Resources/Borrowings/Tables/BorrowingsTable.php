<?php

namespace App\Filament\Resources\Borrowings\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class BorrowingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('borrow_date')
                    ->label('Borrow Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('estimated_return_date')
                    ->label('Est. Return Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('total_rental_cost')
                    ->label('Total Cost')
                    ->money('IDR')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'returned' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}