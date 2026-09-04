<?php

namespace App\Filament\Restaurant\Resources\BranchResource\Pages;

use App\Filament\Restaurant\Resources\BranchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['weekly_hours']['monday']['open']) && !empty($data['weekly_hours']['monday']['close'])) {
            $data['opening_hours'] = $data['weekly_hours']['monday']['open'] . ' - ' . $data['weekly_hours']['monday']['close'];
        }

        return $data;
    }
}
