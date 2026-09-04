<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\MenuCategoryResource\Pages;
use App\Models\MenuCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MenuCategoryResource extends Resource
{
    protected static ?string $model = MenuCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'Menü Kategorileri';
    protected static ?string $modelLabel = 'Menü Kategorisi';
    protected static ?string $pluralModelLabel = 'Menü Kategorileri';
    protected static ?int $navigationSort = 3;

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
                    ->label('Kategori Adı')
                    ->placeholder('Örn: Başlangıçlar, Burgerler, İçecekler')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('order')
                    ->label('Menü Sıralaması')
                    ->numeric()
                    ->default(0)
                    ->helperText('Menüde hangi sırada gösterileceğini belirler.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Kategori Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Yemek Sayısı')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->defaultSort('order')
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
            'index' => Pages\ListMenuCategories::route('/'),
            'create' => Pages\CreateMenuCategory::route('/create'),
            'edit' => Pages\EditMenuCategory::route('/{record}/edit'),
        ];
    }
}
