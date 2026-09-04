<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Filament\Resources\MenuItemResource\RelationManagers;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('restaurant_id')
                    ->relationship('restaurant', 'name')
                    ->live()
                    ->required(),
                Forms\Components\Select::make('menu_category_id')
                    ->relationship('menuCategory', 'name')
                    ->required(),
                Forms\Components\Select::make('branches')
                    ->label('Geçerli Şubeler')
                    ->relationship('branches', 'name', fn(Builder $query, Forms\Get $get) => 
                        $get('restaurant_id') ? $query->where('restaurant_id', $get('restaurant_id')) : $query
                    )
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₺'),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(255)
                    ->default('₺'),
                Forms\Components\FileUpload::make('image')
                    ->image(),
                Forms\Components\Toggle::make('is_popular')
                    ->required(),
                Forms\Components\Toggle::make('is_chef_special')
                    ->required(),
                Forms\Components\Toggle::make('is_vegetarian')
                    ->required(),
                Forms\Components\Toggle::make('is_spicy')
                    ->required(),
                Forms\Components\TextInput::make('allergens')
                    ->maxLength(255),
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('menuCategory.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('branches.name')
                    ->label('Şubeler')
                    ->badge()
                    ->placeholder('Tüm Şubeler'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\IconColumn::make('is_popular')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_chef_special')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_vegetarian')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_spicy')
                    ->boolean(),
                Tables\Columns\TextColumn::make('allergens')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
