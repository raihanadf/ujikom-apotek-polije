<?php

namespace App\Filament\Resources\SuplierResource\Pages;

use App\Filament\Resources\SuplierResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSuplier extends CreateRecord
{
    protected static string $resource = SuplierResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);

        return $data;
    }
}
