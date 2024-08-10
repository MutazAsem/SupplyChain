<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FarmResource\Pages;
use App\Models\Farm;
use App\Models\User;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\TernaryFilter;


class FarmResource extends Resource
{
    protected static ?string $model = Farm::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Farms';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Farm Name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Farm Description')
                            ->autosize()
                            ->nullable()
                            ->maxLength(65535),
                        FileUpload::make('image')
                            ->label('Farm Image')
                            ->directory('farm-images')
                            ->image()
                            ->imageEditor(),

                        Select::make('owner_id')
                            ->label('Farm Owner')
                            ->relationship('farm_owner', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Select the owner of this farm.')
                            ->required(),

                    ])->columns(2),
                Forms\Components\Section::make('Farm Details')
                    ->schema([
                        TextInput::make('type')
                            ->label('Farm Type')
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('commercial_registration_number')
                            ->label('Commercial Registration Number')
                            ->helperText('Enter the commercial registration number for this farm.')
                            ->required()
                            ->numeric()
                            ->maxLength(11)
                            ->unique(Farm::class, 'commercial_registration_number', ignoreRecord: true),
                        Textarea::make('farming_methods')
                            ->label('Farming Methods')
                            ->helperText('Enter the farming methods used on this farm.')
                            ->autosize()
                            ->nullable()
                            ->maxLength(255),
                        Textarea::make('pesticides')
                            ->autosize()
                            ->nullable()
                            ->helperText('Enter the pesticides used on this farm.')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true)
                            ->rules(['boolean'])
                            ->helperText('Toggle to activate or deactivate the farm.')
                    ])->columns(2),

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
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('type')->sortable()->searchable(),
                TextColumn::make('commercial_registration_number')
                    ->label('Commercial Reg. No.')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                IconColumn::make('status')->label('Active')
                    ->sortable()
                    ->toggleable()
                    ->boolean(),
                TextColumn::make('farm_owner.name')
                    ->label('Owner')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->label('status')
                    ->boolean()
                    ->trueLabel('Only activate Farms')
                    ->falseLabel('Only deactivate Farms')
                    ->native(true),
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
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFarms::route('/'),
            'create' => Pages\CreateFarm::route('/create'),
            'edit' => Pages\EditFarm::route('/{record}/edit'),
        ];
    }
}
