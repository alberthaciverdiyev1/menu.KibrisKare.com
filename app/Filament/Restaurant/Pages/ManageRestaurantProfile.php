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
            $formData = $restaurant->attributesToArray();
            $formData['categories'] = $restaurant->categories()->pluck('categories.id')->toArray();
            $this->form->fill($formData);
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
                        Forms\Components\Select::make('categories')
                            ->label('Restoran Kategorileri')
                            ->helperText('Yönetici (Admin) tarafından tanımlanmış kategorilerden restoranınıza uygun olanları seçebilirsiniz.')
                            ->multiple()
                            ->relationship('categories', 'name')
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Hakkında / Açıklama')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon Numarası')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('has_delivery')
                            ->label('Paket Servis Mevcut mu?')
                            ->default(true),
                    ])->columns(2),

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
            ])
            ->model($this->getRestaurant())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        $restaurant = $this->getRestaurant();

        if ($restaurant) {
            $restaurant->update($data);
            $restaurant->categories()->sync($categories);

            Notification::make()
                ->title('Restoran bilgileri başarıyla güncellendi!')
                ->success()
                ->send();
        }
    }
}
