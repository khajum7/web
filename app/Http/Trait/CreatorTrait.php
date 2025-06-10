<?php

namespace App\Http\Trait;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CreatorTrait
{

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
