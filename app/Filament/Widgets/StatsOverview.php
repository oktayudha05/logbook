<?php

namespace App\Filament\Widgets;

use App\Models\Logbook;
use App\Enums\EquipmentType;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;


class StatsOverview extends BaseWidget
{
  protected function getHeading(): ?string
  {
    return 'Stats Overview';
  }

  protected function getDescription(): ?string
  {
    return "Equipment usage statistics of " . " " . now()->format('Y');
  }

  protected function getStats(): array
  {
    $year = now()->year;

    $crane = Logbook::query()
        ->where('type', EquipmentType::CRANE)
        ->whereYear('date', $year)
        ->selectRaw('
          COALESCE(SUM(work_time), 0) as work,
          COALESCE(SUM(delivery_time), 0) as delivery
        ')
        ->first();

    $trailer = Logbook::query()
        ->where('type', EquipmentType::TRAILER)
        ->whereYear('date', $year)
        ->selectRaw('COALESCE(SUM(trailer_time), 0) as trailer')
        ->first();


    return [
      Stat::make('Crane delivery time', $crane->delivery)->description('Total delivery time for crane equipment'),
      Stat::make('Crane usage time', $crane->work + $crane->delivery)->description('Total work time for crane equipment'),
      Stat::make('Trailer usage time', $trailer->trailer)->description('Total work time for trailer equipment'),
    ];
  }
}
