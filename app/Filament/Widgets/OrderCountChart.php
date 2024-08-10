<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class OrderCountChart extends ChartWidget
{
    use HasWidgetShield;
    
    protected static ?string $heading = 'Chart';

    protected static ?int $sort = 0;

    protected function getData(): array
    {
        $data = $this->getOrdersPerMonth();
        return [
            //
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data['ordersPerMonth']
                ]
            ],
            'labels' =>  $data['months']

        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getOrdersPerMonth(): array
    {
        $now = Carbon::now();
        $ordersPerMonth = [];
        $months = collect(range(1, 12))->map(function ($month) use ($now, &$ordersPerMonth) {
            $count = Order::whereMonth('created_at', $month)->count();
            $ordersPerMonth[] = $count;
            return $now->month($month)->format('M');
        })->toArray();

        return [
            'ordersPerMonth' => $ordersPerMonth,
            'months' => $months
        ];
    }
}
