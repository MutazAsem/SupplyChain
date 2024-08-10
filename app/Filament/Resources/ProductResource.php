<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Farm;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\TernaryFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->maxLength(255)
                        ->unique(Product::class, 'name', ignoreRecord: true)
                        ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                            if ($operation !== 'create') {
                                return;
                            }
                            // Replace spaces with hyphens and trim any leading/trailing spaces
                            $slug = preg_replace('/\s+/', '-', trim($state));
                            $set('slug', $slug);
                        }),
                    Forms\Components\TextInput::make('slug')
                        ->disabled()
                        ->dehydrated()
                        ->maxLength(255)
                        ->required()
                        ->unique(Product::class, 'slug', ignoreRecord: true),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->autosize()
                        ->maxLength(65535)
                        ->nullable(),
                    Forms\Components\FileUpload::make('image')
                        ->label('Image')
                        ->directory('product-images')
                        ->image(),
                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->helperText('Select the category to which this product belongs.')
                        ->required(),
                    Forms\Components\Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->helperText('Select the farm where this product is produced.')
                        ->required(),
                    Forms\Components\TextInput::make('unit')
                        ->required()
                        ->helperText('Specify the unit of measure for the product')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('quantity_available')
                        ->label('Quantity Available')
                        ->numeric()
                        ->maxLength(11)
                        ->helperText('Enter the quantity of the product currently available.')
                        ->required(),
                    Forms\Components\TextInput::make('packaging')
                        ->required()
                        ->helperText('Describe the packaging of the product.')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('type')
                        ->required()
                        ->helperText('Specify the type of product.')
                        ->maxLength(255),
                    Forms\Components\Toggle::make('status')
                        ->label('Active')
                        ->helperText('Enable or disable the status of the product.')
                        ->default(true),
                    Forms\Components\TextInput::make('rfid_tag')
                        ->label('RFID Tag')
                        ->maxLength(255)
                        ->unique(Product::class, 'rfid_tag', ignoreRecord: true)
                        ->nullable()
                        ->helperText('Assign an RFID tag to this product for tracking purposes.'),

                ])->columns(2)
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
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('farm.name')
                    ->label('Farm')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity_available')
                    ->label('qty')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rfid_tag')
                    ->label('RFID')
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
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('farm')
                    ->relationship('farm', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('status')
                    ->label('status')
                    ->boolean()
                    ->trueLabel('Only activate Products')
                    ->falseLabel('Only deactivate Products')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
