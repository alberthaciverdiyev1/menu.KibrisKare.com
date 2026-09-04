<?php

namespace App\Filament\Restaurant\Pages;

use App\Models\Branch;
use App\Models\Restaurant;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class QrMenuGenerator extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'QR Menü Oluşturucu';
    protected static ?string $title = 'Şubelere Özel QR Menü Oluşturucu';
    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.restaurant.pages.qr-menu-generator';

    public ?Restaurant $restaurant = null;
    public Collection $branches;

    public function mount(): void
    {
        $user = Auth::user();
        $this->restaurant = $user?->restaurant_id 
            ? Restaurant::find($user->restaurant_id) 
            : Restaurant::first();

        if ($this->restaurant) {
            $this->branches = $this->restaurant->branches()->orderBy('name')->get();
        } else {
            $this->branches = collect();
        }
    }
}
