<?php

namespace App\Filament\Resources\Borrowings\Pages;

use App\Filament\Resources\Borrowings\BorrowingResource;
use App\Filament\Resources\Borrowings\Tables\BorrowingsTable;
use App\Models\Borrowing;
use App\Models\ItemReturn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Carbon;

class ListBorrowings extends ListRecords
{
    protected static string $resource = BorrowingResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => auth()->user()?->role === 'admin'),
        ];
    }
    
    public function table(Table $table): Table
    {
        $table = BorrowingsTable::configure($table);
        
        return $table
            ->actions([
                // Tombol Approve
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function (Borrowing $record) {
                        $item = $record->item;
                        
                        if ($item->available_stock >= $record->quantity) {
                            $record->update(['status' => 'approved']);
                            $item->decrement('available_stock', $record->quantity);
                            
                            Notification::make()
                                ->title('Approved!')
                                ->body("Stock remaining: {$item->fresh()->available_stock}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Stock tidak cukup!')
                                ->danger()
                                ->send();
                        }
                    }),
                    
                // Tombol Reject
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function (Borrowing $record) {
                        $record->update(['status' => 'rejected']);
                        
                        Notification::make()
                            ->title('Rejected!')
                            ->warning()
                            ->send();
                    }),
                    
                // Tombol Return dengan informasi lengkap
                Action::make('return')
                    ->label('Return')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->form(function (Borrowing $record) {
                        return [
                            // Informasi Rental
                            Placeholder::make('rental_info')
                                ->label('💰 Rental Information')
                                ->content(function () use ($record) {
                                    $days = Carbon::parse($record->borrow_date)->diffInDays($record->estimated_return_date);
                                    return "**Rental Details:**\n" .
                                           "• Item: {$record->item->name}\n" .
                                           "• Daily Price: " . $record->item->getFormattedDailyPrice() . "\n" .
                                           "• Quantity: {$record->quantity}\n" .
                                           "• Rental Days: {$days} days\n" .
                                           "• Total Rental Cost: Rp " . number_format($record->total_rental_cost, 0, ',', '.') . "\n\n" .
                                           "**Return Schedule:**\n" .
                                           "• Borrow Date: " . Carbon::parse($record->borrow_date)->format('d M Y') . "\n" .
                                           "• Estimated Return: " . Carbon::parse($record->estimated_return_date)->format('d M Y') . "\n" .
                                           "• **Late Fine: Rp 5,000/day after estimated return date**";
                                }),
                            
                            DatePicker::make('return_date')
                                ->label('Return Date')
                                ->required()
                                ->default(now())
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) use ($record) {
                                    $fine = $this->calculateFine($record, $state);
                                    $set('fine', $fine);
                                    $set('fine_display', $this->formatFineDisplay($fine));
                                }),
                            
                            Placeholder::make('late_calculation')
                                ->label('⚠️ Late Fine Calculation')
                                ->content(function ($get) use ($record) {
                                    $returnDate = $get('return_date') ?? now();
                                    $estimatedReturn = Carbon::parse($record->estimated_return_date);
                                    $returnDateParsed = Carbon::parse($returnDate);
                                    
                                    if ($returnDateParsed->lte($estimatedReturn)) {
                                        return "✅ **No Fine** - You returned on time!";
                                    }
                                    
                                    $daysLate = $returnDateParsed->diffInDays($estimatedReturn);
                                    $fine = $daysLate * 5000;
                                    
                                    return "⚠️ **Late Fine: Rp " . number_format($fine, 0, ',', '.') . "**\n\n" .
                                           "• Estimated Return: " . $estimatedReturn->format('d M Y') . "\n" .
                                           "• Actual Return: " . $returnDateParsed->format('d M Y') . "\n" .
                                           "• Days Late: {$daysLate} days\n" .
                                           "• Fine per Day: Rp 5,000";
                                }),
                            
                            Hidden::make('fine')
                                ->default($this->calculateFine($record, now())),
                            
                            TextInput::make('fine_display')
                                ->label('Fine Amount')
                                ->readOnly()
                                ->default($this->formatFineDisplay($this->calculateFine($record, now()))),
                            
                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(2)
                                ->placeholder('Damage notes or other information (optional)'),
                        ];
                    })
                    ->action(function (Borrowing $record, array $data) {
                        $item = $record->item;
                        $returnDate = $data['return_date'];
                        $fine = $data['fine'];
                        
                        ItemReturn::create([
                            'borrowing_id' => $record->id,
                            'return_date' => $returnDate,
                            'fine' => $fine,
                            'notes' => $data['notes'] ?? null,
                        ]);
                        
                        $record->update(['status' => 'returned']);
                        $item->increment('available_stock', $record->quantity);
                        
                        $totalCost = $record->total_rental_cost + $fine;
                        
                        Notification::make()
                            ->title('✅ Item Returned!')
                            ->body("Total Cost: Rental Rp " . number_format($record->total_rental_cost, 0, ',', '.') . " + Fine Rp " . number_format($fine, 0, ',', '.') . " = Rp " . number_format($totalCost, 0, ',', '.'))
                            ->success()
                            ->send();
                    }),
                    
                // Tombol Edit - Hanya Admin dan status belum returned
                EditAction::make()
                    ->visible(fn ($record) =>
                        auth()->user()?->role === 'admin' && $record->status !== 'returned'
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->role === 'admin'),
                ]),
            ]);
    }
    
    private function calculateFine(Borrowing $record, $returnDate): int
    {
        $estimatedReturn = Carbon::parse($record->estimated_return_date);
        $returnDate = Carbon::parse($returnDate);
        
        if ($returnDate->lte($estimatedReturn)) {
            return 0;
        }
        
        $daysLate = $returnDate->diffInDays($estimatedReturn);
        $finePerDay = 5000;
        
        return $daysLate * $finePerDay;
    }
    
    private function formatFineDisplay(int $fine): string
    {
        if ($fine <= 0) {
            return 'Rp 0 (No fine)';
        }
        
        return 'Rp ' . number_format($fine, 0, ',', '.');
    }
}