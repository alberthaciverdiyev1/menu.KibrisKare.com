<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchReviewResource\Pages;
use App\Models\Branch;
use App\Models\BranchReview;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchReviewResource extends Resource
{
    protected static ?string $model = BranchReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationLabel = 'Yorum & Değerlendirmeler';
    protected static ?string $modelLabel = 'Yorum';
    protected static ?string $pluralModelLabel = 'Yorumlar ve Puanlar';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('branch_id')
                    ->label('Şube')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('rating')
                    ->label('Puan')
                    ->options([
                        5 => '★★★★★ (5 Yıldız)',
                        4 => '★★★★☆ (4 Yıldız)',
                        3 => '★★★☆☆ (3 Yıldız)',
                        2 => '★★☆☆☆ (2 Yıldız)',
                        1 => '★☆☆☆☆ (1 Yıldız)',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('author_name')
                    ->label('Müşteri / Yazar Adı')
                    ->default('Anonim Misafir')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_approved')
                    ->label('Onaylı')
                    ->default(true),
                Forms\Components\Textarea::make('comment')
                    ->label('Yorum Metni')
                    ->columnSpanFull()
                    ->rows(3),
                Forms\Components\TextInput::make('ip_address')
                    ->label('IP Adresi')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.restaurant.name')
                    ->label('Restoran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Şube')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Puan')
                    ->formatStateUsing(fn ($state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state) . " ({$state})")
                    ->color('warning')
                    ->weight('bold')
                    ->sortable(),
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Müşteri')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Yorum')
                    ->limit(50)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->comment),
                Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Onaylı'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('restaurant')
                    ->label('Restorana Göre')
                    ->relationship('branch.restaurant', 'name'),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Şubeye Göre')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Puana Göre')
                    ->options([
                        5 => '5 Yıldız (★★★★★)',
                        4 => '4 Yıldız (★★★★☆)',
                        3 => '3 Yıldız (★★★☆☆)',
                        2 => '2 Yıldız (★★☆☆☆)',
                        1 => '1 Yıldız (★☆☆☆☆)',
                    ]),
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Onay Durumu'),
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
            'index' => Pages\ListBranchReviews::route('/'),
            'create' => Pages\CreateBranchReview::route('/create'),
            'edit' => Pages\EditBranchReview::route('/{record}/edit'),
        ];
    }
}
