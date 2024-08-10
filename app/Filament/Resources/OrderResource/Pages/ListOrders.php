<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderOverview;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All')
            ->icon('heroicon-o-rectangle-stack'),
            'New' => Tab::make()->query(fn ($query) => $query->where('status','new'))
            ->icon('heroicon-o-sparkles'),
            'Processing' => Tab::make()->query(fn ($query) => $query->where('status','Processing'))
            ->icon('heroicon-o-arrow-path'),
            'Shipped' => Tab::make()->query(fn ($query) => $query->where('status','Shipped'))
            ->icon('heroicon-o-truck'),
            'Delivered' => Tab::make()->query(fn ($query) => $query->where('status','Delivered'))
            ->icon('heroicon-o-check-circle'),
            'Cancelled' => Tab::make()->query(fn ($query) => $query->where('status','Cancelled'))
            ->icon('heroicon-o-x-circle'),
        ];
    }
}
