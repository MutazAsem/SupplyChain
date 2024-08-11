<?php

namespace App\Filament\Resources;

use App\Enums\SupplierTypeEnum;
use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use App\Models\User;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    protected static int $globalSearchResultsLimit = 5;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Supplier Details')->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->markAsRequired(false)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('commercial_registration_number')
                        ->label('Commercial Registration Number')
                        ->helperText('Enter the commercial registration number for this Supplier.')
                        ->numeric()
                        ->maxLength(12)
                        ->required()
                        ->markAsRequired(false)
                        ->unique(Supplier::class, 'commercial_registration_number', ignoreRecord: true),
                    Forms\Components\Select::make('owner_id')
                        ->label('Owner')
                        ->options(User::whereHas('roles', function ($query) {
                            $query->where('name', 'supplier');
                        })->pluck('name', 'id'))
                        ->unique(Supplier::class, 'owner_id', ignoreRecord: true)
                        ->required()
                        ->markAsRequired(false)
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->helperText('Select the owner of this supplier.'),
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options(SupplierTypeEnum::class)
                        ->required()
                        ->markAsRequired(false)
                        ->native(false),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->autosize()
                        ->nullable()
                        ->maxLength(65535),
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->imageEditor()
                        ->directory('supplier-images')
                        ->nullable(),
                    Forms\Components\Toggle::make('status')
                        ->label('Active')
                        ->helperText('Enable or disable the status of the Supplier.')
                        ->default(true),
                ])->columns(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier_owner.name')
                    ->label('Owner')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('commercial_registration_number')
                    ->label('Commercial Reg. No.')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')->label('Active')
                    ->sortable()
                    ->toggleable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Supplier name')
                    ->options(User::whereHas('roles', function ($query) {
                        $query->where('name', 'supplier');
                    })->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('type')
                    ->options(SupplierTypeEnum::class),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('status')
                    ->boolean()
                    ->trueLabel('Only activate Supplier')
                    ->falseLabel('Only deactivate Supplier')
                    ->native(true),
            ])->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
