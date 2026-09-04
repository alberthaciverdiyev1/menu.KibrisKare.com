<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Şubelerimiz';
    protected static ?string $modelLabel = 'Şube';
    protected static ?string $pluralModelLabel = 'Şubeler';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('restaurant_id')
                    ->default(fn () => Auth::user()?->restaurant_id),
                Forms\Components\TextInput::make('name')
                    ->label('Şube Adı')
                    ->placeholder('Örn: Lefkoşa Dereboyu Şubesi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('city_id')
                    ->label('Şehir')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->label('Açık Adres')
                    ->live(onBlur: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Şube Telefon Numarası')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Section::make('Harita Konumu (OpenStreetMap)')
                    ->description('Haritaya tıklayarak veya pini sürükleyerek şubenin kesin konumunu seçebilirsiniz.')
                    ->schema([
                        Forms\Components\ViewField::make('map')
                            ->view('filament.forms.components.osm-map-picker')
                            ->viewData([
                                'latStatePath' => 'data.latitude',
                                'lngStatePath' => 'data.longitude',
                                'addressStatePath' => 'data.address',
                            ])
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('latitude')
                            ->label('Harita Enlem (Lat)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Harita Boylam (Lng)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),
                Forms\Components\Section::make('Haftalık Çalışma Saatleri (7 Gün)')
                    ->description('Şubenin açık/kapalı durumu bu saatlere göre belirlenir.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(12)
                            ->schema([
                                Forms\Components\Placeholder::make('header_day')->label('Gün')->content('')->columnSpan(3)->extraAttributes(['class' => 'font-semibold text-sm text-gray-500']),
                                Forms\Components\Placeholder::make('header_open')->label('Açılış')->content('')->columnSpan(3)->extraAttributes(['class' => 'font-semibold text-sm text-gray-500']),
                                Forms\Components\Placeholder::make('header_close')->label('Kapanış')->content('')->columnSpan(3)->extraAttributes(['class' => 'font-semibold text-sm text-gray-500']),
                                Forms\Components\Placeholder::make('header_closed')->label('Tüm Gün Kapalı')->content('')->columnSpan(3)->extraAttributes(['class' => 'font-semibold text-sm text-gray-500']),
                            ])->extraAttributes(['class' => 'hidden md:grid border-b pb-1 mb-1']),

                        ...collect([
                            ['key' => 'monday', 'name' => 'Pazartesi', 'open' => '10:00', 'close' => '23:00'],
                            ['key' => 'tuesday', 'name' => 'Salı', 'open' => '10:00', 'close' => '23:00'],
                            ['key' => 'wednesday', 'name' => 'Çarşamba', 'open' => '10:00', 'close' => '23:00'],
                            ['key' => 'thursday', 'name' => 'Perşembe', 'open' => '10:00', 'close' => '23:00'],
                            ['key' => 'friday', 'name' => 'Cuma', 'open' => '10:00', 'close' => '23:30'],
                            ['key' => 'saturday', 'name' => 'Cumartesi', 'open' => '10:00', 'close' => '23:30'],
                            ['key' => 'sunday', 'name' => 'Pazar', 'open' => '11:00', 'close' => '23:00'],
                        ])->map(function ($day) {
                            return Forms\Components\Grid::make(12)
                                ->extraAttributes(['class' => 'items-center py-1 border-b border-gray-100 dark:border-gray-800 last:border-0'])
                                ->schema([
                                    Forms\Components\Placeholder::make("label_{$day['key']}")
                                        ->hiddenLabel()
                                        ->content($day['name'])
                                        ->columnSpan(['default' => 12, 'md' => 3])
                                        ->extraAttributes(['class' => 'font-medium text-gray-700 dark:text-gray-200']),
                                    Forms\Components\TimePicker::make("weekly_hours.{$day['key']}.open")
                                        ->hiddenLabel()
                                        ->default($day['open'])
                                        ->seconds(false)
                                        ->columnSpan(['default' => 6, 'md' => 3]),
                                    Forms\Components\TimePicker::make("weekly_hours.{$day['key']}.close")
                                        ->hiddenLabel()
                                        ->default($day['close'])
                                        ->seconds(false)
                                        ->columnSpan(['default' => 6, 'md' => 3]),
                                    Forms\Components\Toggle::make("weekly_hours.{$day['key']}.is_closed")
                                        ->label('Kapalı')
                                        ->inline(true)
                                        ->columnSpan(['default' => 12, 'md' => 3]),
                                ]);
                        })->toArray(),
                    ]),
                Forms\Components\Toggle::make('is_main')
                    ->label('Ana / Merkez Şube')
                    ->default(false),
                Forms\Components\Toggle::make('is_active')
                    ->label('Şube Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Şube Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Şehir')
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Adres')
                    ->limit(35),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon'),
                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Puan')
                    ->formatStateUsing(fn ($record) => '★ ' . number_format($record->average_rating, 1) . ' (' . $record->reviews_count . ')')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('opening_hours')
                    ->label('Saatler'),
                Tables\Columns\IconColumn::make('is_main')
                    ->label('Merkez')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
