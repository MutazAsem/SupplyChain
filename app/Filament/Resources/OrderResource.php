<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Address;
use App\Models\Order;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Details')
                    ->schema([
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(
                                fn(callable $set, $state) =>
                                $set('address_id', null) // Reset address_id when supplier changes
                            ),
                        Forms\Components\Select::make('farm_id')
                            ->relationship('farm', 'name')
                            ->required(),
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required(),
                        Forms\Components\Select::make('address_id')
                            // ->relationship('address', 'name')
                            ->options(function (callable $get) {
                                $supplierId = $get('supplier_id');
                                if ($supplierId) {
                                    return Address::where('addressable_type', Supplier::class)
                                        ->where('addressable_id', $supplierId)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                }
                                return [];
                            })
                            ->required(),
                        Forms\Components\Select::make('delivery_id')
                            ->relationship('delivery', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->default(1.00)
                            ->reactive(),
                        Forms\Components\TextInput::make('unit_price')
                            ->required()
                            ->numeric()
                            ->reactive(),
                        Forms\Components\ToggleButtons::make('status')
                            ->required()
                            ->markAsRequired(false)->options(OrderStatusEnum::class)
                            ->icons([
                                'New' => 'heroicon-o-sparkles',
                                'Processing' => 'heroicon-o-arrow-path',
                                'Shipped' => 'heroicon-o-truck',
                                'Delivered' => 'heroicon-o-check-circle',
                                'Cancelled' => 'heroicon-o-x-circle',
                            ])
                            ->colors([
                                'New' => 'info',
                                'Processing' => 'warning',
                                'Shipped' => 'warning',
                                'Delivered' => 'success',
                                'Cancelled' => 'danger',
                            ])
                            ->inline()
                            ->default('New'),
                        Forms\Components\TextInput::make('total_price')
                            ->required()
                            ->numeric()
                            ->dehydrateStateUsing(
                                fn($state, callable $get) =>
                                $get('quantity') * $get('unit_price')
                            )
                            ->disabled(),
                        Forms\Components\Textarea::make('note')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2)->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('farm.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address.name')
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
