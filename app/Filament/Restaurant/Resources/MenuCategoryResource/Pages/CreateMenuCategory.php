<?php

namespace App\Filament\Restaurant\Resources\MenuCategoryResource\Pages;

use App\Filament\Restaurant\Resources\MenuCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMenuCategory extends CreateRecord
{
    protected static string $resource = MenuCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        if ($user && $user->restaurant_id) {
            $data['restaurant_id'] = $user->restaurant_id;
        }

        return $data;
    }
}
