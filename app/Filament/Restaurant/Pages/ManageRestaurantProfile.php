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

            if (empty($formData['weekly_hours'])) {
                $defaultSchedule = [];
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                foreach ($days as $day) {
                    $defaultSchedule[$day] = [
                        'open' => in_array($day, ['friday', 'saturday']) ? '10:00' : ($day === 'sunday' ? '11:00' : '10:00'),
                        'close' => in_array($day, ['friday', 'saturday']) ? '23:30' : '23:00',
                        'is_closed' => false,
                    ];
                }
                $formData['weekly_hours'] = $defaultSchedule;
            }

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
                        Forms\Components\Textarea::make('description')
                            ->label('Hakkında / Açıklama')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('city_id')
                            ->label('Şehir')
                            ->options(\App\Models\City::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
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

                Forms\Components\Section::make('Haftalık Çalışma Saatleri (7 Gün)')
                    ->description('Restoranın açık veya kapalı durumu girdiğiniz bu saatlere ve güncel saate göre otomatik belirlenir.')
                    ->schema([
                        Forms\Components\Fieldset::make('Pazartesi')
                            ->schema([
                                Forms\Components\TimePicker::make('weekly_hours.monday.open')->label('Açılış')->default('10:00')->seconds(false),
                                Forms\Components\TimePicker::make('weekly_hours.monday.close')->label('Kapanış')->default('23:00')->seconds(false),
                                Forms\Components\Toggle::make('weekly_hours.monday.is_closed')->label('Kapalı')->inline(false),
                            ])->columns(3),

                        Forms\Components\Fieldset::make('Salı')
                            ->schema([
                                Forms\Components\TimePicker::make('weekly_hours.tuesday.open')->label('Açılış')->default('10:00')->seconds(false),
                                Forms\Components\TimePicker::make('weekly_hours.tuesday.close')->label('Kapanış')->default('23:00')->seconds(false),
                                Forms\Components\Toggle::make('weekly_hours.tuesday.is_closed')->label('Kapalı')->inline(false),
                            ])->columns(3),

                        Forms\Components\Fieldset::make('Çarşamba')
                            ->schema([
                                Forms\Components\TimePicker::make('weekly_hours.wednesday.open')->label('Açılış')->default('10:00')->seconds(false),
                                Forms\Components\TimePicker::make('weekly_hours.wednesday.close')->label('Kapanış')->default('23:00')->seconds(false),
                                Forms\Components\Toggle::make('weekly_hours.wednesday.is_closed')->label('Kapalı')->inline(false),
                            ])->columns(3),

                        Forms\Components\Fieldset::make('Perşembe')
                            ->schema([
                                Forms\Components\TimePicker::make('weekly_hours.thursday.open')->label('Açılış')->default('10:00')->seconds(false),
                                Forms\Components\TimePicker::make('weekly_hours.thursday.close')->label('Kapanış')->default('23:00')->seconds(false),
                                Forms\Components\Toggle::make('weekly_hours.thursday.is_closed')->label('Kapalı')->inline(false),
                            ])->columns(3),

                        Forms\Components\Fieldset::make('Cuma')
                            ->schema([
                                Forms\Components\TimePicker::make('weekly_hours.friday.open')->label('Açılış')->default('10:00')->seconds(false),
                                Forms\Components\TimePicker::make('weekly_hours.friday.close')->label('Kapanış')->default('23:30')->seconds(false),
                                Forms\Components\Toggle::make('weekly_hours.friday.is_closed')->label('Kapalı')->inline(false),
                            ])->columns(3),

                        Forms\Components\Fieldset::make('Cumartesi')
                            ->schema([
                                Forms\Components\TimePicker::make('weekly_hours.saturday.open')->label('Açılış')->default('10:00')->seconds(false),
                                Forms\Components\TimePicker::make('weekly_hours.saturday.close')->label('Kapanış')->default('23:30')->seconds(false),
                                Forms\Components\Toggle::make('weekly_hours.saturday.is_closed')->label('Kapalı')->inline(false),
                            ])->columns(3),

                        Forms\Components\Fieldset::make('Pazar')
                            ->schema([
                                Forms\Components\TimePicker::make('weekly_hours.sunday.open')->label('Açılış')->default('11:00')->seconds(false),
                                Forms\Components\TimePicker::make('weekly_hours.sunday.close')->label('Kapanış')->default('23:00')->seconds(false),
                                Forms\Components\Toggle::make('weekly_hours.sunday.is_closed')->label('Kapalı')->inline(false),
                            ])->columns(3),

                        Forms\Components\Toggle::make('has_delivery')
                            ->label('Paket Servis Mevcut mu?')
                            ->default(true),
                    ]),

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
            ->model($this->getRestaurant())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $restaurant = $this->getRestaurant();

        if ($restaurant) {
            // Haftalık saatlerden opening_hours özetini oluştur (örn: 10:00 - 23:00)
            if (!empty($data['weekly_hours']['monday']['open']) && !empty($data['weekly_hours']['monday']['close'])) {
                $data['opening_hours'] = $data['weekly_hours']['monday']['open'] . ' - ' . $data['weekly_hours']['monday']['close'];
            }

            $restaurant->fill($data);
            $restaurant->is_open = $restaurant->isOpenNow();
            $restaurant->save();

            Notification::make()
                ->title('Restoran bilgileri ve çalışma saatleri başarıyla kaydedildi!')
                ->success()
                ->send();
        }
    }
}
