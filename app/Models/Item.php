<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'name',
        'code',
        'category_id',
        'stock',
        'available_stock',
        'daily_rental_price', // ✅ Tambahkan ini
        'condition',
        'description',
        'photo',
    ];

    protected $casts = [
        'stock' => 'integer',
        'available_stock' => 'integer',
        'daily_rental_price' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function isAvailable(): bool
    {
        return $this->available_stock > 0 && $this->condition === 'good';
    }
    
    public function getFormattedDailyPrice(): string
    {
        return 'Rp ' . number_format($this->daily_rental_price, 0, ',', '.');
    }
}