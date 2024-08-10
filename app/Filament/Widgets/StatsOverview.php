<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $pollingInterval = '15s';

    protected static bool $isLazy = true;

    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Price', '$' . number_format(Order::sum('total_price'), 2)),
            Stat::make('Delivered Orders', Order::query()->where('status', 'Delivered')->count()),
            Stat::make('Cancelled Orders', Order::query()->where('status', 'Cancelled')->count()),
        ];
    }
}
