<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddressResource\Pages;
use App\Models\Address;
use App\Enums\CityEnum;
use App\Models\Supplier;
use App\Models\Farm;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Addresses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Address Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->autosize()
                    ->maxLength(65535)
                    ->nullable(),
                Forms\Components\Select::make('city')
                    ->label('City')
                    ->options(CityEnum::class)
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('address_link')
                    ->label('Google Maps Link')
                    ->url()
                    ->suffixIcon('heroicon-m-globe-alt')
                    ->suffixIconColor('success')
                    ->nullable(),

                MorphToSelect::make('addressable')
                    ->types([
                        MorphToSelect\Type::make(Farm::class)
                            ->titleAttribute('name')
                            ->label('Farm'),
                        MorphToSelect\Type::make(Supplier::class)
                            ->titleAttribute('name')
                            ->label('Supplier')
                    ])
                    ->label('Location Belongs To')
                    ->searchable()
                    ->required()
                    ->preload()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('addressable_type')
                    ->label('Entity Type')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('addressable.name')
                    ->label('Entity Name')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state, $record) => $record->addressable->name ?? 'N/A')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
                    ->options(CityEnum::class)
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('addressable_type')
                    ->options([
                        'App\Models\Farm' => 'Farm',
                        'App\Models\Supplier' => 'Supplier',
                    ])->label('Entity Type'),
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
                ])
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
            // Define any relation managers here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }
}
