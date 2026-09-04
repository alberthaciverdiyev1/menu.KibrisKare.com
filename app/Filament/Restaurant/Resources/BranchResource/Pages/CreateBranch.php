<?php

namespace App\Filament\Restaurant\Resources\BranchResource\Pages;

use App\Filament\Restaurant\Resources\BranchResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        if ($user && $user->restaurant_id) {
            $data['restaurant_id'] = $user->restaurant_id;
        }

        return $data;
    }
}
