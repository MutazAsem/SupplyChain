<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrderStatusEnum: string implements HasLabel
{
    case NEW = 'New';
    case PROCESSING = 'Processing';
    case SHIPPED = 'Shipped';
    case DELIVERED = 'Delivered';
    case CANCELLED = 'Cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NEW => 'New',
            self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
            
        };
    }
}
