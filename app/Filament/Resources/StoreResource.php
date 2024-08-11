<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Filament\Resources\StoreResource\RelationManagers;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Products & Inventory';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'id';

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'order.id'];
    }

    protected static int $globalSearchResultsLimit = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Store Details')
                    ->schema([
                        // Forms\Components\Select::make('order_id')
                        //     ->relationship('order', 'id')
                        //     ->label('Order ID')
                        //     ->native(false)
                        //     ->searchable()
                        //     ->preload()
                        //     ->required()
                        //     ->markAsRequired(false)
                        Forms\Components\Select::make('order_id')
                            ->label('Order ID')
                            ->options(function () {
                                // استبعاد العناصر التي تم اختيارها سابقًا
                                $usedOrderIds = Store::pluck('order_id');
                                return \App\Models\Order::whereNotIn('id', $usedOrderIds)->pluck('id', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false)
                            ->rules(['unique:stores,order_id']) // تحقق من أن الحقل فريد
                            // ->unique(Order::class, 'id', ignoreRecord: true)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if ($state) {
                                    // Find the order by the selected order_id
                                    $order = Order::find($state);
                                    if ($order) {
                                        // Set values for other fields based on the selected order
                                        $set('supplier_id', $order->supplier_id);
                                        $set('supplier_name', $order->supplier->name);
                                        $set('farm_id', $order->farm_id);
                                        $set('farm_name', $order->farm->name);
                                        $set('product_id', $order->product_id);
                                        $set('product_name', $order->product->name);
                                        $set('address_id', $order->address_id);
                                        $set('address_name', $order->address->name);
                                        $set('delivery_id', $order->delivery_id);
                                        $set('delivery_name', $order->delivery->name);
                                        $set('quantity', $order->quantity);
                                        $set('unit', $order->unit);
                                        $set('unit_price', $order->unit_price);
                                        $set('total_price', $order->total_price);
                                        $set('note', $order->note);
                                    }
                                }
                            }),
                        Hidden::make('supplier_id'),
                        Hidden::make('farm_id'),
                        Hidden::make('product_id'),
                        Hidden::make('address_id'),
                        Hidden::make('delivery_id'),
                        Hidden::make('quantity'),
                        Hidden::make('unit'),
                        Hidden::make('unit_price'),
                        Hidden::make('total_price'),
                        Hidden::make('note'),

                        Forms\Components\TextInput::make('supplier_name')
                            ->label('Supplier Name')
                            ->readonly()
                            ->visibleOn('create'),
                        Forms\Components\TextInput::make('farm_name')
                            ->label('Farm Name')
                            ->readonly()
                            ->visibleOn('create'),
                        Forms\Components\TextInput::make('product_name')
                            ->label('Product Name')
                            ->readonly()
                            ->visibleOn('create'),
                        Forms\Components\TextInput::make('address_name')
                            ->label('Address Name')
                            ->readonly()
                            ->visibleOn('create'),
                        Forms\Components\TextInput::make('delivery_name')
                            ->label('Delivery Name')
                            ->readonly()
                            ->visibleOn('create'),
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false)
                            ->reactive()
                            ->visibleOn('edit')
                            ->disabled(),
                        Forms\Components\Select::make('farm_id')
                            ->relationship('farm', 'name')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false)
                            ->reactive()
                            ->visibleOn('edit')
                            ->disabled(),
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
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false)
                            ->visibleOn('edit')
                            ->disabled(),
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
                        ->visibleOn('edit')
                        ->disabled(),
                        Forms\Components\Select::make('delivery_id')
                            ->options(User::whereHas('roles', function ($query) {
                                $query->where('name', 'delivery');
                            })->pluck('name', 'id'))
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false)
                            ->visibleOn('edit')
                            ->disabled(),
                        Forms\Components\TextInput::make('unit')
                            ->required()
                            ->markAsRequired(false)
                            ->readOnly(),
                        Forms\Components\TextInput::make('unit_price')
                            ->label('Unit Price')
                            ->readonly()
                            ->numeric(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->readonly()
                            ->numeric(),
                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Price')
                            ->readonly()
                            ->numeric(),
                        Forms\Components\Textarea::make('note')
                            ->label('Note (optional)')
                            ->readonly()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_available')
                            ->required()
                            ->markAsRequired(false)
                            ->default(true),
                    ])->columns(2)->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Store ID')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('order.id')
                    ->label('Order ID')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('farm.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address.name')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('delivery.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('Supplier name')
                    ->relationship('supplier', 'name'),
                Tables\Filters\SelectFilter::make('Farm name')
                    ->relationship('farm', 'name'),
                Tables\Filters\SelectFilter::make('Product name')
                    ->relationship('product', 'name'),
                Tables\Filters\SelectFilter::make('delivery name')
                    ->options(User::whereHas('roles', function ($query) {
                        $query->where('name', 'delivery');
                    })->pluck('name', 'id')),
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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
