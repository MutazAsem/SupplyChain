<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryDetailResource\Pages;
use App\Filament\Resources\DeliveryDetailResource\RelationManagers;
use App\Models\DeliveryDetail;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryDetailResource extends Resource
{
    protected static ?string $model = DeliveryDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $recordTitleAttribute = 'plate_number';

    public static function getGloballySearchableAttributes(): array
    {
        return ['plate_number', 'vehicle_type'];
    }

    protected static int $globalSearchResultsLimit = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Delivery Details')
                    ->schema([
                        Forms\Components\Select::make('delivery_id')
                            ->relationship('delivery', 'name')
                            ->required()
                            ->markAsRequired(false),
                        Forms\Components\TextInput::make('plate_number')
                            ->required()
                            ->markAsRequired(false)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('vehicle_type')
                            ->required()
                            ->markAsRequired(false)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('guarantor_registration_no')
                            ->required()
                            ->markAsRequired(false)
                            ->numeric(),
                    ])->columns(2)->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('delivery.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plate_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guarantor_registration_no')
                    ->numeric()
                    ->searchable(),
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
                //
                // Tables\Filters\SelectFilter::make('delivery name')
                //     ->options(User::whereHas('roles', function ($query) {
                //         $query->where('name', 'delivery');
                //     })->pluck('name', 'id')),
            ])
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
            'index' => Pages\ListDeliveryDetails::route('/'),
            'create' => Pages\CreateDeliveryDetail::route('/create'),
            'edit' => Pages\EditDeliveryDetail::route('/{record}/edit'),
        ];
    }
}
