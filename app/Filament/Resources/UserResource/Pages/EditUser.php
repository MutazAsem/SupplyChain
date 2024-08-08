<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    //if we changed the password from Edit User , the password will change else the password will be same
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure we handle the new password correctly
        if (filled($data['new_password'])) {
            $data['password'] = Hash::make($data['new_password']);
        } else {
            // Ensure password is not overwritten if no new password is provided
            unset($data['password']);
        }

        return $data;
    }
}
