<?php

namespace App\Filament\Widgets;

use App\Models\Logbook;
use App\Enums\EquipmentType;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;

class CraneUsageChart extends ChartWidget
{
    protected static ?string $heading = 'Crane usage hour chart this year';
    protected static ?int $sort = 1;

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
                    'grid' => ['display' => true],
                    'ticks' => ['stepSize' => 1],
                ],
                'x' => [
                    'grid' => ['display' => false],
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
            ->selectRaw("DATE_FORMAT(`date`, '%Y-%m') as month, SUM(work_time + delivery_time) as total")
            ->where('type', EquipmentType::CRANE)
            ->whereBetween('date', [$start, $end])
            ->groupByRaw("DATE_FORMAT(`date`, '%Y-%m')")
            ->orderBy('month')
            ->pluck('total', 'month');

        $months = collect(range(1, 12))->map(fn($m) => now()->startOfYear()->month($m)->format('Y-m'));

        return [
            'datasets' => [[
                'label' => 'Work time',
                'data' => $months->map(fn($month) => $rawData[$month] ?? 0),
                'backgroundColor' => '#2a9d90',
                'borderColor' => '#2a9d90',
            ]],
            'labels' => $months->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M')),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
