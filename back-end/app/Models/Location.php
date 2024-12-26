<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $fillable = ['location_name', 'description'];

    public function loadingRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoadingRecord::class);
    }
}
