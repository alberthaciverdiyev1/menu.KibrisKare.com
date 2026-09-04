<?php

namespace App\Filament\Restaurant\Resources\BranchReviewResource\Pages;

use App\Filament\Restaurant\Resources\BranchReviewResource;
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
