<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plate_number', 'vehicle_type', 'guarantor_registration_no', 'delivery_id',
    ];

    public function delivery ():BelongsTo
    {
        return $this->BelongsTo(User::class,'delivery_id');
    }


}
