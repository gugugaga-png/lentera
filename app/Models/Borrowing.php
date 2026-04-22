<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Borrowing extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
        'borrow_date',
        'estimated_return_date',
        'total_rental_cost',  // ✅ Pastikan ada
        'status',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'estimated_return_date' => 'date',
        'quantity' => 'integer',
        'total_rental_cost' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
    
    public function returnRecord()
    {
        return $this->hasOne(ItemReturn::class, 'borrowing_id');
    }
    
    // Hitung total rental cost
    public function calculateTotalRentalCost(): int
    {
        if (!$this->estimated_return_date) {
            return 0;
        }
        
        $borrowDate = Carbon::parse($this->borrow_date);
        $estimatedReturn = Carbon::parse($this->estimated_return_date);
        $days = $borrowDate->diffInDays($estimatedReturn);
        
        return $days * $this->item->daily_rental_price * $this->quantity;
    }
    
    // Hitung denda keterlambatan
    public function calculateLateFine($actualReturnDate = null): int
    {
        $actualReturnDate = $actualReturnDate ?? now();
        $actualReturnDate = Carbon::parse($actualReturnDate);
        $estimatedReturn = Carbon::parse($this->estimated_return_date);
        
        if ($actualReturnDate->lte($estimatedReturn)) {
            return 0;
        }
        
        $daysLate = $actualReturnDate->diffInDays($estimatedReturn);
        $finePerDay = 5000;
        
        return $daysLate * $finePerDay;
    }
    
    // Hitung total biaya (rental + denda)
    public function calculateTotalCost($actualReturnDate = null): int
    {
        $rentalCost = $this->total_rental_cost;
        $lateFine = $this->calculateLateFine($actualReturnDate);
        
        return $rentalCost + $lateFine;
    }
    
    // Format total rental cost
    public function getFormattedTotalRentalCost(): string
    {
        return 'Rp ' . number_format($this->total_rental_cost, 0, ',', '.');
    }
    
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'returned' => 'info',
            default => 'gray',
        };
    }
}