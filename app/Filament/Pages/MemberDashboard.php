<?php

namespace App\Filament\Pages;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\ItemReturn;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use BackedEnum;

class MemberDashboard extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;
    
    // ✅ Hapus keyword 'static' - ini NON-STATIC property
    protected string $view = 'filament.pages.member-dashboard';
    
    public $items = [];
    public $borrowings = [];
    
    // Form untuk peminjaman
    public $selectedItem = null;
    public $quantity = 1;
    public $borrow_date = null;
    public $estimated_return_date = null;
    public $daily_price = 0;
    public $total_cost = 0;
    
    // Form untuk pengembalian
    public $return_borrowing_id = null;
    public $return_date = null;
    public $return_fine = 0;
    
    
    public function mount()
{
    if (!auth()->user()?->isBorrower()) {
        redirect()->to(\App\Filament\Pages\Dashboard::getUrl())->send();
        exit;
    }

    $this->loadItems();
    $this->loadBorrowings();
}

public function loadItems()
{
    $this->items = Item::where('available_stock', '>', 0)
        ->with('category')
        ->get()
        ->map(function ($item) {
            if ($item->photo) {
                $item->photo_url = \Illuminate\Support\Facades\Storage::temporaryUrl(
                    $item->photo,
                    now()->addHours(2)
                );
            } else {
                $item->photo_url = null;
            }
            return $item;
        });
}
public function loadBorrowings()
{
    $this->borrowings = Borrowing::where('user_id', Auth::id())
        ->with('item')
        ->orderBy('created_at', 'desc')
        ->get();
}
    
    public function calculateTotalCost()
    {
        if ($this->borrow_date && $this->estimated_return_date && $this->selectedItem) {
            $days = Carbon::parse($this->borrow_date)->diffInDays(Carbon::parse($this->estimated_return_date));
            $item = Item::find($this->selectedItem);
            $this->total_cost = $days * $item->daily_rental_price * $this->quantity;
        }
    }
    
    public function submitBorrowing()
    {
        $this->validate([
            'selectedItem' => 'required',
            'quantity' => 'required|integer|min:1',
            'borrow_date' => 'required|date',
            'estimated_return_date' => 'required|date|after:borrow_date',
        ]);
        
        $item = Item::find($this->selectedItem);
        
        if ($item->available_stock < $this->quantity) {
            Notification::make()
                ->title('Stock tidak cukup!')
                ->body("Stok tersedia: {$item->available_stock}")
                ->danger()
                ->send();
            return;
        }
        
        $days = Carbon::parse($this->borrow_date)->diffInDays(Carbon::parse($this->estimated_return_date));
        $totalCost = $days * $item->daily_rental_price * $this->quantity;
        
        Borrowing::create([
            'user_id' => Auth::id(),
            'item_id' => $this->selectedItem,
            'quantity' => $this->quantity,
            'borrow_date' => $this->borrow_date,
            'estimated_return_date' => $this->estimated_return_date,
            'total_rental_cost' => $totalCost,
            'status' => 'pending',
        ]);
        
        Notification::make()
            ->title('Peminjaman Diajukan!')
            ->body('Menunggu persetujuan admin/staff')
            ->success()
            ->send();
        
        $this->reset(['selectedItem', 'quantity', 'borrow_date', 'estimated_return_date', 'total_cost']);
        $this->loadBorrowings();
    }
    
    public function calculateReturnFine()
    {
        if ($this->return_borrowing_id && $this->return_date) {
            $borrowing = Borrowing::find($this->return_borrowing_id);
            $estimatedReturn = Carbon::parse($borrowing->estimated_return_date);
            $returnDate = Carbon::parse($this->return_date);
            
            if ($returnDate->gt($estimatedReturn)) {
                $daysLate = $returnDate->diffInDays($estimatedReturn);
                $this->return_fine = $daysLate * 5000;
            } else {
                $this->return_fine = 0;
            }
        }
    }
    
    public function submitReturn()
    {
        $this->validate([
            'return_borrowing_id' => 'required',
            'return_date' => 'required|date',
        ]);
        
        $borrowing = Borrowing::find($this->return_borrowing_id);
        
        if ($borrowing->status !== 'approved') {
            Notification::make()
                ->title('Tidak bisa mengembalikan!')
                ->body('Peminjaman belum disetujui')
                ->danger()
                ->send();
            return;
        }
        
        $estimatedReturn = Carbon::parse($borrowing->estimated_return_date);
        $returnDate = Carbon::parse($this->return_date);
        $fine = 0;
        
        if ($returnDate->gt($estimatedReturn)) {
            $daysLate = $returnDate->diffInDays($estimatedReturn);
            $fine = $daysLate * 5000;
        }
        
        ItemReturn::create([
            'borrowing_id' => $borrowing->id,
            'return_date' => $this->return_date,
            'fine' => $fine,
            'notes' => null,
        ]);
        
        $borrowing->update(['status' => 'returned']);
        $borrowing->item->increment('available_stock', $borrowing->quantity);
        
        Notification::make()
            ->title('Pengembalian Berhasil!')
            ->body($fine > 0 ? "Denda: Rp " . number_format($fine, 0, ',', '.') : "Tepat waktu, tidak ada denda")
            ->success()
            ->send();
        
        $this->reset(['return_borrowing_id', 'return_date', 'return_fine']);
        $this->loadBorrowings();
        $this->loadItems();
    }
    
    public static function canAccess(): bool
{
    return auth()->user()?->isBorrower() ?? false;
}
}