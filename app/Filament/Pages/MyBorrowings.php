<?php

namespace App\Filament\Pages;

use App\Models\Borrowing;
use App\Models\ItemReturn;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use BackedEnum;

class MyBorrowings extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'My Borrowings';
    protected static ?string $title = 'My Borrowings';
    protected static ?int $navigationSort = 1;
    
    protected string $view = 'filament.pages.my-borrowings';
    
    public static function canAccess(): bool
    {
        return auth()->user()?->isBorrower() ?? false;
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Borrowing::where('user_id', Auth::id())
                    ->with('item')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('item.code')
                    ->label('Item Code')
                    ->searchable(),
                    
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                    
                TextColumn::make('borrow_date')
                    ->label('Borrow Date')
                    ->date('d M Y')
                    ->sortable(),
                    
                TextColumn::make('estimated_return_date')
                    ->label('Due Date')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => 
                        $record->status === 'approved' && 
                        Carbon::parse($record->estimated_return_date)->isPast() 
                            ? 'danger' 
                            : 'gray'
                    ),
                    
                TextColumn::make('total_rental_cost')
                    ->label('Total Cost')
                    ->money('IDR')
                    ->sortable(),
                    
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
                    ->label('Requested')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('return')
                    ->label('Return')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->form([
                        DatePicker::make('return_date')
                            ->label('Return Date')
                            ->required()
                            ->default(now()),
                    ])
                    ->action(function (Borrowing $record, array $data) {
                        $returnDate = Carbon::parse($data['return_date']);
                        $dueDate = Carbon::parse($record->estimated_return_date);
                        
                        // Hitung denda
                        $fine = 0;
                        if ($returnDate->gt($dueDate)) {
                            $daysLate = $returnDate->diffInDays($dueDate);
                            $fine = $daysLate * 5000;
                        }
                        
                        // Buat record return
                        ItemReturn::create([
                            'borrowing_id' => $record->id,
                            'return_date' => $data['return_date'],
                            'fine' => $fine,
                            'notes' => null,
                        ]);
                        
                        // Update status
                        $record->update(['status' => 'returned']);
                        
                        // Kembalikan stok
                        $record->item->increment('available_stock', $record->quantity);
                        
                        $fineText = $fine > 0 ? 'Fine: Rp ' . number_format($fine, 0, ',', '.') : 'No fine';
                        
                        Notification::make()
                            ->title('Item Returned Successfully!')
                            ->body($fineText)
                            ->success()
                            ->send();
                            
                        // Refresh table
                        $this->resetTable();
                    }),
                    
                Action::make('view')
                    ->label('Detail')
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Borrowing Details')
                    ->modalContent(function (Borrowing $record) {
                        return view('filament.modals.borrowing-detail', ['record' => $record]);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}