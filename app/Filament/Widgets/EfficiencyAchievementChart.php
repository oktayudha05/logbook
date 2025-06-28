<?php

namespace App\Filament\Widgets;

use App\Models\Logbook;
use App\Models\Equipment;
use App\Models\EfficiencyPlanning;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EfficiencyAchievementChart extends ChartWidget
{
    protected static ?string $heading = 'Comparison Actual and Planning (Rp)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $start = now()->startOfYear();
        $end = now()->endOfYear();

        $logbookData = Logbook::selectRaw("
                DATE_FORMAT(date, '%Y-%m') AS month,
                equipment_id,
                SUM(work_time + delivery_time + COALESCE(trailer_time, 0)) AS total_hours
            ")
            ->whereBetween('date', [$start, $end])
            ->groupBy('month', 'equipment_id')
            ->get();

        $equipmentPrices = Equipment::pluck('price', 'id');

        $actuals = [];

        foreach ($logbookData as $entry) {
            $month = $entry->month;
            $price = $equipmentPrices[$entry->equipment_id] ?? 0;
            $actuals[$month] = ($actuals[$month] ?? 0) + ($entry->total_hours * $price);
        }

        $planning = EfficiencyPlanning::where('year', $start->year)
            ->get()
            ->groupBy(fn($item) => Carbon::createFromDate($item->year, $item->month, 1)->format('Y-m'));

        $targets = [];
        foreach ($planning as $month => $plans) {
            foreach ($plans as $plan) {
                $targets[$month] = ($targets[$month] ?? 0) + $plan->target;
            }
        }

        $allMonths = collect([...array_keys($actuals), ...array_keys($targets)])
            ->unique()
            ->sort()
            ->values();

        return [
            'datasets' => [
                [
                    'label' => 'Aktual',
                    'data' => $allMonths->map(fn($m) => $actuals[$m] ?? 0),
                    'borderColor' => '#2a9d90',
                    'backgroundColor' => '#2a9d90',
                ],
                [
                    'label' => 'Target',
                    'data' => $allMonths->map(fn($m) => $targets[$m] ?? 0),
                    'borderColor' => '#e76e50',
                    'backgroundColor' => '#e76e50',
                ],
            ],
            'labels' => $allMonths->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
