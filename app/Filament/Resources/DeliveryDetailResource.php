<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryDetailResource\Pages;
use App\Filament\Resources\DeliveryDetailResource\RelationManagers;
use App\Models\DeliveryDetail;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('delivery_id')
                    ->relationship('delivery', 'name')
                    ->required(),
                Forms\Components\TextInput::make('plate_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('vehicle_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('guarantor_registration_no')
                    ->numeric(),
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
            'index' => Pages\ListDeliveryDetails::route('/'),
            'create' => Pages\CreateDeliveryDetail::route('/create'),
            'edit' => Pages\EditDeliveryDetail::route('/{record}/edit'),
        ];
    }
}
