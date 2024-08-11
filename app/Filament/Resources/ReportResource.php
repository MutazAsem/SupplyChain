<?php

namespace App\Filament\Resources;

use App\Enums\ReportStatusEnum;
use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';


    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    protected static int $globalSearchResultsLimit = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->markAsRequired(false)
                            ->maxLength(255),
                        Forms\Components\Select::make('store_id')
                            ->relationship('store', 'id')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false),
                        Forms\Components\Select::make('inspector_id')
                            ->options(User::whereHas('roles', function ($query) {
                                $query->where('name', 'inspector');
                            })->pluck('name', 'id'))
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->markAsRequired(false),
                        Forms\Components\TextInput::make('quality_score')
                            ->required()
                            ->markAsRequired(false)
                            ->minValue(1)
                            ->maxValue(5)
                            ->helperText('Enter Quality Score Between 5 and 1')
                            ->numeric(),
                        Forms\Components\FileUpload::make('file_image')
                            ->label('File (Image or Pdf) ')
                            ->required()
                            ->markAsRequired(false)
                            ->imageEditor(),
                        Forms\Components\ToggleButtons::make('status')
                            ->required()
                            ->markAsRequired(false)
                            ->options(ReportStatusEnum::class)
                            ->icons([
                                'Pending' => 'heroicon-o-arrow-path',
                                'Pass' => 'heroicon-o-check-circle',
                                'Fail' => 'heroicon-o-x-circle',
                            ])
                            ->colors([
                                'Pending' => 'warning',
                                'Pass' => 'success',
                                'Fail' => 'danger',
                            ])
                            ->inline()
                            ->default('Pending'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description (optional)')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('comment')
                            ->label('Comment (optional)')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2)->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('store.id')
                    ->label('Store ID')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('inspector.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quality_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('file_image')
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
                Tables\Filters\SelectFilter::make('Store')
                    ->relationship('store', 'id')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('Inspector name')
                    ->options(User::whereHas('roles', function ($query) {
                        $query->where('name', 'inspector');
                    })->pluck('name', 'id')),
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
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
