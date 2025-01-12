<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'license_number','address'];
    protected $appends = ['average_star'];

    public function trucks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Truck::class);
    }
    public function loadingRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoadingRecord::class);
    }
    // محاسبه میانگین امتیاز راننده
    public function getAverageStarAttribute()
    {
        return $this->loadingRecords()->avg('driver_star') ?: 0;
    }

}
