<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\BranchReviewResource\Pages;
use App\Models\Branch;
use App\Models\BranchReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BranchReviewResource extends Resource
{
    protected static ?string $model = BranchReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationLabel = 'Yorum & Değerlendirmeler';
    protected static ?string $modelLabel = 'Yorum';
    protected static ?string $pluralModelLabel = 'Yorumlar ve Puanlar';
    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user && $user->restaurant_id) {
            $query->whereHas('branch', function (Builder $b) use ($user) {
                $b->where('restaurant_id', $user->restaurant_id);
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('branch_id')
                    ->label('Şube')
                    ->options(function () {
                        $user = Auth::user();
                        $q = Branch::query();
                        if ($user && $user->restaurant_id) {
                            $q->where('restaurant_id', $user->restaurant_id);
                        }
                        return $q->pluck('name', 'id');
                    })
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
                    ->label('Onaylı (Menüde Görünsün)')
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
                    ->limit(60)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->comment),
                Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Onaylı'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Şubeye Göre Filtrele')
                    ->options(function () {
                        $user = Auth::user();
                        $q = Branch::query();
                        if ($user && $user->restaurant_id) {
                            $q->where('restaurant_id', $user->restaurant_id);
                        }
                        return $q->pluck('name', 'id');
                    }),
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Puana Göre Filtrele')
                    ->options([
                        5 => '5 Yıldız (★★★★★)',
                        4 => '4 Yıldız (★★★★☆)',
                        3 => '3 Yıldız (★★★☆☆)',
                        2 => '2 Yıldız (★★☆☆☆)',
                        1 => '1 Yıldız (★☆☆☆☆)',
                    ]),
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Onay Durumu')
                    ->trueLabel('Yalnızca Onaylananlar')
                    ->falseLabel('Onaysızlar'),
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
