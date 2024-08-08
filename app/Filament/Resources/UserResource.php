<?php

namespace App\Filament\Resources;

use App\Enums\UserGenderEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\TernaryFilter;





class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Users';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('User Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->maxLength(50)
                            ->required(),
                        Forms\Components\TextInput::make('last_name')
                            ->label('last name')
                            ->maxLength(50)
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label("Email Address")
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->rule('regex:/^7\d{8}$/')
                            ->helperText('Phone number must start with 7 and have 9 digits'),
                        FileUpload::make('avatar_url')
                            ->label('avatar')
                            ->directory('form-attachments')
                            ->preserveFilenames()
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->required()->directory(function ($record) {
                                return 'avatar/' . ($record ? $record->id : 'new');
                            }),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => UserGenderEnum::MALE->value,
                                'female' => UserGenderEnum::FEMALE->value,
                            ]),
                        Forms\Components\Toggle::make('status')
                            ->label('status')
                            ->helperText('Toggle to activate or deactivate the user account')
                            ->default(true),
                    ])->columns(2)->columnSpan('full'),

                Forms\Components\Section::make('Password')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->required()
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->minLength(8)
                            ->maxLength(255)
                            ->rule(Password::default())
                            ->helperText('Password must be at least 8 characters long.'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->required()
                            ->password()
                            ->minLength(8)
                            ->maxLength(255)
                            ->rule(Password::default())
                            ->same('password')
                            ->helperText('Re-enter your password to confirm.')
                    ])->columns(2)->columnSpan('full')->visible(fn ($livewire) => $livewire instanceof CreateUser),

                Forms\Components\Section::make('Change Password')
                    ->schema([
                        Forms\Components\TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->nullable()
                            ->minLength(8)
                            ->maxLength(255)
                            ->rule(Password::default())
                            ->helperText('Password must be at least 8 characters long.'),
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->same('new_password')
                            ->nullable()
                            ->helperText('Re-enter your new password to confirm.')
                    ])->columns(2)->columnSpan('full')->visible(fn ($livewire) => $livewire instanceof EditUser)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('avatar'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),


                Tables\Columns\IconColumn::make('status')
                    ->sortable()
                    ->toggleable()
                    ->label('status')
                    ->boolean(),
                Tables\Columns\IconColumn::make('status')
                    ->sortable()
                    ->toggleable()
                    ->label('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                TernaryFilter::make('status')
                    ->label('status')
                    ->boolean()
                    ->trueLabel('Only activate Users')
                    ->falseLabel('Only Hidden Users')
                    ->native(true),
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Created From'),
                        Forms\Components\DatePicker::make('created_until')->label('Created Until'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
                TrashedFilter::make(),

            ])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
