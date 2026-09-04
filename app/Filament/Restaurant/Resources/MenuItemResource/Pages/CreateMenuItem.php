<?php

namespace App\Filament\Restaurant\Resources\MenuItemResource\Pages;

use App\Filament\Restaurant\Resources\MenuItemResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMenuItem extends CreateRecord
{
    protected static string $resource = MenuItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        if ($user && $user->restaurant_id) {
            $data['restaurant_id'] = $user->restaurant_id;
        }

        return $data;
    }
}
