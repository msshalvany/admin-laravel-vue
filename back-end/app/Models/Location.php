<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes , HasFactory;

    protected $fillable = ['location_name', 'description' , 'phone' , 'ip'];

    public function loadingRecords(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(LoadingRecord::class);
    }

    public function Users(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(User::class);
    }

}
