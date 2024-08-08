<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SupplierTypeEnum: string implements HasLabel
{
    case MAIN_SUPPLIER = 'Main Supplier';
    case SECONDARY_SUPPLIER = 'Secondary Supplier';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::MAIN_SUPPLIER => 'Main Supplier',
            self::SECONDARY_SUPPLIER => 'Secondary Supplier',
        };
    }
}