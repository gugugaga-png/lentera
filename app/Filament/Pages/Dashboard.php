<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function canAccess(): bool
    {
        return !auth()->user()?->isBorrower();
    }

    public function mount(): void
    {
        if (auth()->user()?->isBorrower()) {
            redirect()->to(MemberDashboard::getUrl())->send();
            exit;
        }

        parent::mount();
    }
}