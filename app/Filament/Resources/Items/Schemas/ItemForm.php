<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Models\Category;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Item Information')
                ->schema([
                    TextInput::make('name')
                        ->label('Item Name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, $set) {
                            $set('code', 'ITM-' . strtoupper(Str::slug($state)));
                        }),

                    TextInput::make('code')
                        ->label('Item Code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Select::make('category_id')
                        ->label('Category')
                        ->options(Category::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('condition')
                        ->label('Condition')
                        ->options([
                            'good'        => 'Good',
                            'damaged'     => 'Damaged',
                            'maintenance' => 'Maintenance',
                        ])
                        ->required()
                        ->default('good'),
                ])->columns(2),

            Section::make('Stock & Pricing')  // ✅ Ubah judul
                ->schema([
                    TextInput::make('stock')
                        ->label('Total Stock')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->default(0)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, $set) {
                            $set('available_stock', $state);
                        }),

                    TextInput::make('available_stock')
                        ->label('Available Stock')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->default(0),

                    // ✅ Tambahkan field daily_rental_price
                    TextInput::make('daily_rental_price')
                        ->label('Daily Rental Price (Rp)')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->default(0)
                        ->prefix('Rp')
                        ->helperText('Price per day for borrowing this item'),
                ])->columns(2),

            Section::make('Additional Details')
                ->schema([
                    Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),

                    FileUpload::make('photo')
                        ->label('Item Photo')
                        ->image()
                        ->directory('items')
                        ->nullable()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}