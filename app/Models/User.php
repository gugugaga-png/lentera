<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Role checks
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isBorrower(): bool
    {
        return $this->role === 'borrower';
    }

    // Permission methods berdasarkan dokumen ujian
    public function canManageUsers(): bool { return $this->isAdmin(); }
    public function canManageItems(): bool { return $this->isAdmin() || $this->isStaff(); }
    public function canManageCategories(): bool { return $this->isAdmin() || $this->isStaff(); }
    public function canManageBorrowings(): bool { return $this->isAdmin() || $this->isStaff(); }
    public function canApproveBorrowings(): bool { return $this->isAdmin() || $this->isStaff(); }
    public function canViewActivityLogs(): bool { return $this->isAdmin(); }
    public function canPrintReports(): bool { return $this->isAdmin() || $this->isStaff(); }
    public function canViewItems(): bool { return true; }
    public function canSubmitBorrowing(): bool { return true; }
    public function canReturnItem(): bool { return true; }

    // Relationships
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
    
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}