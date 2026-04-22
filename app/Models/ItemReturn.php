<?php

namespace App\Models;

use App\Traits\LogsActivity; // Tambahkan untuk activity log
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan untuk type hinting

class ItemReturn extends Model
{
    use LogsActivity; // Tambahkan ini agar aktivitas tercatat
    
    protected $table = 'returns';

    protected $fillable = [
        'borrowing_id',
        'return_date',
        'fine',
        'notes', // Jangan lupa tambahkan notes jika ada di migration
    ];
    
    protected $casts = [
        'return_date' => 'date',
        'fine' => 'integer',
    ];

    public function borrowing(): BelongsTo // Tambahkan : BelongsTo untuk type hinting
    {
        return $this->belongsTo(Borrowing::class);
    }
}