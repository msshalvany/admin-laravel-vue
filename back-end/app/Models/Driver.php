<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'license_number','address'];

    public function trucks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Truck::class);
    }
}
