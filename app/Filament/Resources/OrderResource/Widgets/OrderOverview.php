<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::query()->where('status','New')->count()),
            Stat::make('Processing Orders', Order::query()->where('status','Processing')->count()),
            Stat::make('Shipped Orders', Order::query()->where('status','Shipped')->count()),
            // Stat::make('Delivered Orders', Order::query()->where('status','Delivered')->count()),
            Stat::make('Cancelled Orders', Order::query()->where('status','Cancelled')->count()),
            Stat::make('Total Orders', Order::all()->count()),
            Stat::make('Total Price', '$' . number_format(Order::sum('total_price'), 2)),
        ];
    }
}
