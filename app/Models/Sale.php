<?php

namespace App\Models;

use App\Constant\StatusConstant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function address(): HasOne
    {
        return $this->hasOne('App\Models\Address', 'sale_id', 'id');
    }

    public function salesItems() : HasMany
    {
        return $this->hasMany('App\Models\SaleItem', 'sale_id', 'id');

    }

    public function status() : Attribute
    {
        return  Attribute::make(
            get: fn (string $value) => StatusConstant::$orderStatus[$value],
            set: fn(string $value) => strval(array_flip(StatusConstant::$orderStatus)[$value])
        );

    }

    public function approvalStatus() : Attribute
    {
        return  Attribute::make(
            get: fn (string $value) => StatusConstant::$approvedStatus[$value],
            set: fn(string $value) => strval(array_flip(StatusConstant::$approvedStatus)[$value])
        );

    }

    public function scopeSearch($query, $search)
    {
        return $query->orWhere(function ($query) use ($search) {
            $query->where('id', 'like', '%'.$search.'%');
            $query->orWhere('organization_name', 'like', '%'.$search.'%');
        })
        
        ->orWhereHas('address', function ($query) use ($search) {
            $query->where('email', 'like', '%'.$search.'%');
            $query->orWhere('phone', 'like', '%'.$search.'%');
            $query->orWhere('first_name', 'like', '%'.$search.'%');
            $query->orWhere('last_name', 'like', '%'.$search.'%');
            $query->orWhere('country', 'like', '%'.$search.'%');
            $query->orWhere('state', 'like', '%'.$search.'%');
            $query->orWhere('address1', 'like', '%'.$search.'%');
        });
    }
}
