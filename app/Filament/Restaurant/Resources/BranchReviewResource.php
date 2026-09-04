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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

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
                Forms\Components\TextInput::make('branch.name')
                    ->label('Şube')
                    ->disabled(),
                Forms\Components\TextInput::make('rating')
                    ->label('Puan')
                    ->disabled(),
                Forms\Components\TextInput::make('author_name')
                    ->label('Müşteri / Yazar')
                    ->disabled(),
                Forms\Components\TextInput::make('created_at')
                    ->label('Tarih')
                    ->disabled(),
                Forms\Components\Textarea::make('comment')
                    ->label('Yorum Metni')
                    ->columnSpanFull()
                    ->disabled(),
                Forms\Components\Repeater::make('images')
                    ->relationship('images')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Görsel')
                            ->disk('public')
                            ->directory('reviews')
                            ->image()
                            ->disabled(),
                    ])
                    ->columnSpanFull()
                    ->label('Eklenen Fotoğraflar')
                    ->disabled()
                    ->dehydrated(false)
                    ->collapsible(),
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
                    ->limit(65)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->comment),
                Tables\Columns\TextColumn::make('delete_status')
                    ->label('Silme Talebi')
                    ->badge()
                    ->state(fn (BranchReview $record): string => $record->delete_requested ? 'Talep İletildi' : 'Yok')
                    ->color(fn (BranchReview $record): string => $record->delete_requested ? 'danger' : 'gray'),
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('İncele'),
                Tables\Actions\Action::make('request_deletion')
                    ->label('Silme Talebi Gönder')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (BranchReview $record) => !$record->delete_requested)
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Silme Talebi Gerekçesi (Admine İletilecek)')
                            ->placeholder('Örn: Hakaret, asılsız iddia veya spam içeriyor...')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (BranchReview $record, array $data): void {
                        $record->update([
                            'delete_requested' => true,
                            'delete_request_reason' => $data['reason'] ?? 'Restoran tarafından silinmesi talep edildi.',
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Silme Talebi İletildi')
                            ->body('Yorum silme talebiniz site yöneticilerine (Admine) iletildi.')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Yorumu Silme Talebi')
                    ->modalDescription('Bu yorumun sistemden kaldırılması için Admine gerekçenizle birlikte talep gönderilecektir.')
                    ->modalSubmitActionLabel('Talebi Gönder'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranchReviews::route('/'),
        ];
    }
}
