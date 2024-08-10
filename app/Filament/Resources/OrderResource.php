<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
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
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false)
                            ->reactive()
                            ->afterStateUpdated(
                                fn(callable $set, $state) =>
                                $set('address_id', null) // Reset address_id when supplier changes
                            ),
                        Forms\Components\Select::make('farm_id')
                            ->relationship('farm', 'name')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                $set('product_id', null); // Reset product_id when farm changes
                            }),
                        Forms\Components\Select::make('address_id')
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
                            // ->disabled(fn(Forms\Get $get): bool => !filled($get('supplier_id')))
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false),
                        Forms\Components\Select::make('product_id')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false)
                            ->options(function (callable $get) {
                                $farmId = $get('farm_id');
                                if ($farmId) {
                                    return Product::where('farm_id', $farmId)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                }
                                return [];
                            })
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('unit_price', $product->unit_price); // Set the unit price based on selected product
                                        $set('quantity_available', $product->quantity_available); // Set available quantity for helper text


                                        // Set the total price based on the default quantity of 1
                                        $defaultQuantity = 1;
                                        $set('total_price', $product->unit_price * $defaultQuantity);
                                    }
                                }
                            })
                            ->reactive()
                        // ->disabled(fn(Forms\Get $get): bool => !filled($get('farm_id')))
                        ,
                        Forms\Components\Select::make('delivery_id')
                            ->relationship('delivery', 'name')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->markAsRequired(false)
                            ->minValue(1)
                            ->default(1.00)
                            ->numeric()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $quantity = $get('quantity') ?? 1;
                                $unitPrice = $get('unit_price') ?? 0;
                                $set('total_price', $quantity * $unitPrice); // Update total price based on quantity and unit price
                            })
                            ->maxValue(function (callable $get) {
                                return $get('quantity_available') ?? PHP_INT_MAX; // Set max value based on available quantity
                            })
                            ->helperText(function (callable $get) {
                                $available = $get('quantity_available');
                                return $available ? "Available quantity: $available" : "Select a product to see available quantity";
                            }),
                        Forms\Components\TextInput::make('unit_price')
                            ->required()
                            ->markAsRequired(false)
                            ->minValue(1)
                            ->reactive()
                            ->readOnly(),
                        Forms\Components\ToggleButtons::make('status')
                            ->required()
                            ->markAsRequired(false)
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
                            ->markAsRequired(false)
                            ->dehydrateStateUsing(
                                fn($state, callable $get) => $get('quantity') * $get('unit_price')
                            )
                            ->columnSpanFull()
                            ->readOnly(),
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
                    ->sortable()
                    ->searchable(),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->searchable(),
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
            ])
            ->filters([
                //
                Tables\Filters\SelectFilter::make('Supplier name')
                    ->relationship('supplier', 'name'),
                Tables\Filters\SelectFilter::make('Farm name')
                    ->relationship('farm', 'name'),
                Tables\Filters\SelectFilter::make('Product name')
                    ->relationship('product', 'name'),

                // Tables\Filters\SelectFilter::make('delivery name')
                //     ->options(User::whereHas('roles', function ($query) {
                //         $query->where('name', 'delivery');
                //     })->pluck('name', 'id')),

                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Created From')->native(false),
                        Forms\Components\DatePicker::make('created_until')->label('Created Until')->native(false),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
