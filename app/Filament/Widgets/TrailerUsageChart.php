<?php

namespace App\Filament\Widgets;

use App\Models\Logbook;
use Flowframe\Trend\Trend;
use App\Enums\EquipmentType;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class TrailerUsageChart extends ChartWidget
{
  protected static ?string $heading = 'Trailer usage hour chart this year';
  protected static ?int $sort = 2;

  protected function getOptions(): array
  {
    return [
      'elements' => [
        'bar' => [
          'borderWidth' => 0,
        ]
      ],
      'scales' => [
        'y' => [
          'grid' => [
            'display' => true,
          ],
          'ticks' => [
            'stepSize' => 1,
          ],
        ],
        'x' => [
          'grid' => [
            'display' => false,
          ]
        ],
      ],
      'plugins' => [
        'legend' => [
          'display' => true,
        ],
      ],
    ];
  }

  protected function getData(): array
  {
      $start = now()->startOfYear()->format('Y-m-d');
      $end = now()->endOfYear()->format('Y-m-d');

      $rawData = \DB::table('logbooks')
          ->selectRaw("DATE_FORMAT(`date`, '%Y-%m') as month, SUM(trailer_time) as total")
          ->where('type', EquipmentType::TRAILER)
          ->whereBetween('date', [$start, $end])
          ->groupByRaw("DATE_FORMAT(`date`, '%Y-%m')")
          ->orderBy('month')
          ->pluck('total', 'month');

      $months = collect(range(1, 12))->map(function ($month) {
          return now()->startOfYear()->month($month)->format('Y-m');
      });

      return [
          'datasets' => [[
              'label' => 'Work time',
              'data' => $months->map(fn($month) => $rawData[$month] ?? 0),
              'backgroundColor' => '#e8c468',
              'borderColor' => '#e8c468',
          ]],
          'labels' => $months->map(fn($month) => \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M')),
      ];
  }



  protected function getType(): string
  {
    return 'bar';
  }
}
