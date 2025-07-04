<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EquipmentResource;

class EditEquipment extends EditRecord
{
  protected static string $resource = EquipmentResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make()->icon(null),
    ];
  }
}
