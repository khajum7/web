<?php

namespace App\Models;

use App\Http\Trait\CreatorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderLog extends Model
{
    use HasFactory;
    protected $guarded = [];

    use CreatorTrait;
}
