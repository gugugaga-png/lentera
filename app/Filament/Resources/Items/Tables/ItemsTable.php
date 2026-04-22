<?php

namespace App\Filament\Resources\Items\Tables;

use App\Models\Category;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=ALT&background=random'),

                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('name')
                    ->label('Nama Alat')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                // ✅ Tambahkan kolom daily_rental_price
                TextColumn::make('daily_rental_price')
                    ->label('Harga/Hari')
                    ->money('IDR')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('stock')
                    ->label('Total Stok')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('available_stock')
                    ->label('Tersedia')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($record) => $record->available_stock > 0 ? 'success' : 'danger'),

                TextColumn::make('condition')
                    ->label('Kondisi')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'good'        => 'Baik',
                        'damaged'     => 'Rusak',
                        'maintenance' => 'Perbaikan',
                        default       => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'good'        => 'success',
                        'damaged'     => 'danger',
                        'maintenance' => 'warning',
                        default       => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'good'        => 'Baik',
                        'damaged'     => 'Rusak',
                        'maintenance' => 'Perbaikan',
                    ]),

                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(Category::all()->pluck('name', 'id')),
            ]);
    }
}