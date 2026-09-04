<?php

namespace App\Filament\Restaurant\Pages;

use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ManageRestaurantProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Restoran Detayları';
    protected static ?string $title = 'Restoran Bilgilerini Düzenle';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.restaurant.pages.manage-restaurant-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $restaurant = $this->getRestaurant();

        if ($restaurant) {
            $this->form->fill($restaurant->attributesToArray());
        }
    }

    protected function getRestaurant(): ?Restaurant
    {
        $user = Auth::user();
        if ($user && $user->restaurant_id) {
            return Restaurant::find($user->restaurant_id);
        }

        return Restaurant::first();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Genel Bilgiler')
                    ->description('Restoranınızın misafirlere görünen temel kimliği')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Restoran Adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cuisine')
                            ->label('Mutfak / Konsept')
                            ->placeholder('Örn: Kıbrıs Mutfağı, Kebap & Steak')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Hakkında / Açıklama')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('city_id')
                            ->label('Şehir')
                            ->relationship('city', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('address')
                            ->label('Açık Adres')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon Numarası')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Çalışma ve Sipariş Ayarları')
                    ->schema([
                        Forms\Components\TextInput::make('opening_hours')
                            ->label('Çalışma Saatleri')
                            ->placeholder('11:00 - 23:00')
                            ->required(),
                        Forms\Components\TextInput::make('price_range')
                            ->label('Fiyat Seviyesi')
                            ->default('₺₺'),
                        Forms\Components\TextInput::make('min_order')
                            ->label('Minimum Sipariş Tutarı')
                            ->default('200 ₺'),
                        Forms\Components\Toggle::make('is_open')
                            ->label('Şu Anda Açık mı? (Canlı Durum)')
                            ->helperText('Kapalı konuma getirirseniz ziyaretçilere kapalı olarak görünür.')
                            ->default(true),
                        Forms\Components\Toggle::make('has_delivery')
                            ->label('Paket Servis Var mı?')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Görseller')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Restoran Profil / Kapak Görseli')
                            ->image()
                            ->directory('restaurants'),
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Geniş Arka Plan Görseli')
                            ->image()
                            ->directory('restaurants'),
                    ])->columns(2),

                Forms\Components\Section::make('Harita ve Koordinatlar')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Enlem (Latitude)')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Boylam (Longitude)')
                            ->numeric()
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $restaurant = $this->getRestaurant();

        if ($restaurant) {
            $restaurant->update($data);

            Notification::make()
                ->title('Restoran bilgileri başarıyla güncellendi!')
                ->success()
                ->send();
        }
    }
}
