<?php

namespace App\Models;

use App\Enums\ReportStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'store_id','inspector_id', 'name', 'description', 'file_image', 'quality_score', 'comment', 'status',
    ];

    protected $casts = [
        'city' => ReportStatusEnum::class,
    ];

    public function store ():BelongsTo
    {
        return $this->BelongsTo(Store::class,'store_id');
    }

    public function inspector ():BelongsTo
    {
        return $this->BelongsTo(User::class,'inspector_id');
    }
}
