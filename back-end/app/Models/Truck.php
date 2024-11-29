<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use SoftDeletes;

    protected $fillable = ['driver_id', 'plate_number', 'weight', 'status'];

    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function loadingRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoadingRecord::class);
    }
}
