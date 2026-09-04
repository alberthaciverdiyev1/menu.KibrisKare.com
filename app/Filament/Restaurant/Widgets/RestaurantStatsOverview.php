<?php

namespace App\Filament\Restaurant\Widgets;

use App\Models\Branch;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class RestaurantStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $restaurantId = $user?->restaurant_id;

        $restaurant = $restaurantId ? Restaurant::find($restaurantId) : Restaurant::first();

        if (!$restaurant) {
            return [];
        }

        $itemsCount = MenuItem::where('restaurant_id', $restaurant->id)->count();
        $categoriesCount = MenuCategory::where('restaurant_id', $restaurant->id)->count();
        $branchesCount = Branch::where('restaurant_id', $restaurant->id)->count();

        $isOpen = $restaurant->isOpenNow();

        return [
            Stat::make('Canlı Durum', $isOpen ? 'Şu Anda Açık ●' : 'Şu Anda Kapalı ○')
                ->description('Bugün: ' . $restaurant->getTodayHours())
                ->color($isOpen ? 'success' : 'danger'),

            Stat::make('Kayıtlı Menü Ürünleri', $itemsCount . ' Çeşit')
                ->description($categoriesCount . ' Kategori altında')
                ->icon('heroicon-o-book-open')
                ->color('primary'),

            Stat::make('Toplam Şube', $branchesCount . ' Lokasyon')
                ->description('Aktif şubeleriniz')
                ->icon('heroicon-o-map-pin')
                ->color('warning'),

            Stat::make('Müşteri Puanı', $restaurant->rating . ' / 5.0')
                ->description($restaurant->reviews_count . ' Değerlendirme')
                ->icon('heroicon-o-star')
                ->color('amber'),
        ];
    }
}
