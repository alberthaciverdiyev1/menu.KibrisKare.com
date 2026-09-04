<?php

namespace App\Filament\Resources\BranchReviewResource\Pages;

use App\Filament\Resources\BranchReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranchReview extends EditRecord
{
    protected static string $resource = BranchReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
