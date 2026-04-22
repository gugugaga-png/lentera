<?php

namespace App\Filament\Resources\Borrowings\Schemas;

use App\Models\Item;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class BorrowingForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = Auth::user();
        $freeDays = 7;
        $finePerDay = 5000;

        return $schema
            ->components([
                // =========================
                // RULES INFO
                // =========================
                Placeholder::make('rules_info')
                    ->label('📋 Rental Rules')
                    ->content(function () use ($freeDays, $finePerDay) {
                        return "⚠️ **Important Information:**\n\n" .
                               "• Max borrowing period: **{$freeDays} days**\n" .
                               "• Late fine: **Rp " . number_format($finePerDay, 0, ',', '.') . " /day**\n" .
                               "• Please return on time to avoid fines!";
                    }),

                Hidden::make('user_id')
                    ->default($user->id)
                    ->required(),

                // =========================
                // ITEM SELECT (TANPA FILTER CONDITION)
                // =========================
                Select::make('item_id')
                    ->label('Item')
                    ->options(
                        Item::where('available_stock', '>', 0)
                            // ❌ Hapus filter condition 'good'
                            ->get()
                            ->mapWithKeys(fn ($item) => [
                                $item->id => $item->name .
                                    ' (Stock: ' . $item->available_stock . ') - ' .
                                    $item->getFormattedDailyPrice() . '/day'
                            ])
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $item = Item::find($state);

                        if ($item) {
                            $set('daily_price', $item->daily_rental_price);
                            $set('max_quantity', $item->available_stock);

                            if ($get('borrow_date')) {
                                $set(
                                    'estimated_return_date',
                                    Carbon::parse($get('borrow_date'))->addDays(7)
                                );
                            }

                            self::updateTotalCost($set, $get);
                        }
                    }),

                // =========================
                // QUANTITY
                // =========================
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(fn ($get) => optional(Item::find($get('item_id')))->available_stock ?? 1)
                    ->default(1)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set, $get)
                        => self::updateTotalCost($set, $get)
                    ),

                // =========================
                // BORROW DATE
                // =========================
                DatePicker::make('borrow_date')
                    ->label('Borrow Date')
                    ->default(now())
                    ->minDate(now())
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if ($state) {
                            $set(
                                'estimated_return_date',
                                Carbon::parse($state)->addDays(7)
                            );
                            self::updateTotalCost($set, $get);
                        }
                    }),

                // =========================
                // ESTIMATED RETURN DATE
                // =========================
                DatePicker::make('estimated_return_date')
                    ->label('Estimated Return Date')
                    ->required()
                    ->minDate(fn ($get) => $get('borrow_date'))
                    ->default(fn ($get) => $get('borrow_date') ? Carbon::parse($get('borrow_date'))->addDays(7) : null)
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set, $get)
                        => self::updateTotalCost($set, $get)
                    ),

                // =========================
                // RENTAL SUMMARY
                // =========================
                Placeholder::make('rental_summary')
                    ->label('💰 Rental Summary')
                    ->content(function ($get) {
                        $days = self::calculateDays($get);
                        $price = $get('daily_price') ?? 0;
                        $qty = $get('quantity') ?? 1;

                        if ($days <= 0 || $price <= 0) {
                            return 'Select item and dates to see rental summary';
                        }

                        $total = $days * $price * $qty;

                        return "📊 **Rental Summary:**\n\n" .
                               "• Daily Price: Rp " . number_format($price, 0, ',', '.') . "\n" .
                               "• Quantity: {$qty}\n" .
                               "• Rental Days: {$days} days\n" .
                               "• **Total Rental Cost: Rp " . number_format($total, 0, ',', '.') . "**";
                    }),

                // =========================
                // HIDDEN FIELDS
                // =========================
                Hidden::make('daily_price')->default(0),
                Hidden::make('total_rental_cost')->default(0),
                Hidden::make('status')->default('pending'),
            ]);
    }

    private static function calculateDays($get): int
    {
        $borrowDate = $get('borrow_date');
        $estimatedReturn = $get('estimated_return_date');

        if (!$borrowDate || !$estimatedReturn) {
            return 0;
        }

        return Carbon::parse($borrowDate)->diffInDays(Carbon::parse($estimatedReturn));
    }

    private static function updateTotalCost(callable $set, $get): void
    {
        $days = self::calculateDays($get);
        $price = $get('daily_price') ?? 0;
        $qty = $get('quantity') ?? 1;

        $total = $days * $price * $qty;
        $set('total_rental_cost', $total);
    }
}