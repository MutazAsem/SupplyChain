<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'supplier_id', 'farm_id', 'product_id', 'quantity', 'unit_price', 'status',
         'address_id', 'total_price', 'delivery_id','note',
    ];

    protected $casts = [
        'city' => OrderStatusEnum::class,
    ];

    public function supplier ():BelongsTo
    {
        return $this->BelongsTo(Supplier::class,'supplier_id');
    }

    public function farm ():BelongsTo
    {
        return $this->BelongsTo(Farm::class,'farm_id');
    }

    public function product ():BelongsTo
    {
        return $this->BelongsTo(Product::class,'product_id');
    }

    public function address ():BelongsTo
    {
        return $this->BelongsTo(Address::class,'address_id');
    }

    public function delivery ():BelongsTo
    {
        return $this->BelongsTo(User::class,'delivery_id');
    }

}
