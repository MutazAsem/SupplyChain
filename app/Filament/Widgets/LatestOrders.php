<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{

    use HasWidgetShield;
    
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(10)
            ->defaultSort('created_at', 'desc')

            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('farm.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status of order')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match (OrderStatusEnum::from($state)->value) {
                        'New' => 'info',
                        'Processing' => 'warning',
                        'Shipped' => 'warning',
                        'Delivered' => 'success',
                        'Cancelled' => 'danger',
                    })
                    ->icon(fn(string $state): string => match (OrderStatusEnum::from($state)->value) {
                        'New' => 'heroicon-o-sparkles',
                        'Processing' => 'heroicon-o-arrow-path',
                        'Shipped' => 'heroicon-o-truck',
                        'Delivered' => 'heroicon-o-check-circle',
                        'Cancelled' => 'heroicon-o-x-circle',
                    }),

                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address.name')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])->actions([
                    Tables\Actions\EditAction::make()
                    ->url(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
