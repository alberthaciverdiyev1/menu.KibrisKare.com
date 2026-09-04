<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\MenuItemResource\Pages;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Yemekler & İçecekler';
    protected static ?string $modelLabel = 'Menü Ürünü';
    protected static ?string $pluralModelLabel = 'Menü Ürünleri';
    protected static ?int $navigationSort = 4;

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
        $user = Auth::user();
        $restaurantId = $user?->restaurant_id;

        return $form
            ->schema([
                Forms\Components\Hidden::make('restaurant_id')
                    ->default(fn () => Auth::user()?->restaurant_id),

                Forms\Components\Select::make('menu_category_id')
                    ->label('Menü Kategorisi')
                    ->options(function () use ($restaurantId) {
                        return MenuCategory::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('name')
                    ->label('Yemek / Ürün Adı')
                    ->placeholder('Örn: Kıbrıs Şeftali Kebabı')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('İçindekiler & Açıklama')
                    ->rows(2)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->label('Fiyat')
                    ->numeric()
                    ->prefix('₺')
                    ->required(),

                Forms\Components\TextInput::make('currency')
                    ->label('Para Birimi')
                    ->default('₺')
                    ->maxLength(10),

                Forms\Components\FileUpload::make('image')
                    ->label('Yemek Fotoğrafı')
                    ->image()
                    ->directory('menu-items')
                    ->columnSpanFull(),

                Forms\Components\Section::make('Özellikler & Etiketler')
                    ->schema([
                        Forms\Components\Toggle::make('is_popular')
                            ->label('🔥 Popüler Seçim'),
                        Forms\Components\Toggle::make('is_chef_special')
                            ->label('⭐ Şefin Tavsiyesi'),
                        Forms\Components\Toggle::make('is_vegetarian')
                            ->label('🌱 Vejetaryen'),
                        Forms\Components\Toggle::make('is_spicy')
                            ->label('🌶️ Acılı'),
                    ])->columns(4),

                Forms\Components\TextInput::make('allergens')
                    ->label('Alerjen Uyarısı')
                    ->placeholder('Örn: Gluten, Laktoz, Fıstık içerir')
                    ->maxLength(255),

                Forms\Components\TextInput::make('order')
                    ->label('Sıralama')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Görsel')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ürün Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('menuCategory.name')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_popular')
                    ->label('Popüler')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_chef_special')
                    ->label('Şefin')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('menu_category_id')
                    ->label('Kategoriye Göre')
                    ->relationship('menuCategory', 'name', fn(Builder $query) => 
                        Auth::user()?->restaurant_id ? $query->where('restaurant_id', Auth::user()->restaurant_id) : $query
                    ),
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
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
