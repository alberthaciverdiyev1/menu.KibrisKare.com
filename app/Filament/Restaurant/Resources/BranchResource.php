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
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Şube Telefon Numarası')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Section::make('Haftalık Çalışma Saatleri (7 Gün)')
                    ->description('Şubenin açık veya kapalı durumu bu saatlere göre otomatik belirlenir.')
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
                    ]),
                Forms\Components\TextInput::make('latitude')
                    ->label('Harita Enlem (Lat)')
                    ->numeric(),
                Forms\Components\TextInput::make('longitude')
                    ->label('Harita Boylam (Lng)')
                    ->numeric(),
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
